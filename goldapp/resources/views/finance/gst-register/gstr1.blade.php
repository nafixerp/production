@extends('layouts.app')
@section('title','GSTR-1')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-receipt me-2"></i>GSTR-1 — Outward Supplies (Sales)</h5>
    <div class="d-flex gap-2">
        <a href="{{ route('gstr3b.index') }}" class="btn btn-sm btn-outline-info">GSTR-3B</a>
        <button onclick="window.print()" class="btn btn-sm btn-outline-secondary"><i class="bi bi-printer me-1"></i>Print</button>
    </div>
</div>

<div class="card-dark mb-3">
    <form method="GET" class="row g-2">
        <div class="col-md-3"><label class="form-label text-small">From</label><input type="date" name="from" class="form-control form-control-sm" value="{{ $fromDate }}"></div>
        <div class="col-md-3"><label class="form-label text-small">To</label><input type="date" name="to" class="form-control form-control-sm" value="{{ $toDate }}"></div>
        <div class="col-md-2 d-flex align-items-end"><button type="submit" class="btn btn-sm btn-outline-gold w-100">Generate</button></div>
    </form>
</div>

<!-- Summary Boxes -->
<div class="row g-3 mb-3">
    <div class="col-md-2"><div class="stat-mini"><div class="stat-val text-gold">₹{{ number_format($totalTaxable/100000,2) }}L</div><div class="stat-lbl">Taxable Value</div></div></div>
    <div class="col-md-2"><div class="stat-mini"><div class="stat-val text-info">₹{{ number_format($totalCGST,0) }}</div><div class="stat-lbl">CGST</div></div></div>
    <div class="col-md-2"><div class="stat-mini"><div class="stat-val text-info">₹{{ number_format($totalSGST,0) }}</div><div class="stat-lbl">SGST</div></div></div>
    <div class="col-md-2"><div class="stat-mini"><div class="stat-val text-warning">₹{{ number_format($totalIGST,0) }}</div><div class="stat-lbl">IGST</div></div></div>
    <div class="col-md-3"><div class="stat-mini"><div class="stat-val text-success">₹{{ number_format($totalInvoice/100000,2) }}L</div><div class="stat-lbl">Total Invoice Value</div></div></div>
</div>

<div class="table-responsive">
<table class="table table-dark-erp table-sm">
    <thead>
        <tr><th>#</th><th>Invoice No.</th><th>Date</th><th>Customer</th><th>GSTIN</th><th>State</th><th class="text-end">Taxable</th><th class="text-end">CGST</th><th class="text-end">SGST</th><th class="text-end">IGST</th><th class="text-end">Total</th></tr>
    </thead>
    <tbody>
        @forelse($salesData as $i => $row)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $row->invoice_no }}</td>
            <td>{{ $row->invoice_date }}</td>
            <td>{{ $row->customer_name }}</td>
            <td>{{ $row->customer_gstin ?? 'URP' }}</td>
            <td>{{ $row->customer_state }}</td>
            <td class="text-end">{{ number_format($row->taxable_value,2) }}</td>
            <td class="text-end">{{ number_format($row->cgst,2) }}</td>
            <td class="text-end">{{ number_format($row->sgst,2) }}</td>
            <td class="text-end">{{ number_format($row->igst,2) }}</td>
            <td class="text-end text-gold">{{ number_format($row->invoice_value,2) }}</td>
        </tr>
        @empty
        <tr><td colspan="11" class="text-center text-muted">No sales data for this period.</td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr class="fw-bold" style="background:rgba(212,175,55,.1)">
            <td colspan="6" class="text-end">TOTAL</td>
            <td class="text-end">{{ number_format($totalTaxable,2) }}</td>
            <td class="text-end">{{ number_format($totalCGST,2) }}</td>
            <td class="text-end">{{ number_format($totalSGST,2) }}</td>
            <td class="text-end">{{ number_format($totalIGST,2) }}</td>
            <td class="text-end text-gold">{{ number_format($totalInvoice,2) }}</td>
        </tr>
    </tfoot>
</table>
</div>
<style>
.stat-mini{text-align:center;padding:10px;background:#13132a;border:1px solid var(--border-gold);border-radius:6px}
.stat-val{font-size:.95rem;font-weight:700}.stat-lbl{font-size:.65rem;color:rgba(212,175,55,.6);letter-spacing:1px}
.text-small{font-size:.78rem}
</style>
@endsection
