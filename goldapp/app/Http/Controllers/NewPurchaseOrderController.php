<?php
namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\RawMaterial;
use App\Models\PackingMaterial;
use App\Models\FinishedGoods;
use App\Services\DaybookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NewPurchaseOrderController extends Controller
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

        $orders = PurchaseOrder::with('supplier')
            ->when($q, fn($query) => $query->where('po_no', 'like', "%$q%")->orWhere('supplier_name', 'like', "%$q%"))
            ->when($status, fn($query) => $query->where('status', $status))
            ->when($from, fn($query) => $query->where('po_date', '>=', $from))
            ->when($to, fn($query) => $query->where('po_date', '<=', $to))
            ->orderByDesc('po_date')->orderByDesc('id')
            ->paginate(20)->withQueryString();

        return view('procurement.purchase-orders.index', compact('orders', 'q', 'status', 'from', 'to'));
    }

    public function create()
    {
        $suppliers = Supplier::where('status', 'active')->orderBy('name')->get();
        $nextSlno  = $this->daybookService->nextSlno('PO', 'purchase_orders');
        return view('procurement.purchase-orders.form', [
            'order'     => null,
            'suppliers' => $suppliers,
            'nextSlno'  => $nextSlno,
            'items'     => [],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'po_date'     => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
        ]);

        DB::transaction(function () use ($request) {
            $slno     = $this->daybookService->nextSlno('PO', 'purchase_orders');
            $supplier = Supplier::findOrFail($request->supplier_id);
            $items    = $request->input('items', []);

            $taxable = 0; $sgst = 0; $cgst = 0; $igst = 0;
            foreach ($items as $item) {
                $taxable += (float)($item['amount'] ?? 0);
                $sgst    += (float)($item['sgst'] ?? 0);
                $cgst    += (float)($item['cgst'] ?? 0);
                $igst    += (float)($item['igst'] ?? 0);
            }
            $discount = (float)($request->discount_amount ?? 0);
            $tcs      = (float)($request->tcs ?? 0);
            $net      = $taxable - $discount + $sgst + $cgst + $igst + $tcs;
            $roundOff = round($net) - $net;
            $net      = round($net);

            $order = PurchaseOrder::create([
                'slno'            => $slno,
                'po_no'           => $slno,
                'po_date'         => $request->po_date,
                'supplier_id'     => $supplier->id,
                'supplier_name'   => $supplier->name,
                'delivery_date'   => $request->delivery_date ?: null,
                'currency'        => $request->currency ?? 'INR',
                'exchange_rate'   => $request->exchange_rate ?? 1,
                'payment_terms'   => $request->payment_terms ?? $supplier->payment_terms,
                'shipping_address'=> $request->shipping_address,
                'taxable_amount'  => $taxable,
                'discount_amount' => $discount,
                'sgst'            => $sgst,
                'cgst'            => $cgst,
                'igst'            => $igst,
                'tcs'             => $tcs,
                'round_off'       => $roundOff,
                'net_amount'      => $net,
                'status'          => 'draft',
                'narration'       => $request->narration,
                'branch_id'       => $request->branch_id,
                'created_by'      => Auth::id(),
            ]);

            foreach ($items as $item) {
                if (empty($item['item_name'])) continue;
                $qty  = (float)($item['qty'] ?? 0);
                $rate = (float)($item['rate'] ?? 0);
                $amt  = round($qty * $rate * (1 - (float)($item['discount_pct'] ?? 0) / 100), 2);
                PurchaseOrderItem::create([
                    'po_id'           => $order->id,
                    'item_type'       => $item['item_type'] ?? 'RM',
                    'item_id'         => $item['item_id'] ?: null,
                    'item_code'       => $item['item_code'] ?? '',
                    'item_name'       => $item['item_name'],
                    'hsn_code'        => $item['hsn_code'] ?? '',
                    'unit'            => $item['unit'] ?? 'KG',
                    'qty'             => $qty,
                    'pending_qty'     => $qty,
                    'rate'            => $rate,
                    'amount'          => $amt,
                    'discount_pct'    => (float)($item['discount_pct'] ?? 0),
                    'discount_amount' => (float)($item['discount_amount'] ?? 0),
                    'sgst_pct'        => (float)($item['sgst_pct'] ?? 0),
                    'cgst_pct'        => (float)($item['cgst_pct'] ?? 0),
                    'igst_pct'        => (float)($item['igst_pct'] ?? 0),
                    'sgst'            => (float)($item['sgst'] ?? 0),
                    'cgst'            => (float)($item['cgst'] ?? 0),
                    'igst'            => (float)($item['igst'] ?? 0),
                    'net_amount'      => (float)($item['net_amount'] ?? $amt),
                ]);
            }
        });

        return redirect()->route('purchase-orders.index')->with('success', 'Purchase Order created.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load('supplier', 'items', 'grns.items');
        return view('procurement.purchase-orders.show', compact('purchaseOrder'));
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        if (in_array($purchaseOrder->status, ['approved', 'received', 'cancelled'])) {
            return redirect()->route('purchase-orders.show', $purchaseOrder)->with('error', 'Cannot edit this PO.');
        }
        $suppliers = Supplier::where('status', 'active')->orderBy('name')->get();
        $purchaseOrder->load('items');
        return view('procurement.purchase-orders.form', [
            'order'     => $purchaseOrder,
            'suppliers' => $suppliers,
            'nextSlno'  => $purchaseOrder->slno,
            'items'     => $purchaseOrder->items,
        ]);
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $request->validate([
            'po_date'     => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
        ]);

        DB::transaction(function () use ($request, $purchaseOrder) {
            $supplier = Supplier::findOrFail($request->supplier_id);
            $items    = $request->input('items', []);

            $taxable = 0; $sgst = 0; $cgst = 0; $igst = 0;
            foreach ($items as $item) {
                $taxable += (float)($item['amount'] ?? 0);
                $sgst    += (float)($item['sgst'] ?? 0);
                $cgst    += (float)($item['cgst'] ?? 0);
                $igst    += (float)($item['igst'] ?? 0);
            }
            $discount = (float)($request->discount_amount ?? 0);
            $tcs      = (float)($request->tcs ?? 0);
            $net      = $taxable - $discount + $sgst + $cgst + $igst + $tcs;
            $roundOff = round($net) - $net;
            $net      = round($net);

            $purchaseOrder->update([
                'po_date'         => $request->po_date,
                'supplier_id'     => $supplier->id,
                'supplier_name'   => $supplier->name,
                'delivery_date'   => $request->delivery_date ?: null,
                'payment_terms'   => $request->payment_terms,
                'shipping_address'=> $request->shipping_address,
                'taxable_amount'  => $taxable,
                'discount_amount' => $discount,
                'sgst'            => $sgst,
                'cgst'            => $cgst,
                'igst'            => $igst,
                'tcs'             => $tcs,
                'round_off'       => $roundOff,
                'net_amount'      => $net,
                'narration'       => $request->narration,
            ]);

            $purchaseOrder->items()->delete();
            foreach ($items as $item) {
                if (empty($item['item_name'])) continue;
                $qty = (float)($item['qty'] ?? 0);
                $rate = (float)($item['rate'] ?? 0);
                $amt = round($qty * $rate * (1 - (float)($item['discount_pct'] ?? 0) / 100), 2);
                PurchaseOrderItem::create([
                    'po_id'       => $purchaseOrder->id,
                    'item_type'   => $item['item_type'] ?? 'RM',
                    'item_id'     => $item['item_id'] ?: null,
                    'item_code'   => $item['item_code'] ?? '',
                    'item_name'   => $item['item_name'],
                    'hsn_code'    => $item['hsn_code'] ?? '',
                    'unit'        => $item['unit'] ?? 'KG',
                    'qty'         => $qty,
                    'pending_qty' => $qty,
                    'rate'        => $rate,
                    'amount'      => $amt,
                    'discount_pct'=> (float)($item['discount_pct'] ?? 0),
                    'discount_amount' => (float)($item['discount_amount'] ?? 0),
                    'sgst_pct'    => (float)($item['sgst_pct'] ?? 0),
                    'cgst_pct'    => (float)($item['cgst_pct'] ?? 0),
                    'igst_pct'    => (float)($item['igst_pct'] ?? 0),
                    'sgst'        => (float)($item['sgst'] ?? 0),
                    'cgst'        => (float)($item['cgst'] ?? 0),
                    'igst'        => (float)($item['igst'] ?? 0),
                    'net_amount'  => (float)($item['net_amount'] ?? $amt),
                ]);
            }
        });

        return redirect()->route('purchase-orders.index')->with('success', 'Purchase Order updated.');
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'draft') {
            return back()->with('error', 'Only draft POs can be deleted.');
        }
        DB::transaction(function () use ($purchaseOrder) {
            $purchaseOrder->items()->delete();
            $purchaseOrder->delete();
        });
        return redirect()->route('purchase-orders.index')->with('success', 'PO deleted.');
    }

    public function approve(Request $request, $id)
    {
        $order = PurchaseOrder::findOrFail($id);
        if ($order->status !== 'draft') {
            return back()->with('error', 'Only draft POs can be approved.');
        }
        $order->update([
            'status'      => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);
        return redirect()->route('purchase-orders.show', $order)->with('success', 'PO approved.');
    }
}
