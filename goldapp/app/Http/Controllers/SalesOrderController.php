<?php
namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\FinishedGoods;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\SalesQuotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesOrderController extends Controller
{
    private function nextSlno(): string
    {
        $last = SalesOrder::orderByDesc('id')->value('slno');
        $num  = $last ? (int) substr($last, 2) + 1 : 1;
        return 'SO' . str_pad($num, 6, '0', STR_PAD_LEFT);
    }

    public function index(Request $request)
    {
        $orders = SalesOrder::with('items')
            ->when($request->q, fn($q) => $q->where('customer_name', 'like', "%{$request->q}%")->orWhere('so_no', 'like', "%{$request->q}%"))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->orderByDesc('so_date')
            ->paginate(20)->withQueryString();

        return view('sales.sales-orders.index', compact('orders'));
    }

    public function create()
    {
        $customers   = Account::where('atype', 'CUSTOMER')->orderBy('name')->get();
        $fgList      = FinishedGoods::where('status', 1)->orderBy('name')->get();
        $quotations  = SalesQuotation::where('status', 'sent')->orderByDesc('id')->get();
        $slno        = $this->nextSlno();
        return view('sales.sales-orders.form', compact('customers', 'fgList', 'quotations', 'slno'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'so_date'       => 'required|date',
            'customer_name' => 'required|string',
        ]);

        DB::transaction(function () use ($request) {
            $slno  = $this->nextSlno();
            $items = $request->items ?? [];
            [$taxable, $disc, $sgst, $cgst, $igst] = $this->sumItems($items);
            $net = $taxable - $disc + $sgst + $cgst + $igst;
            $advance = (float)($request->advance_received ?? 0);

            $so = SalesOrder::create([
                'slno'             => $slno,
                'so_no'            => $slno,
                'so_date'          => $request->so_date,
                'quot_id'          => $request->quot_id ?: null,
                'customer_id'      => $request->customer_id ?: null,
                'customer_name'    => $request->customer_name,
                'delivery_address' => $request->delivery_address,
                'delivery_date'    => $request->delivery_date ?: null,
                'channel'          => $request->channel ?? 'retail',
                'payment_terms'    => $request->payment_terms,
                'taxable_amount'   => $taxable,
                'discount'         => $disc,
                'sgst'             => $sgst,
                'cgst'             => $cgst,
                'igst'             => $igst,
                'net_amount'       => $net,
                'advance_received' => $advance,
                'balance_amount'   => $net - $advance,
                'status'           => 'open',
                'narration'        => $request->narration,
                'branch_id'        => Auth::user()->branch_id ?? null,
                'created_by'       => Auth::id(),
            ]);

            $this->saveItems($so->id, $items);
        });

        return redirect()->route('sales-orders.index')->with('success', 'Sales Order created.');
    }

    public function show(SalesOrder $salesOrder)
    {
        $salesOrder->load('items', 'dispatches');
        // Calculate dispatch progress per item
        $dispatchedQty = $salesOrder->items->sum('dispatched_qty');
        $orderedQty    = $salesOrder->items->sum('ordered_qty');
        $progress      = $orderedQty > 0 ? round(($dispatchedQty / $orderedQty) * 100) : 0;
        return view('sales.sales-orders.show', compact('salesOrder', 'progress'));
    }

    public function edit(SalesOrder $salesOrder)
    {
        $salesOrder->load('items');
        $customers  = Account::where('atype', 'CUSTOMER')->orderBy('name')->get();
        $fgList     = FinishedGoods::where('status', 1)->orderBy('name')->get();
        $quotations = SalesQuotation::where('status', 'sent')->orderByDesc('id')->get();
        return view('sales.sales-orders.form', compact('salesOrder', 'customers', 'fgList', 'quotations'));
    }

    public function update(Request $request, SalesOrder $salesOrder)
    {
        $request->validate(['so_date' => 'required|date']);

        DB::transaction(function () use ($request, $salesOrder) {
            $items = $request->items ?? [];
            [$taxable, $disc, $sgst, $cgst, $igst] = $this->sumItems($items);
            $net     = $taxable - $disc + $sgst + $cgst + $igst;
            $advance = (float)($request->advance_received ?? 0);

            $salesOrder->update([
                'so_date'          => $request->so_date,
                'customer_id'      => $request->customer_id ?: null,
                'customer_name'    => $request->customer_name,
                'delivery_address' => $request->delivery_address,
                'delivery_date'    => $request->delivery_date ?: null,
                'channel'          => $request->channel ?? 'retail',
                'payment_terms'    => $request->payment_terms,
                'taxable_amount'   => $taxable,
                'discount'         => $disc,
                'sgst'             => $sgst,
                'cgst'             => $cgst,
                'igst'             => $igst,
                'net_amount'       => $net,
                'advance_received' => $advance,
                'balance_amount'   => $net - $advance,
                'narration'        => $request->narration,
            ]);

            $salesOrder->items()->delete();
            $this->saveItems($salesOrder->id, $items);
        });

        return redirect()->route('sales-orders.show', $salesOrder)->with('success', 'Updated.');
    }

    public function destroy(SalesOrder $salesOrder)
    {
        $salesOrder->update(['status' => 'cancelled']);
        return redirect()->route('sales-orders.index')->with('success', 'Order cancelled.');
    }

    public function confirm(SalesOrder $salesOrder)
    {
        $salesOrder->update(['status' => 'confirmed']);
        return redirect()->route('sales-orders.show', $salesOrder)->with('success', 'Order confirmed.');
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

    private function saveItems(int $soId, array $items): void
    {
        foreach ($items as $item) {
            if (empty($item['fg_name'])) continue;
            $fg  = FinishedGoods::find($item['fg_id'] ?? null);
            $qty = (float)($item['ordered_qty'] ?? $item['qty'] ?? 0);
            SalesOrderItem::create([
                'so_id'           => $soId,
                'fg_id'           => $item['fg_id'] ?? null,
                'fg_code'         => $fg?->code ?? $item['fg_code'] ?? '',
                'fg_name'         => $item['fg_name'],
                'hsn_code'        => $item['hsn_code'] ?? $fg?->hsn_code ?? '',
                'unit'            => $item['unit'] ?? '',
                'ordered_qty'     => $qty,
                'dispatched_qty'  => 0,
                'pending_qty'     => $qty,
                'rate'            => (float)($item['rate'] ?? 0),
                'amount'          => (float)($item['amount'] ?? 0),
                'discount_pct'    => (float)($item['discount_pct'] ?? 0),
                'discount_amount' => (float)($item['discount_amount'] ?? 0),
                'sgst'            => (float)($item['sgst'] ?? 0),
                'cgst'            => (float)($item['cgst'] ?? 0),
                'igst'            => (float)($item['igst'] ?? 0),
                'net_amount'      => (float)($item['net_amount'] ?? 0),
            ]);
        }
    }
}
