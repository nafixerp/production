@extends('layouts.app')
@section('title','Sales Analytics')
@section('page-title','Sales Analytics')

@push('styles')
<style>
.report-card { background: var(--bg-card); border: 1px solid var(--border-gold); border-radius: 10px; padding: 20px; margin-bottom: 20px; }
.report-title { color: var(--gold); font-size: .78rem; font-weight: 600; letter-spacing: 1.5px; text-transform: uppercase; margin-bottom: 14px; border-bottom: 1px solid var(--border-gold); padding-bottom: 8px; }
.filter-bar { background: var(--bg-card); border: 1px solid var(--border-gold); border-radius: 8px; padding: 14px 18px; margin-bottom: 18px; }
.filter-bar .form-label { color: var(--text-muted-gold); font-size: .68rem; letter-spacing: 1px; text-transform: uppercase; }
.filter-bar .form-control, .filter-bar .form-select { background: rgba(255,255,255,.04); border-color: var(--border-gold); color: #e8e0c8; font-size: .82rem; }
.filter-bar .form-control:focus, .filter-bar .form-select:focus { background: rgba(255,255,255,.07); border-color: var(--gold); box-shadow: 0 0 0 2px rgba(212,175,55,.15); color: #e8e0c8; }
.kpi-mini { background: rgba(212,175,55,.06); border: 1px solid var(--border-gold); border-radius: 7px; padding: 12px 16px; text-align: center; }
.kpi-mini .label { color: var(--text-muted-gold); font-size: .68rem; letter-spacing: 1px; text-transform: uppercase; }
.kpi-mini .value { color: var(--gold); font-size: 1.25rem; font-weight: 700; }
.analytics-table th { color: var(--text-muted-gold); font-size: .7rem; letter-spacing: 1px; text-transform: uppercase; border-color: var(--border-gold); background: rgba(212,175,55,.04); padding: 8px 10px; }
.analytics-table td { border-color: rgba(212,175,55,.08); padding: 7px 10px; color: #d0c8a8; font-size: .82rem; vertical-align: middle; }
.analytics-table tr:hover td { background: rgba(212,175,55,.04); }
</style>
@endpush

@section('content')
{{-- Filters --}}
<div class="filter-bar">
    <form method="GET" class="row g-3 align-items-end">
        <div class="col-md-2">
            <label class="form-label">From</label>
            <input type="date" name="from" class="form-control" value="{{ $from }}">
        </div>
        <div class="col-md-2">
            <label class="form-label">To</label>
            <input type="date" name="to" class="form-control" value="{{ $to }}">
        </div>
        <div class="col-md-2">
            <label class="form-label">Channel</label>
            <select name="channel" class="form-select">
                <option value="">All</option>
                @foreach($channels as $ch)
                <option value="{{ $ch }}" {{ $channel == $ch ? 'selected' : '' }}>{{ $ch }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn w-100" style="background:var(--gold);color:#000;font-weight:600">
                <i class="bi bi-search me-1"></i>Filter
            </button>
        </div>
    </form>
</div>

{{-- Summary KPIs --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3"><div class="kpi-mini"><div class="label">Total Revenue</div><div class="value">₹{{ number_format($summary['total_revenue'], 0) }}</div></div></div>
    <div class="col-6 col-md-3"><div class="kpi-mini"><div class="label">Total Invoices</div><div class="value">{{ number_format($summary['total_invoices']) }}</div></div></div>
    <div class="col-6 col-md-3"><div class="kpi-mini"><div class="label">Total Discount</div><div class="value">₹{{ number_format($summary['total_discount'], 0) }}</div></div></div>
    <div class="col-6 col-md-3"><div class="kpi-mini"><div class="label">Avg Order Value</div><div class="value">₹{{ number_format($summary['avg_order'], 0) }}</div></div></div>
</div>

{{-- Charts Row --}}
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="report-card">
            <div class="report-title"><i class="bi bi-diagram-3 me-2"></i>Revenue by Channel</div>
            <canvas id="channelChart" height="220"></canvas>
        </div>
    </div>
    <div class="col-md-6">
        <div class="report-card">
            <div class="report-title"><i class="bi bi-graph-up me-2"></i>Monthly Sales Trend</div>
            <canvas id="periodChart" height="220"></canvas>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    {{-- Top Customers --}}
    <div class="col-md-6">
        <div class="report-card">
            <div class="report-title"><i class="bi bi-people me-2"></i>Top 20 Customers by Revenue</div>
            <div class="table-responsive">
                <table class="table analytics-table mb-0">
                    <thead><tr><th>#</th><th>Customer</th><th class="text-end">Invoices</th><th class="text-end">Revenue</th><th class="text-end">Avg Order</th></tr></thead>
                    <tbody>
                        @forelse($by_customer as $i => $cust)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $cust->customer_name ?? '—' }}</td>
                            <td class="text-end">{{ $cust->invoice_count }}</td>
                            <td class="text-end fw-bold" style="color:var(--gold)">₹{{ number_format($cust->revenue, 0) }}</td>
                            <td class="text-end">₹{{ number_format($cust->avg_order, 0) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">No data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Top Products --}}
    <div class="col-md-6">
        <div class="report-card">
            <div class="report-title"><i class="bi bi-box me-2"></i>Top 20 Products by Revenue</div>
            <div class="table-responsive">
                <table class="table analytics-table mb-0">
                    <thead><tr><th>#</th><th>Product</th><th class="text-end">Qty Sold</th><th class="text-end">Revenue</th></tr></thead>
                    <tbody>
                        @forelse($by_product as $i => $prod)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $prod->fg_name ?? '—' }}</td>
                            <td class="text-end">{{ number_format($prod->qty_sold, 2) }}</td>
                            <td class="text-end fw-bold" style="color:var(--gold)">₹{{ number_format($prod->revenue, 0) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">No data</td></tr>
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
Chart.defaults.borderColor = 'rgba(212,175,55,0.08)';
const gold = '#d4af37';

const channelData = @json($by_channel);
new Chart(document.getElementById('channelChart'), {
    type: 'bar',
    data: {
        labels: channelData.map(r => r.channel || 'Direct'),
        datasets: [{
            label: 'Revenue (₹)',
            data: channelData.map(r => parseFloat(r.revenue)||0),
            backgroundColor: 'rgba(212,175,55,0.7)',
            borderColor: gold, borderWidth: 1, borderRadius: 5
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { ticks: { callback: v => '₹'+(v>=1000?(v/1000).toFixed(0)+'K':v) }, grid: { color: 'rgba(212,175,55,0.06)' } },
            x: { grid: { display: false } }
        }
    }
});

const periodData = @json($by_period);
new Chart(document.getElementById('periodChart'), {
    type: 'line',
    data: {
        labels: periodData.map(r => r.label),
        datasets: [{
            label: 'Revenue',
            data: periodData.map(r => parseFloat(r.revenue)||0),
            borderColor: gold, backgroundColor: 'rgba(212,175,55,0.08)',
            borderWidth: 2.5, pointBackgroundColor: gold, pointRadius: 4, fill: true, tension: 0.3
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { ticks: { callback: v => '₹'+(v>=1000?(v/1000).toFixed(0)+'K':v) }, grid: { color: 'rgba(212,175,55,0.06)' } },
            x: { grid: { display: false } }
        }
    }
});
</script>
@endpush
