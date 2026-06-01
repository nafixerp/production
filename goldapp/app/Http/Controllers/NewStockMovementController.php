<?php
namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class NewStockMovementController extends Controller
{
    public function index(Request $request)
    {
        $itemType    = $request->item_type;
        $itemId      = $request->item_id;
        $warehouseId = $request->warehouse_id;
        $movType     = $request->movement_type;
        $from        = $request->from ?? now()->startOfMonth()->format('Y-m-d');
        $to          = $request->to   ?? now()->format('Y-m-d');
        $search      = $request->q;

        $movements = StockMovement::with('warehouse')
            ->when($itemType, fn($q) => $q->where('item_type', $itemType))
            ->when($itemId, fn($q) => $q->where('item_id', $itemId))
            ->when($warehouseId, fn($q) => $q->where('warehouse_id', $warehouseId))
            ->when($movType, fn($q) => $q->where('movement_type', $movType))
            ->when($search, fn($q) => $q->where('item_name', 'like', "%$search%")->orWhere('item_code', 'like', "%$search%"))
            ->whereBetween('movement_date', [$from, $to])
            ->orderByDesc('movement_date')->orderByDesc('id')
            ->paginate(30)->withQueryString();

        $warehouses = Warehouse::orderBy('name')->get();

        $totals = [
            'in_qty'  => $movements->where('movement_type', 'IN')->sum('qty'),
            'out_qty' => $movements->where('movement_type', 'OUT')->sum('qty'),
            'in_val'  => $movements->where('movement_type', 'IN')->sum('cost_amount'),
            'out_val' => $movements->where('movement_type', 'OUT')->sum('cost_amount'),
        ];

        return view('manufacturing.stock-movements.index', compact(
            'movements', 'warehouses', 'itemType', 'warehouseId', 'movType', 'from', 'to', 'search', 'totals'
        ));
    }
}
