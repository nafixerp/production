@extends('layouts.app')
@section('title', isset($budget) ? 'Edit Budget' : 'New Budget Entry')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-bar-chart-steps me-2"></i>{{ isset($budget) ? 'Edit Budget Entry' : 'New Budget Entry' }}</h5>
    <a href="{{ route('budgets.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
</div>
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

<form method="POST" action="{{ isset($budget) ? route('budgets.update',$budget->id) : route('budgets.store') }}">
    @csrf @if(isset($budget)) @method('PUT') @endif
    <div class="card-dark p-3">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Account *</label>
                <select name="account_id" class="form-select" required>
                    <option value="">-- Select --</option>
                    @foreach($accounts as $a)
                    <option value="{{ $a->id }}" {{ old('account_id', $budget->account_id ?? '') == $a->id ? 'selected' : '' }}>{{ $a->code }} - {{ $a->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Cost Centre</label>
                <select name="cost_centre_id" class="form-select">
                    <option value="">-- None --</option>
                    @foreach($costCentres as $cc)
                    <option value="{{ $cc->id }}" {{ old('cost_centre_id', $budget->cost_centre_id ?? '') == $cc->id ? 'selected' : '' }}>{{ $cc->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Financial Year *</label>
                <input type="text" name="financial_year" class="form-control" value="{{ old('financial_year', $budget->financial_year ?? date('Y').'-'.(date('Y')+1)) }}" placeholder="2025-2026" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Month *</label>
                <select name="month" class="form-select" required>
                    @for($m=1;$m<=12;$m++)
                    <option value="{{ $m }}" {{ old('month', $budget->month ?? date('n')) == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Budgeted Amount (₹) *</label>
                <input type="number" step="0.01" name="budgeted_amount" class="form-control" value="{{ old('budgeted_amount', $budget->budgeted_amount ?? '') }}" required>
            </div>
        </div>
    </div>
    <div class="mt-3 d-flex gap-2">
        <button type="submit" class="btn btn-gold">{{ isset($budget) ? 'Update' : 'Save' }}</button>
        <a href="{{ route('budgets.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>
@endsection
