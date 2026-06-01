@extends('layouts.app')
@section('title','Sales Order')
@section('content')
<div class="container-fluid px-4 py-3">
  <div class="d-flex gap-2 align-items-center mb-3">
    <a href="{{ route('sales-orders.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h4 class="text-warning mb-0">Sales Order: {{ $salesOrder->so_no }}</h4>
    @if($salesOrder->status == 'open')
    <form method="POST" action="{{ route('sales-orders.confirm',$salesOrder) }}" class="ms-auto">
      @csrf <button class="btn btn-sm btn-success"><i class="bi bi-check2 me-1"></i>Confirm Order</button>
    </form>
    @endif
  </div>
  @if(session('success'))<div class="alert alert-success py-2">{{ session('success') }}</div>@endif

  <div class="row g-3">
    <div class="col-md-4">
      <div class="card bg-dark border-secondary">
        <div class="card-body">
          <h6 class="text-warning">Order Info</h6>
          <table class="table table-sm table-dark mb-0">
            <tr><td class="text-muted">SO No</td><td><strong>{{ $salesOrder->so_no }}</strong></td></tr>
            <tr><td class="text-muted">Date</td><td>{{ $salesOrder->so_date->format('d/m/Y') }}</td></tr>
            <tr><td class="text-muted">Customer</td><td>{{ $salesOrder->customer_name }}</td></tr>
            <tr><td class="text-muted">Channel</td><td>{{ $salesOrder->channel }}</td></tr>
            <tr><td class="text-muted">Delivery Date</td><td>{{ $salesOrder->delivery_date?->format('d/m/Y') ?? '—' }}</td></tr>
            <tr><td class="text-muted">Payment Terms</td><td>{{ $salesOrder->payment_terms }}</td></tr>
            <tr><td class="text-muted">Status</td><td><span class="badge bg-info">{{ str_replace('_',' ',ucfirst($salesOrder->status)) }}</span></td></tr>
          </table>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card bg-dark border-secondary h-100">
        <div class="card-body">
          <h6 class="text-warning">Amounts</h6>
          <table class="table table-sm table-dark mb-0">
            <tr><td class="text-muted">Taxable</td><td class="text-end">₹{{ number_format($salesOrder->taxable_amount,2) }}</td></tr>
            <tr><td class="text-muted">Discount</td><td class="text-end">₹{{ number_format($salesOrder->discount,2) }}</td></tr>
            <tr><td class="text-muted">SGST</td><td class="text-end">₹{{ number_format($salesOrder->sgst,2) }}</td></tr>
            <tr><td class="text-muted">CGST</td><td class="text-end">₹{{ number_format($salesOrder->cgst,2) }}</td></tr>
            <tr><td class="text-muted">IGST</td><td class="text-end">₹{{ number_format($salesOrder->igst,2) }}</td></tr>
            <tr class="text-warning"><td><strong>Net Amount</strong></td><td class="text-end"><strong>₹{{ number_format($salesOrder->net_amount,2) }}</strong></td></tr>
            <tr><td class="text-muted">Advance</td><td class="text-end">₹{{ number_format($salesOrder->advance_received,2) }}</td></tr>
            <tr class="text-danger"><td><strong>Balance</strong></td><td class="text-end"><strong>₹{{ number_format($salesOrder->balance_amount,2) }}</strong></td></tr>
          </table>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card bg-dark border-secondary h-100">
        <div class="card-body">
          <h6 class="text-warning">Dispatch Progress: {{ $progress }}%</h6>
          <div class="progress mb-3" style="height:20px">
            <div class="progress-bar bg-success" style="width:{{ $progress }}%">{{ $progress }}%</div>
          </div>
          <h6 class="text-warning mt-3">Dispatch Orders</h6>
          @forelse($salesOrder->dispatches as $do)
          <div class="d-flex justify-content-between small mb-1">
            <a href="{{ route('dispatch-orders.show',$do) }}" class="text-warning">{{ $do->do_no }}</a>
            <span class="badge bg-info">{{ $do->status }}</span>
          </div>
          @empty
          <p class="text-muted small">No dispatch orders yet.</p>
          @endforelse
        </div>
      </div>
    </div>
  </div>

  <div class="card bg-dark border-secondary mt-3">
    <div class="card-body">
      <h6 class="text-warning">Order Items</h6>
      <div class="table-responsive">
        <table class="table table-dark table-sm mb-0">
          <thead class="text-warning"><tr><th>FG</th><th>HSN</th><th>Unit</th><th class="text-end">Ordered</th><th class="text-end">Dispatched</th><th class="text-end">Pending</th><th class="text-end">Rate</th><th class="text-end">Net Amt</th></tr></thead>
          <tbody>
            @foreach($salesOrder->items as $item)
            <tr>
              <td>{{ $item->fg_name }}</td>
              <td>{{ $item->hsn_code }}</td>
              <td>{{ $item->unit }}</td>
              <td class="text-end">{{ number_format($item->ordered_qty,4) }}</td>
              <td class="text-end text-success">{{ number_format($item->dispatched_qty,4) }}</td>
              <td class="text-end text-warning">{{ number_format($item->pending_qty,4) }}</td>
              <td class="text-end">{{ number_format($item->rate,2) }}</td>
              <td class="text-end fw-bold">{{ number_format($item->net_amount,2) }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
