@extends('layouts.app')
@section('title','Company Profile')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-building me-2"></i>Company Profile</h5>
</div>

<form method="POST" action="{{ route('company-profile.update') }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="row g-3">
        <div class="col-md-8">
            <div class="card-gold mb-3">
                <div class="card-header-gold"><h5>Basic Information</h5></div>
                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label">Company Name *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $profile->name) }}" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Legal / Registered Name</label>
                        <input type="text" name="legal_name" class="form-control" value="{{ old('legal_name', $profile->legal_name) }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="2">{{ old('address', $profile->address) }}</textarea>
                    </div>
                    <div class="col-4">
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-control" value="{{ old('city', $profile->city) }}">
                    </div>
                    <div class="col-4">
                        <label class="form-label">State</label>
                        <input type="text" name="state" class="form-control" value="{{ old('state', $profile->state) }}">
                    </div>
                    <div class="col-4">
                        <label class="form-label">Pincode</label>
                        <input type="text" name="pincode" class="form-control" value="{{ old('pincode', $profile->pincode) }}">
                    </div>
                    <div class="col-4">
                        <label class="form-label">Country</label>
                        <input type="text" name="country" class="form-control" value="{{ old('country', $profile->country ?? 'India') }}">
                    </div>
                    <div class="col-4">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $profile->phone) }}">
                    </div>
                    <div class="col-4">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $profile->email) }}">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Website</label>
                        <input type="text" name="website" class="form-control" value="{{ old('website', $profile->website) }}" placeholder="https://...">
                    </div>
                </div>
            </div>

            <div class="card-gold mb-3">
                <div class="card-header-gold"><h5>Statutory Details</h5></div>
                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label">GST No</label>
                        <input type="text" name="gst_no" class="form-control" value="{{ old('gst_no', $profile->gst_no) }}" placeholder="22AAAAA0000A1Z5">
                    </div>
                    <div class="col-6">
                        <label class="form-label">PAN No</label>
                        <input type="text" name="pan_no" class="form-control" value="{{ old('pan_no', $profile->pan_no) }}" placeholder="AAAAA0000A">
                    </div>
                    <div class="col-6">
                        <label class="form-label">CIN No</label>
                        <input type="text" name="cin_no" class="form-control" value="{{ old('cin_no', $profile->cin_no) }}">
                    </div>
                    <div class="col-6">
                        <label class="form-label">FSSAI License No</label>
                        <input type="text" name="fssai_no" class="form-control" value="{{ old('fssai_no', $profile->fssai_no) }}">
                    </div>
                </div>
            </div>

            <div class="card-gold">
                <div class="card-header-gold"><h5>Localisation & Format</h5></div>
                <div class="row g-3">
                    <div class="col-3">
                        <label class="form-label">FY Start Month</label>
                        <select name="financial_year_start" class="form-select">
                            @for($m=1;$m<=12;$m++)
                                <option value="{{ $m }}" {{ ($profile->financial_year_start ?? 4) == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-3">
                        <label class="form-label">Currency Code</label>
                        <input type="text" name="currency" class="form-control" value="{{ old('currency', $profile->currency ?? 'INR') }}" maxlength="10">
                    </div>
                    <div class="col-2">
                        <label class="form-label">Symbol</label>
                        <input type="text" name="currency_symbol" class="form-control" value="{{ old('currency_symbol', $profile->currency_symbol ?? '₹') }}" maxlength="5">
                    </div>
                    <div class="col-2">
                        <label class="form-label">Date Format</label>
                        <input type="text" name="date_format" class="form-control" value="{{ old('date_format', $profile->date_format ?? 'd/m/Y') }}">
                    </div>
                    <div class="col-2">
                        <label class="form-label">Decimal Places</label>
                        <input type="number" name="decimal_places" class="form-control" value="{{ old('decimal_places', $profile->decimal_places ?? 2) }}" min="0" max="4">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card-gold">
                <div class="card-header-gold"><h5>Company Logo</h5></div>
                @if($profile->logo_path)
                <div class="text-center mb-3">
                    <img src="{{ asset('storage/'.$profile->logo_path) }}" alt="Logo" style="max-height:120px;max-width:100%;border-radius:6px;border:1px solid var(--border-gold)">
                </div>
                @endif
                <div>
                    <label class="form-label">Upload Logo</label>
                    <input type="file" name="logo" class="form-control" accept="image/*">
                    <small style="color:var(--text-muted-gold)">Max 2MB. JPG/PNG/SVG recommended.</small>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <button type="submit" class="btn-gold"><i class="bi bi-floppy me-1"></i>Save Company Profile</button>
    </div>
</form>
@endsection
