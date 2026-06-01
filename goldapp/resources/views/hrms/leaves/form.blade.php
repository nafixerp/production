@extends('layouts.app')
@section('title', $leave ? 'Edit Leave' : 'Apply Leave')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-calendar-plus me-2"></i>{{ $leave ? 'Edit Leave Application' : 'Apply for Leave' }}</h5>
    <a href="{{ route('leaves.index') }}" class="btn btn-sm btn-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

@if($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card-dark p-4" style="max-width:600px">
    <form method="POST" action="{{ $leave ? route('leaves.update', $leave->id) : route('leaves.store') }}">
        @csrf
        @if($leave) @method('PUT') @endif

        <div class="mb-3">
            <label class="form-label-gold">Employee *</label>
            <select name="employee_id" class="form-select form-control-dark" required>
                <option value="">Select Employee</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" @selected(old('employee_id', $leave->employee_id ?? '') == $emp->id)>{{ $emp->name }} ({{ $emp->employee_code }})</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label-gold">Leave Type *</label>
            <select name="leave_type" class="form-select form-control-dark" required>
                <option value="casual"   @selected(old('leave_type', $leave->leave_type ?? '') == 'casual')>Casual Leave</option>
                <option value="sick"     @selected(old('leave_type', $leave->leave_type ?? '') == 'sick')>Sick Leave</option>
                <option value="earned"   @selected(old('leave_type', $leave->leave_type ?? '') == 'earned')>Earned Leave</option>
                <option value="maternity" @selected(old('leave_type', $leave->leave_type ?? '') == 'maternity')>Maternity Leave</option>
                <option value="unpaid"   @selected(old('leave_type', $leave->leave_type ?? '') == 'unpaid')>Unpaid Leave</option>
            </select>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label-gold">From Date *</label>
                <input type="date" name="from_date" class="form-control form-control-dark" value="{{ old('from_date', $leave->from_date?->format('Y-m-d') ?? '') }}" required id="fromDate">
            </div>
            <div class="col-md-6">
                <label class="form-label-gold">To Date *</label>
                <input type="date" name="to_date" class="form-control form-control-dark" value="{{ old('to_date', $leave->to_date?->format('Y-m-d') ?? '') }}" required id="toDate">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label-gold">Days: <span id="dayCount" class="text-gold fw-bold">0</span></label>
        </div>

        <div class="mb-3">
            <label class="form-label-gold">Reason</label>
            <textarea name="reason" class="form-control form-control-dark" rows="3">{{ old('reason', $leave->reason ?? '') }}</textarea>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-gold"><i class="bi bi-save me-1"></i>{{ $leave ? 'Update' : 'Submit' }}</button>
            <a href="{{ route('leaves.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
function calcDays(){
    const f=document.getElementById('fromDate').value;
    const t=document.getElementById('toDate').value;
    if(f&&t){
        const diff=(new Date(t)-new Date(f))/(1000*60*60*24)+1;
        document.getElementById('dayCount').textContent=diff>0?diff:0;
    }
}
document.getElementById('fromDate').addEventListener('change',calcDays);
document.getElementById('toDate').addEventListener('change',calcDays);
calcDays();
</script>
@endpush
@endsection
