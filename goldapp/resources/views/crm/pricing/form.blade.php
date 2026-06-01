@extends('layouts.app')
@section('title', isset($pricing) ? 'Edit Pricing Rule' : 'New Pricing Rule')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-tag me-2"></i>{{ isset($pricing) ? 'Edit Pricing Rule' : 'New Pricing Rule' }}</h5>
    <a href="{{ route('customer-pricing.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
</div>
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

<form method="POST" action="{{ isset($pricing) ? route('customer-pricing.update',$pricing->id) : route('customer-pricing.store') }}">
    @csrf @if(isset($pricing)) @method('PUT') @endif
    <div class="card-dark p-3">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Customer *</label>
                <select name="customer_id" class="form-select" required>
                    <option value="">-- Select --</option>
                    @foreach($customers as $c)
                    <option value="{{ $c->id }}" {{ old('customer_id', $pricing->customer_id ?? request('customer_id')) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Product (FG) *</label>
                <select name="fg_id" class="form-select" required>
                    <option value="">-- Select --</option>
                    @foreach($products as $p)
                    <option value="{{ $p->id }}" {{ old('fg_id', $pricing->fg_id ?? '') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Price Type *</label>
                <select name="price_type" class="form-select" id="price_type_sel">
                    <option value="fixed" {{ old('price_type', $pricing->price_type ?? 'fixed') === 'fixed' ? 'selected' : '' }}>Fixed Price</option>
                    <option value="discount_pct" {{ old('price_type', $pricing->price_type ?? '') === 'discount_pct' ? 'selected' : '' }}>Discount %</option>
                </select>
            </div>
            <div class="col-md-2" id="price_field">
                <label class="form-label">Price (₹)</label>
                <input type="number" step="0.0001" name="price" class="form-control" value="{{ old('price', $pricing->price ?? '') }}">
            </div>
            <div class="col-md-2" id="discount_field" style="display:none">
                <label class="form-label">Discount %</label>
                <input type="number" step="0.01" name="discount_pct" class="form-control" min="0" max="100" value="{{ old('discount_pct', $pricing->discount_pct ?? '') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Min Qty</label>
                <input type="number" step="0.0001" name="min_qty" class="form-control" value="{{ old('min_qty', $pricing->min_qty ?? 1) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Valid From *</label>
                <input type="date" name="valid_from" class="form-control" value="{{ old('valid_from', isset($pricing->valid_from) ? $pricing->valid_from->format('Y-m-d') : today()->format('Y-m-d')) }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Valid To</label>
                <input type="date" name="valid_to" class="form-control" value="{{ old('valid_to', isset($pricing->valid_to) ? $pricing->valid_to->format('Y-m-d') : '') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="1" {{ old('status', $pricing->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('status', $pricing->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </div>
    </div>
    <div class="mt-3 d-flex gap-2">
        <button type="submit" class="btn btn-gold">{{ isset($pricing) ? 'Update' : 'Save' }}</button>
        <a href="{{ route('customer-pricing.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>
<script>
const sel = document.getElementById('price_type_sel');
function togglePriceFields() {
    const isFixed = sel.value === 'fixed';
    document.getElementById('price_field').style.display = isFixed ? '' : 'none';
    document.getElementById('discount_field').style.display = isFixed ? 'none' : '';
}
sel.addEventListener('change', togglePriceFields);
togglePriceFields();
</script>
@endsection
