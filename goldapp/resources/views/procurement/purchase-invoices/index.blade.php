@extends('layouts.app')
@section('title', 'Purchase Invoices')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="text-gold mb-0"><i class="bi bi-receipt me-2"></i>Purchase Invoices</h4>
    <a href="{{ route('purchase-invoices-new.create') }}" class="btn btn-gold btn-sm"><i class="bi bi-plus-lg me-1"></i>New Invoice</a>
</div>

@if(session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif

<div class="card-erp mb-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-3"><input type="text" name="q" value="{{ $q ?? '' }}" class="form-control form-control-sm form-erp" placeholder="Invoice No / Supplier..."></div>
        <div class="col-md-2">
            <select name="status" class="form-select form-select-sm form-erp">
                <option value="">All Status</option>
                @foreach(['draft','posted','paid','partial','cancelled'] as $st)
                    <option value="{{ $st }}" {{ ($status ?? '') === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2"><input type="date" name="from" value="{{ $from ?? '' }}" class="form-control form-control-sm form-erp"></div>
        <div class="col-md-2"><input type="date" name="to" value="{{ $to ?? '' }}" class="form-control form-control-sm form-erp"></div>
        <div class="col-auto">
            <button class="btn btn-gold btn-sm"><i class="bi bi-search"></i></button>
            <a href="{{ route('purchase-invoices-new.index') }}" class="btn btn-outline-secondary btn-sm ms-1">Clear</a>
        </div>
    </form>
</div>

<div class="card-erp">
    <div class="table-responsive">
        <table class="table table-erp table-hover mb-0">
            <thead>
                <tr><th>Invoice No</th><th>Date</th><th>Supplier</th><th>Supplier Bill No</th><th class="text-end">Net Amount</th><th class="text-end">Paid</th><th class="text-end">Balance</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
            @forelse($invoices as $inv)
                <tr>
                    <td><a href="{{ route('purchase-invoices-new.show', $inv) }}" class="text-gold-light fw-bold">{{ $inv->slno }}</a></td>
                    <td>{{ \Carbon\Carbon::parse($inv->invoice_date)->format('d/m/Y') }}</td>
                    <td>{{ $inv->supplier_name }}</td>
                    <td>{{ $inv->supplier_bill_no ?? '—' }}</td>
                    <td class="text-end">₹{{ number_format($inv->net_amount, 2) }}</td>
                    <td class="text-end text-success">₹{{ number_format($inv->paid_amount, 2) }}</td>
                    <td class="text-end {{ $inv->balance_amount > 0 ? 'text-warning' : 'text-muted' }}">₹{{ number_format($inv->balance_amount, 2) }}</td>
                    <td>
                        @php $c = match($inv->status) { 'posted'=>'primary','paid'=>'success','partial'=>'warning','cancelled'=>'danger',default=>'secondary' }; @endphp
                        <span class="badge bg-{{ $c }}">{{ ucfirst($inv->status) }}</span>
                    </td>
                    <td>
                        <a href="{{ route('purchase-invoices-new.show', $inv) }}" class="btn btn-xs btn-outline-info me-1"><i class="bi bi-eye"></i></a>
                        @if(!in_array($inv->status, ['cancelled']))
                            <a href="{{ route('purchase-invoices-new.edit', $inv) }}" class="btn btn-xs btn-outline-warning me-1"><i class="bi bi-pencil"></i></a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="9" class="text-center text-muted py-4">No invoices found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3">{{ $invoices->links() }}</div>
</div>
@endsection
