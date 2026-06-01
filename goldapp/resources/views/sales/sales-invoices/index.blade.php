@extends('layouts.app')
@section('title','Sales Invoices')
@section('content')
<div class="container-fluid px-4 py-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="text-warning mb-0"><i class="bi bi-receipt me-2"></i>Sales Invoices</h4>
    <a href="{{ route('sales-invoices.create') }}" class="btn btn-warning btn-sm"><i class="bi bi-plus-lg me-1"></i>New Invoice</a>
  </div>
  @if(session('success'))<div class="alert alert-success py-2">{{ session('success') }}</div>@endif
  <form class="row g-2 mb-3" method="GET">
    <div class="col-md-3"><input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm bg-dark text-light border-secondary" placeholder="Customer / Invoice No…"></div>
    <div class="col-md-2">
      <select name="channel" class="form-select form-select-sm bg-dark text-light border-secondary">
        <option value="">All Channels</option>
        @foreach(['retail','wholesale','distributor','online','export'] as $ch)
        <option value="{{ $ch }}" {{ request('channel')==$ch?'selected':'' }}>{{ ucfirst($ch) }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-2"><input type="date" name="from" value="{{ request('from') }}" class="form-control form-control-sm bg-dark text-light border-secondary" placeholder="From"></div>
    <div class="col-md-2"><input type="date" name="to" value="{{ request('to') }}" class="form-control form-control-sm bg-dark text-light border-secondary" placeholder="To"></div>
    <div class="col-auto"><button class="btn btn-warning btn-sm">Filter</button></div>
  </form>
  <div class="card bg-dark border-secondary">
    <div class="table-responsive">
      <table class="table table-dark table-hover table-sm mb-0">
        <thead class="text-warning">
          <tr><th>Invoice No</th><th>Date</th><th>Customer</th><th>Channel</th>
            <th class="text-end">Taxable</th><th class="text-end">Tax</th>
            <th class="text-end">Net Amt</th><th class="text-end">Received</th>
            <th class="text-end">Balance</th><th>Mode</th><th>Status</th><th>Actions</th></tr>
        </thead>
        @php $statusColors=['draft'=>'secondary','posted'=>'info','paid'=>'success','partial'=>'warning','cancelled'=>'danger'] @endphp
        <tbody>
          @forelse($invoices as $inv)
          <tr>
            <td><code>{{ $inv->invoice_no }}</code></td>
            <td>{{ $inv->invoice_date->format('d/m/Y') }}</td>
            <td>{{ $inv->customer_name }}</td>
            <td>{{ $inv->channel }}</td>
            <td class="text-end">{{ number_format($inv->taxable_amount,2) }}</td>
            <td class="text-end">{{ number_format($inv->sgst+$inv->cgst+$inv->igst,2) }}</td>
            <td class="text-end fw-bold">₹{{ number_format($inv->net_amount,2) }}</td>
            <td class="text-end text-success">{{ number_format($inv->received_amount,2) }}</td>
            <td class="text-end {{ $inv->balance_amount>0?'text-danger':'text-success' }}">{{ number_format($inv->balance_amount,2) }}</td>
            <td><span class="badge bg-secondary">{{ $inv->payment_mode }}</span></td>
            <td><span class="badge bg-{{ $statusColors[$inv->status]??'secondary' }}">{{ ucfirst($inv->status) }}</span></td>
            <td>
              <a href="{{ route('sales-invoices.show',$inv) }}" class="btn btn-xs btn-outline-info py-0 px-1"><i class="bi bi-eye"></i></a>
              <a href="{{ route('sales-invoices.edit',$inv) }}" class="btn btn-xs btn-outline-warning py-0 px-1"><i class="bi bi-pencil"></i></a>
              <form method="POST" action="{{ route('sales-invoices.destroy',$inv) }}" class="d-inline" onsubmit="return confirm('Cancel invoice?')">
                @csrf @method('DELETE')
                <button class="btn btn-xs btn-outline-danger py-0 px-1"><i class="bi bi-x-circle"></i></button>
              </form>
            </td>
          </tr>
          @empty
          <tr><td colspan="12" class="text-center text-muted py-4">No invoices found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  <div class="mt-3">{{ $invoices->links() }}</div>
</div>
@endsection
