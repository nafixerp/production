@extends('layouts.app')
@section('title', 'PF/ESI/TDS Detail')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-shield-check me-2"></i>PF/ESI/TDS — {{ date('F', mktime(0,0,0,$record->month,1)) }} {{ $record->year }}</h5>
    <a href="{{ route('pf-esi-tds.index') }}" class="btn btn-sm btn-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<div class="row g-3 mb-4">
    @foreach([
        ['PF Employee', $record->pf_employee_total, '#dc3545'],
        ['PF Employer', $record->pf_employer_total, '#fd7e14'],
        ['ESI Employee', $record->esi_employee_total, '#17a2b8'],
        ['ESI Employer', $record->esi_employer_total, '#0d6efd'],
        ['TDS', $record->tds_total, '#6610f2'],
    ] as [$label, $val, $color])
    <div class="col-md-2">
        <div class="card-dark p-3 text-center">
            <div class="text-muted small">{{ $label }}</div>
            <div style="color:{{ $color }};font-weight:700">₹{{ number_format($val, 2) }}</div>
        </div>
    </div>
    @endforeach
    <div class="col-md-2">
        <div class="card-dark p-3 text-center">
            <div class="text-muted small">Total</div>
            <div class="text-gold fw-bold fs-6">₹{{ number_format($record->pf_employee_total+$record->pf_employer_total+$record->esi_employee_total+$record->esi_employer_total+$record->tds_total, 2) }}</div>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-dark-gold table-sm">
        <thead>
            <tr>
                <th>#</th>
                <th>Employee</th>
                <th>Designation</th>
                <th class="text-end">Basic</th>
                <th class="text-end">Gross</th>
                <th class="text-end">PF (Emp)</th>
                <th class="text-end">PF (Emplr)</th>
                <th class="text-end">ESI (Emp)</th>
                <th class="text-end">ESI (Emplr)</th>
                <th class="text-end">TDS</th>
            </tr>
        </thead>
        <tbody>
            @foreach($details as $d)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $d->employee_name }}</td>
                <td><small>{{ $d->designation ?? '—' }}</small></td>
                <td class="text-end">₹{{ number_format($d->basic, 2) }}</td>
                <td class="text-end">₹{{ number_format($d->gross_salary, 2) }}</td>
                <td class="text-end">₹{{ number_format($d->pf_employee, 2) }}</td>
                <td class="text-end">₹{{ number_format($d->pf_employer, 2) }}</td>
                <td class="text-end">₹{{ number_format($d->esi_employee, 2) }}</td>
                <td class="text-end">₹{{ number_format($d->esi_employer, 2) }}</td>
                <td class="text-end">₹{{ number_format($d->tds, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="border-top:2px solid var(--gold)">
                <th colspan="5" class="text-end text-gold">Total</th>
                <th class="text-end">₹{{ number_format($record->pf_employee_total, 2) }}</th>
                <th class="text-end">₹{{ number_format($record->pf_employer_total, 2) }}</th>
                <th class="text-end">₹{{ number_format($record->esi_employee_total, 2) }}</th>
                <th class="text-end">₹{{ number_format($record->esi_employer_total, 2) }}</th>
                <th class="text-end">₹{{ number_format($record->tds_total, 2) }}</th>
            </tr>
        </tfoot>
    </table>
</div>
@endsection
