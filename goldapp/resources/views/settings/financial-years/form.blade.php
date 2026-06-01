@extends('layouts.app')
@section('title', $year->exists ? 'Edit Financial Year' : 'New Financial Year')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-calendar3 me-2"></i>{{ $year->exists ? 'Edit ' . $year->name : 'New Financial Year' }}</h5>
    <a href="{{ route('financial-years.index') }}" class="btn-outline-gold btn-sm-gold"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>
<div class="card-gold" style="max-width:480px">
    <form method="POST" action="{{ $year->exists ? route('financial-years.update', $year) : route('financial-years.store') }}">
        @csrf
        @if($year->exists) @method('PUT') @endif
        <div class="mb-3">
            <label class="form-label">Year Name *</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $year->name) }}" required placeholder="e.g. FY 2025-26">
        </div>
        <div class="row g-3 mb-3">
            <div class="col-6">
                <label class="form-label">From Date *</label>
                <input type="date" name="from_date" class="form-control" value="{{ old('from_date', $year->from_date?->format('Y-m-d')) }}" required>
            </div>
            <div class="col-6">
                <label class="form-label">To Date *</label>
                <input type="date" name="to_date" class="form-control" value="{{ old('to_date', $year->to_date?->format('Y-m-d')) }}" required>
            </div>
        </div>
        <button type="submit" class="btn-gold"><i class="bi bi-check2 me-1"></i>{{ $year->exists ? 'Update' : 'Create' }} Financial Year</button>
    </form>
</div>
@endsection
