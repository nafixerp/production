@extends('layouts.app')
@section('title','Sales Quotations')
@section('content')
<div class="container-fluid px-4 py-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="text-warning mb-0"><i class="bi bi-file-earmark-text me-2"></i>Sales Quotations</h4>
    <a href="{{ route('sales-quotations.create') }}" class="btn btn-warning btn-sm"><i class="bi bi-plus-lg me-1"></i>New Quotation</a>
  </div>
  @if(session('success'))<div class="alert alert-success py-2">{{ session('success') }}</div>@endif
  <form class="row g-2 mb-3" method="GET">
    <div class="col-md-3"><input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm bg-dark text-light border-secondary" placeholder="Customer / Quot No…"></div>
    <div class="col-md-2">
      <select name="status" class="form-select form-select-sm bg-dark text-light border-secondary">
        <option value="">All Status</option>
        @foreach(['draft','sent','accepted','rejected','expired'] as $s)
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
          <tr><th>Quot No</th><th>Date</th><th>Valid Till</th><th>Customer</th><th>Channel</th><th class="text-end">Net Amt</th><th>Status</th><th>Actions</th></tr>
        </thead>
        @php $statusColors=['draft'=>'secondary','sent'=>'info','accepted'=>'success','rejected'=>'danger','expired'=>'warning'] @endphp
        <tbody>
          @forelse($quotations as $q)
          <tr>
            <td><code>{{ $q->quot_no }}</code></td>
            <td>{{ $q->quot_date->format('d/m/Y') }}</td>
            <td>{{ $q->valid_till?->format('d/m/Y') ?? '—' }}</td>
            <td>{{ $q->customer_name }}</td>
            <td>{{ $q->channel }}</td>
            <td class="text-end">₹{{ number_format($q->net_amount,2) }}</td>
            <td><span class="badge bg-{{ $statusColors[$q->status]??'secondary' }}">{{ ucfirst($q->status) }}</span></td>
            <td>
              <a href="{{ route('sales-quotations.show',$q) }}" class="btn btn-xs btn-outline-info py-0 px-1"><i class="bi bi-eye"></i></a>
              <a href="{{ route('sales-quotations.edit',$q) }}" class="btn btn-xs btn-outline-warning py-0 px-1"><i class="bi bi-pencil"></i></a>
              @if(!in_array($q->status,['accepted','rejected']))
              <form method="POST" action="{{ route('sales-quotations.convert-so',$q) }}" class="d-inline">
                @csrf
                <button class="btn btn-xs btn-outline-success py-0 px-1" title="Convert to SO"><i class="bi bi-arrow-right-circle"></i></button>
              </form>
              @endif
            </td>
          </tr>
          @empty
          <tr><td colspan="8" class="text-center text-muted py-4">No quotations.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  <div class="mt-3">{{ $quotations->links() }}</div>
</div>
@endsection
