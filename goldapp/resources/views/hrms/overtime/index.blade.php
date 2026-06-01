@extends('layouts.app')
@section('title', 'Overtime Records')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-clock-history me-2"></i>Overtime Records</h5>
    <a href="{{ route('overtime.create') }}" class="btn btn-sm btn-gold"><i class="bi bi-plus-circle me-1"></i>Add OT</a>
</div>

@foreach(['success','error'] as $t)
@if(session($t))<div class="alert alert-{{ $t==='error'?'danger':'success' }} alert-dismissible fade show">{{ session($t) }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
@endforeach

<div class="card-dark mb-3">
    <form method="GET" class="row g-2 align-items-end p-3">
        <div class="col-md-3">
            <select name="employee_id" class="form-select form-control-dark">
                <option value="">All Employees</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" @selected(($employeeId??'')==$emp->id)>{{ $emp->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="month" class="form-select form-control-dark">
                <option value="">All Months</option>
                @for($m=1;$m<=12;$m++)
                    <option value="{{ $m }}" @selected(($month??'')==$m)>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                @endfor
            </select>
        </div>
        <div class="col-md-2">
            <input type="number" name="year" value="{{ $year }}" class="form-control form-control-dark" min="2020">
        </div>
        <div class="col-md-2"><button class="btn btn-sm btn-outline-gold w-100">Filter</button></div>
    </form>
</div>

<div class="table-responsive">
    <table class="table table-dark-gold table-hover table-sm">
        <thead>
            <tr>
                <th>#</th>
                <th>Employee</th>
                <th>Date</th>
                <th class="text-end">Hours</th>
                <th class="text-end">Rate/Hr</th>
                <th class="text-end">Amount</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $r)
            <tr>
                <td>{{ $records->firstItem() + $loop->index }}</td>
                <td>{{ $r->employee?->name ?? '—' }}</td>
                <td>{{ $r->date?->format('d M Y') }}</td>
                <td class="text-end">{{ $r->hours }}</td>
                <td class="text-end">₹{{ number_format($r->rate_per_hour, 2) }}</td>
                <td class="text-end">₹{{ number_format($r->amount, 2) }}</td>
                <td>
                    @if($r->status === 'pending')
                        <span class="badge bg-warning text-dark">Pending</span>
                    @elseif($r->status === 'approved')
                        <span class="badge bg-success">Approved</span>
                    @else
                        <span class="badge bg-info">Paid</span>
                    @endif
                </td>
                <td>
                    @if($r->status === 'pending')
                        <form action="{{ route('overtime.approve', $r->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-xs btn-outline-success me-1" title="Approve"><i class="bi bi-check-circle"></i></button>
                        </form>
                    @endif
                    <a href="{{ route('overtime.edit', $r->id) }}" class="btn btn-xs btn-outline-gold me-1"><i class="bi bi-pencil"></i></a>
                    <form action="{{ route('overtime.destroy', $r->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-xs btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center text-muted py-4">No overtime records found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $records->links() }}
@endsection
