@extends('layouts.app')
@section('title', $record ? 'Edit Overtime' : 'Add Overtime')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-clock-history me-2"></i>{{ $record ? 'Edit Overtime Record' : 'Add Overtime Record' }}</h5>
    <a href="{{ route('overtime.index') }}" class="btn btn-sm btn-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

@if($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<div class="card-dark p-4" style="max-width:500px">
    <form method="POST" action="{{ $record ? route('overtime.update', $record->id) : route('overtime.store') }}">
        @csrf
        @if($record) @method('PUT') @endif

        <div class="mb-3">
            <label class="form-label-gold">Employee *</label>
            <select name="employee_id" class="form-select form-control-dark" required>
                <option value="">Select Employee</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" @selected(old('employee_id', $record->employee_id ?? '') == $emp->id)>{{ $emp->name }} ({{ $emp->employee_code }})</option>
                @endforeach
            </select>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label-gold">Date *</label>
                <input type="date" name="date" class="form-control form-control-dark" value="{{ old('date', $record->date?->format('Y-m-d') ?? '') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label-gold">OT Hours *</label>
                <input type="number" step="0.5" name="hours" class="form-control form-control-dark" value="{{ old('hours', $record->hours ?? '') }}" required min="0.5">
            </div>
            <div class="col-md-6">
                <label class="form-label-gold">Rate per Hour (₹)</label>
                <input type="number" step="0.01" name="rate_per_hour" class="form-control form-control-dark" value="{{ old('rate_per_hour', $record->rate_per_hour ?? 0) }}" id="rateField">
            </div>
            <div class="col-md-6">
                <label class="form-label-gold">Amount (₹)</label>
                <input type="number" id="amtDisplay" class="form-control form-control-dark" readonly value="{{ $record?->amount ?? 0 }}">
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-gold"><i class="bi bi-save me-1"></i>{{ $record ? 'Update' : 'Save' }}</button>
            <a href="{{ route('overtime.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.querySelectorAll('[name=hours],[name=rate_per_hour]').forEach(el=>{
    el.addEventListener('input',()=>{
        const h=parseFloat(document.querySelector('[name=hours]').value||0);
        const r=parseFloat(document.querySelector('[name=rate_per_hour]').value||0);
        document.getElementById('amtDisplay').value=(h*r).toFixed(2);
    });
});
</script>
@endpush
@endsection
