@extends('layouts.app')
@section('title', isset($cc) ? 'Edit Cost Centre' : 'New Cost Centre')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-diagram-2 me-2"></i>{{ isset($cc) ? 'Edit: '.$cc->name : 'New Cost Centre' }}</h5>
    <a href="{{ route('cost-centres.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
</div>
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

<form method="POST" action="{{ isset($cc) ? route('cost-centres.update',$cc->id) : route('cost-centres.store') }}">
    @csrf @if(isset($cc)) @method('PUT') @endif
    <div class="card-dark p-3">
        <div class="row g-3">
            <div class="col-md-2"><label class="form-label">Code *</label><input type="text" name="code" class="form-control" value="{{ old('code', $cc->code ?? '') }}" required></div>
            <div class="col-md-4"><label class="form-label">Name *</label><input type="text" name="name" class="form-control" value="{{ old('name', $cc->name ?? '') }}" required></div>
            <div class="col-md-3">
                <label class="form-label">Parent</label>
                <select name="parent_id" class="form-select">
                    <option value="">-- Top Level --</option>
                    @foreach($parents as $p)
                    <option value="{{ $p->id }}" {{ old('parent_id', $cc->parent_id ?? '') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2"><label class="form-label">Budget (₹)</label><input type="number" step="0.01" name="budget" class="form-control" value="{{ old('budget', $cc->budget ?? 0) }}"></div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="1" {{ old('status', $cc->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('status', $cc->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </div>
    </div>
    <div class="mt-3 d-flex gap-2">
        <button type="submit" class="btn btn-gold">{{ isset($cc) ? 'Update' : 'Save' }}</button>
        <a href="{{ route('cost-centres.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>
@endsection
