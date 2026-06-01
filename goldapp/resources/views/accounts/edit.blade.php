@extends('layouts.app')
@section('title','Edit Account')
@section('page-title','Edit Account')

@section('content')
<div class="card-gold" style="max-width:720px">
    <div class="card-header-gold">
        <h5>◆ Edit Account: {{ $account->code }}</h5>
        <a href="{{ route('accounts.index') }}" class="btn-outline-gold btn-sm-gold">← Back</a>
    </div>
    <form method="POST" action="{{ route('accounts.update',$account->id) }}">
        @csrf @method('PUT')
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Account Code *</label>
                <input type="text" name="code" class="form-control" value="{{ old('code',$account->code) }}" required>
            </div>
            <div class="col-md-8">
                <label class="form-label">Account Name *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name',$account->name) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Account Type</label>
                <select name="atype" class="form-select">
                    <option value="">-- Select Type --</option>
                    @foreach(['customer','supplier','bank','expense','income','system'] as $t)
                    <option value="{{ $t }}" {{ old('atype',$account->atype)==$t?'selected':'' }}>{{ ucfirst($t) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Account Group</label>
                <select name="group_id" class="form-select">
                    <option value="">-- Select Group --</option>
                    @foreach($groups as $g)
                    <option value="{{ $g->id }}" {{ old('group_id',$account->group_id)==$g->id?'selected':'' }}>{{ $g->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Opening Balance</label>
                <input type="number" step="0.01" name="opening_balance" class="form-control" value="{{ old('opening_balance',$account->opening_balance) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Dr / Cr</label>
                <select name="ob_type" class="form-select">
                    <option value="dr" {{ old('ob_type',$account->ob_type)=='dr'?'selected':'' }}>Debit</option>
                    <option value="cr" {{ old('ob_type',$account->ob_type)=='cr'?'selected':'' }}>Credit</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="1" {{ old('status',$account->status)==1?'selected':'' }}>Active</option>
                    <option value="0" {{ old('status',$account->status)==0?'selected':'' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone',$account->phone) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">GST No</label>
                <input type="text" name="gst" class="form-control" value="{{ old('gst',$account->gst) }}">
            </div>
            <div class="col-12">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control" rows="2">{{ old('address',$account->address) }}</textarea>
            </div>
        </div>
        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn-gold">Update Account</button>
            <a href="{{ route('accounts.index') }}" class="btn-outline-gold">Cancel</a>
        </div>
    </form>
</div>
@endsection
