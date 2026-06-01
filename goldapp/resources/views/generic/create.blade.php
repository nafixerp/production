@extends('layouts.app')
@section('title', ($row ? 'Edit' : 'Add') . ' ' . $title)
@section('page-title', ($row ? 'Edit' : 'Add') . ' ' . $title)
@section('content')
<div class="card-gold" style="max-width:700px">
    <div class="card-header-gold">
        <h5>◇ {{ $row ? 'Edit' : 'Add' }} {{ $title }}</h5>
        <a href="{{ route($slug.'.index') }}" class="btn-outline-gold btn-sm-gold">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
    <form method="POST" action="{{ $row ? route($slug.'.update', $row->id) : route($slug.'.store') }}">
        @csrf
        @if($row) @method('PUT') @endif
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Code</label>
                <input name="code" value="{{ old('code', $row->code ?? '') }}" class="form-control" placeholder="Enter code">
            </div>
            <div class="col-md-8">
                <label class="form-label">Name <span style="color:#f87171">*</span></label>
                <input name="name" value="{{ old('name', $row->name ?? '') }}" class="form-control" required placeholder="Enter name">
            </div>
            <div class="col-12">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="2" placeholder="Optional description">{{ old('description', $row->description ?? '') }}</textarea>
            </div>
            <div class="col-md-4">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="active" {{ ($row->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ ($row->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </div>
        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn-gold"><i class="bi bi-check-lg me-1"></i>{{ $row ? 'Update' : 'Save' }}</button>
            <a href="{{ route($slug.'.index') }}" class="btn-outline-gold">Cancel</a>
        </div>
    </form>
</div>
@endsection
