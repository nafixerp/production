<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PLReportController extends Controller {
    public function index(Request $request) {
        $from    = $request->get('from', date('Y-m-01'));
        $to      = $request->get('to', date('Y-m-d'));
        $channel = $request->get('channel');

        // Revenue
        $salesQ = DB::table('sales_invoices')->whereBetween('invoice_date', [$from, $to])->where('status', '!=', 'cancelled');
        if ($channel) $salesQ->where('channel', $channel);
        $revenue     = (clone $salesQ)->sum('taxable_amount') ?? 0;
        $discount    = (clone $salesQ)->sum('discount') ?? 0;
        $net_revenue = (clone $salesQ)->sum('net_amount') ?? 0;

        // COGS from daybook
        $cogs = DB::table('daybook')
            ->join('chart_of_accounts', 'daybook.account_id', '=', 'chart_of_accounts.id')
            ->where('chart_of_accounts.code', 'COGS')
            ->whereBetween('daybook.tdate', [$from, $to])
            ->sum(DB::raw('ABS(daybook.amount)')) ?? 0;

        // Expenses breakdown from daybook
        $expenses = DB::table('daybook')
            ->join('chart_of_accounts', 'daybook.account_id', '=', 'chart_of_accounts.id')
            ->where('chart_of_accounts.account_group', 'expense')
            ->where('chart_of_accounts.code', '!=', 'COGS')
            ->whereBetween('daybook.tdate', [$from, $to])
            ->groupBy('chart_of_accounts.code', 'chart_of_accounts.name')
            ->selectRaw('chart_of_accounts.name, SUM(ABS(daybook.amount)) as amount')
            ->get();

        // SKU-level margins
        $sku_margins = DB::table('sales_invoice_items')
            ->join('sales_invoices', 'sales_invoice_items.invoice_id', '=', 'sales_invoices.id')
            ->whereBetween('sales_invoices.invoice_date', [$from, $to])
            ->where('sales_invoices.status', '!=', 'cancelled')
            ->selectRaw('fg_name, SUM(qty) as qty_sold, SUM(net_amount) as revenue, SUM(cost_rate * qty) as cost, SUM(net_amount - cost_rate * qty) as gross_profit')
            ->groupBy('fg_id', 'fg_code', 'fg_name')
            ->orderByDesc('gross_profit')
            ->get();

        $gross_profit  = $net_revenue - $cogs;
        $total_expense = $expenses->sum('amount');
        $net_profit    = $gross_profit - $total_expense;

        // Channels list for filter dropdown
        $channels = DB::table('sales_invoices')->whereNotNull('channel')->distinct()->pluck('channel');

        return view('reports.profit-loss', compact(
            'revenue', 'discount', 'net_revenue', 'cogs', 'gross_profit',
            'expenses', 'total_expense', 'net_profit', 'sku_margins',
            'from', 'to', 'channel', 'channels'
        ));
    }
}
