@extends('layouts.app')
@section('title','Purchase GST Register')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-receipt-cutoff me-2"></i>Purchase GST Register</h5>
    <button onclick="window.print()" class="btn btn-sm btn-outline-secondary"><i class="bi bi-printer me-1"></i>Print</button>
</div>

<div class="card-dark mb-3">
    <form method="GET" class="row g-2">
        <div class="col-md-3"><input type="date" name="from" class="form-control form-control-sm" value="{{ $fromDate }}"></div>
        <div class="col-md-3"><input type="date" name="to" class="form-control form-control-sm" value="{{ $toDate }}"></div>
        <div class="col-md-2"><button type="submit" class="btn btn-sm btn-outline-gold w-100">Generate</button></div>
    </form>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-3"><div class="stat-mini"><div class="stat-val text-gold">₹{{ number_format($totalTaxable/100000,2) }}L</div><div class="stat-lbl">Taxable Value</div></div></div>
    <div class="col-md-3"><div class="stat-mini"><div class="stat-val text-info">₹{{ number_format($totalCGST+$totalSGST,0) }}</div><div class="stat-lbl">CGST + SGST</div></div></div>
    <div class="col-md-3"><div class="stat-mini"><div class="stat-val text-warning">₹{{ number_format($totalIGST,0) }}</div><div class="stat-lbl">IGST</div></div></div>
    <div class="col-md-3"><div class="stat-mini"><div class="stat-val text-success">₹{{ number_format(($totalCGST+$totalSGST+$totalIGST),0) }}</div><div class="stat-lbl">Total ITC</div></div></div>
</div>

<div class="table-responsive">
<table class="table table-dark-erp table-sm">
    <thead>
        <tr><th>#</th><th>Bill No.</th><th>Date</th><th>Supplier</th><th>GSTIN</th><th class="text-end">Taxable</th><th class="text-end">CGST</th><th class="text-end">SGST</th><th class="text-end">IGST</th><th class="text-end">Total</th></tr>
    </thead>
    <tbody>
        @forelse($purchaseData as $i => $row)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $row->bill_no }}</td>
            <td>{{ $row->bill_date }}</td>
            <td>{{ $row->supplier_name }}</td>
            <td>{{ $row->supplier_gstin ?? 'URP' }}</td>
            <td class="text-end">{{ number_format($row->taxable_value,2) }}</td>
            <td class="text-end">{{ number_format($row->cgst,2) }}</td>
            <td class="text-end">{{ number_format($row->sgst,2) }}</td>
            <td class="text-end">{{ number_format($row->igst,2) }}</td>
            <td class="text-end text-gold">{{ number_format($row->invoice_value,2) }}</td>
        </tr>
        @empty
        <tr><td colspan="10" class="text-center text-muted">No purchase data for this period.</td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr class="fw-bold" style="background:rgba(212,175,55,.1)">
            <td colspan="5" class="text-end">TOTAL</td>
            <td class="text-end">{{ number_format($totalTaxable,2) }}</td>
            <td class="text-end">{{ number_format($totalCGST,2) }}</td>
            <td class="text-end">{{ number_format($totalSGST,2) }}</td>
            <td class="text-end">{{ number_format($totalIGST,2) }}</td>
            <td class="text-end text-gold">{{ number_format($totalTaxable+$totalCGST+$totalSGST+$totalIGST,2) }}</td>
        </tr>
    </tfoot>
</table>
</div>
<style>
.stat-mini{text-align:center;padding:10px;background:#13132a;border:1px solid var(--border-gold);border-radius:6px}
.stat-val{font-size:.95rem;font-weight:700}.stat-lbl{font-size:.65rem;color:rgba(212,175,55,.6)}
</style>
@endsection
