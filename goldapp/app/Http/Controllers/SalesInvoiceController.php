<?php
namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceDetail;
use App\Services\DaybookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesInvoiceController extends Controller
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

        $invoices = SalesInvoice::when($q, fn($qry) => $qry->where('customer_name', 'like', "%$q%")->orWhere('invoice_no', 'like', "%$q%"))
            ->when($from, fn($qry) => $qry->where('invoice_date', '>=', $from))
            ->when($to,   fn($qry) => $qry->where('invoice_date', '<=', $to))
            ->orderBy('invoice_date', 'desc')->orderBy('id', 'desc')
            ->paginate(20)->withQueryString();

        return view('sales.index', compact('invoices', 'q', 'from', 'to'));
    }

    public function create()
    {
        $customers    = Account::where('atype', 'CUSTOMER')->orderBy('name')->get();
        $bankAccounts = Account::where('atype', 'BANK')->orderBy('name')->get();
        $nextSlno     = $this->daybookService->generateSlno('SI', SalesInvoice::class);
        return view('sales.create', compact('customers', 'bankAccounts', 'nextSlno'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'invoice_date' => 'required|date',
            'customer_id'  => 'required|exists:account,id',
        ]);

        DB::transaction(function () use ($request) {
            $slno     = $this->daybookService->generateSlno('SI', SalesInvoice::class);
            $customer = Account::findOrFail($request->customer_id);

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
            $otherCharges  = (float)($request->other_charges ?? 0);
            $receivedAmount= (float)($request->received_amount ?? 0);

            $netAmount = $taxableAmount - $discount + $sgst + $cgst + $igst + $otherCharges;
            $roundOff  = round($netAmount) - $netAmount;
            $netAmount = round($netAmount);

            $inv = SalesInvoice::create([
                'slno'            => $slno,
                'invoice_no'      => $slno,
                'invoice_date'    => $request->invoice_date,
                'customer_id'     => $request->customer_id,
                'customer_name'   => $customer->name,
                'taxable_amount'  => $taxableAmount,
                'discount'        => $discount,
                'sgst'            => $sgst,
                'cgst'            => $cgst,
                'igst'            => $igst,
                'other_charges'   => $otherCharges,
                'round_off'       => $roundOff,
                'net_amount'      => $netAmount,
                'received_amount' => $receivedAmount,
                'payment_mode'    => $request->payment_mode ?? 'credit',
                'bank_account_id' => $request->bank_account_id ?: null,
                'cheque_no'       => $request->cheque_no,
                'cheque_date'     => $request->cheque_date ?: null,
                'narration'       => $request->narration,
                'status'          => 1,
                'created_by'      => Auth::id(),
            ]);

            foreach ($items as $item) {
                if (empty($item['item_name'])) continue;
                $qty  = (float)($item['qty'] ?? 0);
                $rate = (float)($item['rate'] ?? 0);
                $amt  = (float)($item['amount'] ?? ($qty * $rate));
                SalesInvoiceDetail::create([
                    'sales_invoice_id' => $inv->id,
                    'slno'             => $slno,
                    'item_code'        => $item['item_code'] ?? '',
                    'item_name'        => $item['item_name'],
                    'hsn_code'         => $item['hsn_code'] ?? null,
                    'unit'             => $item['unit'] ?? 'NOS',
                    'qty'              => $qty,
                    'rate'             => $rate,
                    'amount'           => $amt,
                    'discount'         => (float)($item['discount'] ?? 0),
                    'sgst_pct'         => (float)($item['sgst_pct'] ?? 0),
                    'cgst_pct'         => (float)($item['cgst_pct'] ?? 0),
                    'igst_pct'         => (float)($item['igst_pct'] ?? 0),
                    'sgst'             => (float)($item['sgst'] ?? 0),
                    'cgst'             => (float)($item['cgst'] ?? 0),
                    'igst'             => (float)($item['igst'] ?? 0),
                ]);
            }

            $this->daybookService->insertSalesInvoiceDaybookEntries($inv);
        });

        return redirect()->route('sales.index')->with('success', 'Sales invoice saved successfully.');
    }

    public function show(SalesInvoice $sale)
    {
        $sale->load('details');
        $daybookEntries = \App\Models\Daybook::where('slno', $sale->slno)->get();
        return view('sales.show', compact('sale', 'daybookEntries'));
    }

    public function edit(SalesInvoice $sale)
    {
        $sale->load('details');
        $customers    = Account::where('atype', 'CUSTOMER')->orderBy('name')->get();
        $bankAccounts = Account::where('atype', 'BANK')->orderBy('name')->get();
        return view('sales.edit', compact('sale', 'customers', 'bankAccounts'));
    }

    public function update(Request $request, SalesInvoice $sale)
    {
        $request->validate([
            'invoice_date' => 'required|date',
            'customer_id'  => 'required|exists:account,id',
        ]);

        DB::transaction(function () use ($request, $sale) {
            $customer = Account::findOrFail($request->customer_id);

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
            $otherCharges  = (float)($request->other_charges ?? 0);
            $receivedAmount= (float)($request->received_amount ?? 0);

            $netAmount = $taxableAmount - $discount + $sgst + $cgst + $igst + $otherCharges;
            $roundOff  = round($netAmount) - $netAmount;
            $netAmount = round($netAmount);

            $sale->update([
                'invoice_date'    => $request->invoice_date,
                'customer_id'     => $request->customer_id,
                'customer_name'   => $customer->name,
                'taxable_amount'  => $taxableAmount,
                'discount'        => $discount,
                'sgst'            => $sgst,
                'cgst'            => $cgst,
                'igst'            => $igst,
                'other_charges'   => $otherCharges,
                'round_off'       => $roundOff,
                'net_amount'      => $netAmount,
                'received_amount' => $receivedAmount,
                'payment_mode'    => $request->payment_mode ?? 'credit',
                'bank_account_id' => $request->bank_account_id ?: null,
                'cheque_no'       => $request->cheque_no,
                'cheque_date'     => $request->cheque_date ?: null,
                'narration'       => $request->narration,
            ]);

            $sale->details()->delete();
            foreach ($items as $item) {
                if (empty($item['item_name'])) continue;
                $qty  = (float)($item['qty'] ?? 0);
                $rate = (float)($item['rate'] ?? 0);
                $amt  = (float)($item['amount'] ?? ($qty * $rate));
                SalesInvoiceDetail::create([
                    'sales_invoice_id' => $sale->id,
                    'slno'             => $sale->slno,
                    'item_code'        => $item['item_code'] ?? '',
                    'item_name'        => $item['item_name'],
                    'hsn_code'         => $item['hsn_code'] ?? null,
                    'unit'             => $item['unit'] ?? 'NOS',
                    'qty'              => $qty,
                    'rate'             => $rate,
                    'amount'           => $amt,
                    'discount'         => (float)($item['discount'] ?? 0),
                    'sgst_pct'         => (float)($item['sgst_pct'] ?? 0),
                    'cgst_pct'         => (float)($item['cgst_pct'] ?? 0),
                    'igst_pct'         => (float)($item['igst_pct'] ?? 0),
                    'sgst'             => (float)($item['sgst'] ?? 0),
                    'cgst'             => (float)($item['cgst'] ?? 0),
                    'igst'             => (float)($item['igst'] ?? 0),
                ]);
            }

            $this->daybookService->insertSalesInvoiceDaybookEntries($sale);
        });

        return redirect()->route('sales.index')->with('success', 'Sales invoice updated.');
    }

    public function destroy(SalesInvoice $sale)
    {
        DB::transaction(function () use ($sale) {
            \App\Models\Daybook::where('slno', $sale->slno)->delete();
            \App\Models\DaybookPart::where('slno', $sale->slno)->delete();
            $sale->details()->delete();
            $sale->update(['status' => 0]);
        });
        return redirect()->route('sales.index')->with('success', 'Sales invoice cancelled.');
    }
}
