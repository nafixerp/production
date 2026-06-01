@extends('layouts.app')
@section('title', isset($customer) ? 'Edit Customer' : 'New Customer')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-person-plus me-2"></i>{{ isset($customer) ? 'Edit Customer: '.$customer->name : 'New Customer' }}</h5>
    <a href="{{ route('customers.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
</div>

@if($errors->any())
<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<form method="POST" action="{{ isset($customer) ? route('customers.update',$customer->id) : route('customers.store') }}">
    @csrf @if(isset($customer)) @method('PUT') @endif

    <ul class="nav nav-tabs mb-3" id="customerTabs">
        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-basic">Basic Info</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-contact">Contact & Address</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-credit">Credit & Pricing</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-notes">Notes & Dates</a></li>
    </ul>

    <div class="tab-content">
        <!-- BASIC -->
        <div class="tab-pane fade show active" id="tab-basic">
            <div class="card-dark p-3">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Code *</label>
                        <input type="text" name="code" class="form-control" value="{{ old('code', $customer->code ?? $nextCode ?? '') }}" required>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Name *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $customer->name ?? '') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Contact Person</label>
                        <input type="text" name="contact_person" class="form-control" value="{{ old('contact_person', $customer->contact_person ?? '') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Customer Type *</label>
                        <select name="customer_type" class="form-select" required>
                            @foreach(['retail','wholesale','distributor','online','export'] as $t)
                                <option value="{{ $t }}" {{ old('customer_type', $customer->customer_type ?? 'retail') === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">GST No.</label>
                        <input type="text" name="gst_no" class="form-control" value="{{ old('gst_no', $customer->gst_no ?? '') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">PAN No.</label>
                        <input type="text" name="pan_no" class="form-control" value="{{ old('pan_no', $customer->pan_no ?? '') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="1" {{ (old('status', $customer->status ?? 1) == 1) ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ (old('status', $customer->status ?? 1) == 0) ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- CONTACT -->
        <div class="tab-pane fade" id="tab-contact">
            <div class="card-dark p-3">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Phone *</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $customer->phone ?? '') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Alt Phone</label>
                        <input type="text" name="alt_phone" class="form-control" value="{{ old('alt_phone', $customer->alt_phone ?? '') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $customer->email ?? '') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="2">{{ old('address', $customer->address ?? '') }}</textarea>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-control" value="{{ old('city', $customer->city ?? '') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">State</label>
                        <input type="text" name="state" class="form-control" value="{{ old('state', $customer->state ?? '') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Pincode</label>
                        <input type="text" name="pincode" class="form-control" value="{{ old('pincode', $customer->pincode ?? '') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Country</label>
                        <input type="text" name="country" class="form-control" value="{{ old('country', $customer->country ?? 'India') }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- CREDIT -->
        <div class="tab-pane fade" id="tab-credit">
            <div class="card-dark p-3">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Credit Limit (₹)</label>
                        <input type="number" step="0.01" name="credit_limit" class="form-control" value="{{ old('credit_limit', $customer->credit_limit ?? 0) }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Credit Days</label>
                        <input type="number" name="credit_days" class="form-control" value="{{ old('credit_days', $customer->credit_days ?? 0) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Payment Terms</label>
                        <input type="text" name="payment_terms" class="form-control" value="{{ old('payment_terms', $customer->payment_terms ?? '') }}" placeholder="e.g., Net 30">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Price Tier</label>
                        <select name="price_tier" class="form-select">
                            @foreach(['standard','tier1','tier2','tier3'] as $t)
                                <option value="{{ $t }}" {{ old('price_tier', $customer->price_tier ?? 'standard') === $t ? 'selected' : '' }}>{{ ucfirst(str_replace('tier','Tier ',$t)) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- NOTES -->
        <div class="tab-pane fade" id="tab-notes">
            <div class="card-dark p-3">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="dob" class="form-control" value="{{ old('dob', isset($customer->dob) ? $customer->dob->format('Y-m-d') : '') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Anniversary</label>
                        <input type="date" name="anniversary" class="form-control" value="{{ old('anniversary', isset($customer->anniversary) ? $customer->anniversary->format('Y-m-d') : '') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="4">{{ old('notes', $customer->notes ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3 d-flex gap-2">
        <button type="submit" class="btn btn-gold">{{ isset($customer) ? 'Update' : 'Save' }} Customer</button>
        <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>
@endsection
