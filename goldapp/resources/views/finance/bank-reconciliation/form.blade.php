@extends('layouts.app')
@section('title','New Bank Reconciliation')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-arrow-left-right me-2"></i>New Bank Reconciliation</h5>
    <a href="{{ route('bank-reconciliation.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
</div>
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

<form method="POST" action="{{ route('bank-reconciliation.store') }}">
    @csrf
    <div class="card-dark p-3">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Bank Account *</label>
                <select name="bank_account_id" class="form-select" required>
                    <option value="">-- Select Bank --</option>
                    @foreach($bankAccounts as $b)
                    <option value="{{ $b->id }}">{{ $b->name }} ({{ $b->bank_name }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Statement Date *</label>
                <input type="date" name="statement_date" class="form-control" value="{{ today()->format('Y-m-d') }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Statement Closing Balance *</label>
                <input type="number" step="0.01" name="statement_closing_balance" class="form-control" required>
            </div>
        </div>
    </div>
    <div class="mt-3 d-flex gap-2">
        <button type="submit" class="btn btn-gold">Start Reconciliation</button>
        <a href="{{ route('bank-reconciliation.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>
@endsection
