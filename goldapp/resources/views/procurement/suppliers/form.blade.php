@extends('layouts.app')
@section('title', $supplier ? 'Edit Supplier' : 'New Supplier')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="text-gold mb-0"><i class="bi bi-person-plus-fill me-2"></i>{{ $supplier ? 'Edit Supplier: '.$supplier->name : 'New Supplier' }}</h4>
    <a href="{{ route('suppliers.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<form method="POST" action="{{ $supplier ? route('suppliers.update', $supplier) : route('suppliers.store') }}">
    @csrf
    @if($supplier) @method('PUT') @endif

    <ul class="nav nav-tabs erp-tabs mb-3" id="supplierTabs" role="tablist">
        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-basic">Basic Info</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-bank">Bank Details</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-credit">Credit & Terms</button></li>
    </ul>

    <div class="tab-content">
        <!-- BASIC INFO -->
        <div class="tab-pane fade show active" id="tab-basic">
            <div class="card-erp p-3">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label-erp">Supplier Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" value="{{ old('code', $supplier->code ?? '') }}" class="form-control form-erp" required placeholder="SUP-001">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-erp">Supplier Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $supplier->name ?? '') }}" class="form-control form-erp" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-erp">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select form-erp">
                            @foreach(['active','inactive','blacklisted'] as $st)
                                <option value="{{ $st }}" {{ old('status', $supplier->status ?? 'active') === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-erp">Contact Person</label>
                        <input type="text" name="contact_person" value="{{ old('contact_person', $supplier->contact_person ?? '') }}" class="form-control form-erp">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-erp">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $supplier->phone ?? '') }}" class="form-control form-erp">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-erp">Email</label>
                        <input type="email" name="email" value="{{ old('email', $supplier->email ?? '') }}" class="form-control form-erp">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label-erp">Address</label>
                        <textarea name="address" rows="2" class="form-control form-erp">{{ old('address', $supplier->address ?? '') }}</textarea>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-erp">City</label>
                        <input type="text" name="city" value="{{ old('city', $supplier->city ?? '') }}" class="form-control form-erp">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-erp">State</label>
                        <input type="text" name="state" value="{{ old('state', $supplier->state ?? '') }}" class="form-control form-erp">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label-erp">Pincode</label>
                        <input type="text" name="pincode" value="{{ old('pincode', $supplier->pincode ?? '') }}" class="form-control form-erp">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label-erp">Country</label>
                        <input type="text" name="country" value="{{ old('country', $supplier->country ?? 'India') }}" class="form-control form-erp">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label-erp">Currency</label>
                        <input type="text" name="currency" value="{{ old('currency', $supplier->currency ?? 'INR') }}" class="form-control form-erp" maxlength="10">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-erp">GST No</label>
                        <input type="text" name="gst_no" value="{{ old('gst_no', $supplier->gst_no ?? '') }}" class="form-control form-erp" placeholder="22AAAAA0000A1Z5">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-erp">PAN No</label>
                        <input type="text" name="pan_no" value="{{ old('pan_no', $supplier->pan_no ?? '') }}" class="form-control form-erp" placeholder="AAAAA0000A">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-erp">Rating (0-5)</label>
                        <input type="number" name="rating" value="{{ old('rating', $supplier->rating ?? 0) }}" class="form-control form-erp" min="0" max="5" step="0.1">
                    </div>
                    <div class="col-12">
                        <label class="form-label-erp">Notes</label>
                        <textarea name="notes" rows="2" class="form-control form-erp">{{ old('notes', $supplier->notes ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- BANK -->
        <div class="tab-pane fade" id="tab-bank">
            <div class="card-erp p-3">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label-erp">Bank Name</label>
                        <input type="text" name="bank_name" value="{{ old('bank_name', $supplier->bank_name ?? '') }}" class="form-control form-erp">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-erp">Bank Account No</label>
                        <input type="text" name="bank_account" value="{{ old('bank_account', $supplier->bank_account ?? '') }}" class="form-control form-erp">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-erp">IFSC Code</label>
                        <input type="text" name="ifsc" value="{{ old('ifsc', $supplier->ifsc ?? '') }}" class="form-control form-erp" placeholder="SBIN0001234">
                    </div>
                </div>
            </div>
        </div>

        <!-- CREDIT -->
        <div class="tab-pane fade" id="tab-credit">
            <div class="card-erp p-3">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label-erp">Credit Limit (₹)</label>
                        <input type="number" name="credit_limit" value="{{ old('credit_limit', $supplier->credit_limit ?? 0) }}" class="form-control form-erp" min="0" step="0.01">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-erp">Credit Days</label>
                        <input type="number" name="credit_days" value="{{ old('credit_days', $supplier->credit_days ?? 30) }}" class="form-control form-erp" min="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-erp">Payment Terms</label>
                        <input type="text" name="payment_terms" value="{{ old('payment_terms', $supplier->payment_terms ?? '') }}" class="form-control form-erp" placeholder="Net 30, COD...">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <button type="submit" class="btn btn-gold me-2"><i class="bi bi-check-lg me-1"></i>{{ $supplier ? 'Update Supplier' : 'Create Supplier' }}</button>
        <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>
@endsection
