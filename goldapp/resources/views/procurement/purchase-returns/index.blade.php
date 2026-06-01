@extends('layouts.app')
@section('title', 'Purchase Returns')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="text-gold mb-0"><i class="bi bi-arrow-return-left me-2"></i>Purchase Returns</h4>
    <a href="{{ route('purchase-returns-new.create') }}" class="btn btn-gold btn-sm"><i class="bi bi-plus-lg me-1"></i>New Return</a>
</div>

@if(session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif

<div class="card-erp mb-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-3"><input type="text" name="q" value="{{ $q ?? '' }}" class="form-control form-control-sm form-erp" placeholder="Return No / Supplier..."></div>
        <div class="col-md-2"><input type="date" name="from" value="{{ $from ?? '' }}" class="form-control form-control-sm form-erp"></div>
        <div class="col-md-2"><input type="date" name="to" value="{{ $to ?? '' }}" class="form-control form-control-sm form-erp"></div>
        <div class="col-auto"><button class="btn btn-gold btn-sm"><i class="bi bi-search"></i></button></div>
    </form>
</div>

<div class="card-erp">
    <div class="table-responsive">
        <table class="table table-erp table-hover mb-0">
            <thead><tr><th>Return No</th><th>Date</th><th>Supplier</th><th>Reason</th><th class="text-end">Net Amount</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($returns as $r)
                <tr>
                    <td class="text-gold-light fw-bold">{{ $r->slno }}</td>
                    <td>{{ \Carbon\Carbon::parse($r->return_date)->format('d/m/Y') }}</td>
                    <td>{{ $r->supplier_name }}</td>
                    <td class="small text-muted">{{ Str::limit($r->reason, 60) }}</td>
                    <td class="text-end">₹{{ number_format($r->net_amount, 2) }}</td>
                    <td>
                        @php $c = match($r->status) { 'draft'=>'secondary','approved'=>'primary','completed'=>'success',default=>'secondary' }; @endphp
                        <span class="badge bg-{{ $c }}">{{ ucfirst($r->status) }}</span>
                    </td>
                    <td>
                        <a href="{{ route('purchase-returns-new.edit', $r) }}" class="btn btn-xs btn-outline-warning me-1"><i class="bi bi-pencil"></i></a>
                        @if($r->status === 'draft')
                            <form method="POST" action="{{ route('purchase-returns-new.approve', $r->id) }}" class="d-inline">
                                @csrf
                                <button class="btn btn-xs btn-outline-success" title="Approve & Post"><i class="bi bi-check-circle"></i></button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No returns found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3">{{ $returns->links() }}</div>
</div>
@endsection
