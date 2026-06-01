@extends('layouts.app')
@section('title','Dispatch Order')
@section('content')
<div class="container-fluid px-4 py-3">
  <div class="d-flex gap-2 align-items-center mb-3">
    <a href="{{ route('dispatch-orders.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h4 class="text-warning mb-0">Dispatch Order: {{ $dispatchOrder->do_no }}</h4>
    @if(in_array($dispatchOrder->status,['draft','packed']))
    <form method="POST" action="{{ route('dispatch-orders.dispatch',$dispatchOrder) }}" class="ms-auto">
      @csrf
      <button class="btn btn-primary btn-sm"><i class="bi bi-truck me-1"></i>Mark Dispatched</button>
    </form>
    @endif
  </div>
  @if(session('success'))<div class="alert alert-success py-2">{{ session('success') }}</div>@endif
  @if(session('error'))<div class="alert alert-danger py-2">{{ session('error') }}</div>@endif

  <div class="row g-3">
    <div class="col-md-5">
      <div class="card bg-dark border-secondary h-100">
        <div class="card-body">
          <h6 class="text-warning">Order Details</h6>
          <table class="table table-sm table-dark mb-0">
            <tr><td class="text-muted">DO No</td><td><strong>{{ $dispatchOrder->do_no }}</strong></td></tr>
            <tr><td class="text-muted">DO Date</td><td>{{ $dispatchOrder->do_date->format('d/m/Y') }}</td></tr>
            <tr><td class="text-muted">Customer</td><td>{{ $dispatchOrder->customer_name }}</td></tr>
            <tr><td class="text-muted">Delivery Address</td><td>{{ $dispatchOrder->delivery_address }}</td></tr>
            <tr><td class="text-muted">Driver</td><td>{{ $dispatchOrder->driver_name }} {{ $dispatchOrder->driver_phone ? '('.$dispatchOrder->driver_phone.')':'' }}</td></tr>
            <tr><td class="text-muted">Dispatch Date</td><td>{{ $dispatchOrder->dispatch_date?->format('d/m/Y') ?? '—' }}</td></tr>
            <tr><td class="text-muted">Status</td><td><span class="badge bg-info">{{ ucfirst($dispatchOrder->status) }}</span></td></tr>
          </table>
        </div>
      </div>
    </div>
    <div class="col-md-7">
      <div class="card bg-dark border-secondary">
        <div class="card-body">
          <h6 class="text-warning">Items</h6>
          <div class="table-responsive">
            <table class="table table-dark table-sm mb-0">
              <thead class="text-warning"><tr><th>FG</th><th>Batch</th><th class="text-end">Qty</th><th class="text-end">Cost Rate</th><th class="text-end">Cost Amt</th><th class="text-end">Sell Rate</th><th class="text-end">Sell Amt</th></tr></thead>
              <tbody>
                @foreach($dispatchOrder->items as $item)
                <tr>
                  <td>{{ $item->fg_name }}</td>
                  <td><code>{{ $item->batch_no }}</code></td>
                  <td class="text-end">{{ number_format($item->qty,4) }} {{ $item->unit }}</td>
                  <td class="text-end">{{ number_format($item->cost_rate,4) }}</td>
                  <td class="text-end">{{ number_format($item->cost_amount,2) }}</td>
                  <td class="text-end">{{ number_format($item->selling_rate,4) }}</td>
                  <td class="text-end">{{ number_format($item->selling_amount,2) }}</td>
                </tr>
                @endforeach
                <tr class="text-warning fw-bold">
                  <td colspan="4" class="text-end">Total</td>
                  <td class="text-end">{{ number_format($dispatchOrder->items->sum('cost_amount'),2) }}</td>
                  <td></td>
                  <td class="text-end">{{ number_format($dispatchOrder->items->sum('selling_amount'),2) }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  @if($daybookEntries->count())
  <div class="card bg-dark border-secondary mt-3">
    <div class="card-body">
      <h6 class="text-warning">COGS Daybook Entries (Slno: {{ $dispatchOrder->slno }})</h6>
      <div class="table-responsive">
        <table class="table table-dark table-sm mb-0">
          <thead class="text-warning"><tr><th>Account</th><th>Particular</th><th class="text-end">Amount</th><th>Dr/Cr</th></tr></thead>
          <tbody>
            @foreach($daybookEntries as $e)
            <tr>
              <td>{{ $e->account_name }}</td>
              <td>{{ $e->particular }}</td>
              <td class="text-end {{ $e->amount < 0 ? 'text-danger':'text-success' }}">{{ number_format(abs($e->amount),2) }}</td>
              <td><span class="badge bg-{{ $e->amount<0?'danger':'success' }}">{{ $e->amount<0?'Dr':'Cr' }}</span></td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  @endif
</div>
@endsection
