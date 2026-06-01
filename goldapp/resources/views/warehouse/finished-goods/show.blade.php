@extends('layouts.app')
@section('title','FG Detail')
@section('content')
<div class="container-fluid px-4 py-3">
  <div class="d-flex gap-2 align-items-center mb-3">
    <a href="{{ route('finished-goods.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h4 class="text-warning mb-0">{{ $finishedGood->name }}</h4>
    <span class="badge bg-{{ $finishedGood->status?'success':'secondary' }}">{{ $finishedGood->status?'Active':'Inactive' }}</span>
    <a href="{{ route('finished-goods.edit',$finishedGood) }}" class="btn btn-sm btn-warning ms-auto"><i class="bi bi-pencil me-1"></i>Edit</a>
  </div>
  <div class="row g-3">
    <div class="col-md-4">
      <div class="card bg-dark border-secondary h-100">
        <div class="card-body">
          <h6 class="text-warning">Basic Info</h6>
          <table class="table table-sm table-dark mb-0">
            <tr><td class="text-muted">Code</td><td>{{ $finishedGood->code }}</td></tr>
            <tr><td class="text-muted">Category</td><td>{{ $finishedGood->category }}</td></tr>
            <tr><td class="text-muted">Sub-Category</td><td>{{ $finishedGood->sub_category }}</td></tr>
            <tr><td class="text-muted">Unit</td><td>{{ $finishedGood->unit?->name }}</td></tr>
            <tr><td class="text-muted">HSN Code</td><td>{{ $finishedGood->hsn_code }}</td></tr>
            <tr><td class="text-muted">Barcode</td><td>{{ $finishedGood->barcode }}</td></tr>
            <tr><td class="text-muted">SKU</td><td>{{ $finishedGood->sku }}</td></tr>
          </table>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card bg-dark border-secondary h-100">
        <div class="card-body">
          <h6 class="text-warning">Pricing</h6>
          <table class="table table-sm table-dark mb-0">
            <tr><td class="text-muted">MRP</td><td class="text-end">₹{{ number_format($finishedGood->mrp,2) }}</td></tr>
            <tr><td class="text-muted">Selling</td><td class="text-end">₹{{ number_format($finishedGood->selling_price,2) }}</td></tr>
            <tr><td class="text-muted">Wholesale</td><td class="text-end">₹{{ number_format($finishedGood->wholesale_price,2) }}</td></tr>
            <tr><td class="text-muted">Distributor</td><td class="text-end">₹{{ number_format($finishedGood->distributor_price,2) }}</td></tr>
            <tr><td class="text-muted">Online</td><td class="text-end">₹{{ number_format($finishedGood->online_price,2) }}</td></tr>
            <tr><td class="text-muted">Cost</td><td class="text-end text-warning">₹{{ number_format($finishedGood->cost_price,2) }}</td></tr>
          </table>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card bg-dark border-secondary h-100">
        <div class="card-body">
          <h6 class="text-warning">Current Stock: <span class="text-white">{{ number_format($totalStock,4) }}</span></h6>
          @php $expiryMap = ['available'=>'success','expired'=>'danger','on_hold'=>'warning','dispatched'=>'secondary'] @endphp
          <div class="table-responsive mt-2">
            <table class="table table-sm table-dark mb-0">
              <thead><tr class="text-warning"><th>Batch</th><th>Warehouse</th><th>Expiry</th><th class="text-end">Available</th><th>Status</th></tr></thead>
              <tbody>
                @foreach($stocks as $s)
                @php $avail = $s->qty_in - $s->qty_out - $s->qty_reserved; @endphp
                <tr>
                  <td><code>{{ $s->batch_no }}</code></td>
                  <td>{{ $s->warehouse?->name }}</td>
                  <td>
                    @if($s->expiry_date)
                    @php $days = now()->diffInDays($s->expiry_date, false) @endphp
                    <span class="{{ $days<0?'text-danger':($days<=30?'text-warning':'text-success') }}">
                      {{ $s->expiry_date->format('d/m/Y') }}
                    </span>
                    @else—@endif
                  </td>
                  <td class="text-end">{{ number_format($avail,4) }}</td>
                  <td><span class="badge bg-{{ $expiryMap[$s->status]??'secondary' }}">{{ $s->status }}</span></td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
