@extends('layouts.app')
@section('title', isset($opportunity) ? 'Edit Opportunity' : 'New Opportunity')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-trophy me-2"></i>{{ isset($opportunity) ? 'Edit Opportunity' : 'New Opportunity' }}</h5>
    <a href="{{ route('crm-opportunities.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
</div>
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

<form method="POST" action="{{ isset($opportunity) ? route('crm-opportunities.update',$opportunity->id) : route('crm-opportunities.store') }}">
    @csrf @if(isset($opportunity)) @method('PUT') @endif
    <div class="card-dark p-3">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Title *</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $opportunity->title ?? '') }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Value (₹) *</label>
                <input type="number" step="0.01" name="value" class="form-control" value="{{ old('value', $opportunity->value ?? '') }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Probability (%) *</label>
                <input type="number" name="probability" class="form-control" min="0" max="100" value="{{ old('probability', $opportunity->probability ?? 50) }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Stage *</label>
                <select name="stage" class="form-select" required>
                    @foreach(['prospect','qualified','proposal','negotiation','closed_won','closed_lost'] as $s)
                    <option value="{{ $s }}" {{ old('stage', $opportunity->stage ?? 'prospect') === $s ? 'selected' : '' }}>{{ ucwords(str_replace('_',' ',$s)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Expected Close *</label>
                <input type="date" name="expected_close" class="form-control" value="{{ old('expected_close', isset($opportunity->expected_close) ? $opportunity->expected_close->format('Y-m-d') : '') }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Related Lead</label>
                <select name="lead_id" class="form-select">
                    <option value="">-- None --</option>
                    @foreach($leads as $l)
                    <option value="{{ $l->id }}" {{ old('lead_id', $opportunity->lead_id ?? '') == $l->id ? 'selected' : '' }}>{{ $l->slno }} - {{ $l->contact_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Related Customer</label>
                <select name="customer_id" class="form-select">
                    <option value="">-- None --</option>
                    @foreach($customers as $c)
                    <option value="{{ $c->id }}" {{ old('customer_id', $opportunity->customer_id ?? '') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Assigned To *</label>
                <select name="assigned_to" class="form-select" required>
                    @foreach($staff as $s)
                    <option value="{{ $s->id }}" {{ old('assigned_to', $opportunity->assigned_to ?? '') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">Narration</label>
                <textarea name="narration" class="form-control" rows="2">{{ old('narration', $opportunity->narration ?? '') }}</textarea>
            </div>
        </div>
    </div>
    <div class="mt-3 d-flex gap-2">
        <button type="submit" class="btn btn-gold">{{ isset($opportunity) ? 'Update' : 'Save' }}</button>
        <a href="{{ route('crm-opportunities.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>
@endsection
