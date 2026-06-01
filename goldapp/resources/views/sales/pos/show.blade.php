@extends('layouts.app')
@section('title','POS Session')
@section('content')
<div class="container-fluid px-4 py-3">
  <div class="d-flex gap-2 align-items-center mb-3">
    <a href="{{ route('pos.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h4 class="text-warning mb-0">Session: {{ $posSession->session_no }}</h4>
    <span class="badge bg-{{ $posSession->status=='open'?'success':'secondary' }}">{{ ucfirst($posSession->status) }}</span>
  </div>
  <div class="row g-3 mb-3">
    <div class="col-md-3"><div class="card bg-dark border-secondary text-center p-3"><h6 class="text-muted">Total Sales</h6><h4 class="text-warning">₹{{ number_format($posSession->total_sales,2) }}</h4></div></div>
    <div class="col-md-3"><div class="card bg-dark border-secondary text-center p-3"><h6 class="text-muted">Bills</h6><h4 class="text-warning">{{ $posSession->bills->count() }}</h4></div></div>
    <div class="col-md-3"><div class="card bg-dark border-secondary text-center p-3"><h6 class="text-muted">Opening Cash</h6><h4 class="text-warning">₹{{ number_format($posSession->opening_cash,2) }}</h4></div></div>
    <div class="col-md-3"><div class="card bg-dark border-secondary text-center p-3"><h6 class="text-muted">Closing Cash</h6><h4 class="text-warning">{{ $posSession->closing_cash !== null ? '₹'.number_format($posSession->closing_cash,2) : '—' }}</h4></div></div>
  </div>
  <div class="card bg-dark border-secondary">
    <div class="card-header text-warning">Bills</div>
    <div class="table-responsive">
      <table class="table table-dark table-sm mb-0">
        <thead class="text-warning"><tr><th>Bill No</th><th>Customer</th><th>Payment</th><th class="text-end">Net Amt</th><th class="text-end">Received</th><th class="text-end">Change</th></tr></thead>
        <tbody>
          @foreach($posSession->bills as $b)
          <tr>
            <td><code>{{ $b->bill_no }}</code></td>
            <td>{{ $b->customer_name }}</td>
            <td>{{ $b->payment_mode }}</td>
            <td class="text-end">₹{{ number_format($b->net_amount,2) }}</td>
            <td class="text-end">₹{{ number_format($b->received_amount,2) }}</td>
            <td class="text-end">₹{{ number_format($b->change_amount,2) }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
