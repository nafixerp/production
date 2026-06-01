@extends('layouts.app')
@section('title', 'Goods Receipt Notes')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="text-gold mb-0"><i class="bi bi-box-arrow-in-down me-2"></i>Goods Receipt Notes (GRN)</h4>
    <a href="{{ route('grn.create') }}" class="btn btn-gold btn-sm"><i class="bi bi-plus-lg me-1"></i>New GRN</a>
</div>

@if(session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif

<div class="card-erp mb-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-3"><input type="text" name="q" value="{{ $q ?? '' }}" class="form-control form-control-sm form-erp" placeholder="GRN No / Supplier..."></div>
        <div class="col-md-2">
            <select name="status" class="form-select form-select-sm form-erp">
                <option value="">All Status</option>
                @foreach(['draft','qc_pending','approved','rejected'] as $st)
                    <option value="{{ $st }}" {{ ($status ?? '') === $st ? 'selected' : '' }}>{{ ucwords(str_replace('_',' ',$st)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2"><input type="date" name="from" value="{{ $from ?? '' }}" class="form-control form-control-sm form-erp"></div>
        <div class="col-md-2"><input type="date" name="to" value="{{ $to ?? '' }}" class="form-control form-control-sm form-erp"></div>
        <div class="col-auto">
            <button class="btn btn-gold btn-sm"><i class="bi bi-search"></i></button>
            <a href="{{ route('grn.index') }}" class="btn btn-outline-secondary btn-sm ms-1">Clear</a>
        </div>
    </form>
</div>

<div class="card-erp">
    <div class="table-responsive">
        <table class="table table-erp table-hover mb-0">
            <thead>
                <tr><th>GRN No</th><th>Date</th><th>Supplier</th><th>Supplier Invoice</th><th class="text-end">Net Amount</th><th>Status</th><th>QC Result</th><th>Actions</th></tr>
            </thead>
            <tbody>
            @forelse($grns as $g)
                <tr>
                    <td><a href="{{ route('grn.show', $g) }}" class="text-gold-light fw-bold">{{ $g->slno }}</a></td>
                    <td>{{ \Carbon\Carbon::parse($g->grn_date)->format('d/m/Y') }}</td>
                    <td>{{ $g->supplier_name }}</td>
                    <td>{{ $g->supplier_invoice_no ?? '—' }}</td>
                    <td class="text-end">₹{{ number_format($g->net_amount, 2) }}</td>
                    <td>
                        @php $c = match($g->status) { 'draft'=>'secondary','qc_pending'=>'warning','approved'=>'success','rejected'=>'danger',default=>'secondary' }; @endphp
                        <span class="badge bg-{{ $c }}">{{ ucwords(str_replace('_',' ',$g->status)) }}</span>
                    </td>
                    <td>
                        @if($g->qc_result)
                            <span class="badge bg-{{ $g->qc_result === 'pass' ? 'success' : ($g->qc_result === 'fail' ? 'danger' : 'warning') }}">{{ ucfirst($g->qc_result) }}</span>
                        @else—@endif
                    </td>
                    <td>
                        <a href="{{ route('grn.show', $g) }}" class="btn btn-xs btn-outline-info me-1"><i class="bi bi-eye"></i></a>
                        @if($g->status !== 'approved')
                            <a href="{{ route('grn.edit', $g) }}" class="btn btn-xs btn-outline-warning me-1"><i class="bi bi-pencil"></i></a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-muted py-4">No GRNs found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3">{{ $grns->links() }}</div>
</div>
@endsection
