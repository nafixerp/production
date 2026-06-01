<?php
namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceDetail;
use App\Services\DaybookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseInvoiceController extends Controller
{
    protected DaybookService $daybookService;

    public function __construct(DaybookService $daybookService)
    {
        $this->daybookService = $daybookService;
    }

    public function index(Request $request)
    {
        $q    = $request->q;
        $from = $request->from;
        $to   = $request->to;

        $invoices = PurchaseInvoice::when($q, fn($qry) => $qry->where('supplier_name', 'like', "%$q%")->orWhere('doc_no', 'like', "%$q%"))
            ->when($from, fn($qry) => $qry->where('invoice_date', '>=', $from))
            ->when($to,   fn($qry) => $qry->where('invoice_date', '<=', $to))
            ->orderBy('invoice_date', 'desc')->orderBy('id', 'desc')
            ->paginate(20)->withQueryString();

        return view('purchase.index', compact('invoices', 'q', 'from', 'to'));
    }

    public function create()
    {
        $suppliers    = Account::where('atype', 'SUPPLIER')->orderBy('name')->get();
        $bankAccounts = Account::where('atype', 'BANK')->orderBy('name')->get();
        $nextSlno     = $this->daybookService->generateSlno('PI', PurchaseInvoice::class);
        return view('purchase.create', compact('suppliers', 'bankAccounts', 'nextSlno'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'invoice_date' => 'required|date',
            'supplier_id'  => 'required|exists:account,id',
        ]);

        DB::transaction(function () use ($request) {
            $slno     = $this->daybookService->generateSlno('PI', PurchaseInvoice::class);
            $supplier = Account::findOrFail($request->supplier_id);

            $items         = $request->input('items', []);
            $taxableAmount = 0;
            $totalSgst     = 0;
            $totalCgst     = 0;
            $totalIgst     = 0;

            foreach ($items as $item) {
                $taxableAmount += (float)($item['amount'] ?? 0);
                $totalSgst     += (float)($item['sgst'] ?? 0);
                $totalCgst     += (float)($item['cgst'] ?? 0);
                $totalIgst     += (float)($item['igst'] ?? 0);
            }

            $discount      = (float)($request->discount ?? 0);
            $sgst          = (float)($request->sgst ?? $totalSgst);
            $cgst          = (float)($request->cgst ?? $totalCgst);
            $igst          = (float)($request->igst ?? $totalIgst);
            $tcs           = (float)($request->tcs ?? 0);
            $otherCharges  = (float)($request->other_charges ?? 0);
            $paidAmount    = (float)($request->paid_amount ?? 0);

            $netAmount = $taxableAmount - $discount + $sgst + $cgst + $igst + $tcs + $otherCharges;
            $roundOff  = round($netAmount) - $netAmount;
            $netAmount = round($netAmount);

            $inv = PurchaseInvoice::create([
                'slno'             => $slno,
                'doc_no'           => $slno,
                'invoice_date'     => $request->invoice_date,
                'supplier_id'      => $request->supplier_id,
                'supplier_name'    => $supplier->name,
                'supplier_bill_no' => $request->supplier_bill_no ?? '',
                'taxable_amount'   => $taxableAmount,
                'discount'         => $discount,
                'sgst'             => $sgst,
                'cgst'             => $cgst,
                'igst'             => $igst,
                'tcs'              => $tcs,
                'other_charges'    => $otherCharges,
                'round_off'        => $roundOff,
                'net_amount'       => $netAmount,
                'paid_amount'      => $paidAmount,
                'payment_mode'     => $request->payment_mode ?? 'credit',
                'bank_account_id'  => $request->bank_account_id ?: null,
                'cheque_no'        => $request->cheque_no,
                'cheque_date'      => $request->cheque_date ?: null,
                'narration'        => $request->narration,
                'status'           => 1,
                'created_by'       => Auth::id(),
            ]);

            foreach ($items as $item) {
                if (empty($item['item_name'])) continue;
                $qty  = (float)($item['qty'] ?? 0);
                $rate = (float)($item['rate'] ?? 0);
                $amt  = (float)($item['amount'] ?? ($qty * $rate));
                PurchaseInvoiceDetail::create([
                    'purchase_invoice_id' => $inv->id,
                    'slno'                => $slno,
                    'item_code'           => $item['item_code'] ?? '',
                    'item_name'           => $item['item_name'],
                    'hsn_code'            => $item['hsn_code'] ?? null,
                    'unit'                => $item['unit'] ?? 'NOS',
                    'qty'                 => $qty,
                    'rate'                => $rate,
                    'amount'              => $amt,
                    'discount'            => (float)($item['discount'] ?? 0),
                    'sgst_pct'            => (float)($item['sgst_pct'] ?? 0),
                    'cgst_pct'            => (float)($item['cgst_pct'] ?? 0),
                    'igst_pct'            => (float)($item['igst_pct'] ?? 0),
                    'sgst'                => (float)($item['sgst'] ?? 0),
                    'cgst'                => (float)($item['cgst'] ?? 0),
                    'igst'                => (float)($item['igst'] ?? 0),
                ]);
            }

            $this->daybookService->insertPurchaseInvoiceDaybookEntries($inv);
        });

        return redirect()->route('purchase.index')->with('success', 'Purchase invoice saved successfully.');
    }

    public function show(PurchaseInvoice $purchase)
    {
        $purchase->load('details');
        $daybookEntries = \App\Models\Daybook::where('slno', $purchase->slno)->get();
        return view('purchase.show', compact('purchase', 'daybookEntries'));
    }

    public function edit(PurchaseInvoice $purchase)
    {
        $purchase->load('details');
        $suppliers    = Account::where('atype', 'SUPPLIER')->orderBy('name')->get();
        $bankAccounts = Account::where('atype', 'BANK')->orderBy('name')->get();
        return view('purchase.edit', compact('purchase', 'suppliers', 'bankAccounts'));
    }

    public function update(Request $request, PurchaseInvoice $purchase)
    {
        $request->validate([
            'invoice_date' => 'required|date',
            'supplier_id'  => 'required|exists:account,id',
        ]);

        DB::transaction(function () use ($request, $purchase) {
            $supplier = Account::findOrFail($request->supplier_id);

            $items         = $request->input('items', []);
            $taxableAmount = 0;
            $totalSgst     = 0;
            $totalCgst     = 0;
            $totalIgst     = 0;

            foreach ($items as $item) {
                $taxableAmount += (float)($item['amount'] ?? 0);
                $totalSgst     += (float)($item['sgst'] ?? 0);
                $totalCgst     += (float)($item['cgst'] ?? 0);
                $totalIgst     += (float)($item['igst'] ?? 0);
            }

            $discount     = (float)($request->discount ?? 0);
            $sgst         = (float)($request->sgst ?? $totalSgst);
            $cgst         = (float)($request->cgst ?? $totalCgst);
            $igst         = (float)($request->igst ?? $totalIgst);
            $tcs          = (float)($request->tcs ?? 0);
            $otherCharges = (float)($request->other_charges ?? 0);
            $paidAmount   = (float)($request->paid_amount ?? 0);

            $netAmount = $taxableAmount - $discount + $sgst + $cgst + $igst + $tcs + $otherCharges;
            $roundOff  = round($netAmount) - $netAmount;
            $netAmount = round($netAmount);

            $purchase->update([
                'invoice_date'     => $request->invoice_date,
                'supplier_id'      => $request->supplier_id,
                'supplier_name'    => $supplier->name,
                'supplier_bill_no' => $request->supplier_bill_no ?? '',
                'taxable_amount'   => $taxableAmount,
                'discount'         => $discount,
                'sgst'             => $sgst,
                'cgst'             => $cgst,
                'igst'             => $igst,
                'tcs'              => $tcs,
                'other_charges'    => $otherCharges,
                'round_off'        => $roundOff,
                'net_amount'       => $netAmount,
                'paid_amount'      => $paidAmount,
                'payment_mode'     => $request->payment_mode ?? 'credit',
                'bank_account_id'  => $request->bank_account_id ?: null,
                'cheque_no'        => $request->cheque_no,
                'cheque_date'      => $request->cheque_date ?: null,
                'narration'        => $request->narration,
            ]);

            $purchase->details()->delete();
            foreach ($items as $item) {
                if (empty($item['item_name'])) continue;
                $qty  = (float)($item['qty'] ?? 0);
                $rate = (float)($item['rate'] ?? 0);
                $amt  = (float)($item['amount'] ?? ($qty * $rate));
                PurchaseInvoiceDetail::create([
                    'purchase_invoice_id' => $purchase->id,
                    'slno'                => $purchase->slno,
                    'item_code'           => $item['item_code'] ?? '',
                    'item_name'           => $item['item_name'],
                    'hsn_code'            => $item['hsn_code'] ?? null,
                    'unit'                => $item['unit'] ?? 'NOS',
                    'qty'                 => $qty,
                    'rate'                => $rate,
                    'amount'              => $amt,
                    'discount'            => (float)($item['discount'] ?? 0),
                    'sgst_pct'            => (float)($item['sgst_pct'] ?? 0),
                    'cgst_pct'            => (float)($item['cgst_pct'] ?? 0),
                    'igst_pct'            => (float)($item['igst_pct'] ?? 0),
                    'sgst'                => (float)($item['sgst'] ?? 0),
                    'cgst'                => (float)($item['cgst'] ?? 0),
                    'igst'                => (float)($item['igst'] ?? 0),
                ]);
            }

            $this->daybookService->insertPurchaseInvoiceDaybookEntries($purchase);
        });

        return redirect()->route('purchase.index')->with('success', 'Purchase invoice updated.');
    }

    public function destroy(PurchaseInvoice $purchase)
    {
        DB::transaction(function () use ($purchase) {
            \App\Models\Daybook::where('slno', $purchase->slno)->delete();
            \App\Models\DaybookPart::where('slno', $purchase->slno)->delete();
            $purchase->details()->delete();
            $purchase->update(['status' => 0]);
        });
        return redirect()->route('purchase.index')->with('success', 'Purchase invoice cancelled.');
    }
}
