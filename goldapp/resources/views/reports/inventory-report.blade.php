@extends('layouts.app')
@section('title','Inventory Report')
@section('page-title','Inventory Report')

@push('styles')
<style>
.report-card { background: var(--bg-card); border: 1px solid var(--border-gold); border-radius: 10px; padding: 20px; margin-bottom: 20px; }
.report-title { color: var(--gold); font-size: .78rem; font-weight: 600; letter-spacing: 1.5px; text-transform: uppercase; margin-bottom: 14px; border-bottom: 1px solid var(--border-gold); padding-bottom: 8px; }
.filter-bar { background: var(--bg-card); border: 1px solid var(--border-gold); border-radius: 8px; padding: 14px 18px; margin-bottom: 18px; }
.filter-bar .form-label { color: var(--text-muted-gold); font-size: .68rem; letter-spacing: 1px; text-transform: uppercase; }
.filter-bar .form-control, .filter-bar .form-select { background: rgba(255,255,255,.04); border-color: var(--border-gold); color: #e8e0c8; font-size: .82rem; }
.filter-bar .form-control:focus, .filter-bar .form-select:focus { background: rgba(255,255,255,.07); border-color: var(--gold); box-shadow: 0 0 0 2px rgba(212,175,55,.15); color: #e8e0c8; }
.inv-table th { color: var(--text-muted-gold); font-size: .68rem; letter-spacing: 1px; text-transform: uppercase; border-color: var(--border-gold); background: rgba(212,175,55,.04); padding: 8px 10px; }
.inv-table td { border-color: rgba(212,175,55,.08); padding: 7px 10px; color: #d0c8a8; font-size: .82rem; vertical-align: middle; }
.inv-table tr:hover td { background: rgba(212,175,55,.04); }
.expiry-critical { color: #ff6b6b; font-weight: 600; }
.expiry-warning  { color: #ffd43b; }
.expiry-ok       { color: #51cf66; }
.nav-tabs-gold .nav-link { color: #a09878; border: 1px solid transparent; font-size: .8rem; }
.nav-tabs-gold .nav-link.active { color: var(--gold); background: rgba(212,175,55,.1); border-color: var(--border-gold); border-bottom-color: var(--bg-card); }
.nav-tabs-gold { border-bottom: 1px solid var(--border-gold); }
</style>
@endpush

@section('content')
<div class="filter-bar">
    <form method="GET" class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label">As Of Date</label>
            <input type="date" name="as_of" class="form-control" value="{{ $as_of }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">Category / Type</label>
            <select name="category" class="form-select">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                <option value="{{ $cat }}" {{ $category == $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn w-100" style="background:var(--gold);color:#000;font-weight:600">
                <i class="bi bi-search me-1"></i>Filter
            </button>
        </div>
        <div class="col-md-4 text-end">
            <div style="background:rgba(212,175,55,.1);border:1px solid var(--border-gold);border-radius:7px;padding:10px 16px;display:inline-block">
                <span style="color:var(--text-muted-gold);font-size:.7rem;letter-spacing:1px;text-transform:uppercase">Total Inventory Value</span>
                <div style="color:var(--gold);font-size:1.4rem;font-weight:700">₹{{ number_format($total_value, 2) }}</div>
            </div>
        </div>
    </form>
</div>

{{-- Tabs --}}
<ul class="nav nav-tabs-gold nav-tabs mb-3" id="invTabs" role="tablist">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#valuation"><i class="bi bi-currency-rupee me-1"></i>Stock Valuation</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#expiry"><i class="bi bi-calendar-x me-1"></i>Expiry Alerts <span class="badge bg-danger ms-1">{{ $expiry_alerts->count() }}</span></a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#slow"><i class="bi bi-hourglass me-1"></i>Slow-Moving</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#turnover"><i class="bi bi-arrow-repeat me-1"></i>Turnover Ratio</a></li>
</ul>

<div class="tab-content">
    {{-- Stock Valuation Tab --}}
    <div class="tab-pane fade show active" id="valuation">
        <div class="row g-3 mb-3">
            <div class="col-md-8">
                <div class="report-card" style="padding:16px">
                    <canvas id="valuationChart" height="200"></canvas>
                </div>
            </div>
            <div class="col-md-4">
                <div class="report-card">
                    <div class="report-title">By Category</div>
                    <canvas id="categoryPieChart" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="report-card">
            <div class="report-title"><i class="bi bi-table me-2"></i>Stock Valuation — {{ date('d M Y', strtotime($as_of)) }}</div>
            <div class="table-responsive">
                <table class="table inv-table mb-0">
                    <thead><tr><th>Type</th><th>Code</th><th>Item Name</th><th class="text-end">Balance</th><th class="text-end">Avg Cost</th><th class="text-end">Value</th></tr></thead>
                    <tbody>
                        @forelse($stock_valuation as $item)
                        <tr>
                            <td><span class="badge" style="background:rgba(212,175,55,.15);color:var(--gold);font-size:.65rem">{{ $item->item_type }}</span></td>
                            <td>{{ $item->item_code }}</td>
                            <td>{{ $item->item_name }}</td>
                            <td class="text-end">{{ number_format($item->balance, 3) }}</td>
                            <td class="text-end">₹{{ number_format($item->avg_cost, 2) }}</td>
                            <td class="text-end fw-bold" style="color:var(--gold)">₹{{ number_format($item->value, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-3">No stock records found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Expiry Alerts Tab --}}
    <div class="tab-pane fade" id="expiry">
        <div class="report-card">
            <div class="report-title"><i class="bi bi-calendar-x me-2"></i>Items Expiring Within 30 Days</div>
            <div class="table-responsive">
                <table class="table inv-table mb-0">
                    <thead><tr><th>Type</th><th>Code</th><th>Item</th><th>Batch</th><th>Lot</th><th class="text-end">Balance</th><th>Expiry Date</th><th class="text-end">Days Left</th></tr></thead>
                    <tbody>
                        @forelse($expiry_alerts as $item)
                        @php $days = (int) $item->days_to_expiry; @endphp
                        <tr>
                            <td><span class="badge" style="background:rgba(212,175,55,.15);color:var(--gold);font-size:.65rem">{{ $item->item_type }}</span></td>
                            <td>{{ $item->item_code }}</td>
                            <td>{{ $item->item_name }}</td>
                            <td>{{ $item->batch_no ?? '—' }}</td>
                            <td>{{ $item->lot_no ?? '—' }}</td>
                            <td class="text-end">{{ number_format($item->balance, 3) }}</td>
                            <td>{{ $item->expiry_date ? date('d M Y', strtotime($item->expiry_date)) : '—' }}</td>
                            <td class="text-end">
                                <span class="{{ $days <= 7 ? 'expiry-critical' : ($days <= 15 ? 'expiry-warning' : 'expiry-ok') }}">
                                    {{ $days <= 0 ? 'EXPIRED' : $days . ' days' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center text-muted py-3">No items expiring within 30 days</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Slow Moving Tab --}}
    <div class="tab-pane fade" id="slow">
        <div class="report-card">
            <div class="report-title"><i class="bi bi-hourglass-split me-2"></i>Slow-Moving Items (No movement &gt;30 days)</div>
            <div class="table-responsive">
                <table class="table inv-table mb-0">
                    <thead><tr><th>Type</th><th>Code</th><th>Item</th><th class="text-end">Balance</th><th>Last Movement</th><th class="text-end">Days Idle</th></tr></thead>
                    <tbody>
                        @forelse($slow_moving as $item)
                        <tr>
                            <td><span class="badge" style="background:rgba(212,175,55,.15);color:var(--gold);font-size:.65rem">{{ $item->item_type }}</span></td>
                            <td>{{ $item->item_code }}</td>
                            <td>{{ $item->item_name }}</td>
                            <td class="text-end">{{ number_format($item->balance, 3) }}</td>
                            <td>{{ $item->last_movement_date ? date('d M Y', strtotime($item->last_movement_date)) : '—' }}</td>
                            <td class="text-end text-loss fw-bold">{{ $item->days_idle }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-3">No slow-moving items found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Turnover Ratio Tab --}}
    <div class="tab-pane fade" id="turnover">
        <div class="report-card">
            <div class="report-title"><i class="bi bi-arrow-repeat me-2"></i>Inventory Turnover Ratio</div>
            <p style="color:#a09878;font-size:.8rem">Higher ratio = faster-moving stock. Ratio = COGS / Average Stock Value.</p>
            <div class="table-responsive">
                <table class="table inv-table mb-0">
                    <thead><tr><th>Type</th><th>Code</th><th>Item</th><th class="text-end">COGS Value</th><th class="text-end">Avg Stock</th><th class="text-end">Turnover Ratio</th></tr></thead>
                    <tbody>
                        @forelse($turnover as $item)
                        <tr>
                            <td><span class="badge" style="background:rgba(212,175,55,.15);color:var(--gold);font-size:.65rem">{{ $item->item_type }}</span></td>
                            <td>{{ $item->item_code }}</td>
                            <td>{{ $item->item_name }}</td>
                            <td class="text-end">₹{{ number_format($item->cogs_value, 2) }}</td>
                            <td class="text-end">{{ number_format($item->avg_stock, 3) }}</td>
                            <td class="text-end">
                                <span class="badge" style="background:{{ $item->turnover_ratio > 4 ? 'rgba(81,207,102,.2)' : ($item->turnover_ratio > 2 ? 'rgba(212,175,55,.2)' : 'rgba(255,107,107,.2)') }};
                                    color:{{ $item->turnover_ratio > 4 ? '#51cf66' : ($item->turnover_ratio > 2 ? 'var(--gold)' : '#ff6b6b') }};font-size:.78rem">
                                    {{ number_format($item->turnover_ratio, 2) }}x
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-3">No turnover data</td></tr>
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
const valData = @json($stock_valuation->sortByDesc('value')->take(10)->values());
new Chart(document.getElementById('valuationChart'), {
    type: 'bar',
    data: {
        labels: valData.map(r => r.item_name),
        datasets: [{ label: 'Value (₹)', data: valData.map(r => parseFloat(r.value)||0), backgroundColor: 'rgba(212,175,55,0.65)', borderColor: '#d4af37', borderWidth: 1, borderRadius: 4 }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false }, title: { display: true, text: 'Top 10 Items by Value', color: '#a09878', font: { size: 12 } } },
        scales: {
            y: { ticks: { callback: v => '₹'+(v>=1000?(v/1000).toFixed(0)+'K':v), color: '#a09878' }, grid: { color: 'rgba(212,175,55,0.06)' } },
            x: { ticks: { color: '#a09878', maxRotation: 30, font: { size: 10 } }, grid: { display: false } }
        }
    }
});

// Category pie
const catGroups = {};
valData.forEach(r => { catGroups[r.item_type] = (catGroups[r.item_type]||0) + parseFloat(r.value||0); });
const catLabels = Object.keys(catGroups);
const catValues = Object.values(catGroups);
const pieColors = ['rgba(212,175,55,0.8)','rgba(160,130,28,0.8)','rgba(240,208,96,0.75)','rgba(100,85,20,0.8)','rgba(255,220,80,0.7)'];
new Chart(document.getElementById('categoryPieChart'), {
    type: 'doughnut',
    data: { labels: catLabels, datasets: [{ data: catValues, backgroundColor: pieColors, borderColor: '#0d0d18', borderWidth: 2 }] },
    options: { responsive: true, cutout: '55%', plugins: { legend: { position: 'bottom', labels: { color: '#a09878', font: { size: 10 }, padding: 6, boxWidth: 10 } } } }
});
</script>
@endpush
