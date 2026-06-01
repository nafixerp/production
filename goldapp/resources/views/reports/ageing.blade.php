@extends('layouts.app')
@section('title','Ageing Report')
@section('page-title','AR / AP Ageing Report')

@push('styles')
<style>
.report-card { background: var(--bg-card); border: 1px solid var(--border-gold); border-radius: 10px; padding: 20px; margin-bottom: 20px; }
.report-title { color: var(--gold); font-size: .78rem; font-weight: 600; letter-spacing: 1.5px; text-transform: uppercase; margin-bottom: 14px; border-bottom: 1px solid var(--border-gold); padding-bottom: 8px; }
.filter-bar { background: var(--bg-card); border: 1px solid var(--border-gold); border-radius: 8px; padding: 14px 18px; margin-bottom: 18px; }
.filter-bar .form-label { color: var(--text-muted-gold); font-size: .68rem; letter-spacing: 1px; text-transform: uppercase; }
.filter-bar .form-control, .filter-bar .form-select { background: rgba(255,255,255,.04); border-color: var(--border-gold); color: #e8e0c8; font-size: .82rem; }
.filter-bar .form-control:focus, .filter-bar .form-select:focus { background: rgba(255,255,255,.07); border-color: var(--gold); box-shadow: 0 0 0 2px rgba(212,175,55,.15); color: #e8e0c8; }
.age-table th { color: var(--text-muted-gold); font-size: .68rem; letter-spacing: 1px; text-transform: uppercase; border-color: var(--border-gold); background: rgba(212,175,55,.04); padding: 8px 10px; }
.age-table td { border-color: rgba(212,175,55,.08); padding: 7px 10px; color: #d0c8a8; font-size: .82rem; vertical-align: middle; }
.age-table tr:hover td { background: rgba(212,175,55,.04); }
.age-table tfoot td { font-weight: 700; border-top: 2px solid var(--border-gold); background: rgba(212,175,55,.06); color: var(--gold); }
.bucket-card { background: rgba(212,175,55,.05); border: 1px solid var(--border-gold); border-radius: 8px; padding: 14px; text-align: center; }
.bucket-card .bc-label { color: var(--text-muted-gold); font-size: .68rem; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 5px; }
.bucket-card .bc-value { color: var(--gold); font-size: 1.1rem; font-weight: 700; }
.bucket-card.bucket-over120 { border-color: rgba(255,107,107,.4); }
.bucket-card.bucket-over120 .bc-value { color: #ff6b6b; }
</style>
@endpush

@section('content')
<div class="filter-bar">
    <form method="GET" class="row g-3 align-items-end">
        <div class="col-md-2">
            <label class="form-label">Type</label>
            <select name="type" class="form-select">
                <option value="ar" {{ $type == 'ar' ? 'selected' : '' }}>Accounts Receivable (AR)</option>
                <option value="ap" {{ $type == 'ap' ? 'selected' : '' }}>Accounts Payable (AP)</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">As Of Date</label>
            <input type="date" name="as_of" class="form-control" value="{{ $as_of }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">Party Name</label>
            <input type="text" name="party_name" class="form-control" value="{{ $party_name }}" placeholder="Search party...">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn w-100" style="background:var(--gold);color:#000;font-weight:600">
                <i class="bi bi-search me-1"></i>Generate
            </button>
        </div>
    </form>
</div>

<h5 style="color:var(--gold);margin-bottom:16px">
    <i class="bi bi-clock-history me-2"></i>
    {{ $type === 'ar' ? 'Accounts Receivable' : 'Accounts Payable' }} Ageing — As of {{ date('d M Y', strtotime($as_of)) }}
</h5>

{{-- Bucket Summary Cards --}}
<div class="row g-3 mb-4">
    @foreach($buckets as $key => $bucket)
    <div class="col-6 col-md-2">
        <div class="bucket-card {{ $key === 'over120' ? 'bucket-over120' : '' }}">
            <div class="bc-label">{{ $bucket['label'] }}</div>
            <div class="bc-value">₹{{ number_format($bucket['total'], 0) }}</div>
        </div>
    </div>
    @endforeach
    <div class="col-6 col-md-2">
        <div class="bucket-card" style="border-color:rgba(212,175,55,.5)">
            <div class="bc-label">Grand Total</div>
            <div class="bc-value" style="font-size:1.2rem">₹{{ number_format($grand_total, 0) }}</div>
        </div>
    </div>
</div>

{{-- Ageing Chart --}}
<div class="row g-3 mb-4">
    <div class="col-md-8">
        <div class="report-card">
            <div class="report-title"><i class="bi bi-bar-chart me-2"></i>Ageing Distribution</div>
            <canvas id="ageingChart" height="200"></canvas>
        </div>
    </div>
    <div class="col-md-4">
        <div class="report-card">
            <div class="report-title"><i class="bi bi-pie-chart me-2"></i>Bucket Mix</div>
            <canvas id="ageingPie" height="200"></canvas>
        </div>
    </div>
</div>

{{-- Party Summary --}}
<div class="report-card">
    <div class="report-title"><i class="bi bi-table me-2"></i>Party-wise Ageing Summary</div>
    <div class="table-responsive">
        <table class="table age-table mb-0">
            <thead>
                <tr>
                    <th>Party Name</th>
                    <th class="text-end">0–30 Days</th>
                    <th class="text-end">31–60 Days</th>
                    <th class="text-end">61–90 Days</th>
                    <th class="text-end">91–120 Days</th>
                    <th class="text-end" style="color:#ff6b6b">Over 120</th>
                    <th class="text-end">Total Outstanding</th>
                </tr>
            </thead>
            <tbody>
                @forelse($party_summary as $party => $amounts)
                <tr>
                    <td><strong>{{ $party }}</strong></td>
                    <td class="text-end">{{ $amounts['current'] > 0 ? '₹'.number_format($amounts['current'],0) : '—' }}</td>
                    <td class="text-end">{{ $amounts['b31_60'] > 0 ? '₹'.number_format($amounts['b31_60'],0) : '—' }}</td>
                    <td class="text-end">{{ $amounts['b61_90'] > 0 ? '₹'.number_format($amounts['b61_90'],0) : '—' }}</td>
                    <td class="text-end">{{ $amounts['b91_120'] > 0 ? '₹'.number_format($amounts['b91_120'],0) : '—' }}</td>
                    <td class="text-end" style="{{ $amounts['over120'] > 0 ? 'color:#ff6b6b;font-weight:600' : '' }}">{{ $amounts['over120'] > 0 ? '₹'.number_format($amounts['over120'],0) : '—' }}</td>
                    <td class="text-end fw-bold" style="color:var(--gold)">₹{{ number_format($amounts['total'],0) }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-3">No outstanding balances found</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td>TOTAL</td>
                    <td class="text-end">₹{{ number_format($buckets['current']['total'],0) }}</td>
                    <td class="text-end">₹{{ number_format($buckets['b31_60']['total'],0) }}</td>
                    <td class="text-end">₹{{ number_format($buckets['b61_90']['total'],0) }}</td>
                    <td class="text-end">₹{{ number_format($buckets['b91_120']['total'],0) }}</td>
                    <td class="text-end" style="color:#ff6b6b">₹{{ number_format($buckets['over120']['total'],0) }}</td>
                    <td class="text-end">₹{{ number_format($grand_total,0) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const bucketLabels = @json(array_column($buckets, 'label'));
const bucketTotals = @json(array_column($buckets, 'total'));
const bucketColors = ['rgba(81,207,102,0.7)','rgba(212,175,55,0.7)','rgba(255,180,50,0.7)','rgba(255,120,50,0.7)','rgba(255,107,107,0.8)'];

new Chart(document.getElementById('ageingChart'), {
    type: 'bar',
    data: {
        labels: bucketLabels,
        datasets: [{ label: 'Outstanding (₹)', data: bucketTotals, backgroundColor: bucketColors, borderColor: bucketColors.map(c=>c.replace('0.7','1').replace('0.8','1')), borderWidth: 1, borderRadius: 5 }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { ticks: { callback: v => '₹'+(v>=1000?(v/1000).toFixed(0)+'K':v), color: '#a09878' }, grid: { color: 'rgba(212,175,55,0.06)' } },
            x: { ticks: { color: '#a09878' }, grid: { display: false } }
        }
    }
});

new Chart(document.getElementById('ageingPie'), {
    type: 'doughnut',
    data: { labels: bucketLabels, datasets: [{ data: bucketTotals, backgroundColor: bucketColors, borderColor: '#0d0d18', borderWidth: 2 }] },
    options: { responsive: true, cutout: '55%', plugins: { legend: { position: 'bottom', labels: { color: '#a09878', font: { size: 10 }, padding: 6, boxWidth: 10 } } } }
});
</script>
@endpush
