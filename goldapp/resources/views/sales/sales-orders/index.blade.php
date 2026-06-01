@extends('layouts.app')
@section('title','Sales Orders')
@section('content')
<div class="container-fluid px-4 py-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="text-warning mb-0"><i class="bi bi-cart3 me-2"></i>Sales Orders</h4>
    <a href="{{ route('sales-orders.create') }}" class="btn btn-warning btn-sm"><i class="bi bi-plus-lg me-1"></i>New SO</a>
  </div>
  @if(session('success'))<div class="alert alert-success py-2">{{ session('success') }}</div>@endif
  <form class="row g-2 mb-3" method="GET">
    <div class="col-md-3"><input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm bg-dark text-light border-secondary" placeholder="Customer / SO No…"></div>
    <div class="col-md-2">
      <select name="status" class="form-select form-select-sm bg-dark text-light border-secondary">
        <option value="">All Status</option>
        @foreach(['open','confirmed','partially_dispatched','dispatched','completed','cancelled'] as $s)
        <option value="{{ $s }}" {{ request('status')==$s?'selected':'' }}>{{ str_replace('_',' ',ucfirst($s)) }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-auto"><button class="btn btn-warning btn-sm">Filter</button></div>
  </form>
  <div class="card bg-dark border-secondary">
    <div class="table-responsive">
      <table class="table table-dark table-hover table-sm mb-0">
        <thead class="text-warning">
          <tr><th>SO No</th><th>Date</th><th>Customer</th><th>Delivery</th><th>Channel</th><th class="text-end">Net Amt</th><th>Dispatch Progress</th><th>Status</th><th>Actions</th></tr>
        </thead>
        @php $statusColors=['open'=>'secondary','confirmed'=>'info','partially_dispatched'=>'warning','dispatched'=>'primary','completed'=>'success','cancelled'=>'danger'] @endphp
        <tbody>
          @forelse($orders as $so)
          @php
            $ordered    = $so->items->sum('ordered_qty');
            $dispatched = $so->items->sum('dispatched_qty');
            $progress   = $ordered > 0 ? round(($dispatched / $ordered) * 100) : 0;
          @endphp
          <tr>
            <td><code>{{ $so->so_no }}</code></td>
            <td>{{ $so->so_date->format('d/m/Y') }}</td>
            <td>{{ $so->customer_name }}</td>
            <td>{{ $so->delivery_date?->format('d/m/Y') ?? '—' }}</td>
            <td>{{ $so->channel }}</td>
            <td class="text-end">₹{{ number_format($so->net_amount,2) }}</td>
            <td style="min-width:150px">
              <div class="progress" style="height:14px">
                <div class="progress-bar bg-success" style="width:{{ $progress }}%">{{ $progress }}%</div>
              </div>
            </td>
            <td><span class="badge bg-{{ $statusColors[$so->status]??'secondary' }}">{{ str_replace('_',' ',ucfirst($so->status)) }}</span></td>
            <td>
              <a href="{{ route('sales-orders.show',$so) }}" class="btn btn-xs btn-outline-info py-0 px-1"><i class="bi bi-eye"></i></a>
              <a href="{{ route('sales-orders.edit',$so) }}" class="btn btn-xs btn-outline-warning py-0 px-1"><i class="bi bi-pencil"></i></a>
            </td>
          </tr>
          @empty
          <tr><td colspan="9" class="text-center text-muted py-4">No orders.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  <div class="mt-3">{{ $orders->links() }}</div>
</div>
@endsection
