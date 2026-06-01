<?php
namespace App\Http\Controllers;

use App\Models\InventoryStock;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NewInventoryStockController extends Controller
{
    public function index(Request $request)
    {
        $itemType   = $request->item_type;
        $warehouseId= $request->warehouse_id;
        $search     = $request->q;
        $expiring   = $request->expiring; // days
        $today      = now()->format('Y-m-d');

        $stocks = InventoryStock::with('warehouse')
            ->when($itemType, fn($q) => $q->where('item_type', $itemType))
            ->when($warehouseId, fn($q) => $q->where('warehouse_id', $warehouseId))
            ->when($search, fn($q) => $q->where('item_name', 'like', "%$search%")->orWhere('item_code', 'like', "%$search%"))
            ->when($expiring, fn($q) => $q->whereNotNull('expiry_date')->where('expiry_date', '<=', now()->addDays((int)$expiring)->format('Y-m-d')))
            ->selectRaw('*, (qty_in - qty_out) as balance_qty')
            ->having(DB::raw('qty_in - qty_out'), '>', 0)
            ->orderBy('item_type')->orderBy('item_name')
            ->paginate(30)->withQueryString();

        $warehouses = Warehouse::orderBy('name')->get();

        // Summary by item_type
        $summary = InventoryStock::selectRaw('item_type, COUNT(*) as items, SUM(qty_in - qty_out) as total_qty, SUM((qty_in - qty_out) * cost_rate) as total_value')
            ->groupBy('item_type')->get();

        // Expiry alerts (within 30 days)
        $expiryAlerts = InventoryStock::whereNotNull('expiry_date')
            ->where('expiry_date', '<=', now()->addDays(30)->format('Y-m-d'))
            ->where('expiry_date', '>=', $today)
            ->selectRaw('*, (qty_in - qty_out) as balance_qty')
            ->having(DB::raw('qty_in - qty_out'), '>', 0)
            ->count();

        return view('manufacturing.inventory-stock.index', compact(
            'stocks', 'warehouses', 'itemType', 'warehouseId', 'search', 'expiring', 'summary', 'expiryAlerts'
        ));
    }
}
