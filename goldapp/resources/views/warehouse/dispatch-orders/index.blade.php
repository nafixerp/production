@extends('layouts.app')
@section('title','Dispatch Orders')
@section('content')
<div class="container-fluid px-4 py-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="text-warning mb-0"><i class="bi bi-truck me-2"></i>Dispatch Orders</h4>
    <a href="{{ route('dispatch-orders.create') }}" class="btn btn-warning btn-sm"><i class="bi bi-plus-lg me-1"></i>New DO</a>
  </div>
  @if(session('success'))<div class="alert alert-success py-2">{{ session('success') }}</div>@endif
  @if(session('error'))<div class="alert alert-danger py-2">{{ session('error') }}</div>@endif
  <form class="row g-2 mb-3" method="GET">
    <div class="col-md-3"><input type="text" name="customer" value="{{ request('customer') }}" class="form-control form-control-sm bg-dark text-light border-secondary" placeholder="Customer name…"></div>
    <div class="col-md-2">
      <select name="status" class="form-select form-select-sm bg-dark text-light border-secondary">
        <option value="">All Status</option>
        @foreach(['draft','packed','dispatched','delivered','returned'] as $s)
        <option value="{{ $s }}" {{ request('status')==$s?'selected':'' }}>{{ ucfirst($s) }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-auto"><button class="btn btn-warning btn-sm">Filter</button></div>
  </form>
  <div class="card bg-dark border-secondary">
    <div class="table-responsive">
      <table class="table table-dark table-hover table-sm mb-0">
        <thead class="text-warning">
          <tr><th>DO No</th><th>DO Date</th><th>Customer</th><th>Dispatch Date</th><th>Driver</th><th>Items</th><th>Status</th><th>Actions</th></tr>
        </thead>
        <tbody>
          @php $statusColors=['draft'=>'secondary','packed'=>'info','dispatched'=>'primary','delivered'=>'success','returned'=>'warning'] @endphp
          @forelse($orders as $do)
          <tr>
            <td><code>{{ $do->do_no }}</code></td>
            <td>{{ $do->do_date->format('d/m/Y') }}</td>
            <td>{{ $do->customer_name }}</td>
            <td>{{ $do->dispatch_date?->format('d/m/Y') ?? '—' }}</td>
            <td>{{ $do->driver_name ?? '—' }}</td>
            <td>{{ $do->items->count() }}</td>
            <td><span class="badge bg-{{ $statusColors[$do->status]??'secondary' }}">{{ ucfirst($do->status) }}</span></td>
            <td>
              <a href="{{ route('dispatch-orders.show',$do) }}" class="btn btn-xs btn-outline-info py-0 px-1"><i class="bi bi-eye"></i></a>
              @if(in_array($do->status,['draft','packed']))
              <a href="{{ route('dispatch-orders.edit',$do) }}" class="btn btn-xs btn-outline-warning py-0 px-1"><i class="bi bi-pencil"></i></a>
              @endif
              @if(in_array($do->status,['draft','packed']))
              <form method="POST" action="{{ route('dispatch-orders.dispatch',$do) }}" class="d-inline">
                @csrf
                <button class="btn btn-xs btn-outline-primary py-0 px-1" title="Dispatch"><i class="bi bi-truck"></i></button>
              </form>
              @endif
            </td>
          </tr>
          @empty
          <tr><td colspan="8" class="text-center text-muted py-4">No dispatch orders.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  <div class="mt-3">{{ $orders->links() }}</div>
</div>
@endsection
