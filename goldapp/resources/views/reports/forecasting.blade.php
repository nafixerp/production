@extends('layouts.app')
@section('title','Sales Forecasting')
@section('page-title','Sales Forecasting')

@push('styles')
<style>
.report-card { background: var(--bg-card); border: 1px solid var(--border-gold); border-radius: 10px; padding: 20px; margin-bottom: 20px; }
.report-title { color: var(--gold); font-size: .78rem; font-weight: 600; letter-spacing: 1.5px; text-transform: uppercase; margin-bottom: 14px; border-bottom: 1px solid var(--border-gold); padding-bottom: 8px; }
.filter-bar { background: var(--bg-card); border: 1px solid var(--border-gold); border-radius: 8px; padding: 14px 18px; margin-bottom: 18px; }
.filter-bar .form-label { color: var(--text-muted-gold); font-size: .68rem; letter-spacing: 1px; text-transform: uppercase; }
.filter-bar .form-control, .filter-bar .form-select { background: rgba(255,255,255,.04); border-color: var(--border-gold); color: #e8e0c8; font-size: .82rem; }
.filter-bar .form-control:focus, .filter-bar .form-select:focus { background: rgba(255,255,255,.07); border-color: var(--gold); box-shadow: 0 0 0 2px rgba(212,175,55,.15); color: #e8e0c8; }
.sku-forecast-card { background: rgba(212,175,55,.04); border: 1px solid var(--border-gold); border-radius: 8px; padding: 14px; margin-bottom: 14px; }
.sku-forecast-card .sku-name { color: var(--gold); font-weight: 600; font-size: .88rem; }
.trend-badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: .68rem; font-weight: 600; }
.trend-up { background: rgba(81,207,102,.2); color: #51cf66; }
.trend-down { background: rgba(255,107,107,.2); color: #ff6b6b; }
.trend-stable { background: rgba(212,175,55,.2); color: var(--gold); }
.forecast-table th { color: var(--text-muted-gold); font-size: .68rem; letter-spacing: 1px; text-transform: uppercase; border-color: var(--border-gold); background: rgba(212,175,55,.04); padding: 8px 10px; }
.forecast-table td { border-color: rgba(212,175,55,.08); padding: 7px 10px; color: #d0c8a8; font-size: .82rem; }
.forecast-table tr:hover td { background: rgba(212,175,55,.04); }
.forecast-row { background: rgba(81,207,102,.06) !important; }
.forecast-row td { color: #51cf66 !important; font-weight: 600; }
</style>
@endpush

@section('content')
<div class="filter-bar">
    <form method="GET" class="row g-3 align-items-end">
        <div class="col-md-2">
            <label class="form-label">History (Months)</label>
            <select name="months_back" class="form-select">
                @foreach([3,6,9,12] as $m)
                <option value="{{ $m }}" {{ $months_back == $m ? 'selected' : '' }}>{{ $m }} months</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">SKU / Product</label>
            <select name="sku" class="form-select">
                <option value="">All SKUs</option>
                @foreach($sku_list as $s)
                <option value="{{ $s }}" {{ $sku_filter == $s ? 'selected' : '' }}>{{ $s }}</option>
                @endforeach
            </select>
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
                <i class="bi bi-graph-up me-1"></i>Forecast
            </button>
        </div>
    </form>
</div>

{{-- Overall Trend Chart --}}
<div class="report-card">
    <div class="report-title"><i class="bi bi-graph-up-arrow me-2"></i>Overall Monthly Revenue Trend & 3-Month Forecast</div>
    <canvas id="overallTrendChart" height="160"></canvas>
</div>

{{-- SKU Forecasts --}}
@forelse($forecasts as $fc)
<div class="sku-forecast-card">
    <div class="d-flex justify-content-between align-items-start mb-2">
        <div>
            <span class="sku-name">{{ $fc['sku'] }}</span>
            <span class="trend-badge {{ $fc['trend'] === 'up' ? 'trend-up' : ($fc['trend'] === 'down' ? 'trend-down' : 'trend-stable') }} ms-2">
                <i class="bi bi-arrow-{{ $fc['trend'] === 'up' ? 'up' : ($fc['trend'] === 'down' ? 'down' : 'right') }}"></i>
                {{ ucfirst($fc['trend']) }}
            </span>
        </div>
        <div class="text-end">
            <div style="color:var(--text-muted-gold);font-size:.68rem;text-transform:uppercase;letter-spacing:1px">3-Month MA Forecast</div>
            <div style="color:var(--gold);font-weight:700">₹{{ number_format($fc['forecast_rev'], 0) }}/mo &nbsp; {{ number_format($fc['forecast_qty'], 1) }} units/mo</div>
        </div>
    </div>
    <canvas id="skuChart_{{ Str::slug($fc['sku']) }}" height="80"></canvas>
</div>
@empty
<div class="report-card text-center text-muted py-4">No sales history found for the selected filters.</div>
@endforelse

{{-- Forecast Summary Table --}}
@if(count($forecasts) > 0)
<div class="report-card">
    <div class="report-title"><i class="bi bi-table me-2"></i>Forecast Summary — Next 3 Months</div>
    <div class="table-responsive">
        <table class="table forecast-table mb-0">
            <thead>
                <tr>
                    <th>SKU / Product</th>
                    <th>Trend</th>
                    @if(count($forecasts) > 0 && count($forecasts[0]['next_months']) > 0)
                    @foreach($forecasts[0]['next_months'] as $nm)
                    <th class="text-end">{{ $nm }} (Fcst Rev)</th>
                    @endforeach
                    @endif
                    <th class="text-end">Fcst Qty/mo</th>
                </tr>
            </thead>
            <tbody>
                @foreach($forecasts as $fc)
                <tr>
                    <td><strong>{{ $fc['sku'] }}</strong></td>
                    <td>
                        <span class="trend-badge {{ $fc['trend'] === 'up' ? 'trend-up' : ($fc['trend'] === 'down' ? 'trend-down' : 'trend-stable') }}">
                            {{ ucfirst($fc['trend']) }}
                        </span>
                    </td>
                    @foreach($fc['next_months'] as $nm)
                    <td class="text-end">₹{{ number_format($fc['forecast_rev'], 0) }}</td>
                    @endforeach
                    <td class="text-end">{{ number_format($fc['forecast_qty'], 1) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
Chart.defaults.color = '#a09878';
Chart.defaults.borderColor = 'rgba(212,175,55,0.08)';
const gold = '#d4af37';

// Overall trend chart with forecast
const overallData = @json($overall_monthly);
const overallLabels = overallData.map(r => r.label);
const overallValues = overallData.map(r => parseFloat(r.revenue)||0);

// Add 3 forecast months
@if(count($forecasts) > 0)
const allSkuRevs = @json(array_column($forecasts, 'forecast_rev'));
const avgForecastRev = allSkuRevs.length > 0 ? allSkuRevs.reduce((a,b)=>a+b,0) : 0;
const nextMonths = @json(count($forecasts) > 0 ? $forecasts[0]['next_months'] : []);
@else
const avgForecastRev = 0; const nextMonths = [];
@endif

const fcLabels = [...overallLabels, ...nextMonths];
const fcActual  = [...overallValues, ...nextMonths.map(()=>null)];
const fcForecast = [...overallValues.map(()=>null), avgForecastRev, avgForecastRev, avgForecastRev];

new Chart(document.getElementById('overallTrendChart'), {
    type: 'line',
    data: {
        labels: fcLabels,
        datasets: [
            {
                label: 'Actual Revenue',
                data: fcActual,
                borderColor: gold, backgroundColor: 'rgba(212,175,55,0.08)',
                borderWidth: 2.5, pointBackgroundColor: gold, pointRadius: 4, fill: true, tension: 0.3, spanGaps: false
            },
            {
                label: 'Forecast (3-Month MA)',
                data: fcForecast,
                borderColor: '#51cf66', backgroundColor: 'rgba(81,207,102,0.06)',
                borderWidth: 2, borderDash: [6,4], pointBackgroundColor: '#51cf66', pointRadius: 5,
                fill: false, tension: 0.2, spanGaps: false
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { labels: { color: '#a09878', font: { size: 11 } } } },
        scales: {
            y: { ticks: { callback: v => '₹'+(v>=1000?(v/1000).toFixed(0)+'K':v) }, grid: { color: 'rgba(212,175,55,0.06)' } },
            x: { grid: { display: false } }
        }
    }
});

// Per-SKU mini charts
@foreach($forecasts as $fc)
(function() {
    const skuHistory = @json($fc['history']);
    const skuLabels  = skuHistory.map(r => r.label);
    const skuValues  = skuHistory.map(r => parseFloat(r.revenue)||0);
    const fcVal      = {{ $fc['forecast_rev'] }};
    const nextMo     = @json($fc['next_months']);
    const allLabels  = [...skuLabels, ...nextMo];
    const actualData = [...skuValues, null, null, null];
    const fcastData  = [...skuValues.map(()=>null), fcVal, fcVal, fcVal];

    const ctx = document.getElementById('skuChart_{{ Str::slug($fc['sku']) }}');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: allLabels,
                datasets: [
                    { label: 'Actual', data: actualData, borderColor: gold, borderWidth: 2, pointRadius: 3, pointBackgroundColor: gold, tension: 0.3, fill: false, spanGaps: false },
                    { label: 'Forecast', data: fcastData, borderColor: '#51cf66', borderWidth: 1.5, borderDash: [5,4], pointRadius: 4, pointBackgroundColor: '#51cf66', fill: false, spanGaps: false }
                ]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { ticks: { callback: v => '₹'+(v>=1000?(v/1000).toFixed(0)+'K':v), font: { size: 10 }, color: '#a09878' }, grid: { color: 'rgba(212,175,55,0.06)' } },
                    x: { ticks: { font: { size: 10 }, color: '#a09878' }, grid: { display: false } }
                }
            }
        });
    }
})();
@endforeach
</script>
@endpush
