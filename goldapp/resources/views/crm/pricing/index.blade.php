@extends('layouts.app')
@section('title','Customer Pricing')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-tag me-2"></i>Customer Pricing Rules</h5>
    <a href="{{ route('customer-pricing.create') }}" class="btn btn-sm btn-gold"><i class="bi bi-plus-lg me-1"></i>New Rule</a>
</div>

<div class="card-dark mb-3">
    <form method="GET" class="row g-2">
        <div class="col-md-4">
            <select name="customer_id" class="form-select form-select-sm">
                <option value="">All Customers</option>
                @foreach($customers as $c)
                <option value="{{ $c->id }}" {{ request('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
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

<div class="table-responsive">
<table class="table table-dark-erp table-hover">
    <thead>
        <tr><th>Customer</th><th>Product</th><th>Type</th><th>Price / Discount</th><th>Min Qty</th><th>Valid From</th><th>Valid To</th><th>Status</th><th>Actions</th></tr>
    </thead>
    <tbody>
        @forelse($pricing as $p)
        <tr>
            <td>{{ $p->customer->name ?? '-' }}</td>
            <td>{{ $p->finishedGood->name ?? '-' }}</td>
            <td><span class="badge bg-info">{{ ucfirst(str_replace('_',' ',$p->price_type)) }}</span></td>
            <td>
                @if($p->price_type === 'fixed') ₹{{ number_format($p->price,2) }}
                @else {{ $p->discount_pct }}%
                @endif
            </td>
            <td>{{ $p->min_qty }}</td>
            <td>{{ $p->valid_from->format('d M Y') }}</td>
            <td>{{ $p->valid_to ? $p->valid_to->format('d M Y') : 'Open' }}</td>
            <td><span class="badge {{ $p->status ? 'bg-success' : 'bg-danger' }}">{{ $p->status ? 'Active' : 'Inactive' }}</span></td>
            <td>
                <a href="{{ route('customer-pricing.edit',$p->id) }}" class="btn btn-xs btn-outline-warning"><i class="bi bi-pencil"></i></a>
                <form method="POST" action="{{ route('customer-pricing.destroy',$p->id) }}" class="d-inline" onsubmit="return confirm('Delete?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-xs btn-outline-danger"><i class="bi bi-trash"></i></button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="9" class="text-center text-muted">No pricing rules.</td></tr>
        @endforelse
    </tbody>
</table>
</div>
{{ $pricing->links() }}
<style>.btn-xs{padding:2px 6px;font-size:.7rem}</style>
@endsection
