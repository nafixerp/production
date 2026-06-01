@extends('layouts.app')
@section('title','Customers')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-people-fill me-2"></i>Customers</h5>
    <a href="{{ route('customers.create') }}" class="btn btn-sm btn-gold"><i class="bi bi-plus-lg me-1"></i>New Customer</a>
</div>

<div class="card-dark mb-3">
    <form method="GET" class="row g-2">
        <div class="col-md-4"><input type="text" name="search" class="form-control form-control-sm" placeholder="Search name, code, phone..." value="{{ request('search') }}"></div>
        <div class="col-md-2">
            <select name="customer_type" class="form-select form-select-sm">
                <option value="">All Types</option>
                @foreach(['retail','wholesale','distributor','online','export'] as $t)
                    <option value="{{ $t }}" {{ request('customer_type')===$t?'selected':'' }}>{{ ucfirst($t) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select form-select-sm">
                <option value="">All Status</option>
                <option value="1" {{ request('status')==='1'?'selected':'' }}>Active</option>
                <option value="0" {{ request('status')==='0'?'selected':'' }}>Inactive</option>
            </select>
        </div>
        <div class="col-md-2"><button type="submit" class="btn btn-sm btn-outline-gold w-100">Filter</button></div>
    </form>
</div>

@if(session('success'))<div class="alert alert-success alert-sm">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger alert-sm">{{ session('error') }}</div>@endif

<div class="table-responsive">
<table class="table table-dark-erp table-hover">
    <thead>
        <tr>
            <th>Code</th><th>Name</th><th>Type</th><th>Phone</th><th>City</th>
            <th>Credit Limit</th><th>Outstanding</th><th>Points</th><th>Status</th><th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($customers as $c)
        <tr>
            <td><span class="badge bg-secondary">{{ $c->code }}</span></td>
            <td>
                <a href="{{ route('customers.show', $c->id) }}" class="text-gold-link">{{ $c->name }}</a>
                @if($c->contact_person)<br><small class="text-muted">{{ $c->contact_person }}</small>@endif
            </td>
            <td><span class="badge badge-type-{{ $c->customer_type }}">{{ ucfirst($c->customer_type) }}</span></td>
            <td>{{ $c->phone }}</td>
            <td>{{ $c->city }}</td>
            <td class="text-end">{{ number_format($c->credit_limit, 2) }}</td>
            <td class="text-end {{ $c->outstanding_balance > $c->credit_limit && $c->credit_limit > 0 ? 'text-danger fw-bold' : '' }}">
                {{ number_format($c->outstanding_balance, 2) }}
                @if($c->outstanding_balance > $c->credit_limit && $c->credit_limit > 0)
                    <i class="bi bi-exclamation-triangle-fill text-danger" title="Over credit limit"></i>
                @endif
            </td>
            <td class="text-center">{{ $c->loyalty_points }}</td>
            <td><span class="badge {{ $c->status ? 'bg-success' : 'bg-danger' }}">{{ $c->status ? 'Active' : 'Inactive' }}</span></td>
            <td>
                <a href="{{ route('customers.edit', $c->id) }}" class="btn btn-xs btn-outline-warning"><i class="bi bi-pencil"></i></a>
                <form method="POST" action="{{ route('customers.destroy', $c->id) }}" class="d-inline" onsubmit="return confirm('Delete?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-xs btn-outline-danger"><i class="bi bi-trash"></i></button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="10" class="text-center text-muted">No customers found.</td></tr>
        @endforelse
    </tbody>
</table>
</div>
{{ $customers->links() }}

<style>
.badge-type-retail{background:#3498db}.badge-type-wholesale{background:#2ecc71}.badge-type-distributor{background:#9b59b6}
.badge-type-online{background:#e67e22}.badge-type-export{background:#e74c3c}
.text-gold-link{color:var(--gold);text-decoration:none}.text-gold-link:hover{color:var(--gold-light)}
.btn-xs{padding:2px 6px;font-size:.72rem}
</style>
@endsection
