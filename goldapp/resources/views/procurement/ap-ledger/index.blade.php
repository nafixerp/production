@extends('layouts.app')
@section('title', 'AP Ledger - Accounts Payable Ageing')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="text-gold mb-0"><i class="bi bi-bar-chart-line me-2"></i>Accounts Payable — Ageing Report</h4>
</div>

<div class="card-erp mb-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label-erp">Supplier</label>
            <select name="supplier_id" class="form-select form-select-sm form-erp">
                <option value="">All Suppliers</option>
                @foreach($suppliers as $s)
                    <option value="{{ $s->id }}" {{ ($supplierId ?? '') == $s->id ? 'selected' : '' }}>{{ $s->code }} - {{ $s->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label-erp">As of Date</label>
            <input type="date" name="as_of" value="{{ $asOf }}" class="form-control form-control-sm form-erp">
        </div>
        <div class="col-auto"><button class="btn btn-gold btn-sm"><i class="bi bi-filter me-1"></i>Apply</button></div>
    </form>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-3">
    @foreach([['0-30 Days', $totals['bucket_0_30'], 'success'],['31-60 Days', $totals['bucket_31_60'], 'info'],['61-90 Days', $totals['bucket_61_90'], 'warning'],['90+ Days', $totals['bucket_90plus'], 'danger'],['Total', $totals['total'], 'gold']] as [$label, $val, $color])
    <div class="col">
        <div class="card-erp p-3 text-center">
            <div class="small text-muted">{{ $label }}</div>
            <div class="fw-bold {{ $color === 'gold' ? 'text-gold' : 'text-'.$color }} fs-5">₹{{ number_format($val, 0) }}</div>
        </div>
    </div>
    @endforeach
</div>

<!-- Ageing Table -->
<div class="card-erp mb-3">
    <div class="p-3 border-bottom-gold"><h6 class="text-gold mb-0">Supplier-wise Ageing</h6></div>
    <div class="table-responsive">
        <table class="table table-erp table-hover mb-0">
            <thead>
                <tr>
                    <th>Supplier</th>
                    <th class="text-end">0-30 Days</th>
                    <th class="text-end">31-60 Days</th>
                    <th class="text-end">61-90 Days</th>
                    <th class="text-end">90+ Days</th>
                    <th class="text-end">Total Outstanding</th>
                </tr>
            </thead>
            <tbody>
            @forelse($ageing as $row)
                <tr>
                    <td><strong>{{ $row->supplier_name }}</strong></td>
                    <td class="text-end text-success">₹{{ number_format($row->bucket_0_30, 2) }}</td>
                    <td class="text-end text-info">₹{{ number_format($row->bucket_31_60, 2) }}</td>
                    <td class="text-end text-warning">₹{{ number_format($row->bucket_61_90, 2) }}</td>
                    <td class="text-end text-danger">₹{{ number_format($row->bucket_90plus, 2) }}</td>
                    <td class="text-end fw-bold text-gold">₹{{ number_format($row->total_outstanding, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No outstanding invoices.</td></tr>
            @endforelse
            </tbody>
            <tfoot class="table-gold">
                <tr>
                    <td class="fw-bold">TOTAL</td>
                    <td class="text-end fw-bold">₹{{ number_format($totals['bucket_0_30'], 2) }}</td>
                    <td class="text-end fw-bold">₹{{ number_format($totals['bucket_31_60'], 2) }}</td>
                    <td class="text-end fw-bold">₹{{ number_format($totals['bucket_61_90'], 2) }}</td>
                    <td class="text-end fw-bold">₹{{ number_format($totals['bucket_90plus'], 2) }}</td>
                    <td class="text-end fw-bold text-gold">₹{{ number_format($totals['total'], 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@if($supplierId && count($ledger) > 0)
<div class="card-erp">
    <div class="p-3 border-bottom-gold"><h6 class="text-gold mb-0">Supplier Transaction Ledger</h6></div>
    <div class="table-responsive">
        <table class="table table-sm table-erp mb-0">
            <thead><tr><th>Date</th><th>Doc No</th><th>Type</th><th>Ref No</th><th class="text-end">Debit</th><th class="text-end">Credit</th><th class="text-end">Balance</th><th>Narration</th></tr></thead>
            <tbody>
            @php $runBal = 0; @endphp
            @foreach($ledger as $entry)
                @php $runBal += $entry->credit - $entry->debit; @endphp
                <tr>
                    <td>{{ \Carbon\Carbon::parse($entry->tdate)->format('d/m/Y') }}</td>
                    <td>{{ $entry->slno }}</td>
                    <td><span class="badge bg-secondary">{{ $entry->vtype }}</span></td>
                    <td>{{ $entry->ref_no }}</td>
                    <td class="text-end {{ $entry->debit > 0 ? 'text-success' : 'text-muted' }}">{{ $entry->debit > 0 ? '₹'.number_format($entry->debit, 2) : '—' }}</td>
                    <td class="text-end {{ $entry->credit > 0 ? 'text-danger' : 'text-muted' }}">{{ $entry->credit > 0 ? '₹'.number_format($entry->credit, 2) : '—' }}</td>
                    <td class="text-end {{ $runBal > 0 ? 'text-warning' : '' }}">₹{{ number_format($runBal, 2) }}</td>
                    <td class="small text-muted">{{ $entry->narration }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
