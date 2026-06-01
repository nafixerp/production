<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DashboardController extends Controller {
    public function index() {
        $today       = date('Y-m-d');
        $month_start = date('Y-m-01');

        // KPI Cards
        $data['today_sales']     = DB::table('sales_invoices')->whereDate('invoice_date', $today)->where('status', '!=', 'cancelled')->sum('net_amount') ?? 0;
        $data['today_purchases'] = DB::table('purchase_invoices')->whereDate('invoice_date', $today)->where('status', '!=', 'cancelled')->sum('net_amount') ?? 0;
        $data['month_sales']     = DB::table('sales_invoices')->where('invoice_date', '>=', $month_start)->where('status', '!=', 'cancelled')->sum('net_amount') ?? 0;
        $data['month_purchases'] = DB::table('purchase_invoices')->where('invoice_date', '>=', $month_start)->where('status', '!=', 'cancelled')->sum('net_amount') ?? 0;
        $data['open_orders']     = DB::table('sales_orders')->whereIn('status', ['open', 'confirmed', 'partially_dispatched'])->count();
        $data['pending_po']      = DB::table('purchase_orders')->whereIn('status', ['approved', 'partially_received'])->count();
        $data['overdue_ar']      = DB::table('ar_ledger')->where('tdate', '<', now()->subDays(30))->where('credit', '>', 0)->sum('credit') ?? 0;
        $data['cash_balance']    = DB::table('daybook')
            ->join('chart_of_accounts', 'daybook.account_id', '=', 'chart_of_accounts.id')
            ->where('chart_of_accounts.account_type', 'CASH')
            ->sum('daybook.amount') ?? 0;

        // Chart: 6-month monthly sales trend
        $data['monthly_sales_chart'] = DB::table('sales_invoices')
            ->selectRaw("DATE_FORMAT(invoice_date,'%b %Y') as month, SUM(net_amount) as total")
            ->where('invoice_date', '>=', now()->subMonths(6))
            ->where('status', '!=', 'cancelled')
            ->groupByRaw("DATE_FORMAT(invoice_date,'%Y-%m')")
            ->orderByRaw("DATE_FORMAT(invoice_date,'%Y-%m')")
            ->get();

        // Chart: Top 5 products this month
        $data['top_products'] = DB::table('sales_invoice_items')
            ->join('sales_invoices', 'sales_invoice_items.invoice_id', '=', 'sales_invoices.id')
            ->selectRaw('fg_name, SUM(net_amount) as total')
            ->where('sales_invoices.status', '!=', 'cancelled')
            ->whereMonth('sales_invoices.invoice_date', date('m'))
            ->groupBy('fg_name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Chart: Sales channel mix
        $data['channel_mix'] = DB::table('sales_invoices')
            ->selectRaw('channel, SUM(net_amount) as total')
            ->where('status', '!=', 'cancelled')
            ->whereMonth('invoice_date', date('m'))
            ->groupBy('channel')
            ->get();

        // Recent invoices
        $data['recent_invoices'] = DB::table('sales_invoices')->latest()->limit(8)->get();

        // Low stock alerts
        $data['low_stock'] = DB::table('inventory_stock')
            ->selectRaw('item_type, item_name, SUM(qty_in - qty_out) as balance, MIN(expiry_date) as next_expiry')
            ->groupBy('item_type', 'item_id', 'item_code', 'item_name')
            ->havingRaw('SUM(qty_in - qty_out) < 100')
            ->limit(5)
            ->get();

        return view('dashboard.index', $data);
    }
}
