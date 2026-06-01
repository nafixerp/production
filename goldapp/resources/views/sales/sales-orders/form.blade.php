@extends('layouts.app')
@section('title', isset($salesOrder) ? 'Edit Sales Order' : 'New Sales Order')
@section('content')
<div class="container-fluid px-4 py-3">
  <div class="d-flex gap-2 align-items-center mb-3">
    <a href="{{ route('sales-orders.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h4 class="text-warning mb-0">{{ isset($salesOrder) ? 'Edit: '.$salesOrder->so_no : 'New Sales Order ('.$slno.')' }}</h4>
  </div>
  @if($errors->any())<div class="alert alert-danger py-2"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{$e}}</li>@endforeach</ul></div>@endif
  <form method="POST" action="{{ isset($salesOrder) ? route('sales-orders.update',$salesOrder) : route('sales-orders.store') }}">
    @csrf @if(isset($salesOrder)) @method('PUT') @endif
    <div class="card bg-dark border-secondary p-3 mb-3">
      <div class="row g-3">
        <div class="col-md-2"><label class="form-label text-light">SO Date <span class="text-danger">*</span></label>
          <input type="date" name="so_date" class="form-control bg-dark text-light border-secondary" value="{{ old('so_date',$salesOrder->so_date?->format('Y-m-d')??today()->toDateString()) }}" required></div>
        <div class="col-md-3"><label class="form-label text-light">Customer</label>
          <select name="customer_id" class="form-select bg-dark text-light border-secondary" onchange="fillCustName(this)">
            <option value="">-- Select --</option>
            @foreach($customers as $c)
            <option value="{{ $c->id }}" data-name="{{ $c->name }}" {{ old('customer_id',$salesOrder->customer_id??'')==$c->id?'selected':'' }}>{{ $c->name }}</option>
            @endforeach
          </select></div>
        <div class="col-md-3"><label class="form-label text-light">Customer Name <span class="text-danger">*</span></label>
          <input type="text" name="customer_name" id="custName" class="form-control bg-dark text-light border-secondary" value="{{ old('customer_name',$salesOrder->customer_name??'') }}" required></div>
        <div class="col-md-2"><label class="form-label text-light">Channel</label>
          <select name="channel" class="form-select bg-dark text-light border-secondary">
            @foreach(['retail','wholesale','distributor','online','export'] as $ch)
            <option value="{{ $ch }}" {{ old('channel',$salesOrder->channel??'retail')==$ch?'selected':'' }}>{{ ucfirst($ch) }}</option>
            @endforeach
          </select></div>
        <div class="col-md-2"><label class="form-label text-light">Delivery Date</label>
          <input type="date" name="delivery_date" class="form-control bg-dark text-light border-secondary" value="{{ old('delivery_date',$salesOrder->delivery_date?->format('Y-m-d')??'') }}"></div>
        <div class="col-md-4"><label class="form-label text-light">Delivery Address</label>
          <textarea name="delivery_address" rows="2" class="form-control bg-dark text-light border-secondary">{{ old('delivery_address',$salesOrder->delivery_address??'') }}</textarea></div>
        <div class="col-md-3"><label class="form-label text-light">Payment Terms</label>
          <input type="text" name="payment_terms" class="form-control bg-dark text-light border-secondary" value="{{ old('payment_terms',$salesOrder->payment_terms??'') }}" placeholder="e.g. Net 30"></div>
        <div class="col-md-2"><label class="form-label text-light">Advance Received</label>
          <input type="number" step="0.01" name="advance_received" class="form-control bg-dark text-light border-secondary" value="{{ old('advance_received',$salesOrder->advance_received??0) }}"></div>
        <div class="col-md-3"><label class="form-label text-light">Narration</label>
          <input type="text" name="narration" class="form-control bg-dark text-light border-secondary" value="{{ old('narration',$salesOrder->narration??'') }}"></div>
      </div>
    </div>

    @include('sales._item_table', [
      'items' => old('items', isset($salesOrder) ? $salesOrder->items->map(fn($i) => array_merge($i->toArray(), ['qty' => $i->ordered_qty]))->toArray() : [[]]),
      'fgList' => $fgList
    ])

    <div class="card bg-dark border-secondary p-3 mb-3">
      <div class="row justify-content-end g-2">
        <div class="col-md-3">
          <div class="d-flex justify-content-between"><span class="text-muted">Taxable Amount</span><strong id="sumTaxable">0.00</strong></div>
          <div class="d-flex justify-content-between"><span class="text-muted">Discount</span><strong id="sumDiscount">0.00</strong></div>
          <div class="d-flex justify-content-between"><span class="text-muted">SGST</span><strong id="sumSgst">0.00</strong></div>
          <div class="d-flex justify-content-between"><span class="text-muted">CGST</span><strong id="sumCgst">0.00</strong></div>
          <div class="d-flex justify-content-between"><span class="text-muted">IGST</span><strong id="sumIgst">0.00</strong></div>
          <hr class="border-secondary">
          <div class="d-flex justify-content-between text-warning"><strong>Net Amount</strong><strong id="sumNet">0.00</strong></div>
        </div>
      </div>
    </div>

    <button class="btn btn-warning"><i class="bi bi-check-lg me-1"></i>Save</button>
    <a href="{{ route('sales-orders.index') }}" class="btn btn-secondary ms-2">Cancel</a>
  </form>
</div>
@include('sales._item_scripts')
@endsection
