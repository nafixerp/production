<?php
namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\ArLedger;
use App\Models\Daybook;
use App\Models\DaybookPart;
use App\Models\FgStock;
use App\Models\FinishedGoods;
use App\Models\SalesInvoiceModel;
use App\Models\SalesReturn;
use App\Models\SalesReturnItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesReturnController extends Controller
{
    private function nextSlno(): string
    {
        $last = SalesReturn::orderByDesc('id')->value('slno');
        $num  = $last ? (int) substr($last, 2) + 1 : 1;
        return 'SR' . str_pad($num, 6, '0', STR_PAD_LEFT);
    }

    public function index(Request $request)
    {
        $returns = SalesReturn::with('customer')
            ->when($request->q, fn($q) => $q->where('customer_name', 'like', "%{$request->q}%"))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->orderByDesc('return_date')
            ->paginate(20)->withQueryString();
        return view('sales.sales-returns.index', compact('returns'));
    }

    public function create()
    {
        $customers = Account::where('atype', 'CUSTOMER')->orderBy('name')->get();
        $invoices  = SalesInvoiceModel::whereIn('status', ['posted','paid','partial'])->orderByDesc('id')->get();
        $fgList    = FinishedGoods::where('status', 1)->orderBy('name')->get();
        $slno      = $this->nextSlno();
        return view('sales.sales-returns.form', compact('customers','invoices','fgList','slno'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'return_date'   => 'required|date',
            'customer_name' => 'required|string',
            'items'         => 'required|array|min:1',
        ]);

        DB::transaction(function () use ($request) {
            $slno  = $this->nextSlno();
            $items = $request->items ?? [];
            $taxable = collect($items)->sum(fn($i) => (float)($i['amount'] ?? 0));
            $sgst    = collect($items)->sum(fn($i) => (float)($i['sgst'] ?? 0));
            $cgst    = collect($items)->sum(fn($i) => (float)($i['cgst'] ?? 0));
            $igst    = collect($items)->sum(fn($i) => (float)($i['igst'] ?? 0));
            $net     = $taxable + $sgst + $cgst + $igst;

            $ret = SalesReturn::create([
                'slno'          => $slno,
                'return_no'     => $slno,
                'return_date'   => $request->return_date,
                'invoice_id'    => $request->invoice_id ?: null,
                'so_id'         => $request->so_id ?: null,
                'customer_id'   => $request->customer_id ?: null,
                'customer_name' => $request->customer_name,
                'channel'       => $request->channel ?? 'retail',
                'reason'        => $request->reason,
                'taxable_amount'=> $taxable,
                'sgst'          => $sgst,
                'cgst'          => $cgst,
                'igst'          => $igst,
                'net_amount'    => $net,
                'refund_mode'   => $request->refund_mode ?? 'credit_note',
                'status'        => 'draft',
                'narration'     => $request->narration,
                'branch_id'     => Auth::user()->branch_id ?? null,
                'created_by'    => Auth::id(),
            ]);

            foreach ($items as $item) {
                if (empty($item['fg_name'])) continue;
                $fg = FinishedGoods::find($item['fg_id'] ?? null);
                SalesReturnItem::create([
                    'return_id' => $ret->id,
                    'fg_id'     => $item['fg_id'] ?? null,
                    'fg_code'   => $fg?->code ?? '',
                    'fg_name'   => $item['fg_name'],
                    'unit'      => $item['unit'] ?? '',
                    'qty'       => (float)($item['qty'] ?? 0),
                    'rate'      => (float)($item['rate'] ?? 0),
                    'amount'    => (float)($item['amount'] ?? 0),
                    'batch_no'  => $item['batch_no'] ?? null,
                ]);
            }
        });

        return redirect()->route('sales-returns.index')->with('success', 'Sales return saved.');
    }

    public function show(SalesReturn $salesReturn)
    {
        $salesReturn->load('items','customer');
        $daybookEntries = Daybook::where('slno', $salesReturn->slno)->get();
        return view('sales.sales-returns.show', compact('salesReturn','daybookEntries'));
    }

    public function edit(SalesReturn $salesReturn)
    {
        if ($salesReturn->status !== 'draft') {
            return redirect()->route('sales-returns.show', $salesReturn)->with('error', 'Cannot edit approved returns.');
        }
        $salesReturn->load('items');
        $customers = Account::where('atype', 'CUSTOMER')->orderBy('name')->get();
        $invoices  = SalesInvoiceModel::whereIn('status', ['posted','paid','partial'])->orderByDesc('id')->get();
        $fgList    = FinishedGoods::where('status', 1)->orderBy('name')->get();
        return view('sales.sales-returns.form', compact('salesReturn','customers','invoices','fgList'));
    }

    public function update(Request $request, SalesReturn $salesReturn)
    {
        if ($salesReturn->status !== 'draft') {
            return redirect()->back()->with('error', 'Cannot edit approved returns.');
        }
        DB::transaction(function () use ($request, $salesReturn) {
            $items   = $request->items ?? [];
            $taxable = collect($items)->sum(fn($i) => (float)($i['amount'] ?? 0));
            $sgst    = collect($items)->sum(fn($i) => (float)($i['sgst'] ?? 0));
            $cgst    = collect($items)->sum(fn($i) => (float)($i['cgst'] ?? 0));
            $igst    = collect($items)->sum(fn($i) => (float)($i['igst'] ?? 0));

            $salesReturn->update([
                'return_date'    => $request->return_date,
                'customer_id'    => $request->customer_id ?: null,
                'customer_name'  => $request->customer_name,
                'reason'         => $request->reason,
                'taxable_amount' => $taxable,
                'sgst'           => $sgst,
                'cgst'           => $cgst,
                'igst'           => $igst,
                'net_amount'     => $taxable + $sgst + $cgst + $igst,
                'refund_mode'    => $request->refund_mode ?? 'credit_note',
                'narration'      => $request->narration,
            ]);

            $salesReturn->items()->delete();
            foreach ($items as $item) {
                if (empty($item['fg_name'])) continue;
                $fg = FinishedGoods::find($item['fg_id'] ?? null);
                SalesReturnItem::create([
                    'return_id' => $salesReturn->id,
                    'fg_id'     => $item['fg_id'] ?? null,
                    'fg_code'   => $fg?->code ?? '',
                    'fg_name'   => $item['fg_name'],
                    'unit'      => $item['unit'] ?? '',
                    'qty'       => (float)($item['qty'] ?? 0),
                    'rate'      => (float)($item['rate'] ?? 0),
                    'amount'    => (float)($item['amount'] ?? 0),
                    'batch_no'  => $item['batch_no'] ?? null,
                ]);
            }
        });
        return redirect()->route('sales-returns.show', $salesReturn)->with('success', 'Updated.');
    }

    public function destroy(SalesReturn $salesReturn)
    {
        $salesReturn->items()->delete();
        $salesReturn->delete();
        return redirect()->route('sales-returns.index')->with('success', 'Deleted.');
    }

    /** Approve return: reverse daybook, restore FG stock */
    public function approve(SalesReturn $salesReturn)
    {
        if ($salesReturn->status !== 'draft') {
            return redirect()->back()->with('error', 'Already approved.');
        }

        DB::transaction(function () use ($salesReturn) {
            $slno   = $salesReturn->slno;
            $tdate  = $salesReturn->return_date;
            $branch = $salesReturn->branch_id;
            $ref    = "Sales Return {$salesReturn->return_no} – {$salesReturn->customer_name}";

            Daybook::where('slno', $slno)->delete();
            DaybookPart::where('slno', $slno)->delete();

            // Reverse entries (credit customer, debit sales)
            $customer = Account::find($salesReturn->customer_id);
            $salesAcc = Account::where('code', 'RS')->orWhere('code', 'SALES')->first();

            if ($customer) {
                Daybook::create([
                    'slno' => $slno, 'account_id' => $customer->id,
                    'account_code' => $customer->code, 'account_name' => $customer->name,
                    'amount' => $salesReturn->net_amount, // Credit customer (positive)
                    'particular' => "Sales Return Credit – $ref",
                    'tdate' => $tdate, 'vtype' => 'SR', 'branch_id' => $branch,
                ]);
            }
            if ($salesAcc) {
                Daybook::create([
                    'slno' => $slno, 'account_id' => $salesAcc->id,
                    'account_code' => $salesAcc->code, 'account_name' => $salesAcc->name,
                    'amount' => -$salesReturn->taxable_amount,
                    'particular' => "Sales Return Debit – $ref",
                    'tdate' => $tdate, 'vtype' => 'SR', 'branch_id' => $branch,
                ]);
            }

            DaybookPart::create([
                'slno' => $slno, 'vchno' => $salesReturn->return_no,
                'particular' => $ref, 'tdate' => $tdate,
                'vtype' => 'SR', 'narration' => $salesReturn->reason,
                'branch_id' => $branch, 'created_by' => Auth::id(),
            ]);

            // AR Ledger credit
            if ($customer) {
                $lastBal = ArLedger::where('customer_id', $customer->id)->orderByDesc('id')->value('balance') ?? 0;
                ArLedger::create([
                    'customer_id' => $customer->id,
                    'tdate'       => $tdate,
                    'slno'        => $slno,
                    'vtype'       => 'SR',
                    'ref_no'      => $salesReturn->return_no,
                    'debit'       => 0,
                    'credit'      => $salesReturn->net_amount,
                    'balance'     => $lastBal - $salesReturn->net_amount,
                    'narration'   => "Sales Return {$salesReturn->return_no}",
                ]);
            }

            // Restore FG stock
            foreach ($salesReturn->items as $item) {
                if (!$item->fg_id) continue;
                if ($item->batch_no) {
                    // Return to existing batch
                    FgStock::where('fg_id', $item->fg_id)
                        ->where('batch_no', $item->batch_no)
                        ->decrement('qty_out', $item->qty);
                }
            }

            $salesReturn->update(['status' => 'approved']);
        });

        return redirect()->route('sales-returns.show', $salesReturn)->with('success', 'Return approved & stock restored.');
    }
}
