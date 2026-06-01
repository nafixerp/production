@extends('layouts.app')
@section('title', isset($complaint) ? 'Edit Complaint' : 'New Complaint')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-exclamation-circle me-2"></i>{{ isset($complaint) ? 'Edit Complaint: '.$complaint->slno : 'New Complaint' }}</h5>
    <a href="{{ route('customer-complaints.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
</div>
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

<form method="POST" action="{{ isset($complaint) ? route('customer-complaints.update',$complaint->id) : route('customer-complaints.store') }}">
    @csrf @if(isset($complaint)) @method('PUT') @endif
    <div class="card-dark p-3">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Ref No. *</label>
                <input type="text" name="slno" class="form-control" value="{{ old('slno', $complaint->slno ?? $slno ?? '') }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Date *</label>
                <input type="date" name="complaint_date" class="form-control" value="{{ old('complaint_date', isset($complaint) ? $complaint->complaint_date->format('Y-m-d') : today()->format('Y-m-d')) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Customer *</label>
                <select name="customer_id" class="form-select" required>
                    <option value="">-- Select Customer --</option>
                    @foreach($customers as $c)
                    <option value="{{ $c->id }}" {{ old('customer_id', $complaint->customer_id ?? request('customer_id')) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Type *</label>
                <select name="complaint_type" class="form-select" required>
                    @foreach(['quality','delivery','billing','service','product','other'] as $t)
                    <option value="{{ $t }}" {{ old('complaint_type', $complaint->complaint_type ?? '') === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Priority *</label>
                <select name="priority" class="form-select" required>
                    @foreach(['low','medium','high','critical'] as $p)
                    <option value="{{ $p }}" {{ old('priority', $complaint->priority ?? 'medium') === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                    @endforeach
                </select>
            </div>
            @if(isset($complaint))
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    @foreach(['open','acknowledged','investigating','resolved','closed'] as $s)
                    <option value="{{ $s }}" {{ old('status', $complaint->status) === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="col-12">
                <label class="form-label">Subject *</label>
                <input type="text" name="subject" class="form-control" value="{{ old('subject', $complaint->subject ?? '') }}" required>
            </div>
            <div class="col-12">
                <label class="form-label">Description *</label>
                <textarea name="description" class="form-control" rows="3" required>{{ old('description', $complaint->description ?? '') }}</textarea>
            </div>
            @if(isset($complaint) && in_array($complaint->status, ['investigating','resolved','closed']))
            <div class="col-12">
                <label class="form-label">Resolution</label>
                <textarea name="resolution" class="form-control" rows="3">{{ old('resolution', $complaint->resolution ?? '') }}</textarea>
            </div>
            @endif
        </div>
    </div>
    <div class="mt-3 d-flex gap-2">
        <button type="submit" class="btn btn-gold">{{ isset($complaint) ? 'Update' : 'Save' }}</button>
        <a href="{{ route('customer-complaints.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>
@endsection
