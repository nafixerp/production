@extends('layouts.app')
@section('title', 'Employees')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-people me-2"></i>Employees</h5>
    <a href="{{ route('employees.create') }}" class="btn btn-sm btn-gold">
        <i class="bi bi-plus-circle me-1"></i> Add Employee
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card-dark mb-3">
    <form method="GET" class="row g-2 align-items-end p-3">
        <div class="col-md-5">
            <input type="text" name="q" value="{{ $q ?? '' }}" class="form-control form-control-dark" placeholder="Search name, code, designation…">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select form-control-dark">
                <option value="">All Status</option>
                <option value="active" @selected(($status??'')=='active')>Active</option>
                <option value="inactive" @selected(($status??'')=='inactive')>Inactive</option>
                <option value="terminated" @selected(($status??'')=='terminated')>Terminated</option>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-sm btn-outline-gold w-100">Filter</button>
        </div>
        <div class="col-md-2">
            <a href="{{ route('employees.index') }}" class="btn btn-sm btn-secondary w-100">Reset</a>
        </div>
    </form>
</div>

<div class="table-responsive">
    <table class="table table-dark-gold table-hover table-sm">
        <thead>
            <tr>
                <th>#</th>
                <th>Code</th>
                <th>Name</th>
                <th>Designation</th>
                <th>Mobile</th>
                <th class="text-end">Basic</th>
                <th class="text-end">Gross</th>
                <th>PF</th>
                <th>ESI</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($employees as $emp)
            <tr>
                <td>{{ $employees->firstItem() + $loop->index }}</td>
                <td><span class="badge bg-secondary">{{ $emp->employee_code }}</span></td>
                <td><a href="{{ route('employees.show', $emp->id) }}" class="text-gold-light">{{ $emp->name }}</a></td>
                <td>{{ $emp->designation ?? '—' }}</td>
                <td>{{ $emp->mobile ?? '—' }}</td>
                <td class="text-end">₹{{ number_format($emp->basic_salary, 0) }}</td>
                <td class="text-end">₹{{ number_format($emp->gross_salary, 0) }}</td>
                <td>@if($emp->pf_applicable)<span class="badge bg-success">PF</span>@else<span class="text-muted">—</span>@endif</td>
                <td>@if($emp->esi_applicable)<span class="badge bg-info">ESI</span>@else<span class="text-muted">—</span>@endif</td>
                <td>
                    @if($emp->status === 'active')
                        <span class="badge bg-success">Active</span>
                    @elseif($emp->status === 'inactive')
                        <span class="badge bg-warning text-dark">Inactive</span>
                    @else
                        <span class="badge bg-danger">Terminated</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('employees.edit', $emp->id) }}" class="btn btn-xs btn-outline-gold me-1"><i class="bi bi-pencil"></i></a>
                    <form action="{{ route('employees.destroy', $emp->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete employee?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-xs btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="11" class="text-center text-muted py-4">No employees found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $employees->links() }}
@endsection
