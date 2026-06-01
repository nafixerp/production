@extends('layouts.app')
@section('title','Profit & Loss Report')
@section('page-title','Profit & Loss Report')

@push('styles')
<style>
.report-card { background: var(--bg-card); border: 1px solid var(--border-gold); border-radius: 10px; padding: 24px; margin-bottom: 20px; }
.report-section-title { color: var(--gold); font-size: .75rem; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 12px; border-bottom: 1px solid var(--border-gold); padding-bottom: 8px; }
.pl-row { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid rgba(212,175,55,.06); color: #d0c8a8; font-size: .88rem; }
.pl-row:last-child { border-bottom: none; }
.pl-row.subtotal { border-top: 1px solid var(--border-gold); padding-top: 10px; margin-top: 4px; font-weight: 600; color: #e8e0c8; }
.pl-row.grand-total { background: rgba(212,175,55,.1); border-radius: 6px; padding: 12px 14px; margin-top: 8px; font-size: 1rem; font-weight: 700; }
.pl-row .pl-label { color: #a09878; }
.pl-row .pl-value { font-weight: 600; }
.text-profit { color: #51cf66 !important; }
.text-loss { color: #ff6b6b !important; }
.text-gold { color: var(--gold) !important; }
.filter-form { background: var(--bg-card); border: 1px solid var(--border-gold); border-radius: 8px; padding: 16px 20px; margin-bottom: 20px; }
.filter-form .form-label { color: var(--text-muted-gold); font-size: .7rem; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 4px; }
.filter-form .form-control, .filter-form .form-select { background: rgba(255,255,255,.04); border: 1px solid var(--border-gold); color: #e8e0c8; font-size: .82rem; }
.filter-form .form-control:focus, .filter-form .form-select:focus { background: rgba(255,255,255,.06); border-color: var(--gold); box-shadow: 0 0 0 2px rgba(212,175,55,.15); color: #e8e0c8; }
.sku-table th { color: var(--text-muted-gold); font-size: .7rem; letter-spacing: 1.2px; text-transform: uppercase; border-color: var(--border-gold); padding: 8px 10px; background: rgba(212,175,55,.04); }
.sku-table td { border-color: rgba(212,175,55,.08); padding: 7px 10px; color: #d0c8a8; font-size: .82rem; vertical-align: middle; }
.sku-table tr:hover td { background: rgba(212,175,55,.04); }
</style>
@endpush

@section('content')
{{-- Filter Form --}}
<div class="filter-form">
    <form method="GET" class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label">From Date</label>
            <input type="date" name="from" class="form-control" value="{{ $from }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">To Date</label>
            <input type="date" name="to" class="form-control" value="{{ $to }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">Channel</label>
            <select name="channel" class="form-select">
                <option value="">All Channels</option>
                @foreach($channels as $ch)
                <option value="{{ $ch }}" {{ $channel == $ch ? 'selected' : '' }}>{{ $ch }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn w-100" style="background:var(--gold);color:#000;font-weight:600">
                <i class="bi bi-search me-1"></i> Generate Report
            </button>
        </div>
    </form>
</div>

<div class="row g-3">
    {{-- P&L Statement --}}
    <div class="col-md-5">
        <div class="report-card">
            <div class="report-section-title"><i class="bi bi-file-earmark-bar-graph me-2"></i>Profit & Loss Statement</div>
            <small class="text-muted d-block mb-3">Period: {{ date('d M Y', strtotime($from)) }} — {{ date('d M Y', strtotime($to)) }}{{ $channel ? ' | ' . $channel : '' }}</small>

            {{-- Revenue Section --}}
            <div class="pl-row"><span class="pl-label">Gross Sales</span><span class="pl-value">₹{{ number_format($revenue, 2) }}</span></div>
            <div class="pl-row"><span class="pl-label text-loss">Less: Discounts</span><span class="pl-value text-loss">(₹{{ number_format($discount, 2) }})</span></div>
            <div class="pl-row subtotal"><span>Net Revenue</span><span class="text-gold">₹{{ number_format($net_revenue, 2) }}</span></div>

            <div class="mt-3"></div>
            {{-- COGS --}}
            <div class="pl-row"><span class="pl-label">Less: Cost of Goods Sold (COGS)</span><span class="pl-value text-loss">(₹{{ number_format($cogs, 2) }})</span></div>
            <div class="pl-row subtotal" style="background:rgba(81,207,102,.08);border-radius:6px;padding:10px 14px;">
                <span>Gross Profit</span>
                <span class="{{ $gross_profit >= 0 ? 'text-profit' : 'text-loss' }} fw-bold fs-5">₹{{ number_format($gross_profit, 2) }}</span>
            </div>

            <div class="mt-3"></div>
            {{-- Operating Expenses --}}
            <div class="report-section-title" style="margin-top:12px">Operating Expenses</div>
            @foreach($expenses as $exp)
            <div class="pl-row">
                <span class="pl-label">{{ $exp->name }}</span>
                <span class="pl-value text-loss">(₹{{ number_format($exp->amount, 2) }})</span>
            </div>
            @endforeach
            <div class="pl-row subtotal"><span>Total Expenses</span><span class="text-loss">(₹{{ number_format($total_expense, 2) }})</span></div>

            <div class="mt-3"></div>
            {{-- Net Profit --}}
            <div class="pl-row grand-total">
                <span style="font-size:1rem;letter-spacing:.5px">NET PROFIT / LOSS</span>
                <span class="{{ $net_profit >= 0 ? 'text-profit' : 'text-loss' }}" style="font-size:1.2rem">
                    {{ $net_profit >= 0 ? '' : '(' }}₹{{ number_format(abs($net_profit), 2) }}{{ $net_profit >= 0 ? '' : ')' }}
                </span>
            </div>

            {{-- Margin KPIs --}}
            <div class="row g-2 mt-3">
                <div class="col-6">
                    <div style="background:rgba(212,175,55,.06);border:1px solid var(--border-gold);border-radius:6px;padding:10px;text-align:center">
                        <div style="font-size:.65rem;color:var(--text-muted-gold);letter-spacing:1px;text-transform:uppercase">Gross Margin</div>
                        <div style="font-size:1.1rem;font-weight:700;color:{{ $gross_profit >= 0 ? '#51cf66' : '#ff6b6b' }}">
                            {{ $net_revenue > 0 ? number_format(($gross_profit / $net_revenue) * 100, 1) : 0 }}%
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div style="background:rgba(212,175,55,.06);border:1px solid var(--border-gold);border-radius:6px;padding:10px;text-align:center">
                        <div style="font-size:.65rem;color:var(--text-muted-gold);letter-spacing:1px;text-transform:uppercase">Net Margin</div>
                        <div style="font-size:1.1rem;font-weight:700;color:{{ $net_profit >= 0 ? '#51cf66' : '#ff6b6b' }}">
                            {{ $net_revenue > 0 ? number_format(($net_profit / $net_revenue) * 100, 1) : 0 }}%
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SKU Margin Chart + Table --}}
    <div class="col-md-7">
        <div class="report-card">
            <div class="report-section-title"><i class="bi bi-bar-chart me-2"></i>SKU Gross Margin Analysis</div>
            <canvas id="skuMarginChart" height="180" class="mb-3"></canvas>

            <div class="table-responsive">
                <table class="table sku-table mb-0">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th class="text-end">Qty Sold</th>
                            <th class="text-end">Revenue</th>
                            <th class="text-end">COGS</th>
                            <th class="text-end">Gross Profit</th>
                            <th class="text-end">Margin%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sku_margins as $sku)
                        @php
                            $gpm = $sku->revenue > 0 ? ($sku->gross_profit / $sku->revenue) * 100 : 0;
                        @endphp
                        <tr>
                            <td><strong>{{ $sku->fg_name }}</strong></td>
                            <td class="text-end">{{ number_format($sku->qty_sold, 2) }}</td>
                            <td class="text-end">₹{{ number_format($sku->revenue, 0) }}</td>
                            <td class="text-end text-loss">₹{{ number_format($sku->cost, 0) }}</td>
                            <td class="text-end {{ $sku->gross_profit >= 0 ? 'text-profit' : 'text-loss' }} fw-bold">
                                ₹{{ number_format($sku->gross_profit, 0) }}
                            </td>
                            <td class="text-end">
                                <span class="badge" style="background:{{ $gpm >= 30 ? 'rgba(81,207,102,.2)' : ($gpm >= 10 ? 'rgba(212,175,55,.2)' : 'rgba(255,107,107,.2)') }};
                                    color:{{ $gpm >= 30 ? '#51cf66' : ($gpm >= 10 ? 'var(--gold)' : '#ff6b6b') }}">
                                    {{ number_format($gpm, 1) }}%
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-3">No SKU data for selected period</td></tr>
                        @endforelse
                    </tbody>
                    @if($sku_margins->count() > 0)
                    <tfoot>
                        <tr style="border-top:2px solid var(--border-gold);background:rgba(212,175,55,.04)">
                            <td><strong>TOTAL</strong></td>
                            <td class="text-end fw-bold">{{ number_format($sku_margins->sum('qty_sold'), 2) }}</td>
                            <td class="text-end fw-bold">₹{{ number_format($sku_margins->sum('revenue'), 0) }}</td>
                            <td class="text-end text-loss fw-bold">₹{{ number_format($sku_margins->sum('cost'), 0) }}</td>
                            <td class="text-end text-profit fw-bold">₹{{ number_format($sku_margins->sum('gross_profit'), 0) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const skuData = @json($sku_margins->take(8));
new Chart(document.getElementById('skuMarginChart'), {
    type: 'bar',
    data: {
        labels: skuData.map(s => s.fg_name || 'Unknown'),
        datasets: [
            { label: 'Revenue', data: skuData.map(s => parseFloat(s.revenue)||0), backgroundColor: 'rgba(212,175,55,0.6)', borderColor: '#d4af37', borderWidth: 1, borderRadius: 4 },
            { label: 'COGS',    data: skuData.map(s => parseFloat(s.cost)||0),    backgroundColor: 'rgba(255,107,107,0.5)', borderColor: '#ff6b6b', borderWidth: 1, borderRadius: 4 },
            { label: 'GP',      data: skuData.map(s => parseFloat(s.gross_profit)||0), backgroundColor: 'rgba(81,207,102,0.5)', borderColor: '#51cf66', borderWidth: 1, borderRadius: 4 },
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { labels: { color: '#a09878', font: { size: 11 } } } },
        scales: {
            y: { ticks: { callback: v => '₹' + (v>=1000?(v/1000).toFixed(0)+'K':v), color: '#a09878' }, grid: { color: 'rgba(212,175,55,0.06)' } },
            x: { ticks: { color: '#a09878', maxRotation: 30 }, grid: { display: false } }
        }
    }
});
</script>
@endpush
