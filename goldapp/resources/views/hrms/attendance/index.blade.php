@extends('layouts.app')
@section('title', 'Attendance')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-calendar-check me-2"></i>Attendance — {{ date('F Y', mktime(0,0,0,$month,1,$year)) }}</h5>
    <a href="{{ route('attendance.mark') }}" class="btn btn-sm btn-gold"><i class="bi bi-plus-circle me-1"></i>Mark Attendance</a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show"><{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="card-dark mb-3">
    <form method="GET" class="row g-2 align-items-end p-3">
        <div class="col-md-2">
            <select name="month" class="form-select form-control-dark">
                @for($m=1;$m<=12;$m++)
                    <option value="{{ $m }}" @selected($m==$month)>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                @endfor
            </select>
        </div>
        <div class="col-md-2">
            <input type="number" name="year" value="{{ $year }}" class="form-control form-control-dark" min="2020" max="2099">
        </div>
        <div class="col-md-3">
            <select name="department_id" class="form-select form-control-dark">
                <option value="">All Departments</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" @selected($dept->id == $department)>{{ $dept->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2"><button class="btn btn-sm btn-outline-gold w-100">Filter</button></div>
    </form>
</div>

{{-- Legend --}}
<div class="d-flex gap-3 mb-2 small">
    <span><span style="display:inline-block;width:14px;height:14px;background:#28a745;border-radius:2px"></span> Present</span>
    <span><span style="display:inline-block;width:14px;height:14px;background:#dc3545;border-radius:2px"></span> Absent</span>
    <span><span style="display:inline-block;width:14px;height:14px;background:#ffc107;border-radius:2px"></span> Half Day</span>
    <span><span style="display:inline-block;width:14px;height:14px;background:#17a2b8;border-radius:2px"></span> Leave</span>
    <span><span style="display:inline-block;width:14px;height:14px;background:#6c757d;border-radius:2px"></span> Holiday/Weekend</span>
</div>

<div class="table-responsive" style="overflow-x:auto">
    <table class="table table-dark-gold table-sm" style="min-width:1000px">
        <thead>
            <tr>
                <th style="min-width:150px;position:sticky;left:0;background:var(--bg-card)">Employee</th>
                @for($d=1;$d<=$daysInMonth;$d++)
                    @php $dow = date('N', mktime(0,0,0,$month,$d,$year)); @endphp
                    <th class="text-center px-1" style="min-width:32px;font-size:.65rem;{{ $dow>=6 ? 'color:#ffc107' : '' }}">
                        {{ $d }}<br><span style="font-size:.55rem">{{ date('D', mktime(0,0,0,$month,$d,$year)) }}</span>
                    </th>
                @endfor
                <th class="text-center">P</th>
                <th class="text-center">A</th>
                <th class="text-center">L</th>
            </tr>
        </thead>
        <tbody>
            @forelse($employees as $emp)
            @php $empRecords = $records->get($emp->id, collect())->keyBy(fn($r)=>$r->date->day); @endphp
            <tr>
                <td style="position:sticky;left:0;background:var(--bg-dark)">
                    <a href="{{ route('employees.show', $emp->id) }}" class="text-gold-light small">{{ $emp->name }}</a>
                    <br><small class="text-muted" style="font-size:.65rem">{{ $emp->employee_code }}</small>
                </td>
                @for($d=1;$d<=$daysInMonth;$d++)
                    @php
                        $rec = $empRecords->get($d);
                        $status = $rec?->status ?? null;
                        $dow = date('N', mktime(0,0,0,$month,$d,$year));
                        $colors = [
                            'present'  => '#28a745',
                            'absent'   => '#dc3545',
                            'half_day' => '#ffc107',
                            'leave'    => '#17a2b8',
                            'holiday'  => '#6c757d',
                            'weekend'  => '#6c757d',
                        ];
                        $bg = $status ? ($colors[$status] ?? '#444') : ($dow >= 6 ? '#2d2d2d' : 'transparent');
                        $abbr = ['present'=>'P','absent'=>'A','half_day'=>'H','leave'=>'L','holiday'=>'Ho','weekend'=>'W'];
                        $label = $status ? ($abbr[$status] ?? '?') : ($dow >= 6 ? 'W' : '');
                    @endphp
                    <td class="text-center px-0" style="font-size:.65rem">
                        <span style="display:inline-block;width:24px;height:22px;border-radius:3px;background:{{ $bg }};line-height:22px;color:#fff;font-weight:600">
                            {{ $label }}
                        </span>
                    </td>
                @endfor
                @php
                    $present = $empRecords->whereIn('status', ['present','half_day'])->count();
                    $absent  = $empRecords->where('status', 'absent')->count();
                    $leave   = $empRecords->where('status', 'leave')->count();
                @endphp
                <td class="text-center text-success fw-bold">{{ $present }}</td>
                <td class="text-center text-danger fw-bold">{{ $absent }}</td>
                <td class="text-center text-info fw-bold">{{ $leave }}</td>
            </tr>
            @empty
            <tr><td colspan="{{ $daysInMonth + 4 }}" class="text-center text-muted py-4">No active employees.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
