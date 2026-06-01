<?php
namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\BatchTraceability;
use App\Models\Daybook;
use App\Models\DaybookPart;
use App\Models\DispatchOrder;
use App\Models\DispatchOrderItem;
use App\Models\FgStock;
use App\Models\FinishedGoods;
use App\Models\SalesOrder;
use App\Models\Warehouse;
use App\Services\DaybookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DispatchOrderController extends Controller
{
    public function __construct(protected DaybookService $daybookService) {}

    private function nextSlno(): string
    {
        $last = DispatchOrder::orderByDesc('id')->value('slno');
        $num  = $last ? (int) substr($last, 2) + 1 : 1;
        return 'DO' . str_pad($num, 6, '0', STR_PAD_LEFT);
    }

    public function index(Request $request)
    {
        $orders = DispatchOrder::with('items')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->customer, fn($q) => $q->where('customer_name', 'like', "%{$request->customer}%"))
            ->orderByDesc('do_date')
            ->paginate(20)->withQueryString();

        return view('warehouse.dispatch-orders.index', compact('orders'));
    }

    public function create()
    {
        $customers   = Account::where('atype', 'CUSTOMER')->orderBy('name')->get();
        $salesOrders = SalesOrder::whereIn('status', ['confirmed','partially_dispatched'])->orderByDesc('id')->get();
        $warehouses  = Warehouse::where('status', 1)->orderBy('name')->get();
        $fgList      = FinishedGoods::where('status', 1)->orderBy('name')->get();
        $slno        = $this->nextSlno();
        return view('warehouse.dispatch-orders.form', compact('customers','salesOrders','warehouses','fgList','slno'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'do_date'       => 'required|date',
            'customer_name' => 'required|string',
            'items'         => 'required|array|min:1',
        ]);

        DB::transaction(function () use ($request) {
            $slno = $this->nextSlno();
            $do = DispatchOrder::create([
                'slno'            => $slno,
                'do_no'           => $slno,
                'do_date'         => $request->do_date,
                'sales_order_id'  => $request->sales_order_id ?: null,
                'customer_id'     => $request->customer_id ?: null,
                'customer_name'   => $request->customer_name,
                'delivery_address'=> $request->delivery_address,
                'vehicle_id'      => $request->vehicle_id ?: null,
                'driver_name'     => $request->driver_name,
                'driver_phone'    => $request->driver_phone,
                'route_id'        => $request->route_id ?: null,
                'dispatch_date'   => $request->dispatch_date ?: null,
                'status'          => 'draft',
                'narration'       => $request->narration,
                'branch_id'       => Auth::user()->branch_id ?? null,
                'created_by'      => Auth::id(),
            ]);

            foreach ($request->items as $item) {
                if (empty($item['fg_id'])) continue;
                $fg = FinishedGoods::find($item['fg_id']);
                $qty = (float)($item['qty'] ?? 0);
                $costRate = (float)($item['cost_rate'] ?? $fg?->cost_price ?? 0);
                $sellRate = (float)($item['selling_rate'] ?? $fg?->selling_price ?? 0);
                DispatchOrderItem::create([
                    'do_id'          => $do->id,
                    'fg_id'          => $item['fg_id'],
                    'fg_code'        => $fg?->code,
                    'fg_name'        => $fg?->name,
                    'batch_no'       => $item['batch_no'] ?? '',
                    'warehouse_id'   => $item['warehouse_id'] ?? null,
                    'qty'            => $qty,
                    'unit'           => $item['unit'] ?? $fg?->unit?->name ?? '',
                    'cost_rate'      => $costRate,
                    'cost_amount'    => round($qty * $costRate, 2),
                    'selling_rate'   => $sellRate,
                    'selling_amount' => round($qty * $sellRate, 2),
                ]);
            }
        });

        return redirect()->route('dispatch-orders.index')->with('success', 'Dispatch Order created.');
    }

    public function show(DispatchOrder $dispatchOrder)
    {
        $dispatchOrder->load('items');
        $daybookEntries = Daybook::where('slno', $dispatchOrder->slno)->get();
        return view('warehouse.dispatch-orders.show', compact('dispatchOrder', 'daybookEntries'));
    }

    public function edit(DispatchOrder $dispatchOrder)
    {
        if (!in_array($dispatchOrder->status, ['draft', 'packed'])) {
            return redirect()->route('dispatch-orders.index')->with('error', 'Cannot edit dispatched orders.');
        }
        $dispatchOrder->load('items');
        $customers   = Account::where('atype', 'CUSTOMER')->orderBy('name')->get();
        $salesOrders = SalesOrder::whereIn('status', ['confirmed','partially_dispatched'])->orderByDesc('id')->get();
        $warehouses  = Warehouse::where('status', 1)->orderBy('name')->get();
        $fgList      = FinishedGoods::where('status', 1)->orderBy('name')->get();
        return view('warehouse.dispatch-orders.form', compact('dispatchOrder','customers','salesOrders','warehouses','fgList'));
    }

    public function update(Request $request, DispatchOrder $dispatchOrder)
    {
        $request->validate(['do_date' => 'required|date']);

        DB::transaction(function () use ($request, $dispatchOrder) {
            $dispatchOrder->update($request->only([
                'do_date','customer_id','customer_name','delivery_address',
                'vehicle_id','driver_name','driver_phone','route_id','dispatch_date','narration'
            ]));

            $dispatchOrder->items()->delete();
            foreach ($request->items ?? [] as $item) {
                if (empty($item['fg_id'])) continue;
                $fg = FinishedGoods::find($item['fg_id']);
                $qty = (float)($item['qty'] ?? 0);
                $costRate = (float)($item['cost_rate'] ?? $fg?->cost_price ?? 0);
                $sellRate = (float)($item['selling_rate'] ?? $fg?->selling_price ?? 0);
                DispatchOrderItem::create([
                    'do_id'          => $dispatchOrder->id,
                    'fg_id'          => $item['fg_id'],
                    'fg_code'        => $fg?->code,
                    'fg_name'        => $fg?->name,
                    'batch_no'       => $item['batch_no'] ?? '',
                    'warehouse_id'   => $item['warehouse_id'] ?? null,
                    'qty'            => $qty,
                    'unit'           => $item['unit'] ?? '',
                    'cost_rate'      => $costRate,
                    'cost_amount'    => round($qty * $costRate, 2),
                    'selling_rate'   => $sellRate,
                    'selling_amount' => round($qty * $sellRate, 2),
                ]);
            }
        });

        return redirect()->route('dispatch-orders.show', $dispatchOrder)->with('success', 'Updated.');
    }

    public function destroy(DispatchOrder $dispatchOrder)
    {
        $dispatchOrder->items()->delete();
        $dispatchOrder->delete();
        return redirect()->route('dispatch-orders.index')->with('success', 'Deleted.');
    }

    /**
     * POST /dispatch-orders/{id}/dispatch
     * Mark dispatched, reduce fg_stock qty_out, update batch_traceability,
     * post COGS double-entry daybook entries.
     */
    public function dispatch(int $id)
    {
        $do = DispatchOrder::with('items')->findOrFail($id);

        if ($do->status !== 'draft' && $do->status !== 'packed') {
            return redirect()->back()->with('error', 'Order already dispatched.');
        }

        DB::transaction(function () use ($do) {
            $slno   = $do->slno;
            $tdate  = $do->dispatch_date ?? $do->do_date;
            $branch = $do->branch_id;

            // Get COGS and FG-STOCK accounts
            $cogsAcc    = Account::where('code', 'COGS')->first();
            $fgStockAcc = Account::where('code', 'FG-STOCK')->first();

            $totalCost = 0;

            foreach ($do->items as $item) {
                $qty  = (float)$item->qty;
                $cost = (float)$item->cost_amount;
                $totalCost += $cost;

                // Reduce fg_stock qty_out (FIFO: oldest batch first)
                if ($item->batch_no) {
                    FgStock::where('fg_id', $item->fg_id)
                        ->where('batch_no', $item->batch_no)
                        ->where('status', 'available')
                        ->increment('qty_out', $qty);
                }

                // Update batch_traceability
                BatchTraceability::where('batch_no', $item->batch_no)
                    ->where('fg_id', $item->fg_id)
                    ->increment('qty_dispatched', $qty);
                BatchTraceability::where('batch_no', $item->batch_no)
                    ->where('fg_id', $item->fg_id)
                    ->decrement('qty_available', $qty);
            }

            // Post COGS double-entry
            if ($totalCost > 0 && $cogsAcc && $fgStockAcc) {
                // Clear any previous entries for this DO slno
                Daybook::where('slno', $slno)->where('vtype', 'DO')->delete();

                // COGS Debit (negative = debit)
                Daybook::create([
                    'slno'         => $slno,
                    'account_id'   => $cogsAcc->id,
                    'account_code' => $cogsAcc->code,
                    'account_name' => $cogsAcc->name,
                    'amount'       => -$totalCost,
                    'particular'   => "COGS – Dispatch {$do->do_no} to {$do->customer_name}",
                    'tdate'        => $tdate,
                    'vtype'        => 'DO',
                    'branch_id'    => $branch,
                ]);

                // FG-STOCK Credit (positive = credit, stock reduces = credit FG asset)
                Daybook::create([
                    'slno'         => $slno,
                    'account_id'   => $fgStockAcc->id,
                    'account_code' => $fgStockAcc->code,
                    'account_name' => $fgStockAcc->name,
                    'amount'       => $totalCost,
                    'particular'   => "FG Stock out – Dispatch {$do->do_no}",
                    'tdate'        => $tdate,
                    'vtype'        => 'DO',
                    'branch_id'    => $branch,
                ]);

                DaybookPart::firstOrCreate(['slno' => $slno], [
                    'slno'       => $slno,
                    'vchno'      => $do->do_no,
                    'particular' => "Dispatch {$do->do_no}",
                    'tdate'      => $tdate,
                    'vtype'      => 'DO',
                    'narration'  => $do->narration,
                    'branch_id'  => $branch,
                    'created_by' => Auth::id(),
                ]);
            }

            $do->update([
                'status'        => 'dispatched',
                'dispatch_date' => $tdate,
            ]);
        });

        return redirect()->route('dispatch-orders.show', $do)->with('success', 'Order dispatched & COGS posted.');
    }

    /**
     * POST /dispatch-orders/{id}/deliver
     * Mark delivered, update customer ledger.
     */
    public function deliver(int $id)
    {
        $do = DispatchOrder::findOrFail($id);
        $do->update(['status' => 'delivered']);
        return redirect()->route('dispatch-orders.show', $do)->with('success', 'Marked as delivered.');
    }
}
