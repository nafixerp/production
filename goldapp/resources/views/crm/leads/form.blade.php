@extends('layouts.app')
@section('title', isset($lead) ? 'Edit Lead' : 'New Lead')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-person-plus me-2"></i>{{ isset($lead) ? 'Edit Lead' : 'New Lead' }}</h5>
    <a href="{{ route('crm-leads.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
</div>
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

<form method="POST" action="{{ isset($lead) ? route('crm-leads.update',$lead->id) : route('crm-leads.store') }}">
    @csrf @if(isset($lead)) @method('PUT') @endif
    <div class="card-dark p-3">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Ref No. *</label>
                <input type="text" name="slno" class="form-control" value="{{ old('slno', $lead->slno ?? $slno ?? '') }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Lead Date *</label>
                <input type="date" name="lead_date" class="form-control" value="{{ old('lead_date', isset($lead) ? $lead->lead_date->format('Y-m-d') : today()->format('Y-m-d')) }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Priority</label>
                <select name="priority" class="form-select">
                    @foreach(['low','medium','high'] as $p)
                    <option value="{{ $p }}" {{ old('priority', $lead->priority ?? 'medium') === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    @foreach(['new','contacted','qualified','proposal_sent','negotiating','won','lost'] as $s)
                    <option value="{{ $s }}" {{ old('status', $lead->status ?? 'new') === $s ? 'selected' : '' }}>{{ ucwords(str_replace('_',' ',$s)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Company Name</label>
                <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $lead->company_name ?? '') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Contact Name *</label>
                <input type="text" name="contact_name" class="form-control" value="{{ old('contact_name', $lead->contact_name ?? '') }}" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Phone *</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone', $lead->phone ?? '') }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $lead->email ?? '') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">City *</label>
                <input type="text" name="city" class="form-control" value="{{ old('city', $lead->city ?? '') }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Source *</label>
                <select name="source" class="form-select" required>
                    @foreach(['cold_call','referral','website','exhibition','social_media','walk_in','other'] as $s)
                    <option value="{{ $s }}" {{ old('source', $lead->source ?? '') === $s ? 'selected' : '' }}>{{ ucwords(str_replace('_',' ',$s)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Estimated Value (₹)</label>
                <input type="number" step="0.01" name="estimated_value" class="form-control" value="{{ old('estimated_value', $lead->estimated_value ?? '') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Expected Close Date</label>
                <input type="date" name="expected_close_date" class="form-control" value="{{ old('expected_close_date', isset($lead->expected_close_date) ? $lead->expected_close_date->format('Y-m-d') : '') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Assigned To</label>
                <select name="assigned_to" class="form-select">
                    <option value="">-- Select --</option>
                    @foreach($staff as $s)
                    <option value="{{ $s->id }}" {{ old('assigned_to', $lead->assigned_to ?? '') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">Product Interest</label>
                <textarea name="product_interest" class="form-control" rows="2">{{ old('product_interest', $lead->product_interest ?? '') }}</textarea>
            </div>
            <div class="col-12">
                <label class="form-label">Notes / Narration</label>
                <textarea name="narration" class="form-control" rows="2">{{ old('narration', $lead->narration ?? '') }}</textarea>
            </div>
            <div class="col-12" id="lost_section" style="{{ old('status', $lead->status ?? '') === 'lost' ? '' : 'display:none' }}">
                <label class="form-label">Lost Reason</label>
                <textarea name="lost_reason" class="form-control" rows="2">{{ old('lost_reason', $lead->lost_reason ?? '') }}</textarea>
            </div>
        </div>
    </div>
    <div class="mt-3 d-flex gap-2">
        <button type="submit" class="btn btn-gold">{{ isset($lead) ? 'Update' : 'Save' }} Lead</button>
        <a href="{{ route('crm-leads.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>
<script>
document.querySelector('[name="status"]').addEventListener('change', function() {
    document.getElementById('lost_section').style.display = this.value === 'lost' ? '' : 'none';
});
</script>
@endsection
