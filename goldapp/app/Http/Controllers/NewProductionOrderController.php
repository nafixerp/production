<?php
namespace App\Http\Controllers;

use App\Models\ProductionOrderNew;
use App\Models\ProductionBom;
use App\Models\ProductionJournalNew;
use App\Models\ProductionCostingNew;
use App\Models\WipTracking;
use App\Models\InventoryStock;
use App\Models\StockMovement;
use App\Models\FinishedGoods;
use App\Models\RecipeBom;
use App\Models\RecipeBomDetail;
use App\Services\DaybookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NewProductionOrderController extends Controller
{
    protected DaybookService $daybookService;

    public function __construct(DaybookService $daybookService)
    {
        $this->daybookService = $daybookService;
    }

    public function index(Request $request)
    {
        $q        = $request->q;
        $status   = $request->status;
        $priority = $request->priority;
        $from     = $request->from;
        $to       = $request->to;

        $orders = ProductionOrderNew::with('costing')
            ->when($q, fn($query) => $query->where('fg_name', 'like', "%$q%")->orWhere('slno', 'like', "%$q%"))
            ->when($status, fn($query) => $query->where('status', $status))
            ->when($priority, fn($query) => $query->where('priority', $priority))
            ->when($from, fn($query) => $query->where('order_date', '>=', $from))
            ->when($to, fn($query) => $query->where('order_date', '<=', $to))
            ->orderByDesc('order_date')->orderByDesc('id')
            ->paginate(20)->withQueryString();

        return view('manufacturing.production-orders.index', compact('orders', 'q', 'status', 'priority', 'from', 'to'));
    }

    public function create()
    {
        $fgList  = FinishedGoods::where('status', 1)->orderBy('name')->get();
        $recipes = RecipeBom::orderBy('name')->get();
        $nextSlno = $this->daybookService->nextSlno('PROD', 'production_orders');

        return view('manufacturing.production-orders.form', [
            'order'    => null,
            'fgList'   => $fgList,
            'recipes'  => $recipes,
            'nextSlno' => $nextSlno,
            'bom'      => [],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_date' => 'required|date',
            'fg_id'      => 'required|exists:finished_goods,id',
            'order_qty'  => 'required|numeric|min:0.0001',
        ]);

        DB::transaction(function () use ($request) {
            $slno = $this->daybookService->nextSlno('PROD', 'production_orders');
            $fg   = FinishedGoods::findOrFail($request->fg_id);
            $boms = $request->input('bom', []);

            $order = ProductionOrderNew::create([
                'slno'          => $slno,
                'po_no'         => $slno,
                'order_date'    => $request->order_date,
                'planned_start' => $request->planned_start ?: null,
                'planned_end'   => $request->planned_end ?: null,
                'fg_id'         => $fg->id,
                'fg_code'       => $fg->code ?? '',
                'fg_name'       => $fg->name,
                'recipe_id'     => $request->recipe_id ?: null,
                'batch_no'      => $request->batch_no ?? $slno,
                'order_qty'     => (float)$request->order_qty,
                'unit'          => $request->unit ?? $fg->unit ?? 'KG',
                'status'        => 'draft',
                'priority'      => $request->priority ?? 'normal',
                'cost_centre'   => $request->cost_centre,
                'narration'     => $request->narration,
                'branch_id'     => $request->branch_id,
                'created_by'    => Auth::id(),
            ]);

            foreach ($boms as $bom) {
                if (empty($bom['item_name'])) continue;
                $reqQty   = (float)($bom['required_qty'] ?? 0);
                $costRate = (float)($bom['cost_rate'] ?? 0);
                ProductionBom::create([
                    'production_order_id' => $order->id,
                    'item_type'           => $bom['item_type'] ?? 'RM',
                    'item_id'             => $bom['item_id'] ?: null,
                    'item_code'           => $bom['item_code'] ?? '',
                    'item_name'           => $bom['item_name'],
                    'unit'                => $bom['unit'] ?? 'KG',
                    'required_qty'        => $reqQty,
                    'wastage_pct'         => (float)($bom['wastage_pct'] ?? 0),
                    'cost_rate'           => $costRate,
                    'cost_amount'         => round($reqQty * $costRate, 2),
                    'warehouse_id'        => $bom['warehouse_id'] ?: null,
                ]);
            }
        });

        return redirect()->route('production-orders-new.index')->with('success', 'Production Order created.');
    }

    public function show(ProductionOrderNew $productionOrderNew)
    {
        $productionOrderNew->load('bom', 'journal', 'costing', 'wipTracking');
        $totalIssuedCost = $productionOrderNew->bom->sum('cost_amount');
        return view('manufacturing.production-orders.show', compact('productionOrderNew', 'totalIssuedCost'));
    }

    public function edit(ProductionOrderNew $productionOrderNew)
    {
        if (in_array($productionOrderNew->status, ['completed', 'cancelled'])) {
            return redirect()->route('production-orders-new.show', $productionOrderNew)->with('error', 'Cannot edit this order.');
        }
        $fgList  = FinishedGoods::where('status', 1)->orderBy('name')->get();
        $recipes = RecipeBom::orderBy('name')->get();
        $productionOrderNew->load('bom');

        return view('manufacturing.production-orders.form', [
            'order'    => $productionOrderNew,
            'fgList'   => $fgList,
            'recipes'  => $recipes,
            'nextSlno' => $productionOrderNew->slno,
            'bom'      => $productionOrderNew->bom,
        ]);
    }

    public function update(Request $request, ProductionOrderNew $productionOrderNew)
    {
        $request->validate([
            'order_date' => 'required|date',
            'fg_id'      => 'required|exists:finished_goods,id',
            'order_qty'  => 'required|numeric|min:0.0001',
        ]);

        DB::transaction(function () use ($request, $productionOrderNew) {
            $fg   = FinishedGoods::findOrFail($request->fg_id);
            $boms = $request->input('bom', []);

            $productionOrderNew->update([
                'order_date'    => $request->order_date,
                'planned_start' => $request->planned_start ?: null,
                'planned_end'   => $request->planned_end ?: null,
                'fg_id'         => $fg->id,
                'fg_code'       => $fg->code ?? '',
                'fg_name'       => $fg->name,
                'recipe_id'     => $request->recipe_id ?: null,
                'batch_no'      => $request->batch_no ?? $productionOrderNew->slno,
                'order_qty'     => (float)$request->order_qty,
                'unit'          => $request->unit ?? 'KG',
                'priority'      => $request->priority ?? 'normal',
                'cost_centre'   => $request->cost_centre,
                'narration'     => $request->narration,
            ]);

            $productionOrderNew->bom()->delete();
            foreach ($boms as $bom) {
                if (empty($bom['item_name'])) continue;
                $reqQty   = (float)($bom['required_qty'] ?? 0);
                $costRate = (float)($bom['cost_rate'] ?? 0);
                ProductionBom::create([
                    'production_order_id' => $productionOrderNew->id,
                    'item_type'           => $bom['item_type'] ?? 'RM',
                    'item_id'             => $bom['item_id'] ?: null,
                    'item_code'           => $bom['item_code'] ?? '',
                    'item_name'           => $bom['item_name'],
                    'unit'                => $bom['unit'] ?? 'KG',
                    'required_qty'        => $reqQty,
                    'wastage_pct'         => (float)($bom['wastage_pct'] ?? 0),
                    'cost_rate'           => $costRate,
                    'cost_amount'         => round($reqQty * $costRate, 2),
                    'warehouse_id'        => $bom['warehouse_id'] ?: null,
                ]);
            }
        });

        return redirect()->route('production-orders-new.index')->with('success', 'Production Order updated.');
    }

    public function destroy(ProductionOrderNew $productionOrderNew)
    {
        if (!in_array($productionOrderNew->status, ['draft', 'cancelled'])) {
            return back()->with('error', 'Only draft orders can be deleted.');
        }
        DB::transaction(function () use ($productionOrderNew) {
            $productionOrderNew->bom()->delete();
            $productionOrderNew->delete();
        });
        return redirect()->route('production-orders-new.index')->with('success', 'Production Order deleted.');
    }

    /**
     * Issue materials to production
     * POST /production-orders/{id}/issue
     */
    public function issue(Request $request, $id)
    {
        $order = ProductionOrderNew::with('bom')->findOrFail($id);

        if (!in_array($order->status, ['released', 'in_progress'])) {
            return back()->with('error', 'Order must be released before issuing materials.');
        }

        DB::transaction(function () use ($request, $order) {
            $totalCost  = 0;
            $issueDate  = $request->issue_date ?? now()->format('Y-m-d');
            $slno       = $this->daybookService->nextSlno('MI', 'stock_movements', 'narration');

            foreach ($order->bom as $bomLine) {
                $issueQty  = (float)($request->input("issue_qty.{$bomLine->id}", $bomLine->required_qty - $bomLine->issued_qty));
                if ($issueQty <= 0) continue;

                $costRate  = $bomLine->cost_rate;
                $costAmt   = round($issueQty * $costRate, 2);
                $totalCost += $costAmt;

                // Deduct from inventory (FIFO: oldest batch first)
                $remaining = $issueQty;
                $stockRows = InventoryStock::where('item_type', $bomLine->item_type)
                    ->where('item_id', $bomLine->item_id ?? 0)
                    ->where('status', 'available')
                    ->selectRaw('*, (qty_in - qty_out) as balance_qty')
                    ->orderBy('mfg_date')->orderBy('id')
                    ->get();

                foreach ($stockRows as $stock) {
                    if ($remaining <= 0) break;
                    $avail = max(0, $stock->qty_in - $stock->qty_out);
                    $deduct = min($avail, $remaining);
                    if ($deduct <= 0) continue;
                    $stock->increment('qty_out', $deduct);
                    $remaining -= $deduct;
                }

                // Update running balance
                $runningBalance = InventoryStock::where('item_type', $bomLine->item_type)
                    ->where('item_id', $bomLine->item_id ?? 0)
                    ->selectRaw('SUM(qty_in - qty_out) as bal')->value('bal') ?? 0;

                StockMovement::create([
                    'movement_date'   => $issueDate,
                    'item_type'       => $bomLine->item_type,
                    'item_id'         => $bomLine->item_id ?? 0,
                    'item_code'       => $bomLine->item_code,
                    'item_name'       => $bomLine->item_name,
                    'warehouse_id'    => $bomLine->warehouse_id,
                    'movement_type'   => 'OUT',
                    'ref_type'        => 'PROD-ISSUE',
                    'ref_id'          => $order->id,
                    'qty'             => $issueQty,
                    'unit'            => $bomLine->unit,
                    'cost_rate'       => $costRate,
                    'cost_amount'     => $costAmt,
                    'running_balance' => $runningBalance,
                    'narration'       => "Issue to Production {$order->slno}",
                    'created_by'      => Auth::id(),
                ]);

                // Update BOM issued qty
                $bomLine->update([
                    'issued_qty'  => $bomLine->issued_qty + $issueQty,
                    'cost_amount' => round(($bomLine->issued_qty + $issueQty) * $costRate, 2),
                ]);
            }

            // Log journal
            ProductionJournalNew::create([
                'production_order_id' => $order->id,
                'journal_date'        => $issueDate,
                'stage'               => 'material_issue',
                'qty'                 => $order->order_qty,
                'unit'                => $order->unit,
                'remarks'             => "Materials issued. Total cost: ₹" . number_format($totalCost, 2),
                'done_by'             => Auth::id(),
            ]);

            // Update order status
            if ($order->status === 'released') {
                $order->update(['status' => 'in_progress', 'actual_start' => $issueDate]);
            }

            // Post daybook: WIP -cost (Debit), RM Stock +cost (Credit)
            if ($totalCost > 0) {
                $miSlno = $this->daybookService->nextSlno('MI', 'stock_movements');
                $this->daybookService->insertMaterialIssueDaybookEntries(
                    $miSlno, $totalCost, $issueDate, $order->branch_id,
                    "Material Issue - Production {$order->slno}"
                );
            }

            // WIP tracking
            WipTracking::create([
                'production_order_id' => $order->id,
                'stage'               => 'material_issue',
                'started_at'          => now(),
                'input_qty'           => $order->order_qty,
                'output_qty'          => 0,
                'loss_qty'            => 0,
                'remarks'             => "Materials issued",
                'operator_id'         => Auth::id(),
            ]);
        });

        return redirect()->route('production-orders-new.show', $order)->with('success', 'Materials issued to production.');
    }

    /**
     * Complete production order — FG receipt
     * POST /production-orders/{id}/complete
     */
    public function complete(Request $request, $id)
    {
        $order = ProductionOrderNew::with('bom')->findOrFail($id);

        if ($order->status !== 'in_progress') {
            return back()->with('error', 'Order must be in_progress to complete.');
        }

        DB::transaction(function () use ($request, $order) {
            $producedQty  = (float)($request->produced_qty ?? $order->order_qty);
            $completeDate = $request->complete_date ?? now()->format('Y-m-d');
            $labourCost   = (float)($request->labour_cost ?? 0);
            $overheadCost = (float)($request->overhead_cost ?? 0);

            // Calculate costs from BOM
            $rmCost = $order->bom->where('item_type', 'RM')->sum('cost_amount');
            $pmCost = $order->bom->where('item_type', 'PM')->sum('cost_amount');
            $totalCost = $rmCost + $pmCost + $labourCost + $overheadCost;
            $costPerUnit = $producedQty > 0 ? round($totalCost / $producedQty, 4) : 0;

            // Add FG to inventory (IN)
            $stock = InventoryStock::firstOrCreate([
                'item_type'    => 'FG',
                'item_id'      => $order->fg_id,
                'warehouse_id' => $request->warehouse_id ?: null,
                'batch_no'     => $order->batch_no,
            ], [
                'item_code'   => $order->fg_code,
                'item_name'   => $order->fg_name,
                'unit'        => $order->unit,
                'cost_rate'   => $costPerUnit,
                'status'      => 'available',
            ]);
            $stock->increment('qty_in', $producedQty);
            $stock->update(['cost_rate' => $costPerUnit]);

            // Running balance
            $runningBalance = InventoryStock::where('item_type', 'FG')
                ->where('item_id', $order->fg_id)
                ->selectRaw('SUM(qty_in - qty_out) as bal')->value('bal') ?? 0;

            StockMovement::create([
                'movement_date'   => $completeDate,
                'item_type'       => 'FG',
                'item_id'         => $order->fg_id,
                'item_code'       => $order->fg_code,
                'item_name'       => $order->fg_name,
                'movement_type'   => 'IN',
                'ref_type'        => 'PROD-FG',
                'ref_id'          => $order->id,
                'qty'             => $producedQty,
                'unit'            => $order->unit,
                'cost_rate'       => $costPerUnit,
                'cost_amount'     => $totalCost,
                'running_balance' => $runningBalance,
                'narration'       => "FG Receipt from Production {$order->slno}",
                'created_by'      => Auth::id(),
            ]);

            // Create costing record
            $plannedCost = $order->bom->sum('cost_amount') + $labourCost + $overheadCost;
            ProductionCostingNew::updateOrCreate(
                ['production_order_id' => $order->id],
                [
                    'fg_id'          => $order->fg_id,
                    'fg_name'        => $order->fg_name,
                    'batch_no'       => $order->batch_no,
                    'order_qty'      => $order->order_qty,
                    'produced_qty'   => $producedQty,
                    'rm_cost'        => $rmCost,
                    'pm_cost'        => $pmCost,
                    'labour_cost'    => $labourCost,
                    'overhead_cost'  => $overheadCost,
                    'total_cost'     => $totalCost,
                    'cost_per_unit'  => $costPerUnit,
                    'variance'       => $totalCost - $plannedCost,
                ]
            );

            // Production journal
            ProductionJournalNew::create([
                'production_order_id' => $order->id,
                'journal_date'        => $completeDate,
                'stage'               => 'fg_receipt',
                'qty'                 => $producedQty,
                'unit'                => $order->unit,
                'remarks'             => "FG Received. Total cost: ₹" . number_format($totalCost, 2),
                'done_by'             => Auth::id(),
            ]);

            // WIP tracking — close
            WipTracking::create([
                'production_order_id' => $order->id,
                'stage'               => 'fg_receipt',
                'started_at'          => $order->actual_start ? $order->actual_start->toDateTimeString() : now(),
                'completed_at'        => now(),
                'input_qty'           => $order->order_qty,
                'output_qty'          => $producedQty,
                'loss_qty'            => max(0, $order->order_qty - $producedQty),
                'operator_id'         => Auth::id(),
            ]);

            // Update order
            $order->update([
                'produced_qty' => $producedQty,
                'status'       => 'completed',
                'actual_end'   => $completeDate,
            ]);

            // Post daybook: FG-STOCK -total_cost (Debit), WIP +total_cost (Credit)
            if ($totalCost > 0) {
                $fgrSlno = $this->daybookService->nextSlno('FGR', 'stock_movements');
                $this->daybookService->insertFGReceiptDaybookEntries(
                    $fgrSlno, $totalCost, $completeDate, $order->branch_id,
                    "FG Receipt - Production {$order->slno}"
                );
            }
        });

        return redirect()->route('production-orders-new.show', $order)->with('success', 'Production completed. FG added to stock.');
    }

    /**
     * Issue form — confirmation page
     */
    public function issueForm($id)
    {
        $order = ProductionOrderNew::with('bom')->findOrFail($id);
        return view('manufacturing.production-orders.issue', compact('order'));
    }

    /**
     * Calculate cost for order
     */
    public function calculateCost($id)
    {
        $order = ProductionOrderNew::with('bom')->findOrFail($id);
        $rmCost = $order->bom->where('item_type', 'RM')->sum('cost_amount');
        $pmCost = $order->bom->where('item_type', 'PM')->sum('cost_amount');
        return response()->json([
            'rm_cost'    => $rmCost,
            'pm_cost'    => $pmCost,
            'total_cost' => $rmCost + $pmCost,
        ]);
    }
}
