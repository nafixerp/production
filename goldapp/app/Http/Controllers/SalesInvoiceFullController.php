<?php
namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\ArLedger;
use App\Models\Daybook;
use App\Models\DaybookPart;
use App\Models\FgStock;
use App\Models\FinishedGoods;
use App\Models\SalesInvoiceItem;
use App\Models\SalesInvoiceModel;
use App\Models\SalesOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesInvoiceFullController extends Controller
{
    private function nextSlno(): string
    {
        $last = SalesInvoiceModel::orderByDesc('id')->value('slno');
        $num  = $last ? (int) substr($last, 2) + 1 : 1;
        return 'SI' . str_pad($num, 6, '0', STR_PAD_LEFT);
    }

    public function index(Request $request)
    {
        $invoices = SalesInvoiceModel::with('customer')
            ->when($request->q, fn($q) => $q->where('customer_name', 'like', "%{$request->q}%")->orWhere('invoice_no', 'like', "%{$request->q}%"))
            ->when($request->channel, fn($q) => $q->where('channel', $request->channel))
            ->when($request->from, fn($q) => $q->where('invoice_date', '>=', $request->from))
            ->when($request->to,   fn($q) => $q->where('invoice_date', '<=', $request->to))
            ->orderByDesc('invoice_date')->orderByDesc('id')
            ->paginate(20)->withQueryString();

        return view('sales.sales-invoices.index', compact('invoices'));
    }

    public function create()
    {
        $customers    = Account::where('atype', 'CUSTOMER')->orderBy('name')->get();
        $bankAccounts = Account::where('atype', 'BANK')->orderBy('name')->get();
        $salesOrders  = SalesOrder::whereIn('status', ['confirmed','partially_dispatched'])->orderByDesc('id')->get();
        $fgList       = FinishedGoods::where('status', 1)->orderBy('name')->get();
        $slno         = $this->nextSlno();
        return view('sales.sales-invoices.form', compact('customers','bankAccounts','salesOrders','fgList','slno'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'invoice_date'  => 'required|date',
            'customer_name' => 'required|string',
            'items'         => 'required|array|min:1',
        ]);

        DB::transaction(function () use ($request) {
            $slno  = $this->nextSlno();
            $items = $request->items ?? [];

            [$taxable, $disc, $sgst, $cgst, $igst, $totalCost] = $this->sumItems($items);
            $tcs          = (float)($request->tcs ?? 0);
            $other        = (float)($request->other_charges ?? 0);
            $received     = (float)($request->received_amount ?? 0);
            $grossNet     = $taxable - $disc + $sgst + $cgst + $igst + $tcs + $other;
            $roundOff     = round($grossNet) - $grossNet;
            $net          = $grossNet + $roundOff;
            $balance      = $net - $received;

            $customer = Account::find($request->customer_id);

            $inv = SalesInvoiceModel::create([
                'slno'             => $slno,
                'invoice_no'       => $slno,
                'invoice_date'     => $request->invoice_date,
                'so_id'            => $request->so_id ?: null,
                'customer_id'      => $request->customer_id ?: null,
                'customer_name'    => $request->customer_name,
                'billing_address'  => $request->billing_address,
                'shipping_address' => $request->shipping_address,
                'channel'          => $request->channel ?? 'retail',
                'taxable_amount'   => $taxable,
                'discount'         => $disc,
                'sgst'             => $sgst,
                'cgst'             => $cgst,
                'igst'             => $igst,
                'tcs'              => $tcs,
                'other_charges'    => $other,
                'round_off'        => $roundOff,
                'net_amount'       => $net,
                'received_amount'  => $received,
                'balance_amount'   => $balance,
                'payment_mode'     => $request->payment_mode ?? 'credit',
                'bank_account_id'  => $request->bank_account_id ?: null,
                'cheque_no'        => $request->cheque_no,
                'utr_no'           => $request->utr_no,
                'irn_no'           => $request->irn_no,
                'eway_bill_no'     => $request->eway_bill_no,
                'status'           => 'posted',
                'narration'        => $request->narration,
                'branch_id'        => Auth::user()->branch_id ?? null,
                'created_by'       => Auth::id(),
            ]);

            $this->saveItems($inv->id, $items);

            // Post double-entry daybook
            $this->postDaybook($inv, $customer, $received, $taxable, $disc, $sgst, $cgst, $igst, $tcs, $roundOff, $totalCost);

            // Post AR ledger
            $this->postArLedger($inv, $net, $received, $customer);

            // Reduce fg_stock
            $this->reduceFgStock($items);
        });

        return redirect()->route('sales-invoices.index')->with('success', 'Invoice posted.');
    }

    public function show(SalesInvoiceModel $salesInvoice)
    {
        $salesInvoice->load('items', 'customer');
        $daybookEntries = Daybook::where('slno', $salesInvoice->slno)->get();
        $arEntries      = ArLedger::where('slno', $salesInvoice->slno)->get();
        return view('sales.sales-invoices.show', compact('salesInvoice', 'daybookEntries', 'arEntries'));
    }

    public function edit(SalesInvoiceModel $salesInvoice)
    {
        $salesInvoice->load('items');
        $customers    = Account::where('atype', 'CUSTOMER')->orderBy('name')->get();
        $bankAccounts = Account::where('atype', 'BANK')->orderBy('name')->get();
        $salesOrders  = SalesOrder::whereIn('status', ['confirmed','partially_dispatched'])->orderByDesc('id')->get();
        $fgList       = FinishedGoods::where('status', 1)->orderBy('name')->get();
        return view('sales.sales-invoices.form', compact('salesInvoice','customers','bankAccounts','salesOrders','fgList'));
    }

    public function update(Request $request, SalesInvoiceModel $salesInvoice)
    {
        $request->validate([
            'invoice_date'  => 'required|date',
            'customer_name' => 'required|string',
        ]);

        DB::transaction(function () use ($request, $salesInvoice) {
            // Reverse old FG stock reduction
            foreach ($salesInvoice->items as $oldItem) {
                if ($oldItem->fg_id && $oldItem->batch_no) {
                    FgStock::where('fg_id', $oldItem->fg_id)
                        ->where('batch_no', $oldItem->batch_no)
                        ->decrement('qty_out', $oldItem->qty);
                }
            }

            // Reverse old daybook and AR
            Daybook::where('slno', $salesInvoice->slno)->delete();
            DaybookPart::where('slno', $salesInvoice->slno)->delete();
            ArLedger::where('slno', $salesInvoice->slno)->delete();

            $salesInvoice->items()->delete();

            $items    = $request->items ?? [];
            [$taxable, $disc, $sgst, $cgst, $igst, $totalCost] = $this->sumItems($items);
            $tcs      = (float)($request->tcs ?? 0);
            $other    = (float)($request->other_charges ?? 0);
            $received = (float)($request->received_amount ?? 0);
            $gross    = $taxable - $disc + $sgst + $cgst + $igst + $tcs + $other;
            $roundOff = round($gross) - $gross;
            $net      = $gross + $roundOff;

            $customer = Account::find($request->customer_id);

            $salesInvoice->update([
                'invoice_date'     => $request->invoice_date,
                'so_id'            => $request->so_id ?: null,
                'customer_id'      => $request->customer_id ?: null,
                'customer_name'    => $request->customer_name,
                'billing_address'  => $request->billing_address,
                'shipping_address' => $request->shipping_address,
                'channel'          => $request->channel ?? 'retail',
                'taxable_amount'   => $taxable,
                'discount'         => $disc,
                'sgst'             => $sgst,
                'cgst'             => $cgst,
                'igst'             => $igst,
                'tcs'              => $tcs,
                'other_charges'    => $other,
                'round_off'        => $roundOff,
                'net_amount'       => $net,
                'received_amount'  => $received,
                'balance_amount'   => $net - $received,
                'payment_mode'     => $request->payment_mode ?? 'credit',
                'bank_account_id'  => $request->bank_account_id ?: null,
                'cheque_no'        => $request->cheque_no,
                'utr_no'           => $request->utr_no,
                'narration'        => $request->narration,
            ]);

            $this->saveItems($salesInvoice->id, $items);
            $this->postDaybook($salesInvoice, $customer, $received, $taxable, $disc, $sgst, $cgst, $igst, $tcs, $roundOff, $totalCost);
            $this->postArLedger($salesInvoice, $net, $received, $customer);
            $this->reduceFgStock($items);
        });

        return redirect()->route('sales-invoices.show', $salesInvoice)->with('success', 'Invoice updated.');
    }

    public function destroy(SalesInvoiceModel $salesInvoice)
    {
        DB::transaction(function () use ($salesInvoice) {
            // Restore FG stock
            foreach ($salesInvoice->items as $item) {
                if ($item->fg_id && $item->batch_no) {
                    FgStock::where('fg_id', $item->fg_id)
                        ->where('batch_no', $item->batch_no)
                        ->decrement('qty_out', $item->qty);
                }
            }
            Daybook::where('slno', $salesInvoice->slno)->delete();
            DaybookPart::where('slno', $salesInvoice->slno)->delete();
            ArLedger::where('slno', $salesInvoice->slno)->delete();
            $salesInvoice->items()->delete();
            $salesInvoice->update(['status' => 'cancelled']);
        });
        return redirect()->route('sales-invoices.index')->with('success', 'Invoice cancelled.');
    }

    // ──────────────────────────────────────────────────────────────
    // PRIVATE HELPERS
    // ──────────────────────────────────────────────────────────────

    private function sumItems(array $items): array
    {
        $taxable   = 0; $disc = 0; $sgst = 0; $cgst = 0; $igst = 0; $cost = 0;
        foreach ($items as $i) {
            $taxable += (float)($i['amount'] ?? 0);
            $disc    += (float)($i['discount_amount'] ?? 0);
            $sgst    += (float)($i['sgst'] ?? 0);
            $cgst    += (float)($i['cgst'] ?? 0);
            $igst    += (float)($i['igst'] ?? 0);
            $cost    += (float)($i['qty'] ?? 0) * (float)($i['cost_rate'] ?? 0);
        }
        return [$taxable, $disc, $sgst, $cgst, $igst, $cost];
    }

    private function saveItems(int $invId, array $items): void
    {
        foreach ($items as $item) {
            if (empty($item['fg_name'])) continue;
            $fg = FinishedGoods::find($item['fg_id'] ?? null);
            SalesInvoiceItem::create([
                'invoice_id'      => $invId,
                'fg_id'           => $item['fg_id'] ?? null,
                'fg_code'         => $fg?->code ?? $item['fg_code'] ?? '',
                'fg_name'         => $item['fg_name'],
                'hsn_code'        => $item['hsn_code'] ?? $fg?->hsn_code ?? '',
                'unit'            => $item['unit'] ?? '',
                'qty'             => (float)($item['qty'] ?? 0),
                'rate'            => (float)($item['rate'] ?? 0),
                'amount'          => (float)($item['amount'] ?? 0),
                'discount_pct'    => (float)($item['discount_pct'] ?? 0),
                'discount_amount' => (float)($item['discount_amount'] ?? 0),
                'sgst_pct'        => (float)($item['sgst_pct'] ?? 0),
                'cgst_pct'        => (float)($item['cgst_pct'] ?? 0),
                'igst_pct'        => (float)($item['igst_pct'] ?? 0),
                'sgst'            => (float)($item['sgst'] ?? 0),
                'cgst'            => (float)($item['cgst'] ?? 0),
                'igst'            => (float)($item['igst'] ?? 0),
                'net_amount'      => (float)($item['net_amount'] ?? 0),
                'batch_no'        => $item['batch_no'] ?? null,
                'cost_rate'       => (float)($item['cost_rate'] ?? $fg?->cost_price ?? 0),
            ]);
        }
    }

    /**
     * DOUBLE-ENTRY for Sales Invoice
     * amount < 0 = Debit   amount > 0 = Credit
     *
     * Customer        -net_amount         Debit customer (they owe us)
     * Customer        +received_amount    Credit customer (reduces receivable if paid now)
     * Cash/Bank       -received_amount    Debit cash/bank (money in)
     * Sales (RS)      +taxable_amount     Credit sales revenue
     * Discount (DISC) -discount           Debit discount expense
     * SGST            +sgst               Credit SGST payable
     * CGST            +cgst               Credit CGST payable
     * IGST            +igst               Credit IGST payable
     * TCS             +tcs                Credit TCS payable
     * ROUND           balancing entry     SUM = 0
     * COGS            -totalCost          Debit COGS
     * FG-STOCK        +totalCost          Credit FG asset out
     */
    private function postDaybook(
        SalesInvoiceModel $inv,
        ?Account $customer,
        float $received,
        float $taxable,
        float $disc,
        float $sgst,
        float $cgst,
        float $igst,
        float $tcs,
        float $roundOff,
        float $totalCost
    ): void {
        $slno   = $inv->slno;
        $tdate  = $inv->invoice_date;
        $vtype  = 'SI';
        $branch = $inv->branch_id;
        $ref    = "Sales Inv {$inv->invoice_no} – {$inv->customer_name}";

        Daybook::where('slno', $slno)->delete();
        DaybookPart::where('slno', $slno)->delete();

        $entries = [];

        // Customer Debit (they owe net)
        if ($customer) {
            $entries[] = [$customer, -$inv->net_amount, "Customer Debit – $ref"];
            if ($received > 0) {
                $entries[] = [$customer, $received, "Customer Payment – $ref"];
            }
        }

        // Cash / Bank Debit if payment received
        if ($received > 0) {
            $cashBankCode = match ($inv->payment_mode) {
                'bank', 'cheque', 'upi' => 'BANK',
                default                  => 'CASH',
            };
            $cbAcc = Account::where('code', $cashBankCode)->first()
                  ?? Account::where('atype', strtoupper($cashBankCode))->first();
            if ($cbAcc) {
                $entries[] = [$cbAcc, -$received, "Cash/Bank received – $ref"];
            }
        }

        // Sales Credit
        $salesAcc = Account::where('code', 'RS')->orWhere('code', 'SALES')->first();
        if ($salesAcc && $taxable != 0) {
            $entries[] = [$salesAcc, $taxable, "Sales – $ref"];
        }

        // Discount Debit
        if ($disc > 0) {
            $discAcc = Account::where('code', 'DISC')->orWhere('code', 'DISCOUNT')->first();
            if ($discAcc) {
                $entries[] = [$discAcc, -$disc, "Discount – $ref"];
            }
        }

        // Tax Credits
        foreach (['SGST' => $sgst, 'CGST' => $cgst, 'IGST' => $igst, 'TCS' => $tcs] as $code => $amount) {
            if ($amount > 0) {
                $acc = Account::where('code', $code)->first();
                if ($acc) $entries[] = [$acc, $amount, "$code – $ref"];
            }
        }

        // Insert all lines
        foreach ($entries as [$acc, $amount, $particular]) {
            if (round($amount, 4) == 0) continue;
            Daybook::create([
                'slno'         => $slno,
                'account_id'   => $acc->id,
                'account_code' => $acc->code,
                'account_name' => $acc->name,
                'amount'       => $amount,
                'particular'   => $particular,
                'tdate'        => $tdate,
                'vtype'        => $vtype,
                'branch_id'    => $branch,
            ]);
        }

        // ROUND entry to make SUM = 0
        $sum = (float) Daybook::where('slno', $slno)->sum('amount');
        if (abs($sum) > 0.001) {
            $roundAcc = Account::where('code', 'ROUND')->first();
            if ($roundAcc) {
                Daybook::create([
                    'slno'         => $slno,
                    'account_id'   => $roundAcc->id,
                    'account_code' => $roundAcc->code,
                    'account_name' => $roundAcc->name,
                    'amount'       => -$sum,
                    'particular'   => "Round Off – $ref",
                    'tdate'        => $tdate,
                    'vtype'        => $vtype,
                    'branch_id'    => $branch,
                ]);
            }
        }

        // COGS entries
        if ($totalCost > 0) {
            $cogsAcc    = Account::where('code', 'COGS')->first();
            $fgStockAcc = Account::where('code', 'FG-STOCK')->first();
            if ($cogsAcc && $fgStockAcc) {
                Daybook::create([
                    'slno' => $slno, 'account_id' => $cogsAcc->id,
                    'account_code' => $cogsAcc->code, 'account_name' => $cogsAcc->name,
                    'amount' => -$totalCost, 'particular' => "COGS – $ref",
                    'tdate' => $tdate, 'vtype' => 'COGS', 'branch_id' => $branch,
                ]);
                Daybook::create([
                    'slno' => $slno, 'account_id' => $fgStockAcc->id,
                    'account_code' => $fgStockAcc->code, 'account_name' => $fgStockAcc->name,
                    'amount' => $totalCost, 'particular' => "FG Stock Out – $ref",
                    'tdate' => $tdate, 'vtype' => 'COGS', 'branch_id' => $branch,
                ]);
            }
        }

        DaybookPart::create([
            'slno'       => $slno,
            'vchno'      => $inv->invoice_no,
            'particular' => $ref,
            'tdate'      => $tdate,
            'vtype'      => $vtype,
            'narration'  => $inv->narration,
            'branch_id'  => $branch,
            'created_by' => $inv->created_by,
        ]);
    }

    private function postArLedger(SalesInvoiceModel $inv, float $net, float $received, ?Account $customer): void
    {
        if (!$customer) return;

        // Get last balance for this customer
        $lastBal = ArLedger::where('customer_id', $customer->id)->orderByDesc('id')->value('balance') ?? 0;
        $newBal  = $lastBal + $net;

        ArLedger::create([
            'customer_id' => $customer->id,
            'tdate'       => $inv->invoice_date,
            'slno'        => $inv->slno,
            'vtype'       => 'SI',
            'ref_no'      => $inv->invoice_no,
            'debit'       => $net,
            'credit'      => 0,
            'balance'     => $newBal,
            'narration'   => "Sales Invoice {$inv->invoice_no}",
        ]);

        if ($received > 0) {
            ArLedger::create([
                'customer_id' => $customer->id,
                'tdate'       => $inv->invoice_date,
                'slno'        => $inv->slno . '-P',
                'vtype'       => 'RCPT',
                'ref_no'      => $inv->invoice_no,
                'debit'       => 0,
                'credit'      => $received,
                'balance'     => $newBal - $received,
                'narration'   => "Payment against Invoice {$inv->invoice_no}",
            ]);
        }
    }

    private function reduceFgStock(array $items): void
    {
        foreach ($items as $item) {
            if (empty($item['fg_id']) || empty($item['batch_no'])) continue;
            $qty = (float)($item['qty'] ?? 0);
            if ($qty > 0) {
                FgStock::where('fg_id', $item['fg_id'])
                    ->where('batch_no', $item['batch_no'])
                    ->where('status', 'available')
                    ->increment('qty_out', $qty);
            }
        }
    }
}
