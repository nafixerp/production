@extends('layouts.app')
@section('title', isset($salesQuotation) ? 'Edit Quotation' : 'New Quotation')
@section('content')
<div class="container-fluid px-4 py-3">
  <div class="d-flex gap-2 align-items-center mb-3">
    <a href="{{ route('sales-quotations.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h4 class="text-warning mb-0">{{ isset($salesQuotation) ? 'Edit: '.$salesQuotation->quot_no : 'New Quotation ('.$slno.')' }}</h4>
  </div>
  @if($errors->any())<div class="alert alert-danger py-2"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{$e}}</li>@endforeach</ul></div>@endif
  <form method="POST" action="{{ isset($salesQuotation) ? route('sales-quotations.update',$salesQuotation) : route('sales-quotations.store') }}">
    @csrf @if(isset($salesQuotation)) @method('PUT') @endif
    <div class="card bg-dark border-secondary p-3 mb-3">
      <div class="row g-3">
        <div class="col-md-2"><label class="form-label text-light">Quot Date <span class="text-danger">*</span></label>
          <input type="date" name="quot_date" class="form-control bg-dark text-light border-secondary" value="{{ old('quot_date',$salesQuotation->quot_date?->format('Y-m-d')??today()->toDateString()) }}" required></div>
        <div class="col-md-2"><label class="form-label text-light">Valid Till</label>
          <input type="date" name="valid_till" class="form-control bg-dark text-light border-secondary" value="{{ old('valid_till',$salesQuotation->valid_till?->format('Y-m-d')??'') }}"></div>
        <div class="col-md-3"><label class="form-label text-light">Customer</label>
          <select name="customer_id" class="form-select bg-dark text-light border-secondary" onchange="fillCustName(this)">
            <option value="">-- Select --</option>
            @foreach($customers as $c)
            <option value="{{ $c->id }}" data-name="{{ $c->name }}" {{ old('customer_id',$salesQuotation->customer_id??'')==$c->id?'selected':'' }}>{{ $c->name }}</option>
            @endforeach
          </select></div>
        <div class="col-md-3"><label class="form-label text-light">Customer Name <span class="text-danger">*</span></label>
          <input type="text" name="customer_name" id="custName" class="form-control bg-dark text-light border-secondary" value="{{ old('customer_name',$salesQuotation->customer_name??'') }}" required></div>
        <div class="col-md-2"><label class="form-label text-light">Channel</label>
          <select name="channel" class="form-select bg-dark text-light border-secondary">
            @foreach(['retail','wholesale','distributor','online','export'] as $ch)
            <option value="{{ $ch }}" {{ old('channel',$salesQuotation->channel??'retail')==$ch?'selected':'' }}>{{ ucfirst($ch) }}</option>
            @endforeach
          </select></div>
        <div class="col-md-4"><label class="form-label text-light">Customer Address</label>
          <textarea name="customer_address" rows="2" class="form-control bg-dark text-light border-secondary">{{ old('customer_address',$salesQuotation->customer_address??'') }}</textarea></div>
        <div class="col-md-4"><label class="form-label text-light">Terms</label>
          <textarea name="terms" rows="2" class="form-control bg-dark text-light border-secondary">{{ old('terms',$salesQuotation->terms??'') }}</textarea></div>
        <div class="col-md-4"><label class="form-label text-light">Narration</label>
          <textarea name="narration" rows="2" class="form-control bg-dark text-light border-secondary">{{ old('narration',$salesQuotation->narration??'') }}</textarea></div>
      </div>
    </div>

    @include('sales._item_table', ['items' => old('items', isset($salesQuotation) ? $salesQuotation->items->toArray() : [[]]), 'fgList' => $fgList])

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
    <a href="{{ route('sales-quotations.index') }}" class="btn btn-secondary ms-2">Cancel</a>
  </form>
</div>
@include('sales._item_scripts')
@endsection
