<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesAnalyticsController extends Controller {
    public function index(Request $request) {
        $from    = $request->get('from', date('Y-m-01'));
        $to      = $request->get('to', date('Y-m-d'));
        $channel = $request->get('channel');
        $group   = $request->get('group', 'channel'); // channel | customer | product | period

        $baseQ = DB::table('sales_invoices')
            ->whereBetween('invoice_date', [$from, $to])
            ->where('status', '!=', 'cancelled');
        if ($channel) $baseQ->where('channel', $channel);

        // By channel
        $by_channel = (clone $baseQ)
            ->selectRaw('channel, COUNT(*) as invoice_count, SUM(net_amount) as revenue')
            ->groupBy('channel')
            ->orderByDesc('revenue')
            ->get();

        // By customer
        $by_customer = (clone $baseQ)
            ->selectRaw('customer_name, COUNT(*) as invoice_count, SUM(net_amount) as revenue, AVG(net_amount) as avg_order')
            ->groupBy('customer_id', 'customer_name')
            ->orderByDesc('revenue')
            ->limit(20)
            ->get();

        // By product (from items table)
        $by_product = DB::table('sales_invoice_items')
            ->join('sales_invoices', 'sales_invoice_items.invoice_id', '=', 'sales_invoices.id')
            ->whereBetween('sales_invoices.invoice_date', [$from, $to])
            ->where('sales_invoices.status', '!=', 'cancelled')
            ->when($channel, fn($q) => $q->where('sales_invoices.channel', $channel))
            ->selectRaw('fg_name, SUM(qty) as qty_sold, SUM(net_amount) as revenue')
            ->groupBy('fg_id', 'fg_code', 'fg_name')
            ->orderByDesc('revenue')
            ->limit(20)
            ->get();

        // By period (monthly trend)
        $by_period = (clone $baseQ)
            ->selectRaw("DATE_FORMAT(invoice_date, '%Y-%m') as period, DATE_FORMAT(invoice_date, '%b %Y') as label, COUNT(*) as invoice_count, SUM(net_amount) as revenue")
            ->groupByRaw("DATE_FORMAT(invoice_date, '%Y-%m')")
            ->orderBy('period')
            ->get();

        // Summary totals
        $summary = [
            'total_revenue'  => (clone $baseQ)->sum('net_amount') ?? 0,
            'total_invoices' => (clone $baseQ)->count(),
            'total_discount' => (clone $baseQ)->sum('discount') ?? 0,
            'avg_order'      => (clone $baseQ)->avg('net_amount') ?? 0,
        ];

        $channels = DB::table('sales_invoices')->whereNotNull('channel')->distinct()->pluck('channel');

        return view('reports.sales-analytics', compact(
            'by_channel', 'by_customer', 'by_product', 'by_period',
            'summary', 'from', 'to', 'channel', 'channels', 'group'
        ));
    }
}
