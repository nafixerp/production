<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryReportController extends Controller {
    public function index(Request $request) {
        $as_of    = $request->get('as_of', date('Y-m-d'));
        $category = $request->get('category');

        // Stock valuation: current balance × cost rate
        $stock_valuation = DB::table('inventory_stock')
            ->selectRaw('item_type, item_code, item_name, SUM(qty_in - qty_out) as balance, AVG(cost_rate) as avg_cost, SUM((qty_in - qty_out) * cost_rate) as value')
            ->when($category, fn($q) => $q->where('item_type', $category))
            ->groupBy('item_type', 'item_id', 'item_code', 'item_name')
            ->havingRaw('SUM(qty_in - qty_out) > 0')
            ->orderByDesc('value')
            ->get();

        $total_value = $stock_valuation->sum('value');

        // Slow-moving items (no movement in last 30 days)
        $slow_moving = DB::table('inventory_stock as a')
            ->selectRaw('a.item_type, a.item_code, a.item_name, SUM(a.qty_in - a.qty_out) as balance, MAX(a.tdate) as last_movement_date, DATEDIFF(NOW(), MAX(a.tdate)) as days_idle')
            ->groupBy('a.item_type', 'a.item_id', 'a.item_code', 'a.item_name')
            ->havingRaw('SUM(a.qty_in - a.qty_out) > 0 AND DATEDIFF(NOW(), MAX(a.tdate)) > 30')
            ->orderByDesc('days_idle')
            ->limit(20)
            ->get();

        // Expiry alerts: items expiring in next 30 days
        $expiry_alerts = DB::table('inventory_stock')
            ->selectRaw('item_type, item_code, item_name, batch_no, lot_no, SUM(qty_in - qty_out) as balance, MIN(expiry_date) as expiry_date, DATEDIFF(MIN(expiry_date), NOW()) as days_to_expiry')
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', now()->addDays(30))
            ->groupBy('item_type', 'item_id', 'item_code', 'item_name', 'batch_no', 'lot_no', 'expiry_date')
            ->havingRaw('SUM(qty_in - qty_out) > 0')
            ->orderBy('expiry_date')
            ->get();

        // Inventory turnover ratio (COGS / Average Inventory)
        $turnover = DB::table('inventory_stock')
            ->selectRaw('item_type, item_code, item_name,
                SUM(qty_out * cost_rate) as cogs_value,
                AVG(qty_in - qty_out) as avg_stock,
                CASE WHEN AVG(qty_in - qty_out) > 0 THEN ROUND(SUM(qty_out * cost_rate) / AVG(qty_in - qty_out), 2) ELSE 0 END as turnover_ratio')
            ->groupBy('item_type', 'item_id', 'item_code', 'item_name')
            ->orderByDesc('turnover_ratio')
            ->limit(20)
            ->get();

        $categories = DB::table('inventory_stock')->distinct()->pluck('item_type');

        return view('reports.inventory-report', compact(
            'stock_valuation', 'total_value', 'slow_moving', 'expiry_alerts',
            'turnover', 'as_of', 'category', 'categories'
        ));
    }
}
