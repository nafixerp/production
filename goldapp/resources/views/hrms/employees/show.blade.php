@extends('layouts.app')
@section('title', 'Employee: '.$employee->name)

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-person-badge me-2"></i>{{ $employee->name }}</h5>
    <div class="d-flex gap-2">
        <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-sm btn-outline-gold"><i class="bi bi-pencil me-1"></i>Edit</a>
        <a href="{{ route('employees.index') }}" class="btn btn-sm btn-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
</div>

<div class="row g-3">
    {{-- Basic Info Card --}}
    <div class="col-md-4">
        <div class="card-dark p-3 h-100">
            <div class="text-center mb-3">
                <div style="width:72px;height:72px;border-radius:50%;background:rgba(212,175,55,.15);border:2px solid var(--gold);display:flex;align-items:center;justify-content:center;margin:0 auto">
                    <i class="bi bi-person-fill" style="font-size:2rem;color:var(--gold)"></i>
                </div>
                <h6 class="text-gold mt-2 mb-0">{{ $employee->name }}</h6>
                <small class="text-muted">{{ $employee->designation ?? 'No designation' }}</small>
                <br>
                <span class="badge mt-1 {{ $employee->status === 'active' ? 'bg-success' : ($employee->status === 'inactive' ? 'bg-warning text-dark' : 'bg-danger') }}">
                    {{ ucfirst($employee->status) }}
                </span>
            </div>
            <hr style="border-color:var(--border-gold)">
            <div class="small">
                <div class="d-flex justify-content-between py-1"><span class="text-muted">Code</span><strong class="text-gold">{{ $employee->employee_code }}</strong></div>
                <div class="d-flex justify-content-between py-1"><span class="text-muted">Mobile</span><span>{{ $employee->mobile ?? '—' }}</span></div>
                <div class="d-flex justify-content-between py-1"><span class="text-muted">Email</span><span>{{ $employee->email ?? '—' }}</span></div>
                <div class="d-flex justify-content-between py-1"><span class="text-muted">Joined</span><span>{{ $employee->date_of_joining?->format('d M Y') ?? '—' }}</span></div>
            </div>
        </div>
    </div>

    {{-- Salary Card --}}
    <div class="col-md-4">
        <div class="card-dark p-3 h-100">
            <h6 class="text-gold mb-3"><i class="bi bi-currency-rupee me-1"></i>Salary Structure</h6>
            <div class="small">
                <div class="d-flex justify-content-between py-1 border-bottom" style="border-color:var(--border-gold)!important"><span class="text-muted">Basic</span><strong>₹{{ number_format($employee->basic_salary, 2) }}</strong></div>
                <div class="d-flex justify-content-between py-1"><span class="text-muted">HRA</span><span>₹{{ number_format($employee->hra, 2) }}</span></div>
                <div class="d-flex justify-content-between py-1"><span class="text-muted">Transport</span><span>₹{{ number_format($employee->transport_allowance, 2) }}</span></div>
                <div class="d-flex justify-content-between py-1"><span class="text-muted">Other</span><span>₹{{ number_format($employee->other_allowance, 2) }}</span></div>
                <div class="d-flex justify-content-between py-1 border-top" style="border-color:var(--gold)!important"><span class="text-gold fw-bold">Gross</span><strong class="text-gold">₹{{ number_format($employee->gross_salary, 2) }}</strong></div>
            </div>
            <hr style="border-color:var(--border-gold)">
            <div class="d-flex gap-2 flex-wrap">
                @if($employee->pf_applicable)<span class="badge bg-success">PF</span>@endif
                @if($employee->esi_applicable)<span class="badge bg-info">ESI</span>@endif
                @if($employee->tds_applicable)<span class="badge bg-warning text-dark">TDS</span>@endif
            </div>
        </div>
    </div>

    {{-- Attendance This Month --}}
    <div class="col-md-4">
        <div class="card-dark p-3 h-100">
            <h6 class="text-gold mb-3"><i class="bi bi-calendar-check me-1"></i>This Month Attendance</h6>
            <div class="row g-2 text-center">
                <div class="col-6">
                    <div style="background:rgba(40,167,69,.15);border:1px solid #28a745;border-radius:8px;padding:12px">
                        <div style="font-size:1.5rem;color:#28a745;font-weight:700">{{ $attendanceSummary['present'] ?? 0 }}</div>
                        <small class="text-muted">Present</small>
                    </div>
                </div>
                <div class="col-6">
                    <div style="background:rgba(220,53,69,.15);border:1px solid #dc3545;border-radius:8px;padding:12px">
                        <div style="font-size:1.5rem;color:#dc3545;font-weight:700">{{ $attendanceSummary['absent'] ?? 0 }}</div>
                        <small class="text-muted">Absent</small>
                    </div>
                </div>
                <div class="col-6">
                    <div style="background:rgba(255,193,7,.15);border:1px solid #ffc107;border-radius:8px;padding:12px">
                        <div style="font-size:1.5rem;color:#ffc107;font-weight:700">{{ $attendanceSummary['half_day'] ?? 0 }}</div>
                        <small class="text-muted">Half Day</small>
                    </div>
                </div>
                <div class="col-6">
                    <div style="background:rgba(23,162,184,.15);border:1px solid #17a2b8;border-radius:8px;padding:12px">
                        <div style="font-size:1.5rem;color:#17a2b8;font-weight:700">{{ $attendanceSummary['leave'] ?? 0 }}</div>
                        <small class="text-muted">On Leave</small>
                    </div>
                </div>
            </div>
            @if($latestPayroll)
            <hr style="border-color:var(--border-gold)">
            <div class="small">
                <div class="text-muted mb-1">Last Payroll ({{ $latestPayroll->payrollMonth?->month_name }} {{ $latestPayroll->payrollMonth?->year }})</div>
                <div class="d-flex justify-content-between"><span>Net Salary</span><strong class="text-gold">₹{{ number_format($latestPayroll->net_salary, 2) }}</strong></div>
                <div class="d-flex justify-content-between"><span>Status</span>
                    <span class="badge {{ $latestPayroll->status === 'paid' ? 'bg-success' : 'bg-warning text-dark' }}">{{ ucfirst($latestPayroll->status) }}</span>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
