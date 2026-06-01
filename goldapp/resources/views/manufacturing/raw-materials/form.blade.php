@extends('layouts.app')
@section('title', $material ? 'Edit Raw Material' : 'New Raw Material')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="text-gold mb-0"><i class="bi bi-box-seam me-2"></i>{{ $material ? 'Edit: '.$material->name : 'New Raw Material' }}</h4>
    <a href="{{ route('raw-materials-new.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

<form method="POST" action="{{ $material ? route('raw-materials-new.update', $material) : route('raw-materials-new.store') }}">
    @csrf
    @if($material) @method('PUT') @endif

    <div class="card-erp p-3 mb-3">
        <h6 class="text-gold mb-3">Basic Information</h6>
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label-erp">Material Code <span class="text-danger">*</span></label>
                <input type="text" name="code" value="{{ old('code', $material->code ?? '') }}" class="form-control form-erp" required placeholder="RM-001">
            </div>
            <div class="col-md-6">
                <label class="form-label-erp">Material Name <span class="text-danger">*</span></label>
                <input type="text" name="name" value="{{ old('name', $material->name ?? '') }}" class="form-control form-erp" required>
            </div>
            <div class="col-md-3">
                <label class="form-label-erp">Status</label>
                <select name="status" class="form-select form-erp">
                    <option value="1" {{ old('status', $material->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('status', $material->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label-erp">Category</label>
                <input type="text" name="category" value="{{ old('category', $material->category ?? '') }}" class="form-control form-erp" placeholder="Spices, Flour, Oil...">
            </div>
            <div class="col-md-3">
                <label class="form-label-erp">Sub Category</label>
                <input type="text" name="sub_category" value="{{ old('sub_category', $material->sub_category ?? '') }}" class="form-control form-erp">
            </div>
            <div class="col-md-3">
                <label class="form-label-erp">Unit of Measure</label>
                <select name="unit_id" class="form-select form-erp">
                    <option value="">-- Select Unit --</option>
                    @foreach($units as $u)
                        <option value="{{ $u->id }}" {{ old('unit_id', $material->unit_id ?? '') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label-erp">HSN Code</label>
                <input type="text" name="hsn_code" value="{{ old('hsn_code', $material->hsn_code ?? '') }}" class="form-control form-erp">
            </div>
            <div class="col-md-3">
                <label class="form-label-erp">Tax / GST</label>
                <select name="tax_id" class="form-select form-erp">
                    <option value="">-- Select Tax --</option>
                    @foreach($taxes as $t)
                        <option value="{{ $t->id }}" {{ old('tax_id', $material->tax_id ?? '') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <label class="form-label-erp">Description</label>
                <textarea name="description" rows="2" class="form-control form-erp">{{ old('description', $material->description ?? '') }}</textarea>
            </div>
        </div>
    </div>

    <div class="card-erp p-3 mb-3">
        <h6 class="text-gold mb-3">Stock Parameters</h6>
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label-erp">Reorder Quantity</label>
                <input type="number" name="reorder_qty" value="{{ old('reorder_qty', $material->reorder_qty ?? 0) }}" class="form-control form-erp" step="0.0001" min="0">
            </div>
            <div class="col-md-3">
                <label class="form-label-erp">Minimum Stock Level</label>
                <input type="number" name="min_stock" value="{{ old('min_stock', $material->min_stock ?? 0) }}" class="form-control form-erp" step="0.0001" min="0">
            </div>
            <div class="col-md-3">
                <label class="form-label-erp">Maximum Stock Level</label>
                <input type="number" name="max_stock" value="{{ old('max_stock', $material->max_stock ?? 0) }}" class="form-control form-erp" step="0.0001" min="0">
            </div>
            <div class="col-md-3">
                <label class="form-label-erp">Lead Time (Days)</label>
                <input type="number" name="lead_time_days" value="{{ old('lead_time_days', $material->lead_time_days ?? 0) }}" class="form-control form-erp" min="0">
            </div>
            <div class="col-md-3">
                <label class="form-label-erp">Shelf Life (Days)</label>
                <input type="number" name="shelf_life_days" value="{{ old('shelf_life_days', $material->shelf_life_days ?? '') }}" class="form-control form-erp" min="0" placeholder="Leave blank if N/A">
            </div>
            <div class="col-md-3">
                <label class="form-label-erp">Storage Temperature</label>
                <input type="text" name="storage_temp" value="{{ old('storage_temp', $material->storage_temp ?? '') }}" class="form-control form-erp" placeholder="2-8°C, Ambient...">
            </div>
            <div class="col-md-3">
                <label class="form-label-erp">FSSAI Regulated?</label>
                <select name="is_fssai_regulated" class="form-select form-erp">
                    <option value="0" {{ old('is_fssai_regulated', $material->is_fssai_regulated ?? 0) == 0 ? 'selected' : '' }}>No</option>
                    <option value="1" {{ old('is_fssai_regulated', $material->is_fssai_regulated ?? 0) == 1 ? 'selected' : '' }}>Yes</option>
                </select>
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-gold"><i class="bi bi-check-lg me-1"></i>{{ $material ? 'Update' : 'Create' }} Raw Material</button>
    <a href="{{ route('raw-materials-new.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
</form>
@endsection
