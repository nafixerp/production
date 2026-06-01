@extends('layouts.app')
@section('title','Balance Sheet')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-bank2 me-2"></i>Balance Sheet</h5>
    <button onclick="window.print()" class="btn btn-sm btn-outline-secondary"><i class="bi bi-printer me-1"></i>Print</button>
</div>

<div class="card-dark mb-3">
    <form method="GET" class="row g-2">
        <div class="col-md-3"><label class="form-label text-small">As of Date</label><input type="date" name="as_of" class="form-control form-control-sm" value="{{ $asOf }}"></div>
        <div class="col-md-2 d-flex align-items-end"><button type="submit" class="btn btn-sm btn-outline-gold w-100">Generate</button></div>
    </form>
</div>

@if(!$balanced)
<div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Balance Sheet does NOT balance! Assets: ₹{{ number_format($totalAssets,2) }} vs Liabilities+Equity: ₹{{ number_format($totalLiabilitiesEquity,2) }}. Difference: ₹{{ number_format(abs($totalAssets - $totalLiabilitiesEquity),2) }}</div>
@else
<div class="alert alert-success"><i class="bi bi-check-circle-fill me-2"></i>Balance Sheet balances. Total Assets = Liabilities + Equity = ₹{{ number_format($totalAssets,2) }}</div>
@endif

<div class="row g-3">
    <!-- ASSETS -->
    <div class="col-md-6">
        <div class="bs-section">
            <div class="bs-header asset-hdr">ASSETS</div>
            @foreach($assets as $item)
            <div class="bs-row">
                <span class="text-small">{{ $item['account']->code }} - {{ $item['account']->name }}</span>
                <span>{{ number_format($item['balance'],2) }}</span>
            </div>
            @endforeach
            <div class="bs-total">
                <span>TOTAL ASSETS</span>
                <span class="text-gold">₹{{ number_format($totalAssets,2) }}</span>
            </div>
        </div>
    </div>

    <!-- LIABILITIES + EQUITY -->
    <div class="col-md-6">
        <div class="bs-section">
            <div class="bs-header liab-hdr">LIABILITIES</div>
            @foreach($liabilities as $item)
            <div class="bs-row">
                <span class="text-small">{{ $item['account']->code }} - {{ $item['account']->name }}</span>
                <span>{{ number_format($item['balance'],2) }}</span>
            </div>
            @endforeach
            <div class="bs-subtotal">
                <span>Total Liabilities</span>
                <span>₹{{ number_format($totalLiabilities,2) }}</span>
            </div>

            <div class="bs-header equity-hdr mt-2">EQUITY</div>
            @foreach($equity as $item)
            <div class="bs-row">
                <span class="text-small">{{ $item['account']->code }} - {{ $item['account']->name }}</span>
                <span>{{ number_format($item['balance'],2) }}</span>
            </div>
            @endforeach
            <div class="bs-row">
                <span class="text-small text-gold">Retained Earnings (Net P&L)</span>
                <span class="{{ $netPL >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($netPL,2) }}</span>
            </div>
            <div class="bs-subtotal">
                <span>Total Equity</span>
                <span>₹{{ number_format($totalEquity,2) }}</span>
            </div>

            <div class="bs-total">
                <span>TOTAL LIABILITIES + EQUITY</span>
                <span class="text-gold">₹{{ number_format($totalLiabilitiesEquity,2) }}</span>
            </div>
        </div>
    </div>
</div>

<style>
.bs-section{background:#13132a;border:1px solid var(--border-gold);border-radius:8px;overflow:hidden}
.bs-header{padding:8px 16px;font-size:.72rem;font-weight:700;letter-spacing:2px;text-transform:uppercase}
.asset-hdr{background:rgba(52,152,219,.15);color:#3498db}
.liab-hdr{background:rgba(231,76,60,.15);color:#e74c3c}
.equity-hdr{background:rgba(155,89,182,.15);color:#9b59b6}
.bs-row{display:flex;justify-content:space-between;padding:5px 16px;font-size:.83rem;border-top:1px solid rgba(255,255,255,.03)}
.bs-subtotal{display:flex;justify-content:space-between;padding:7px 16px;background:rgba(0,0,0,.2);border-top:1px solid var(--border-gold);font-size:.85rem;font-weight:600}
.bs-total{display:flex;justify-content:space-between;padding:10px 16px;background:rgba(212,175,55,.1);border-top:2px solid var(--gold);font-size:.95rem;font-weight:700}
.text-small{font-size:.8rem}
</style>
@endsection
