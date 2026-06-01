@extends('layouts.app')
@section('title', $bill->exists ? 'Edit E-Way Bill' : 'Generate E-Way Bill')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-truck me-2"></i>{{ $bill->exists ? 'E-Way Bill #'.$bill->ewb_no : 'Generate E-Way Bill' }}</h5>
    <a href="{{ route('eway-bills.index') }}" class="btn-outline-gold btn-sm-gold"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>
<div class="card-gold" style="max-width:680px">
    <form method="POST" action="{{ $bill->exists ? route('eway-bills.update', $bill) : route('eway-bills.store') }}">
        @csrf
        @if($bill->exists) @method('PUT') @endif

        @if(!$bill->exists)
        <div class="row g-3 mb-3">
            <div class="col-6">
                <label class="form-label">Sales Invoice ID *</label>
                <input type="number" name="sales_invoice_id" class="form-control" value="{{ old('sales_invoice_id') }}" required>
            </div>
        </div>
        <div class="row g-3 mb-3">
            <div class="col-6">
                <label class="form-label">From GSTIN *</label>
                <input type="text" name="from_gstin" class="form-control" value="{{ old('from_gstin') }}" placeholder="Supplier GSTIN" required>
            </div>
            <div class="col-6">
                <label class="form-label">To GSTIN *</label>
                <input type="text" name="to_gstin" class="form-control" value="{{ old('to_gstin') }}" placeholder="Buyer GSTIN" required>
            </div>
        </div>
        @endif

        <div class="row g-3 mb-3">
            <div class="col-6">
                <label class="form-label">Transporter ID</label>
                <input type="text" name="transporter_id" class="form-control" value="{{ old('transporter_id', $bill->transporter_id) }}">
            </div>
            <div class="col-6">
                <label class="form-label">Vehicle No</label>
                <input type="text" name="vehicle_no" class="form-control" value="{{ old('vehicle_no', $bill->vehicle_no) }}" placeholder="e.g. MH12AB1234">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Distance (km)</label>
            <input type="number" name="distance_km" class="form-control" value="{{ old('distance_km', $bill->distance_km) }}">
        </div>

        @if($bill->exists)
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                @foreach(['active','cancelled','extended'] as $s)
                    <option value="{{ $s }}" {{ $bill->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        @endif

        <button type="submit" class="btn-gold"><i class="bi bi-check2 me-1"></i>{{ $bill->exists ? 'Update' : 'Generate EWB' }}</button>
    </form>
</div>
@endsection
