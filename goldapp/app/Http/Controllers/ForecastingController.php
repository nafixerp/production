<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ForecastingController extends Controller {
    public function index(Request $request) {
        $months_back  = (int) $request->get('months_back', 6);
        $sku_filter   = $request->get('sku');
        $channel      = $request->get('channel');

        // Historical monthly sales data
        $historyQ = DB::table('sales_invoice_items')
            ->join('sales_invoices', 'sales_invoice_items.invoice_id', '=', 'sales_invoices.id')
            ->where('sales_invoices.status', '!=', 'cancelled')
            ->where('sales_invoices.invoice_date', '>=', now()->subMonths($months_back))
            ->selectRaw("fg_name, DATE_FORMAT(sales_invoices.invoice_date, '%Y-%m') as period,
                DATE_FORMAT(sales_invoices.invoice_date, '%b %Y') as label,
                SUM(qty) as qty_sold, SUM(net_amount) as revenue")
            ->when($sku_filter, fn($q) => $q->where('sales_invoice_items.fg_name', $sku_filter))
            ->when($channel, fn($q) => $q->where('sales_invoices.channel', $channel))
            ->groupBy('fg_id', 'fg_name', DB::raw("DATE_FORMAT(sales_invoices.invoice_date, '%Y-%m')"))
            ->orderBy('fg_name')
            ->orderBy('period')
            ->get();

        // Build per-SKU history and compute 3-month moving average forecast
        $skus = $historyQ->groupBy('fg_name');
        $forecasts = [];

        foreach ($skus as $sku_name => $months) {
            $months = $months->values();
            $history = $months->map(fn($m) => [
                'period'  => $m->period,
                'label'   => $m->label,
                'qty'     => (float) $m->qty_sold,
                'revenue' => (float) $m->revenue,
            ])->toArray();

            // Simple 3-month moving average
            $n = count($history);
            $window = min(3, $n);
            $last_qtys    = array_slice(array_column($history, 'qty'), -$window);
            $last_revs    = array_slice(array_column($history, 'revenue'), -$window);
            $forecast_qty = $window > 0 ? round(array_sum($last_qtys) / $window, 2) : 0;
            $forecast_rev = $window > 0 ? round(array_sum($last_revs) / $window, 2) : 0;

            // Generate next 3 months labels
            $next_months = [];
            for ($i = 1; $i <= 3; $i++) {
                $next_months[] = now()->addMonths($i)->format('M Y');
            }

            $forecasts[] = [
                'sku'          => $sku_name,
                'history'      => $history,
                'forecast_qty' => $forecast_qty,
                'forecast_rev' => $forecast_rev,
                'next_months'  => $next_months,
                'trend'        => $this->calculateTrend($history),
            ];
        }

        // Overall monthly totals for the trend chart
        $overall_monthly = DB::table('sales_invoices')
            ->where('status', '!=', 'cancelled')
            ->where('invoice_date', '>=', now()->subMonths($months_back))
            ->when($channel, fn($q) => $q->where('channel', $channel))
            ->selectRaw("DATE_FORMAT(invoice_date, '%Y-%m') as period, DATE_FORMAT(invoice_date, '%b %Y') as label, SUM(net_amount) as revenue, COUNT(*) as invoice_count")
            ->groupByRaw("DATE_FORMAT(invoice_date, '%Y-%m')")
            ->orderBy('period')
            ->get();

        $sku_list  = DB::table('sales_invoice_items')->distinct()->pluck('fg_name');
        $channels  = DB::table('sales_invoices')->whereNotNull('channel')->distinct()->pluck('channel');

        return view('reports.forecasting', compact(
            'forecasts', 'overall_monthly', 'sku_list', 'channels',
            'sku_filter', 'channel', 'months_back'
        ));
    }

    private function calculateTrend(array $history): string {
        $n = count($history);
        if ($n < 2) return 'stable';
        $first_half = array_slice($history, 0, intval($n / 2));
        $second_half = array_slice($history, intval($n / 2));
        $avg_first  = array_sum(array_column($first_half,  'revenue')) / max(count($first_half),  1);
        $avg_second = array_sum(array_column($second_half, 'revenue')) / max(count($second_half), 1);
        if ($avg_second > $avg_first * 1.05) return 'up';
        if ($avg_second < $avg_first * 0.95) return 'down';
        return 'stable';
    }
}
