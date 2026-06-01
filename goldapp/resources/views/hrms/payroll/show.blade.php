@extends('layouts.app')
@section('title', 'Payroll: '.$payrollMonth->month_name.' '.$payrollMonth->year)

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <h5 class="text-gold mb-0">
        <i class="bi bi-wallet2 me-2"></i>Payroll — {{ $payrollMonth->month_name }} {{ $payrollMonth->year }}
    </h5>
    <div class="d-flex gap-2">
        @if(in_array($payrollMonth->status, ['processed','approved']))
        <form action="{{ route('payroll.postSalary', $payrollMonth->id) }}" method="POST" onsubmit="return confirm('Post salary to daybook and mark as paid?')">
            @csrf
            <button class="btn btn-sm btn-success"><i class="bi bi-journal-check me-1"></i>Post Salary to Daybook</button>
        </form>
        @endif
        <a href="{{ route('payroll.index') }}" class="btn btn-sm btn-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
</div>

@foreach(['success','error'] as $t)
@if(session($t))<div class="alert alert-{{ $t==='error'?'danger':'success' }} alert-dismissible fade show">{{ session($t) }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
@endforeach

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    @php $badgeClass = ['draft'=>'bg-secondary','processed'=>'bg-info','approved'=>'bg-warning text-dark','paid'=>'bg-success']; @endphp
    <div class="col-md-2">
        <div class="card-dark p-3 text-center">
            <div class="text-muted small">Status</div>
            <span class="badge {{ $badgeClass[$payrollMonth->status] ?? 'bg-secondary' }} mt-1">{{ ucfirst($payrollMonth->status) }}</span>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card-dark p-3 text-center">
            <div class="text-muted small">Employees</div>
            <div class="text-gold fw-bold fs-5">{{ $payrollMonth->total_employees }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-dark p-3 text-center">
            <div class="text-muted small">Total Gross</div>
            <div class="fw-bold">₹{{ number_format($payrollMonth->total_gross, 2) }}</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card-dark p-3 text-center">
            <div class="text-muted small">Deductions</div>
            <div class="text-danger fw-bold">₹{{ number_format($payrollMonth->total_deductions, 2) }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-dark p-3 text-center">
            <div class="text-muted small">Net Payable</div>
            <div class="text-success fw-bold fs-5">₹{{ number_format($payrollMonth->total_net, 2) }}</div>
        </div>
    </div>
</div>

{{-- Employee Slips --}}
<div class="table-responsive">
    <table class="table table-dark-gold table-hover table-sm">
        <thead>
            <tr>
                <th>#</th>
                <th>Employee</th>
                <th>Designation</th>
                <th class="text-center">Days<br><small>W/P/L/A</small></th>
                <th class="text-end">Basic</th>
                <th class="text-end">Gross</th>
                <th class="text-end">OT</th>
                <th class="text-end">Earnings</th>
                <th class="text-end">PF(E)</th>
                <th class="text-end">ESI(E)</th>
                <th class="text-end">TDS</th>
                <th class="text-end">Ded.</th>
                <th class="text-end text-gold">Net</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payrollMonth->details as $d)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td><strong>{{ $d->employee_name }}</strong></td>
                <td><small>{{ $d->designation ?? '—' }}</small></td>
                <td class="text-center small">{{ $d->working_days }}/{{ $d->present_days }}/{{ $d->leave_days }}/{{ $d->absent_days }}</td>
                <td class="text-end">{{ number_format($d->basic, 0) }}</td>
                <td class="text-end">{{ number_format($d->gross_salary, 0) }}</td>
                <td class="text-end">{{ number_format($d->overtime_amount, 0) }}</td>
                <td class="text-end">{{ number_format($d->total_earnings, 0) }}</td>
                <td class="text-end text-danger">{{ number_format($d->pf_employee, 0) }}</td>
                <td class="text-end text-danger">{{ number_format($d->esi_employee, 0) }}</td>
                <td class="text-end text-danger">{{ number_format($d->tds, 0) }}</td>
                <td class="text-end text-danger">{{ number_format($d->total_deductions, 0) }}</td>
                <td class="text-end text-success fw-bold">{{ number_format($d->net_salary, 0) }}</td>
                <td><span class="badge {{ $d->status === 'paid' ? 'bg-success' : 'bg-warning text-dark' }}">{{ ucfirst($d->status) }}</span></td>
            </tr>
            @empty
            <tr><td colspan="14" class="text-center text-muted py-4">No payroll details.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="border-top:2px solid var(--gold)">
                <th colspan="4" class="text-end text-gold">Total</th>
                <th class="text-end">₹{{ number_format($payrollMonth->details->sum('basic'), 0) }}</th>
                <th class="text-end">₹{{ number_format($payrollMonth->details->sum('gross_salary'), 0) }}</th>
                <th class="text-end">₹{{ number_format($payrollMonth->details->sum('overtime_amount'), 0) }}</th>
                <th class="text-end">₹{{ number_format($payrollMonth->details->sum('total_earnings'), 0) }}</th>
                <th class="text-end text-danger">₹{{ number_format($payrollMonth->details->sum('pf_employee'), 0) }}</th>
                <th class="text-end text-danger">₹{{ number_format($payrollMonth->details->sum('esi_employee'), 0) }}</th>
                <th class="text-end text-danger">₹{{ number_format($payrollMonth->details->sum('tds'), 0) }}</th>
                <th class="text-end text-danger">₹{{ number_format($payrollMonth->total_deductions, 0) }}</th>
                <th class="text-end text-success fw-bold">₹{{ number_format($payrollMonth->total_net, 0) }}</th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</div>
@endsection
