@extends('layouts.app')
@section('title', 'Purchase Orders')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="text-gold mb-0"><i class="bi bi-cart-plus-fill me-2"></i>Purchase Orders</h4>
    <a href="{{ route('purchase-orders.create') }}" class="btn btn-gold btn-sm"><i class="bi bi-plus-lg me-1"></i>New PO</a>
</div>

@if(session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
@if(session('error'))<div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif

<div class="card-erp mb-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-3"><input type="text" name="q" value="{{ $q ?? '' }}" class="form-control form-control-sm form-erp" placeholder="PO No / Supplier..."></div>
        <div class="col-md-2">
            <select name="status" class="form-select form-select-sm form-erp">
                <option value="">All Status</option>
                @foreach(['draft','approved','partially_received','received','cancelled'] as $st)
                    <option value="{{ $st }}" {{ ($status ?? '') === $st ? 'selected' : '' }}>{{ ucwords(str_replace('_',' ',$st)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2"><input type="date" name="from" value="{{ $from ?? '' }}" class="form-control form-control-sm form-erp"></div>
        <div class="col-md-2"><input type="date" name="to" value="{{ $to ?? '' }}" class="form-control form-control-sm form-erp"></div>
        <div class="col-auto">
            <button class="btn btn-gold btn-sm"><i class="bi bi-search"></i></button>
            <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-secondary btn-sm ms-1">Clear</a>
        </div>
    </form>
</div>

<div class="card-erp">
    <div class="table-responsive">
        <table class="table table-erp table-hover mb-0">
            <thead>
                <tr>
                    <th>PO No</th>
                    <th>Date</th>
                    <th>Supplier</th>
                    <th>Delivery Date</th>
                    <th class="text-end">Net Amount</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($orders as $o)
                <tr>
                    <td><a href="{{ route('purchase-orders.show', $o) }}" class="text-gold-light fw-bold">{{ $o->slno }}</a></td>
                    <td>{{ \Carbon\Carbon::parse($o->po_date)->format('d/m/Y') }}</td>
                    <td>{{ $o->supplier_name }}</td>
                    <td>{{ $o->delivery_date ? \Carbon\Carbon::parse($o->delivery_date)->format('d/m/Y') : '—' }}</td>
                    <td class="text-end">₹{{ number_format($o->net_amount, 2) }}</td>
                    <td>
                        @php
                            $color = match($o->status) {
                                'draft' => 'secondary', 'approved' => 'primary',
                                'partially_received' => 'warning', 'received' => 'success', 'cancelled' => 'danger', default => 'secondary'
                            };
                        @endphp
                        <span class="badge bg-{{ $color }}">{{ ucwords(str_replace('_',' ',$o->status)) }}</span>
                    </td>
                    <td>
                        <a href="{{ route('purchase-orders.show', $o) }}" class="btn btn-xs btn-outline-info me-1"><i class="bi bi-eye"></i></a>
                        @if($o->status === 'draft')
                            <a href="{{ route('purchase-orders.edit', $o) }}" class="btn btn-xs btn-outline-warning me-1"><i class="bi bi-pencil"></i></a>
                            <form method="POST" action="{{ route('purchase-orders.approve', $o->id) }}" class="d-inline">
                                @csrf
                                <button class="btn btn-xs btn-outline-success me-1" title="Approve"><i class="bi bi-check-circle"></i></button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No purchase orders found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3">{{ $orders->links() }}</div>
</div>
@endsection
