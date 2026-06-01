@extends('layouts.app')
@section('title', isset($activity) ? 'Edit Activity' : 'Log Activity')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-calendar-plus me-2"></i>{{ isset($activity) ? 'Edit Activity' : 'Log Activity' }}</h5>
    <a href="{{ route('crm-activities.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
</div>
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

<form method="POST" action="{{ isset($activity) ? route('crm-activities.update',$activity->id) : route('crm-activities.store') }}">
    @csrf @if(isset($activity)) @method('PUT') @endif
    <div class="card-dark p-3">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Activity Date *</label>
                <input type="date" name="activity_date" class="form-control" value="{{ old('activity_date', isset($activity) ? $activity->activity_date->format('Y-m-d') : today()->format('Y-m-d')) }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Activity Type *</label>
                <select name="activity_type" class="form-select" required>
                    @foreach(['call','email','visit','demo','follow_up','whatsapp','meeting'] as $t)
                    <option value="{{ $t }}" {{ old('activity_type', $activity->activity_type ?? '') === $t ? 'selected' : '' }}>{{ ucwords(str_replace('_',' ',$t)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Related Lead</label>
                <select name="lead_id" class="form-select">
                    <option value="">-- None --</option>
                    @foreach($leads as $l)
                    <option value="{{ $l->id }}" {{ old('lead_id', $activity->lead_id ?? $preLeadId ?? '') == $l->id ? 'selected' : '' }}>{{ $l->slno }} - {{ $l->contact_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Related Customer</label>
                <select name="customer_id" class="form-select">
                    <option value="">-- None --</option>
                    @foreach($customers as $c)
                    <option value="{{ $c->id }}" {{ old('customer_id', $activity->customer_id ?? $preCustomerId ?? '') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Subject *</label>
                <input type="text" name="subject" class="form-control" value="{{ old('subject', $activity->subject ?? '') }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Done By *</label>
                <input type="number" name="done_by" class="form-control" value="{{ old('done_by', $activity->done_by ?? auth()->id()) }}" required>
            </div>
            <div class="col-12">
                <label class="form-label">Notes *</label>
                <textarea name="notes" class="form-control" rows="3" required>{{ old('notes', $activity->notes ?? '') }}</textarea>
            </div>
            <div class="col-12">
                <label class="form-label">Outcome</label>
                <textarea name="outcome" class="form-control" rows="2">{{ old('outcome', $activity->outcome ?? '') }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Next Action</label>
                <input type="text" name="next_action" class="form-control" value="{{ old('next_action', $activity->next_action ?? '') }}" placeholder="e.g., Send proposal, Follow up call...">
            </div>
            <div class="col-md-3">
                <label class="form-label">Next Action Date</label>
                <input type="date" name="next_action_date" class="form-control" value="{{ old('next_action_date', isset($activity->next_action_date) ? $activity->next_action_date->format('Y-m-d') : '') }}">
            </div>
        </div>
    </div>
    <div class="mt-3 d-flex gap-2">
        <button type="submit" class="btn btn-gold">{{ isset($activity) ? 'Update' : 'Save' }} Activity</button>
        <a href="{{ route('crm-activities.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>
@endsection
