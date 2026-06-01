@extends('layouts.app')
@section('title','GSTR-3B Summary')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-clipboard-data me-2"></i>GSTR-3B Summary</h5>
    <div class="d-flex gap-2">
        <a href="{{ route('gstr1.index') }}" class="btn btn-sm btn-outline-info">GSTR-1</a>
        <button onclick="window.print()" class="btn btn-sm btn-outline-secondary"><i class="bi bi-printer me-1"></i>Print</button>
    </div>
</div>

<div class="card-dark mb-3">
    <form method="GET" class="row g-2">
        <div class="col-md-3"><input type="date" name="from" class="form-control form-control-sm" value="{{ $fromDate }}"></div>
        <div class="col-md-3"><input type="date" name="to" class="form-control form-control-sm" value="{{ $toDate }}"></div>
        <div class="col-md-2"><button type="submit" class="btn btn-sm btn-outline-gold w-100">Generate</button></div>
    </form>
</div>

<div class="row g-3">
    <!-- 3.1 Outward Supplies -->
    <div class="col-md-6">
        <div class="card-dark p-3">
            <h6 class="text-gold mb-3">3.1 Outward Supplies (Sales)</h6>
            <table class="table table-sm table-borderless mb-0">
                <tr><th class="text-muted">Taxable Value</th><td class="text-end fw-bold">₹{{ number_format($outward->taxable ?? 0,2) }}</td></tr>
                <tr><th class="text-muted">CGST</th><td class="text-end">₹{{ number_format($outward->cgst ?? 0,2) }}</td></tr>
                <tr><th class="text-muted">SGST/UTGST</th><td class="text-end">₹{{ number_format($outward->sgst ?? 0,2) }}</td></tr>
                <tr><th class="text-muted">IGST</th><td class="text-end">₹{{ number_format($outward->igst ?? 0,2) }}</td></tr>
                <tr><th class="text-muted">Cess</th><td class="text-end">₹{{ number_format($outward->cess ?? 0,2) }}</td></tr>
            </table>
        </div>
    </div>

    <!-- 4. ITC Available -->
    <div class="col-md-6">
        <div class="card-dark p-3">
            <h6 class="text-gold mb-3">4. Input Tax Credit (Purchases)</h6>
            <table class="table table-sm table-borderless mb-0">
                <tr><th class="text-muted">Taxable Value</th><td class="text-end">₹{{ number_format($inward->taxable ?? 0,2) }}</td></tr>
                <tr><th class="text-muted">CGST (ITC)</th><td class="text-end text-success">₹{{ number_format($inward->cgst ?? 0,2) }}</td></tr>
                <tr><th class="text-muted">SGST (ITC)</th><td class="text-end text-success">₹{{ number_format($inward->sgst ?? 0,2) }}</td></tr>
                <tr><th class="text-muted">IGST (ITC)</th><td class="text-end text-success">₹{{ number_format($inward->igst ?? 0,2) }}</td></tr>
                <tr><th class="text-muted">Cess (ITC)</th><td class="text-end text-success">₹{{ number_format($inward->cess ?? 0,2) }}</td></tr>
            </table>
        </div>
    </div>

    <!-- Net Tax Payable -->
    <div class="col-12">
        <div class="card-dark p-3" style="border:2px solid var(--gold)">
            <h6 class="text-gold mb-3">6. Net Tax Payable</h6>
            <div class="row g-3 text-center">
                <div class="col-md-3">
                    <div class="stat-mini">
                        <div class="stat-val {{ $netTaxPayable['cgst'] > 0 ? 'text-danger' : 'text-success' }}">₹{{ number_format(abs($netTaxPayable['cgst']),2) }}</div>
                        <div class="stat-lbl">CGST {{ $netTaxPayable['cgst'] > 0 ? 'Payable' : 'Refundable' }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-mini">
                        <div class="stat-val {{ $netTaxPayable['sgst'] > 0 ? 'text-danger' : 'text-success' }}">₹{{ number_format(abs($netTaxPayable['sgst']),2) }}</div>
                        <div class="stat-lbl">SGST {{ $netTaxPayable['sgst'] > 0 ? 'Payable' : 'Refundable' }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-mini">
                        <div class="stat-val {{ $netTaxPayable['igst'] > 0 ? 'text-danger' : 'text-success' }}">₹{{ number_format(abs($netTaxPayable['igst']),2) }}</div>
                        <div class="stat-lbl">IGST {{ $netTaxPayable['igst'] > 0 ? 'Payable' : 'Refundable' }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-mini">
                        @php $totalNet = array_sum($netTaxPayable); @endphp
                        <div class="stat-val {{ $totalNet > 0 ? 'text-danger' : 'text-success' }} fw-bold">₹{{ number_format(abs($totalNet),2) }}</div>
                        <div class="stat-lbl">Total {{ $totalNet > 0 ? 'Payable' : 'Refundable' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.stat-mini{padding:10px;background:#0e0e1f;border:1px solid var(--border-gold);border-radius:6px}
.stat-val{font-size:1rem;font-weight:700}.stat-lbl{font-size:.65rem;color:rgba(212,175,55,.6);letter-spacing:1px;margin-top:2px}
</style>
@endsection
