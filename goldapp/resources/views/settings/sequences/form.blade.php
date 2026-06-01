@extends('layouts.app')
@section('title', $seq->exists ? 'Edit Sequence' : 'New Sequence')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-123 me-2"></i>{{ $seq->exists ? 'Edit — '.$seq->module : 'New Sequence Config' }}</h5>
    <a href="{{ route('sequences.index') }}" class="btn-outline-gold btn-sm-gold"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>
<div class="card-gold" style="max-width:600px">
    <form method="POST" action="{{ $seq->exists ? route('sequences.update', $seq) : route('sequences.store') }}">
        @csrf
        @if($seq->exists) @method('PUT') @endif

        @if(!$seq->exists)
        <div class="mb-3">
            <label class="form-label">Module *</label>
            <input type="text" name="module" class="form-control" value="{{ old('module') }}" required placeholder="e.g. sales_order">
        </div>
        @else
        <div class="mb-3">
            <label class="form-label">Module</label>
            <input type="text" class="form-control" value="{{ $seq->module }}" readonly>
        </div>
        @endif

        <div class="row g-3 mb-3">
            <div class="col-6">
                <label class="form-label">Prefix *</label>
                <input type="text" name="prefix" class="form-control" value="{{ old('prefix', $seq->prefix) }}" required placeholder="e.g. SO">
            </div>
            <div class="col-6">
                <label class="form-label">Suffix</label>
                <input type="text" name="suffix" class="form-control" value="{{ old('suffix', $seq->suffix) }}" placeholder="Optional">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Format *</label>
            <input type="text" name="format" class="form-control" value="{{ old('format', $seq->format ?? '{PREFIX}/{YY}/{SEQ4}') }}" required>
            <small style="color:var(--text-muted-gold)">
                Variables: {PREFIX} {SUFFIX} {YY} {YYYY} {MM} {DD} {SEQ4} {SEQ6}
            </small>
        </div>

        <div class="mb-3">
            <label class="form-label">Reset Cycle</label>
            <select name="reset_cycle" class="form-select">
                @foreach(['never','yearly','monthly','daily'] as $rc)
                    <option value="{{ $rc }}" {{ old('reset_cycle', $seq->reset_cycle ?? 'yearly') === $rc ? 'selected' : '' }}>{{ ucfirst($rc) }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="branch_specific" value="1" id="branchSpec"
                    {{ old('branch_specific', $seq->branch_specific ?? 0) ? 'checked' : '' }}>
                <label class="form-check-label" for="branchSpec" style="color:#ccc">Branch-specific sequences</label>
            </div>
        </div>

        <button type="submit" class="btn-gold"><i class="bi bi-check2 me-1"></i>{{ $seq->exists ? 'Update' : 'Create' }} Sequence</button>
    </form>
</div>
@endsection
