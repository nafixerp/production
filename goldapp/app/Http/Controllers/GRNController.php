<?php
namespace App\Http\Controllers;

use App\Models\Grn;
use App\Models\GrnItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\InventoryStock;
use App\Models\StockMovement;
use App\Services\DaybookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GRNController extends Controller
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

        $grns = Grn::with('supplier')
            ->when($q, fn($query) => $query->where('grn_no', 'like', "%$q%")->orWhere('supplier_name', 'like', "%$q%"))
            ->when($status, fn($query) => $query->where('status', $status))
            ->when($from, fn($query) => $query->where('grn_date', '>=', $from))
            ->when($to, fn($query) => $query->where('grn_date', '<=', $to))
            ->orderByDesc('grn_date')->orderByDesc('id')
            ->paginate(20)->withQueryString();

        return view('procurement.grn.index', compact('grns', 'q', 'status', 'from', 'to'));
    }

    public function create(Request $request)
    {
        $suppliers = Supplier::where('status', 'active')->orderBy('name')->get();
        $poId      = $request->po_id;
        $po        = $poId ? PurchaseOrder::with('items')->find($poId) : null;
        $openPOs   = PurchaseOrder::whereIn('status', ['approved', 'partially_received'])->with('supplier')->orderByDesc('id')->get();

        return view('procurement.grn.form', [
            'grn'       => null,
            'suppliers' => $suppliers,
            'po'        => $po,
            'openPOs'   => $openPOs,
            'items'     => $po ? $po->items->map(fn($i) => (object)[
                'po_item_id'  => $i->id,
                'item_type'   => $i->item_type,
                'item_id'     => $i->item_id,
                'item_code'   => $i->item_code,
                'item_name'   => $i->item_name,
                'unit'        => $i->unit,
                'ordered_qty' => $i->pending_qty,
                'received_qty'=> 0,
                'accepted_qty'=> 0,
                'rejected_qty'=> 0,
                'rate'        => $i->rate,
                'amount'      => 0,
                'batch_no'    => '',
                'mfg_date'    => null,
                'expiry_date' => null,
                'warehouse_id'=> null,
            ])->all() : [],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'grn_date'    => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
        ]);

        DB::transaction(function () use ($request) {
            $slno     = $this->daybookService->nextSlno('GRN', 'grn');
            $supplier = Supplier::findOrFail($request->supplier_id);
            $items    = $request->input('items', []);

            $taxable = 0; $sgst = 0; $cgst = 0; $igst = 0; $net = 0;
            foreach ($items as $item) {
                $taxable += (float)($item['amount'] ?? 0);
                $net     += (float)($item['amount'] ?? 0);
            }

            $grn = Grn::create([
                'slno'                  => $slno,
                'grn_no'                => $slno,
                'grn_date'              => $request->grn_date,
                'po_id'                 => $request->po_id ?: null,
                'supplier_id'           => $supplier->id,
                'supplier_name'         => $supplier->name,
                'supplier_invoice_no'   => $request->supplier_invoice_no,
                'supplier_invoice_date' => $request->supplier_invoice_date ?: null,
                'taxable_amount'        => $taxable,
                'sgst'                  => $sgst,
                'cgst'                  => $cgst,
                'igst'                  => $igst,
                'net_amount'            => $net,
                'status'                => 'qc_pending',
                'narration'             => $request->narration,
                'branch_id'             => $request->branch_id,
                'created_by'            => Auth::id(),
            ]);

            foreach ($items as $item) {
                if (empty($item['item_name'])) continue;
                $receivedQty  = (float)($item['received_qty'] ?? 0);
                $acceptedQty  = (float)($item['accepted_qty'] ?? $receivedQty);
                $rejectedQty  = $receivedQty - $acceptedQty;
                $rate         = (float)($item['rate'] ?? 0);
                $amount       = round($acceptedQty * $rate, 2);

                GrnItem::create([
                    'grn_id'       => $grn->id,
                    'po_item_id'   => $item['po_item_id'] ?: null,
                    'item_type'    => $item['item_type'] ?? 'RM',
                    'item_id'      => $item['item_id'] ?: null,
                    'item_code'    => $item['item_code'] ?? '',
                    'item_name'    => $item['item_name'],
                    'unit'         => $item['unit'] ?? 'KG',
                    'ordered_qty'  => (float)($item['ordered_qty'] ?? 0),
                    'received_qty' => $receivedQty,
                    'accepted_qty' => $acceptedQty,
                    'rejected_qty' => $rejectedQty,
                    'rate'         => $rate,
                    'amount'       => $amount,
                    'batch_no'     => $item['batch_no'] ?? null,
                    'mfg_date'     => $item['mfg_date'] ?: null,
                    'expiry_date'  => $item['expiry_date'] ?: null,
                    'warehouse_id' => $item['warehouse_id'] ?: null,
                ]);

                // Update inventory stock (IN)
                if ($acceptedQty > 0) {
                    $this->updateStockIN($item, $acceptedQty, $rate, $grn);
                }

                // Update PO pending qty
                if (!empty($item['po_item_id'])) {
                    $poItem = PurchaseOrderItem::find($item['po_item_id']);
                    if ($poItem) {
                        $newPending = max(0, $poItem->pending_qty - $acceptedQty);
                        $poItem->update(['pending_qty' => $newPending]);
                    }
                }
            }

            // Update PO status
            if ($request->po_id) {
                $po = PurchaseOrder::find($request->po_id);
                if ($po) {
                    $allReceived = $po->items()->where('pending_qty', '>', 0)->doesntExist();
                    $po->update(['status' => $allReceived ? 'received' : 'partially_received']);
                }
            }
        });

        return redirect()->route('grn.index')->with('success', 'GRN saved.');
    }

    protected function updateStockIN(array $item, float $qty, float $rate, Grn $grn): void
    {
        // Get or create inventory stock record
        $stock = InventoryStock::firstOrCreate([
            'item_type'    => $item['item_type'] ?? 'RM',
            'item_id'      => $item['item_id'] ?? 0,
            'warehouse_id' => $item['warehouse_id'] ?: null,
            'batch_no'     => $item['batch_no'] ?: null,
        ], [
            'item_code'   => $item['item_code'] ?? '',
            'item_name'   => $item['item_name'],
            'unit'        => $item['unit'] ?? 'KG',
            'cost_rate'   => $rate,
            'status'      => 'available',
            'mfg_date'    => $item['mfg_date'] ?: null,
            'expiry_date' => $item['expiry_date'] ?: null,
        ]);
        $stock->increment('qty_in', $qty);
        if ($item['expiry_date'] ?? null) $stock->update(['expiry_date' => $item['expiry_date']]);

        // Running balance
        $runningBalance = InventoryStock::where('item_type', $item['item_type'] ?? 'RM')
            ->where('item_id', $item['item_id'] ?? 0)
            ->selectRaw('SUM(qty_in - qty_out) as bal')->value('bal') ?? 0;

        StockMovement::create([
            'movement_date'   => $grn->grn_date,
            'item_type'       => $item['item_type'] ?? 'RM',
            'item_id'         => $item['item_id'] ?? 0,
            'item_code'       => $item['item_code'] ?? '',
            'item_name'       => $item['item_name'],
            'warehouse_id'    => $item['warehouse_id'] ?: null,
            'batch_no'        => $item['batch_no'] ?: null,
            'movement_type'   => 'IN',
            'ref_type'        => 'GRN',
            'ref_id'          => $grn->id,
            'qty'             => $qty,
            'unit'            => $item['unit'] ?? 'KG',
            'cost_rate'       => $rate,
            'cost_amount'     => round($qty * $rate, 2),
            'running_balance' => $runningBalance,
            'narration'       => "GRN {$grn->slno}",
            'created_by'      => Auth::id(),
        ]);
    }

    public function show(Grn $grn)
    {
        $grn->load('supplier', 'purchaseOrder', 'items');
        return view('procurement.grn.show', compact('grn'));
    }

    public function edit(Grn $grn)
    {
        if ($grn->status === 'approved') {
            return redirect()->route('grn.show', $grn)->with('error', 'Approved GRN cannot be edited.');
        }
        $suppliers = Supplier::where('status', 'active')->orderBy('name')->get();
        $openPOs   = PurchaseOrder::whereIn('status', ['approved', 'partially_received'])->with('supplier')->orderByDesc('id')->get();
        $grn->load('items');

        return view('procurement.grn.form', [
            'grn'       => $grn,
            'suppliers' => $suppliers,
            'openPOs'   => $openPOs,
            'po'        => $grn->purchaseOrder,
            'items'     => $grn->items,
        ]);
    }

    public function update(Request $request, Grn $grn)
    {
        $request->validate([
            'grn_date'    => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
        ]);

        DB::transaction(function () use ($request, $grn) {
            $supplier = Supplier::findOrFail($request->supplier_id);
            $items    = $request->input('items', []);
            $taxable  = collect($items)->sum(fn($i) => (float)($i['amount'] ?? 0));

            $grn->update([
                'grn_date'              => $request->grn_date,
                'supplier_id'           => $supplier->id,
                'supplier_name'         => $supplier->name,
                'supplier_invoice_no'   => $request->supplier_invoice_no,
                'supplier_invoice_date' => $request->supplier_invoice_date ?: null,
                'taxable_amount'        => $taxable,
                'net_amount'            => $taxable,
                'narration'             => $request->narration,
            ]);

            $grn->items()->delete();
            foreach ($items as $item) {
                if (empty($item['item_name'])) continue;
                $receivedQty = (float)($item['received_qty'] ?? 0);
                $acceptedQty = (float)($item['accepted_qty'] ?? $receivedQty);
                GrnItem::create([
                    'grn_id'       => $grn->id,
                    'po_item_id'   => $item['po_item_id'] ?: null,
                    'item_type'    => $item['item_type'] ?? 'RM',
                    'item_id'      => $item['item_id'] ?: null,
                    'item_code'    => $item['item_code'] ?? '',
                    'item_name'    => $item['item_name'],
                    'unit'         => $item['unit'] ?? 'KG',
                    'ordered_qty'  => (float)($item['ordered_qty'] ?? 0),
                    'received_qty' => $receivedQty,
                    'accepted_qty' => $acceptedQty,
                    'rejected_qty' => $receivedQty - $acceptedQty,
                    'rate'         => (float)($item['rate'] ?? 0),
                    'amount'       => round($acceptedQty * (float)($item['rate'] ?? 0), 2),
                    'batch_no'     => $item['batch_no'] ?? null,
                    'mfg_date'     => $item['mfg_date'] ?: null,
                    'expiry_date'  => $item['expiry_date'] ?: null,
                    'warehouse_id' => $item['warehouse_id'] ?: null,
                ]);
            }
        });

        return redirect()->route('grn.index')->with('success', 'GRN updated.');
    }

    public function destroy(Grn $grn)
    {
        if ($grn->status === 'approved') {
            return back()->with('error', 'Cannot delete approved GRN.');
        }
        DB::transaction(function () use ($grn) {
            $grn->items()->delete();
            $grn->delete();
        });
        return redirect()->route('grn.index')->with('success', 'GRN deleted.');
    }
}
