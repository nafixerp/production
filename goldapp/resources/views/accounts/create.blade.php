@extends('layouts.app')
@section('title','New Account')
@section('page-title','New Account')

@section('content')
<div class="card-gold" style="max-width:720px">
    <div class="card-header-gold">
        <h5>◆ Create Account</h5>
        <a href="{{ route('accounts.index') }}" class="btn-outline-gold btn-sm-gold">← Back</a>
    </div>
    <form method="POST" action="{{ route('accounts.store') }}">
        @csrf
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Account Code *</label>
                <input type="text" name="code" class="form-control" value="{{ old('code') }}" required placeholder="e.g. CUST001">
            </div>
            <div class="col-md-8">
                <label class="form-label">Account Name *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="Full account name">
            </div>
            <div class="col-md-6">
                <label class="form-label">Account Type</label>
                <select name="atype" class="form-select">
                    <option value="">-- Select Type --</option>
                    <option value="customer" {{ old('atype')=='customer'?'selected':'' }}>Customer</option>
                    <option value="supplier" {{ old('atype')=='supplier'?'selected':'' }}>Supplier</option>
                    <option value="bank"     {{ old('atype')=='bank'?'selected':'' }}>Bank</option>
                    <option value="expense"  {{ old('atype')=='expense'?'selected':'' }}>Expense</option>
                    <option value="income"   {{ old('atype')=='income'?'selected':'' }}>Income</option>
                    <option value="system"   {{ old('atype')=='system'?'selected':'' }}>System</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Account Group</label>
                <select name="group_id" class="form-select">
                    <option value="">-- Select Group --</option>
                    @foreach($groups as $g)
                    <option value="{{ $g->id }}" {{ old('group_id')==$g->id?'selected':'' }}>{{ $g->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Opening Balance</label>
                <input type="number" step="0.01" name="opening_balance" class="form-control" value="{{ old('opening_balance',0) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Dr / Cr</label>
                <select name="ob_type" class="form-select">
                    <option value="dr" {{ old('ob_type','dr')=='dr'?'selected':'' }}>Debit</option>
                    <option value="cr" {{ old('ob_type')=='cr'?'selected':'' }}>Credit</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="1" {{ old('status',1)==1?'selected':'' }}>Active</option>
                    <option value="0" {{ old('status')==0?'selected':'' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">GST No</label>
                <input type="text" name="gst" class="form-control" value="{{ old('gst') }}">
            </div>
            <div class="col-12">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
            </div>
        </div>
        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn-gold">Save Account</button>
            <a href="{{ route('accounts.index') }}" class="btn-outline-gold">Cancel</a>
        </div>
    </form>
</div>
@endsection
