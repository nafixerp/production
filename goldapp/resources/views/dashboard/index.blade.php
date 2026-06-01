@extends('layouts.app')
@section('title','Dashboard')
@section('page-title','ERP Dashboard')

@push('styles')
<style>
.kpi-card { background: var(--bg-card); border: 1px solid var(--border-gold); border-radius: 10px; padding: 20px 22px; position: relative; overflow: hidden; transition: transform .2s; }
.kpi-card:hover { transform: translateY(-2px); }
.kpi-card .kpi-label { color: var(--text-muted-gold); font-size: .72rem; letter-spacing: 1.5px; text-transform: uppercase; margin-bottom: 6px; }
.kpi-card .kpi-value { color: var(--gold); font-size: 1.6rem; font-weight: 700; line-height: 1; }
.kpi-card .kpi-sub { color: #a09878; font-size: .75rem; margin-top: 4px; }
.kpi-card .kpi-icon { position: absolute; right: 18px; top: 50%; transform: translateY(-50%); font-size: 2.4rem; color: rgba(212,175,55,.12); }
.chart-card { background: var(--bg-card); border: 1px solid var(--border-gold); border-radius: 10px; padding: 18px; }
.chart-card .chart-title { color: var(--gold); font-size: .8rem; font-weight: 600; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 14px; border-bottom: 1px solid var(--border-gold); padding-bottom: 10px; }
.tbl-dark-gold { color: #d0c8a8; font-size: .8rem; }
.tbl-dark-gold thead th { color: var(--text-muted-gold); font-size: .68rem; letter-spacing: 1.5px; text-transform: uppercase; border-color: var(--border-gold); background: rgba(212,175,55,.04); padding: 8px 12px; }
.tbl-dark-gold tbody td { border-color: rgba(212,175,55,.08); padding: 8px 12px; vertical-align: middle; }
.tbl-dark-gold tbody tr:hover td { background: rgba(212,175,55,.04); }
.badge-status { font-size: .65rem; padding: 3px 8px; border-radius: 4px; }
.stock-low { color: #ff6b6b; }
.text-profit { color: #51cf66; }
.text-loss { color: #ff6b6b; }
</style>
@endpush

@section('content')
{{-- KPI Cards Row 1 --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="kpi-card">
            <div class="kpi-label">Today's Sales</div>
            <div class="kpi-value">₹{{ number_format($today_sales, 0) }}</div>
            <div class="kpi-sub">Net revenue today</div>
            <div class="kpi-icon"><i class="bi bi-bag-check-fill"></i></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="kpi-card">
            <div class="kpi-label">Month Sales</div>
            <div class="kpi-value">₹{{ number_format($month_sales, 0) }}</div>
            <div class="kpi-sub">{{ date('F Y') }}</div>
            <div class="kpi-icon"><i class="bi bi-graph-up-arrow"></i></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="kpi-card">
            <div class="kpi-label">Open Orders</div>
            <div class="kpi-value">{{ number_format($open_orders) }}</div>
            <div class="kpi-sub">{{ $pending_po }} POs pending</div>
            <div class="kpi-icon"><i class="bi bi-clipboard2-data-fill"></i></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="kpi-card">
            <div class="kpi-label">Cash Balance</div>
            <div class="kpi-value {{ $cash_balance >= 0 ? 'text-profit' : 'text-loss' }}">₹{{ number_format($cash_balance, 0) }}</div>
            <div class="kpi-sub">AR Overdue: ₹{{ number_format($overdue_ar, 0) }}</div>
            <div class="kpi-icon"><i class="bi bi-cash-coin"></i></div>
        </div>
    </div>
</div>

{{-- KPI Cards Row 2 --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="kpi-card">
            <div class="kpi-label">Today Purchases</div>
            <div class="kpi-value">₹{{ number_format($today_purchases, 0) }}</div>
            <div class="kpi-sub">Net purchase today</div>
            <div class="kpi-icon"><i class="bi bi-cart-fill"></i></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="kpi-card">
            <div class="kpi-label">Month Purchases</div>
            <div class="kpi-value">₹{{ number_format($month_purchases, 0) }}</div>
            <div class="kpi-sub">{{ date('F Y') }}</div>
            <div class="kpi-icon"><i class="bi bi-truck"></i></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="kpi-card">
            <div class="kpi-label">Gross Margin</div>
            @php $margin = $month_sales > 0 ? (($month_sales - $month_purchases) / $month_sales) * 100 : 0; @endphp
            <div class="kpi-value {{ $margin >= 0 ? 'text-profit' : 'text-loss' }}">{{ number_format($margin, 1) }}%</div>
            <div class="kpi-sub">Sales − Purchases / Sales</div>
            <div class="kpi-icon"><i class="bi bi-percent"></i></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="kpi-card">
            <div class="kpi-label">AR Overdue</div>
            <div class="kpi-value text-loss">₹{{ number_format($overdue_ar, 0) }}</div>
            <div class="kpi-sub">Outstanding &gt;30 days</div>
            <div class="kpi-icon"><i class="bi bi-exclamation-triangle-fill"></i></div>
        </div>
    </div>
</div>

{{-- Charts Row --}}
<div class="row g-3 mb-4">
    <div class="col-12 col-md-7">
        <div class="chart-card">
            <div class="chart-title"><i class="bi bi-bar-chart-line me-2"></i>6-Month Sales Trend</div>
            <canvas id="salesTrendChart" height="220"></canvas>
        </div>
    </div>
    <div class="col-12 col-md-5">
        <div class="chart-card">
            <div class="chart-title"><i class="bi bi-pie-chart me-2"></i>Sales Channel Mix (This Month)</div>
            <canvas id="channelMixChart" height="220"></canvas>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-12 col-md-6">
        <div class="chart-card">
            <div class="chart-title"><i class="bi bi-trophy me-2"></i>Top 5 Products This Month</div>
            <canvas id="topProductsChart" height="240"></canvas>
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="chart-card">
            <div class="chart-title"><i class="bi bi-exclamation-diamond me-2"></i>Low Stock Alerts</div>
            <div class="table-responsive">
                <table class="table tbl-dark-gold mb-0">
                    <thead><tr><th>Item</th><th>Type</th><th class="text-end">Balance</th><th>Next Expiry</th></tr></thead>
                    <tbody>
                        @forelse($low_stock as $item)
                        <tr>
                            <td>{{ $item->item_name }}</td>
                            <td><span class="badge" style="background:rgba(212,175,55,.15);color:var(--gold)">{{ $item->item_type }}</span></td>
                            <td class="text-end stock-low fw-bold">{{ number_format($item->balance ?? 0, 2) }}</td>
                            <td>{{ isset($item->next_expiry) ? date('d M Y', strtotime($item->next_expiry)) : '—' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">No low stock alerts</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Recent Invoices Table --}}
<div class="row g-3">
    <div class="col-12">
        <div class="chart-card">
            <div class="chart-title d-flex justify-content-between align-items-center">
                <span><i class="bi bi-receipt me-2"></i>Recent Sales Invoices</span>
                <a href="#" class="btn btn-sm" style="background:rgba(212,175,55,.15);color:var(--gold);border:1px solid var(--border-gold);font-size:.72rem">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table tbl-dark-gold mb-0">
                    <thead>
                        <tr><th>Invoice No</th><th>Date</th><th>Customer</th><th>Channel</th><th class="text-end">Amount</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        @forelse($recent_invoices as $inv)
                        <tr>
                            <td><strong>{{ $inv->invoice_no ?? '—' }}</strong></td>
                            <td>{{ isset($inv->invoice_date) ? date('d M Y', strtotime($inv->invoice_date)) : '—' }}</td>
                            <td>{{ $inv->customer_name ?? '—' }}</td>
                            <td>{{ $inv->channel ?? '—' }}</td>
                            <td class="text-end">₹{{ number_format($inv->net_amount ?? 0, 2) }}</td>
                            <td>
                                @php $st = strtolower($inv->status ?? ''); @endphp
                                <span class="badge-status badge {{ $st === 'paid' ? 'bg-success' : ($st === 'cancelled' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                    {{ ucfirst($inv->status ?? 'draft') }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-3">No invoices found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
Chart.defaults.color = '#a09878';
Chart.defaults.borderColor = 'rgba(212,175,55,0.1)';
const gold = '#d4af37';
const goldFill = 'rgba(212,175,55,0.1)';

// 6-Month Sales Trend Line Chart
const salesTrendData = @json($monthly_sales_chart);
new Chart(document.getElementById('salesTrendChart'), {
    type: 'line',
    data: {
        labels: salesTrendData.map(r => r.month),
        datasets: [{
            label: 'Net Sales (₹)',
            data: salesTrendData.map(r => parseFloat(r.total) || 0),
            borderColor: gold,
            backgroundColor: goldFill,
            borderWidth: 2.5,
            pointBackgroundColor: gold,
            pointRadius: 4,
            fill: true,
            tension: 0.35
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { ticks: { callback: v => '₹' + (v >= 1000 ? (v/1000).toFixed(0)+'K' : v) }, grid: { color: 'rgba(212,175,55,0.06)' } },
            x: { grid: { color: 'rgba(212,175,55,0.06)' } }
        }
    }
});

// Top Products Bar Chart
const topProductsData = @json($top_products);
new Chart(document.getElementById('topProductsChart'), {
    type: 'bar',
    data: {
        labels: topProductsData.map(r => r.fg_name || 'Unknown'),
        datasets: [{
            label: 'Revenue (₹)',
            data: topProductsData.map(r => parseFloat(r.total) || 0),
            backgroundColor: ['rgba(212,175,55,0.8)','rgba(240,208,96,0.7)','rgba(160,130,28,0.7)','rgba(212,175,55,0.5)','rgba(240,208,96,0.4)'],
            borderColor: gold, borderWidth: 1, borderRadius: 5
        }]
    },
    options: {
        responsive: true,
        indexAxis: 'y',
        plugins: { legend: { display: false } },
        scales: {
            x: { ticks: { callback: v => '₹' + (v >= 1000 ? (v/1000).toFixed(0)+'K' : v) }, grid: { color: 'rgba(212,175,55,0.06)' } },
            y: { grid: { display: false } }
        }
    }
});

// Channel Mix Doughnut Chart
const channelData = @json($channel_mix);
const pieColors = ['rgba(212,175,55,0.85)','rgba(160,130,28,0.85)','rgba(240,208,96,0.8)','rgba(100,85,20,0.8)','rgba(255,220,80,0.7)'];
new Chart(document.getElementById('channelMixChart'), {
    type: 'doughnut',
    data: {
        labels: channelData.map(r => r.channel || 'Direct'),
        datasets: [{
            data: channelData.map(r => parseFloat(r.total) || 0),
            backgroundColor: pieColors,
            borderColor: '#0d0d18', borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        cutout: '60%',
        plugins: {
            legend: { position: 'bottom', labels: { color: '#a09878', font: { size: 11 }, padding: 10, boxWidth: 12 } }
        }
    }
});
</script>
@endpush
