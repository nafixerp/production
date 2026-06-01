@extends('layouts.app')
@section('title', 'Shift Management')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-clock me-2"></i>Shifts</h5>
    <a href="{{ route('shifts.create') }}" class="btn btn-sm btn-gold"><i class="bi bi-plus-circle me-1"></i>Add Shift</a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="card-dark mb-3">
    <form method="GET" class="row g-2 p-3">
        <div class="col-md-5">
            <input type="text" name="q" value="{{ $q ?? '' }}" class="form-control form-control-dark" placeholder="Search name or code…">
        </div>
        <div class="col-md-2"><button class="btn btn-sm btn-outline-gold w-100">Search</button></div>
    </form>
</div>

<div class="table-responsive">
    <table class="table table-dark-gold table-hover table-sm">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Code</th>
                <th>Start</th>
                <th>End</th>
                <th>Break (min)</th>
                <th>Working Hrs</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($shifts as $s)
            <tr>
                <td>{{ $shifts->firstItem() + $loop->index }}</td>
                <td>{{ $s->name }}</td>
                <td><span class="badge bg-secondary">{{ $s->code }}</span></td>
                <td>{{ $s->start_time }}</td>
                <td>{{ $s->end_time }}</td>
                <td class="text-center">{{ $s->break_minutes }}</td>
                <td class="text-center">{{ $s->working_hours }}</td>
                <td><span class="badge {{ $s->status === 'active' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($s->status) }}</span></td>
                <td>
                    <a href="{{ route('shifts.edit', $s->id) }}" class="btn btn-xs btn-outline-gold me-1"><i class="bi bi-pencil"></i></a>
                    <form action="{{ route('shifts.destroy', $s->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-xs btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="9" class="text-center text-muted py-4">No shifts found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $shifts->links() }}
@endsection
