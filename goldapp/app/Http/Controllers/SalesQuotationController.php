<?php
namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\FinishedGoods;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\SalesQuotation;
use App\Models\SalesQuotationItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesQuotationController extends Controller
{
    private function nextSlno(): string
    {
        $last = SalesQuotation::orderByDesc('id')->value('slno');
        $num  = $last ? (int) substr($last, 2) + 1 : 1;
        return 'QT' . str_pad($num, 6, '0', STR_PAD_LEFT);
    }

    public function index(Request $request)
    {
        $quotations = SalesQuotation::with('customer')
            ->when($request->q, fn($q) => $q->where('customer_name', 'like', "%{$request->q}%")->orWhere('quot_no', 'like', "%{$request->q}%"))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->orderByDesc('quot_date')
            ->paginate(20)->withQueryString();
        return view('sales.sales-quotations.index', compact('quotations'));
    }

    public function create()
    {
        $customers = Account::where('atype', 'CUSTOMER')->orderBy('name')->get();
        $fgList    = FinishedGoods::where('status', 1)->orderBy('name')->get();
        $slno      = $this->nextSlno();
        return view('sales.sales-quotations.form', compact('customers', 'fgList', 'slno'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'quot_date'     => 'required|date',
            'customer_name' => 'required|string',
        ]);

        DB::transaction(function () use ($request) {
            $slno  = $this->nextSlno();
            $items = $request->items ?? [];
            [$taxable, $disc, $sgst, $cgst, $igst] = $this->sumItems($items);
            $net = $taxable - $disc + $sgst + $cgst + $igst;

            $quot = SalesQuotation::create([
                'slno'             => $slno,
                'quot_no'          => $slno,
                'quot_date'        => $request->quot_date,
                'valid_till'       => $request->valid_till ?: null,
                'customer_id'      => $request->customer_id ?: null,
                'customer_name'    => $request->customer_name,
                'customer_address' => $request->customer_address,
                'channel'          => $request->channel ?? 'retail',
                'taxable_amount'   => $taxable,
                'discount'         => $disc,
                'sgst'             => $sgst,
                'cgst'             => $cgst,
                'igst'             => $igst,
                'net_amount'       => $net,
                'status'           => 'draft',
                'terms'            => $request->terms,
                'narration'        => $request->narration,
                'created_by'       => Auth::id(),
            ]);

            $this->saveItems($quot->id, $items);
        });

        return redirect()->route('sales-quotations.index')->with('success', 'Quotation saved.');
    }

    public function show(SalesQuotation $salesQuotation)
    {
        $salesQuotation->load('items');
        return view('sales.sales-quotations.show', compact('salesQuotation'));
    }

    public function edit(SalesQuotation $salesQuotation)
    {
        $salesQuotation->load('items');
        $customers = Account::where('atype', 'CUSTOMER')->orderBy('name')->get();
        $fgList    = FinishedGoods::where('status', 1)->orderBy('name')->get();
        return view('sales.sales-quotations.form', compact('salesQuotation', 'customers', 'fgList'));
    }

    public function update(Request $request, SalesQuotation $salesQuotation)
    {
        $request->validate(['quot_date' => 'required|date']);

        DB::transaction(function () use ($request, $salesQuotation) {
            $items = $request->items ?? [];
            [$taxable, $disc, $sgst, $cgst, $igst] = $this->sumItems($items);
            $net = $taxable - $disc + $sgst + $cgst + $igst;

            $salesQuotation->update([
                'quot_date'        => $request->quot_date,
                'valid_till'       => $request->valid_till ?: null,
                'customer_id'      => $request->customer_id ?: null,
                'customer_name'    => $request->customer_name,
                'customer_address' => $request->customer_address,
                'channel'          => $request->channel ?? 'retail',
                'taxable_amount'   => $taxable,
                'discount'         => $disc,
                'sgst'             => $sgst,
                'cgst'             => $cgst,
                'igst'             => $igst,
                'net_amount'       => $net,
                'terms'            => $request->terms,
                'narration'        => $request->narration,
            ]);

            $salesQuotation->items()->delete();
            $this->saveItems($salesQuotation->id, $items);
        });

        return redirect()->route('sales-quotations.show', $salesQuotation)->with('success', 'Updated.');
    }

    public function destroy(SalesQuotation $salesQuotation)
    {
        $salesQuotation->update(['status' => 'rejected']);
        return redirect()->route('sales-quotations.index')->with('success', 'Quotation cancelled.');
    }

    public function convertToSO(SalesQuotation $salesQuotation)
    {
        if ($salesQuotation->status === 'accepted') {
            return redirect()->route('sales-quotations.show', $salesQuotation)->with('error', 'Already converted.');
        }

        DB::transaction(function () use ($salesQuotation) {
            $lastSo = SalesOrder::orderByDesc('id')->value('slno');
            $num    = $lastSo ? (int) substr($lastSo, 2) + 1 : 1;
            $soSlno = 'SO' . str_pad($num, 6, '0', STR_PAD_LEFT);

            $so = SalesOrder::create([
                'slno'             => $soSlno,
                'so_no'            => $soSlno,
                'so_date'          => now()->toDateString(),
                'quot_id'          => $salesQuotation->id,
                'customer_id'      => $salesQuotation->customer_id,
                'customer_name'    => $salesQuotation->customer_name,
                'delivery_address' => $salesQuotation->customer_address,
                'channel'          => $salesQuotation->channel,
                'taxable_amount'   => $salesQuotation->taxable_amount,
                'discount'         => $salesQuotation->discount,
                'sgst'             => $salesQuotation->sgst,
                'cgst'             => $salesQuotation->cgst,
                'igst'             => $salesQuotation->igst,
                'net_amount'       => $salesQuotation->net_amount,
                'balance_amount'   => $salesQuotation->net_amount,
                'status'           => 'open',
                'narration'        => "From Quotation {$salesQuotation->quot_no}",
                'created_by'       => Auth::id(),
            ]);

            foreach ($salesQuotation->items as $qi) {
                SalesOrderItem::create([
                    'so_id'           => $so->id,
                    'fg_id'           => $qi->fg_id,
                    'fg_code'         => $qi->fg_code,
                    'fg_name'         => $qi->fg_name,
                    'hsn_code'        => $qi->hsn_code,
                    'unit'            => $qi->unit,
                    'ordered_qty'     => $qi->qty,
                    'dispatched_qty'  => 0,
                    'pending_qty'     => $qi->qty,
                    'rate'            => $qi->rate,
                    'amount'          => $qi->amount,
                    'discount_pct'    => $qi->discount_pct,
                    'discount_amount' => $qi->discount_amount,
                    'sgst'            => $qi->sgst,
                    'cgst'            => $qi->cgst,
                    'igst'            => $qi->igst,
                    'net_amount'      => $qi->net_amount,
                ]);
            }

            $salesQuotation->update(['status' => 'accepted']);
        });

        return redirect()->route('sales-orders.index')->with('success', 'Quotation converted to Sales Order.');
    }

    private function sumItems(array $items): array
    {
        $taxable = collect($items)->sum(fn($i) => (float)($i['amount'] ?? 0));
        $disc    = collect($items)->sum(fn($i) => (float)($i['discount_amount'] ?? 0));
        $sgst    = collect($items)->sum(fn($i) => (float)($i['sgst'] ?? 0));
        $cgst    = collect($items)->sum(fn($i) => (float)($i['cgst'] ?? 0));
        $igst    = collect($items)->sum(fn($i) => (float)($i['igst'] ?? 0));
        return [$taxable, $disc, $sgst, $cgst, $igst];
    }

    private function saveItems(int $quotId, array $items): void
    {
        foreach ($items as $item) {
            if (empty($item['fg_name'])) continue;
            $fg = FinishedGoods::find($item['fg_id'] ?? null);
            SalesQuotationItem::create([
                'quot_id'         => $quotId,
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
            ]);
        }
    }
}
