@extends('layouts.app')
@section('title', $shift ? 'Edit Shift' : 'Add Shift')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-clock me-2"></i>{{ $shift ? 'Edit Shift' : 'Add Shift' }}</h5>
    <a href="{{ route('shifts.index') }}" class="btn btn-sm btn-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

@if($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<div class="card-dark p-4" style="max-width:500px">
    <form method="POST" action="{{ $shift ? route('shifts.update', $shift->id) : route('shifts.store') }}">
        @csrf
        @if($shift) @method('PUT') @endif

        <div class="row g-3">
            <div class="col-md-7">
                <label class="form-label-gold">Shift Name *</label>
                <input type="text" name="name" class="form-control form-control-dark" value="{{ old('name', $shift->name ?? '') }}" required>
            </div>
            <div class="col-md-5">
                <label class="form-label-gold">Code *</label>
                <input type="text" name="code" class="form-control form-control-dark" value="{{ old('code', $shift->code ?? '') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label-gold">Start Time *</label>
                <input type="time" name="start_time" class="form-control form-control-dark" value="{{ old('start_time', $shift->start_time ?? '') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label-gold">End Time *</label>
                <input type="time" name="end_time" class="form-control form-control-dark" value="{{ old('end_time', $shift->end_time ?? '') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label-gold">Break (minutes)</label>
                <input type="number" name="break_minutes" class="form-control form-control-dark" value="{{ old('break_minutes', $shift->break_minutes ?? 30) }}" min="0">
            </div>
            <div class="col-md-6">
                <label class="form-label-gold">Working Hours</label>
                <input type="number" step="0.5" name="working_hours" class="form-control form-control-dark" value="{{ old('working_hours', $shift->working_hours ?? 8) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label-gold">Status</label>
                <select name="status" class="form-select form-control-dark">
                    <option value="active" @selected(old('status', $shift->status ?? 'active') == 'active')>Active</option>
                    <option value="inactive" @selected(old('status', $shift->status ?? '') == 'inactive')>Inactive</option>
                </select>
            </div>
        </div>

        <div class="mt-3 d-flex gap-2">
            <button type="submit" class="btn btn-gold"><i class="bi bi-save me-1"></i>{{ $shift ? 'Update' : 'Save' }}</button>
            <a href="{{ route('shifts.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
