<?php
namespace App\Http\Controllers;

use App\Models\FgStock;
use App\Models\FinishedGoods;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class FGStockController extends Controller
{
    public function index(Request $request)
    {
        $warehouseId   = $request->warehouse_id;
        $expiryStatus  = $request->expiry_status; // expired | expiring_soon | ok
        $fgSearch      = $request->fg;

        $query = FgStock::with(['finishedGood', 'warehouse'])
            ->when($warehouseId, fn($q) => $q->where('warehouse_id', $warehouseId))
            ->when($fgSearch, fn($q) => $q->where('fg_name', 'like', "%$fgSearch%")
                ->orWhere('fg_code', 'like', "%$fgSearch%"))
            ->when($expiryStatus === 'expired', fn($q) => $q->where('expiry_date', '<', now()))
            ->when($expiryStatus === 'expiring_soon', fn($q) => $q->whereBetween('expiry_date', [now(), now()->addDays(30)]))
            ->when($expiryStatus === 'ok', fn($q) => $q->where(fn($q2) =>
                $q2->whereNull('expiry_date')->orWhere('expiry_date', '>', now()->addDays(30))))
            ->orderBy('fg_name')
            ->orderBy('expiry_date');

        $stocks = $query->get();

        // FIFO valuation: group by fg_id, sum available * cost_rate
        $fgValuation = $stocks->groupBy('fg_id')->map(function ($rows) {
            $totalQty   = 0;
            $totalValue = 0;
            foreach ($rows->sortBy('expiry_date') as $row) {
                $avail = max(0, $row->qty_in - $row->qty_out - $row->qty_reserved);
                $totalQty   += $avail;
                $totalValue += $avail * $row->cost_rate;
            }
            return ['qty' => $totalQty, 'value' => $totalValue, 'fifo_rate' => $totalQty > 0 ? $totalValue / $totalQty : 0];
        });

        $warehouses = Warehouse::where('status', 1)->orderBy('name')->get();

        return view('warehouse.fg-stock.index', compact('stocks', 'warehouses', 'fgValuation',
            'warehouseId', 'expiryStatus', 'fgSearch'));
    }
}
