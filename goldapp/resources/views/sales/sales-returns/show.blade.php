@extends('layouts.app')
@section('title','Sales Return')
@section('content')
<div class="container-fluid px-4 py-3">
  <div class="d-flex gap-2 align-items-center mb-3">
    <a href="{{ route('sales-returns.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h4 class="text-warning mb-0">Return: {{ $salesReturn->return_no }}</h4>
    <span class="badge bg-{{ $salesReturn->status=='draft'?'secondary':($salesReturn->status=='approved'?'success':'info') }}">{{ ucfirst($salesReturn->status) }}</span>
    @if($salesReturn->status=='draft')
    <form method="POST" action="{{ route('sales-returns.approve',$salesReturn) }}" class="ms-auto">
      @csrf <button class="btn btn-sm btn-success"><i class="bi bi-check2 me-1"></i>Approve & Restore Stock</button>
    </form>
    @endif
  </div>
  @if(session('success'))<div class="alert alert-success py-2">{{ session('success') }}</div>@endif

  <div class="row g-3">
    <div class="col-md-5">
      <div class="card bg-dark border-secondary">
        <div class="card-body">
          <table class="table table-sm table-dark mb-0">
            <tr><td class="text-muted">Customer</td><td>{{ $salesReturn->customer_name }}</td></tr>
            <tr><td class="text-muted">Date</td><td>{{ $salesReturn->return_date->format('d/m/Y') }}</td></tr>
            <tr><td class="text-muted">Channel</td><td>{{ $salesReturn->channel }}</td></tr>
            <tr><td class="text-muted">Refund Mode</td><td>{{ str_replace('_',' ',$salesReturn->refund_mode) }}</td></tr>
            <tr><td class="text-muted">Reason</td><td>{{ $salesReturn->reason }}</td></tr>
            <tr class="text-warning"><td><strong>Net Amount</strong></td><td><strong>₹{{ number_format($salesReturn->net_amount,2) }}</strong></td></tr>
          </table>
        </div>
      </div>
    </div>
    <div class="col-md-7">
      <div class="card bg-dark border-secondary">
        <div class="card-body">
          <h6 class="text-warning">Return Items</h6>
          <table class="table table-dark table-sm mb-0">
            <thead class="text-warning"><tr><th>FG</th><th>Batch</th><th class="text-end">Qty</th><th class="text-end">Rate</th><th class="text-end">Amount</th></tr></thead>
            <tbody>
              @foreach($salesReturn->items as $item)
              <tr>
                <td>{{ $item->fg_name }}</td>
                <td><code>{{ $item->batch_no }}</code></td>
                <td class="text-end">{{ number_format($item->qty,4) }} {{ $item->unit }}</td>
                <td class="text-end">{{ number_format($item->rate,2) }}</td>
                <td class="text-end">{{ number_format($item->amount,2) }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  @if($daybookEntries->count())
  <div class="card bg-dark border-secondary mt-3">
    <div class="card-body">
      <h6 class="text-warning">Reversal Daybook Entries</h6>
      <table class="table table-dark table-sm mb-0">
        <thead class="text-warning"><tr><th>Account</th><th>Particular</th><th class="text-end">Amount</th><th>Dr/Cr</th></tr></thead>
        <tbody>
          @foreach($daybookEntries as $e)
          <tr>
            <td>{{ $e->account_name }}</td>
            <td>{{ $e->particular }}</td>
            <td class="text-end {{ $e->amount<0?'text-danger':'text-success' }}">₹{{ number_format(abs($e->amount),2) }}</td>
            <td><span class="badge bg-{{ $e->amount<0?'danger':'success' }}">{{ $e->amount<0?'Debit':'Credit' }}</span></td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @endif
</div>
@endsection
