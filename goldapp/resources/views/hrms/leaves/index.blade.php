@extends('layouts.app')
@section('title', 'Leave Applications')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-calendar-x me-2"></i>Leave Applications</h5>
    <a href="{{ route('leaves.create') }}" class="btn btn-sm btn-gold"><i class="bi bi-plus-circle me-1"></i>Apply Leave</a>
</div>

@foreach(['success','error'] as $t)
@if(session($t))<div class="alert alert-{{ $t === 'error' ? 'danger' : 'success' }} alert-dismissible fade show">{{ session($t) }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
@endforeach

<div class="card-dark mb-3">
    <form method="GET" class="row g-2 align-items-end p-3">
        <div class="col-md-3">
            <select name="status" class="form-select form-control-dark">
                <option value="">All Status</option>
                <option value="pending"  @selected(($status??'')=='pending')>Pending</option>
                <option value="approved" @selected(($status??'')=='approved')>Approved</option>
                <option value="rejected" @selected(($status??'')=='rejected')>Rejected</option>
            </select>
        </div>
        <div class="col-md-4">
            <select name="employee_id" class="form-select form-control-dark">
                <option value="">All Employees</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" @selected(($employeeId??'')==$emp->id)>{{ $emp->name }}</option>
                @endforeach
            </select>
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
                <th>Type</th>
                <th>From</th>
                <th>To</th>
                <th>Days</th>
                <th>Reason</th>
                <th>Status</th>
                <th>Applied</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($leaves as $l)
            <tr>
                <td>{{ $leaves->firstItem() + $loop->index }}</td>
                <td>{{ $l->employee?->name ?? '—' }}</td>
                <td><span class="badge bg-secondary">{{ ucfirst($l->leave_type) }}</span></td>
                <td>{{ $l->from_date?->format('d M Y') }}</td>
                <td>{{ $l->to_date?->format('d M Y') }}</td>
                <td class="text-center">{{ $l->days }}</td>
                <td>{{ Str::limit($l->reason, 40) }}</td>
                <td>
                    @if($l->status === 'pending')
                        <span class="badge bg-warning text-dark">Pending</span>
                    @elseif($l->status === 'approved')
                        <span class="badge bg-success">Approved</span>
                    @else
                        <span class="badge bg-danger">Rejected</span>
                    @endif
                </td>
                <td>{{ $l->created_at?->format('d M Y') }}</td>
                <td>
                    @if($l->status === 'pending')
                        <form action="{{ route('leaves.approve', $l->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-xs btn-outline-success me-1" title="Approve"><i class="bi bi-check-circle"></i></button>
                        </form>
                        <form action="{{ route('leaves.reject', $l->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-xs btn-outline-danger" title="Reject"><i class="bi bi-x-circle"></i></button>
                        </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="10" class="text-center text-muted py-4">No leave applications found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $leaves->links() }}
@endsection
