@extends('layouts.app')
@section('title', $employee ? 'Edit Employee' : 'Add Employee')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <h5 class="text-gold mb-0">
        <i class="bi bi-person-badge me-2"></i>{{ $employee ? 'Edit Employee: '.$employee->name : 'Add Employee' }}
    </h5>
    <a href="{{ route('employees.index') }}" class="btn btn-sm btn-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<form method="POST" action="{{ $employee ? route('employees.update', $employee->id) : route('employees.store') }}">
    @csrf
    @if($employee) @method('PUT') @endif

    <ul class="nav nav-tabs nav-tabs-gold mb-3" id="empTabs">
        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#personal">Personal</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#salary">Salary</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#bank">Bank</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#compliance">Compliance</button></li>
    </ul>

    <div class="tab-content">
        {{-- PERSONAL TAB --}}
        <div class="tab-pane fade show active" id="personal">
            <div class="card-dark p-3">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label-gold">Employee Code *</label>
                        <input type="text" name="employee_code" class="form-control form-control-dark" value="{{ old('employee_code', $employee->employee_code ?? '') }}" required>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label-gold">Full Name *</label>
                        <input type="text" name="name" class="form-control form-control-dark" value="{{ old('name', $employee->name ?? '') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-gold">Designation</label>
                        <input type="text" name="designation" class="form-control form-control-dark" value="{{ old('designation', $employee->designation ?? '') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-gold">Department</label>
                        <select name="department_id" class="form-select form-control-dark">
                            <option value="">Select Department</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" @selected(old('department_id', $employee->department_id ?? '') == $dept->id)>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-gold">Branch</label>
                        <select name="branch_id" class="form-select form-control-dark">
                            <option value="">Select Branch</option>
                            @foreach($branches as $br)
                                <option value="{{ $br->id }}" @selected(old('branch_id', $employee->branch_id ?? '') == $br->id)>{{ $br->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-gold">Gender</label>
                        <select name="gender" class="form-select form-control-dark">
                            <option value="">Select</option>
                            <option value="male" @selected(old('gender', $employee->gender ?? '') == 'male')>Male</option>
                            <option value="female" @selected(old('gender', $employee->gender ?? '') == 'female')>Female</option>
                            <option value="other" @selected(old('gender', $employee->gender ?? '') == 'other')>Other</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-gold">Date of Birth</label>
                        <input type="date" name="date_of_birth" class="form-control form-control-dark" value="{{ old('date_of_birth', $employee->date_of_birth?->format('Y-m-d') ?? '') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-gold">Date of Joining</label>
                        <input type="date" name="date_of_joining" class="form-control form-control-dark" value="{{ old('date_of_joining', $employee->date_of_joining?->format('Y-m-d') ?? '') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-gold">Mobile</label>
                        <input type="text" name="mobile" class="form-control form-control-dark" value="{{ old('mobile', $employee->mobile ?? '') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-gold">Email</label>
                        <input type="email" name="email" class="form-control form-control-dark" value="{{ old('email', $employee->email ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-gold">Address</label>
                        <textarea name="address" class="form-control form-control-dark" rows="2">{{ old('address', $employee->address ?? '') }}</textarea>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-gold">Status</label>
                        <select name="status" class="form-select form-control-dark">
                            <option value="active" @selected(old('status', $employee->status ?? 'active') == 'active')>Active</option>
                            <option value="inactive" @selected(old('status', $employee->status ?? '') == 'inactive')>Inactive</option>
                            <option value="terminated" @selected(old('status', $employee->status ?? '') == 'terminated')>Terminated</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- SALARY TAB --}}
        <div class="tab-pane fade" id="salary">
            <div class="card-dark p-3">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label-gold">Basic Salary</label>
                        <input type="number" step="0.01" name="basic_salary" class="form-control form-control-dark" value="{{ old('basic_salary', $employee->basic_salary ?? 0) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-gold">HRA</label>
                        <input type="number" step="0.01" name="hra" class="form-control form-control-dark" value="{{ old('hra', $employee->hra ?? 0) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-gold">Transport Allowance</label>
                        <input type="number" step="0.01" name="transport_allowance" class="form-control form-control-dark" value="{{ old('transport_allowance', $employee->transport_allowance ?? 0) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-gold">Other Allowance</label>
                        <input type="number" step="0.01" name="other_allowance" class="form-control form-control-dark" value="{{ old('other_allowance', $employee->other_allowance ?? 0) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-gold">Gross Salary (auto)</label>
                        <input type="number" step="0.01" id="gross_display" class="form-control form-control-dark" readonly value="{{ old('gross_salary', $employee->gross_salary ?? 0) }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- BANK TAB --}}
        <div class="tab-pane fade" id="bank">
            <div class="card-dark p-3">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label-gold">Bank Name</label>
                        <input type="text" name="bank_name" class="form-control form-control-dark" value="{{ old('bank_name', $employee->bank_name ?? '') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-gold">Account Number</label>
                        <input type="text" name="bank_account" class="form-control form-control-dark" value="{{ old('bank_account', $employee->bank_account ?? '') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-gold">IFSC Code</label>
                        <input type="text" name="bank_ifsc" class="form-control form-control-dark" value="{{ old('bank_ifsc', $employee->bank_ifsc ?? '') }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- COMPLIANCE TAB --}}
        <div class="tab-pane fade" id="compliance">
            <div class="card-dark p-3">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label-gold">PAN Number</label>
                        <input type="text" name="pan_number" class="form-control form-control-dark" value="{{ old('pan_number', $employee->pan_number ?? '') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-gold">Aadhaar Number</label>
                        <input type="text" name="aadhaar_number" class="form-control form-control-dark" value="{{ old('aadhaar_number', $employee->aadhaar_number ?? '') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-gold">PF Number</label>
                        <input type="text" name="pf_number" class="form-control form-control-dark" value="{{ old('pf_number', $employee->pf_number ?? '') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-gold">ESI Number</label>
                        <input type="text" name="esi_number" class="form-control form-control-dark" value="{{ old('esi_number', $employee->esi_number ?? '') }}">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label-gold d-block mb-2">Applicability</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="pf_applicable" value="1" id="pf_app" @checked(old('pf_applicable', $employee->pf_applicable ?? false))>
                            <label class="form-check-label text-gold" for="pf_app">PF Applicable</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="esi_applicable" value="1" id="esi_app" @checked(old('esi_applicable', $employee->esi_applicable ?? false))>
                            <label class="form-check-label text-gold" for="esi_app">ESI Applicable</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="tds_applicable" value="1" id="tds_app" @checked(old('tds_applicable', $employee->tds_applicable ?? false))>
                            <label class="form-check-label text-gold" for="tds_app">TDS Applicable</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3 d-flex gap-2">
        <button type="submit" class="btn btn-gold">
            <i class="bi bi-save me-1"></i>{{ $employee ? 'Update' : 'Save' }} Employee
        </button>
        <a href="{{ route('employees.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>

@push('scripts')
<script>
(function(){
    const fields = ['basic_salary','hra','transport_allowance','other_allowance'];
    function updateGross(){
        const g = fields.reduce((s,n)=>s+parseFloat(document.querySelector('[name='+n+']').value||0),0);
        document.getElementById('gross_display').value = g.toFixed(2);
    }
    fields.forEach(n=>document.querySelector('[name='+n+']').addEventListener('input',updateGross));
})();
</script>
@endpush
@endsection
