@extends('layouts.app')
@section('title', isset($account) ? 'Edit Account' : 'New Account')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-diagram-3 me-2"></i>{{ isset($account) ? 'Edit: '.$account->name : 'New Account' }}</h5>
    <a href="{{ route('chart-of-accounts.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
</div>
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

<form method="POST" action="{{ isset($account) ? route('chart-of-accounts.update',$account->id) : route('chart-of-accounts.store') }}">
    @csrf @if(isset($account)) @method('PUT') @endif
    <div class="card-dark p-3">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Code *</label>
                <input type="text" name="code" class="form-control" value="{{ old('code', $account->code ?? '') }}" required>
            </div>
            <div class="col-md-5">
                <label class="form-label">Name *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $account->name ?? '') }}" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Account Group *</label>
                <select name="account_group" class="form-select" required>
                    @foreach(['asset','liability','equity','income','expense','tax'] as $g)
                    <option value="{{ $g }}" {{ old('account_group', $account->account_group ?? '') === $g ? 'selected' : '' }}>{{ ucfirst($g) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Account Type</label>
                <input type="text" name="account_type" class="form-control" value="{{ old('account_type', $account->account_type ?? '') }}" placeholder="e.g., cash, bank, sales, cogs">
            </div>
            <div class="col-md-4">
                <label class="form-label">Parent Account</label>
                <select name="parent_id" class="form-select">
                    <option value="">-- None (Top Level) --</option>
                    @foreach($allAccounts as $pa)
                    <option value="{{ $pa->id }}" {{ old('parent_id', $account->parent_id ?? '') == $pa->id ? 'selected' : '' }}>{{ $pa->code }} - {{ $pa->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Opening Balance</label>
                <input type="number" step="0.0001" name="opening_balance" class="form-control" value="{{ old('opening_balance', $account->opening_balance ?? 0) }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">OB Type</label>
                <select name="ob_type" class="form-select">
                    <option value="dr" {{ old('ob_type', $account->ob_type ?? 'dr') === 'dr' ? 'selected' : '' }}>Debit (Dr)</option>
                    <option value="cr" {{ old('ob_type', $account->ob_type ?? '') === 'cr' ? 'selected' : '' }}>Credit (Cr)</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Currency</label>
                <input type="text" name="currency" class="form-control" value="{{ old('currency', $account->currency ?? 'INR') }}">
            </div>
            <div class="col-12">
                <div class="row g-3">
                    <div class="col-md-2">
                        <div class="form-check mt-4">
                            <input type="checkbox" name="is_bank_account" class="form-check-input" id="is_bank" value="1" {{ old('is_bank_account', $account->is_bank_account ?? 0) ? 'checked' : '' }} onchange="toggleBank(this.checked)">
                            <label class="form-check-label" for="is_bank">Bank Account</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-check mt-4">
                            <input type="checkbox" name="is_control_account" class="form-check-input" value="1" {{ old('is_control_account', $account->is_control_account ?? 0) ? 'checked' : '' }}>
                            <label class="form-check-label">Control Account</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-check mt-4">
                            <input type="checkbox" name="allow_direct_posting" class="form-check-input" value="1" {{ old('allow_direct_posting', $account->allow_direct_posting ?? 1) ? 'checked' : '' }}>
                            <label class="form-check-label">Allow Posting</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="1" {{ old('status', $account->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status', $account->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            <div id="bank_fields" style="{{ old('is_bank_account', $account->is_bank_account ?? 0) ? '' : 'display:none' }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Bank Name</label>
                        <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name', $account->bank_name ?? '') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Account No.</label>
                        <input type="text" name="bank_account_no" class="form-control" value="{{ old('bank_account_no', $account->bank_account_no ?? '') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">IFSC</label>
                        <input type="text" name="ifsc" class="form-control" value="{{ old('ifsc', $account->ifsc ?? '') }}">
                    </div>
                </div>
            </div>
            <div class="col-12">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="2">{{ old('notes', $account->notes ?? '') }}</textarea>
            </div>
        </div>
    </div>
    <div class="mt-3 d-flex gap-2">
        <button type="submit" class="btn btn-gold">{{ isset($account) ? 'Update' : 'Save' }} Account</button>
        <a href="{{ route('chart-of-accounts.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>
<script>
function toggleBank(checked) {
    document.getElementById('bank_fields').style.display = checked ? '' : 'none';
}
</script>
@endsection
