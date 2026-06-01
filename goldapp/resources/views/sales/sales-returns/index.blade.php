@extends('layouts.app')
@section('title','Sales Returns')
@section('content')
<div class="container-fluid px-4 py-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="text-warning mb-0"><i class="bi bi-arrow-return-left me-2"></i>Sales Returns</h4>
    <a href="{{ route('sales-returns.create') }}" class="btn btn-warning btn-sm"><i class="bi bi-plus-lg me-1"></i>New Return</a>
  </div>
  @if(session('success'))<div class="alert alert-success py-2">{{ session('success') }}</div>@endif
  <form class="row g-2 mb-3" method="GET">
    <div class="col-md-3"><input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm bg-dark text-light border-secondary" placeholder="Customer…"></div>
    <div class="col-md-2">
      <select name="status" class="form-select form-select-sm bg-dark text-light border-secondary">
        <option value="">All</option>
        @foreach(['draft','approved','completed'] as $s)
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
          <tr><th>Return No</th><th>Date</th><th>Customer</th><th>Reason</th><th class="text-end">Net Amt</th><th>Refund Mode</th><th>Status</th><th>Actions</th></tr>
        </thead>
        <tbody>
          @forelse($returns as $r)
          <tr>
            <td><code>{{ $r->return_no }}</code></td>
            <td>{{ $r->return_date->format('d/m/Y') }}</td>
            <td>{{ $r->customer_name }}</td>
            <td>{{ Str::limit($r->reason,40) }}</td>
            <td class="text-end">₹{{ number_format($r->net_amount,2) }}</td>
            <td>{{ str_replace('_',' ',$r->refund_mode) }}</td>
            <td><span class="badge bg-{{ $r->status=='draft'?'secondary':($r->status=='approved'?'success':'info') }}">{{ ucfirst($r->status) }}</span></td>
            <td>
              <a href="{{ route('sales-returns.show',$r) }}" class="btn btn-xs btn-outline-info py-0 px-1"><i class="bi bi-eye"></i></a>
              @if($r->status=='draft')
              <a href="{{ route('sales-returns.edit',$r) }}" class="btn btn-xs btn-outline-warning py-0 px-1"><i class="bi bi-pencil"></i></a>
              <form method="POST" action="{{ route('sales-returns.approve',$r) }}" class="d-inline">
                @csrf <button class="btn btn-xs btn-outline-success py-0 px-1" title="Approve"><i class="bi bi-check2"></i></button>
              </form>
              @endif
            </td>
          </tr>
          @empty
          <tr><td colspan="8" class="text-center text-muted py-4">No returns.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  <div class="mt-3">{{ $returns->links() }}</div>
</div>
@endsection
