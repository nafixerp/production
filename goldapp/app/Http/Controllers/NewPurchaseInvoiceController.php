<?php
namespace App\Http\Controllers;

use App\Models\PurchaseInvoiceNew;
use App\Models\PurchaseInvoiceItemNew;
use App\Models\Supplier;
use App\Models\ApLedger;
use App\Models\Account;
use App\Models\Grn;
use App\Models\Daybook;
use App\Services\DaybookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NewPurchaseInvoiceController extends Controller
{
    protected DaybookService $daybookService;

    public function __construct(DaybookService $daybookService)
    {
        $this->daybookService = $daybookService;
    }

    public function index(Request $request)
    {
        $q      = $request->q;
        $status = $request->status;
        $from   = $request->from;
        $to     = $request->to;

        $invoices = PurchaseInvoiceNew::with('supplier')
            ->when($q, fn($q2) => $q2->where('supplier_name', 'like', "%$q%")->orWhere('slno', 'like', "%$q%"))
            ->when($status, fn($q2) => $q2->where('status', $status))
            ->when($from, fn($q2) => $q2->where('invoice_date', '>=', $from))
            ->when($to, fn($q2) => $q2->where('invoice_date', '<=', $to))
            ->orderByDesc('invoice_date')->orderByDesc('id')
            ->paginate(20)->withQueryString();

        return view('procurement.purchase-invoices.index', compact('invoices', 'q', 'status', 'from', 'to'));
    }

    public function create()
    {
        $suppliers    = Supplier::where('status', 'active')->orderBy('name')->get();
        $bankAccounts = Account::where('atype', 'BANK')->orderBy('name')->get();
        $openGRNs     = Grn::where('status', 'approved')->with('supplier')->orderByDesc('id')->get();
        $nextSlno     = $this->daybookService->nextSlno('PINV', 'purchase_invoices');

        return view('procurement.purchase-invoices.form', [
            'invoice'      => null,
            'suppliers'    => $suppliers,
            'bankAccounts' => $bankAccounts,
            'openGRNs'     => $openGRNs,
            'nextSlno'     => $nextSlno,
            'items'        => [],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'invoice_date' => 'required|date',
            'supplier_id'  => 'required|exists:suppliers,id',
        ]);

        DB::transaction(function () use ($request) {
            $slno     = $this->daybookService->nextSlno('PINV', 'purchase_invoices');
            $supplier = Supplier::findOrFail($request->supplier_id);
            $items    = $request->input('items', []);

            $taxable = 0; $sgst = 0; $cgst = 0; $igst = 0;
            foreach ($items as $item) {
                $taxable += (float)($item['amount'] ?? 0);
                $sgst    += (float)($item['sgst'] ?? 0);
                $cgst    += (float)($item['cgst'] ?? 0);
                $igst    += (float)($item['igst'] ?? 0);
            }

            $discount     = (float)($request->discount ?? 0);
            $tcs          = (float)($request->tcs ?? 0);
            $otherCharges = (float)($request->other_charges ?? 0);
            $paidAmount   = (float)($request->paid_amount ?? 0);
            $net          = $taxable - $discount + $sgst + $cgst + $igst + $tcs + $otherCharges;
            $roundOff     = round($net) - $net;
            $net          = round($net);
            $balance      = $net - $paidAmount;

            $inv = PurchaseInvoiceNew::create([
                'slno'              => $slno,
                'invoice_no'        => $slno,
                'invoice_date'      => $request->invoice_date,
                'grn_id'            => $request->grn_id ?: null,
                'po_id'             => $request->po_id ?: null,
                'supplier_id'       => $supplier->id,
                'supplier_name'     => $supplier->name,
                'supplier_bill_no'  => $request->supplier_bill_no,
                'supplier_bill_date'=> $request->supplier_bill_date ?: null,
                'taxable_amount'    => $taxable,
                'discount'          => $discount,
                'sgst'              => $sgst,
                'cgst'              => $cgst,
                'igst'              => $igst,
                'tcs'               => $tcs,
                'other_charges'     => $otherCharges,
                'round_off'         => $roundOff,
                'net_amount'        => $net,
                'paid_amount'       => $paidAmount,
                'balance_amount'    => $balance,
                'payment_mode'      => $request->payment_mode ?? 'credit',
                'bank_account_id'   => $request->bank_account_id ?: null,
                'cheque_no'         => $request->cheque_no,
                'cheque_date'       => $request->cheque_date ?: null,
                'status'            => 'posted',
                'narration'         => $request->narration,
                'branch_id'         => $request->branch_id,
                'created_by'        => Auth::id(),
            ]);

            foreach ($items as $item) {
                if (empty($item['item_name'])) continue;
                $qty  = (float)($item['qty'] ?? 0);
                $rate = (float)($item['rate'] ?? 0);
                $amt  = (float)($item['amount'] ?? round($qty * $rate, 2));
                $iSgst = (float)($item['sgst'] ?? 0);
                $iCgst = (float)($item['cgst'] ?? 0);
                $iIgst = (float)($item['igst'] ?? 0);
                PurchaseInvoiceItemNew::create([
                    'invoice_id' => $inv->id,
                    'item_type'  => $item['item_type'] ?? 'RM',
                    'item_id'    => $item['item_id'] ?: null,
                    'item_code'  => $item['item_code'] ?? '',
                    'item_name'  => $item['item_name'],
                    'hsn_code'   => $item['hsn_code'] ?? '',
                    'unit'       => $item['unit'] ?? 'KG',
                    'qty'        => $qty,
                    'rate'       => $rate,
                    'amount'     => $amt,
                    'sgst'       => $iSgst,
                    'cgst'       => $iCgst,
                    'igst'       => $iIgst,
                    'net_amount' => $amt + $iSgst + $iCgst + $iIgst,
                ]);
            }

            // Post daybook entries
            $this->daybookService->insertNewPurchaseInvoiceDaybookEntries($inv);

            // Update AP ledger
            $this->postAPLedger($inv, $supplier);
        });

        return redirect()->route('purchase-invoices-new.index')->with('success', 'Purchase Invoice posted with daybook entries.');
    }

    protected function postAPLedger(PurchaseInvoiceNew $inv, Supplier $supplier): void
    {
        // Get running balance
        $prevBalance = ApLedger::where('supplier_id', $supplier->id)
            ->orderByDesc('tdate')->orderByDesc('id')->value('balance') ?? 0;

        $newBalance = $prevBalance + $inv->net_amount;

        ApLedger::create([
            'supplier_id' => $supplier->id,
            'tdate'       => $inv->invoice_date,
            'slno'        => $inv->slno,
            'vtype'       => 'PINV',
            'ref_no'      => $inv->supplier_bill_no,
            'debit'       => 0,
            'credit'      => $inv->net_amount,
            'balance'     => $newBalance,
            'narration'   => "Purchase Invoice {$inv->slno}",
        ]);

        // If paid now, reduce balance
        if ($inv->paid_amount > 0) {
            $paidBalance = $newBalance - $inv->paid_amount;
            ApLedger::create([
                'supplier_id' => $supplier->id,
                'tdate'       => $inv->invoice_date,
                'slno'        => $inv->slno . '-PAY',
                'vtype'       => 'PAY',
                'ref_no'      => $inv->slno,
                'debit'       => $inv->paid_amount,
                'credit'      => 0,
                'balance'     => $paidBalance,
                'narration'   => "Payment against {$inv->slno}",
            ]);
        }
    }

    public function show(PurchaseInvoiceNew $purchaseInvoiceNew)
    {
        $purchaseInvoiceNew->load('supplier', 'items', 'grn');
        $daybookEntries = Daybook::where('slno', $purchaseInvoiceNew->slno)->get();
        return view('procurement.purchase-invoices.show', [
            'invoice'        => $purchaseInvoiceNew,
            'daybookEntries' => $daybookEntries,
        ]);
    }

    public function edit(PurchaseInvoiceNew $purchaseInvoiceNew)
    {
        if (in_array($purchaseInvoiceNew->status, ['paid', 'cancelled'])) {
            return redirect()->route('purchase-invoices-new.show', $purchaseInvoiceNew)->with('error', 'Cannot edit this invoice.');
        }
        $purchaseInvoiceNew->load('items');
        $suppliers    = Supplier::where('status', 'active')->orderBy('name')->get();
        $bankAccounts = Account::where('atype', 'BANK')->orderBy('name')->get();
        $openGRNs     = Grn::where('status', 'approved')->with('supplier')->orderByDesc('id')->get();

        return view('procurement.purchase-invoices.form', [
            'invoice'      => $purchaseInvoiceNew,
            'suppliers'    => $suppliers,
            'bankAccounts' => $bankAccounts,
            'openGRNs'     => $openGRNs,
            'nextSlno'     => $purchaseInvoiceNew->slno,
            'items'        => $purchaseInvoiceNew->items,
        ]);
    }

    public function update(Request $request, PurchaseInvoiceNew $purchaseInvoiceNew)
    {
        $request->validate([
            'invoice_date' => 'required|date',
            'supplier_id'  => 'required|exists:suppliers,id',
        ]);

        DB::transaction(function () use ($request, $purchaseInvoiceNew) {
            $supplier = Supplier::findOrFail($request->supplier_id);
            $items    = $request->input('items', []);

            $taxable = 0; $sgst = 0; $cgst = 0; $igst = 0;
            foreach ($items as $item) {
                $taxable += (float)($item['amount'] ?? 0);
                $sgst    += (float)($item['sgst'] ?? 0);
                $cgst    += (float)($item['cgst'] ?? 0);
                $igst    += (float)($item['igst'] ?? 0);
            }

            $discount     = (float)($request->discount ?? 0);
            $tcs          = (float)($request->tcs ?? 0);
            $otherCharges = (float)($request->other_charges ?? 0);
            $paidAmount   = (float)($request->paid_amount ?? 0);
            $net          = $taxable - $discount + $sgst + $cgst + $igst + $tcs + $otherCharges;
            $roundOff     = round($net) - $net;
            $net          = round($net);

            $purchaseInvoiceNew->update([
                'invoice_date'      => $request->invoice_date,
                'supplier_id'       => $supplier->id,
                'supplier_name'     => $supplier->name,
                'supplier_bill_no'  => $request->supplier_bill_no,
                'supplier_bill_date'=> $request->supplier_bill_date ?: null,
                'taxable_amount'    => $taxable,
                'discount'          => $discount,
                'sgst'              => $sgst,
                'cgst'              => $cgst,
                'igst'              => $igst,
                'tcs'               => $tcs,
                'other_charges'     => $otherCharges,
                'round_off'         => $roundOff,
                'net_amount'        => $net,
                'paid_amount'       => $paidAmount,
                'balance_amount'    => $net - $paidAmount,
                'payment_mode'      => $request->payment_mode ?? 'credit',
                'narration'         => $request->narration,
            ]);

            $purchaseInvoiceNew->items()->delete();
            foreach ($items as $item) {
                if (empty($item['item_name'])) continue;
                $qty  = (float)($item['qty'] ?? 0);
                $rate = (float)($item['rate'] ?? 0);
                $amt  = (float)($item['amount'] ?? round($qty * $rate, 2));
                $iSgst = (float)($item['sgst'] ?? 0);
                $iCgst = (float)($item['cgst'] ?? 0);
                $iIgst = (float)($item['igst'] ?? 0);
                PurchaseInvoiceItemNew::create([
                    'invoice_id' => $purchaseInvoiceNew->id,
                    'item_type'  => $item['item_type'] ?? 'RM',
                    'item_id'    => $item['item_id'] ?: null,
                    'item_code'  => $item['item_code'] ?? '',
                    'item_name'  => $item['item_name'],
                    'hsn_code'   => $item['hsn_code'] ?? '',
                    'unit'       => $item['unit'] ?? 'KG',
                    'qty'        => $qty,
                    'rate'       => $rate,
                    'amount'     => $amt,
                    'sgst'       => $iSgst,
                    'cgst'       => $iCgst,
                    'igst'       => $iIgst,
                    'net_amount' => $amt + $iSgst + $iCgst + $iIgst,
                ]);
            }

            $this->daybookService->insertNewPurchaseInvoiceDaybookEntries($purchaseInvoiceNew->fresh());
        });

        return redirect()->route('purchase-invoices-new.index')->with('success', 'Invoice updated.');
    }

    public function destroy(PurchaseInvoiceNew $purchaseInvoiceNew)
    {
        DB::transaction(function () use ($purchaseInvoiceNew) {
            Daybook::where('slno', $purchaseInvoiceNew->slno)->delete();
            ApLedger::where('slno', $purchaseInvoiceNew->slno)->delete();
            $purchaseInvoiceNew->items()->delete();
            $purchaseInvoiceNew->update(['status' => 'cancelled']);
        });
        return redirect()->route('purchase-invoices-new.index')->with('success', 'Invoice cancelled.');
    }
}
