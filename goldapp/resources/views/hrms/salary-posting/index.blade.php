@extends('layouts.app')
@section('title', 'Salary Posting')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-journal-text me-2"></i>Salary Posting — Daybook Entries</h5>
</div>

<div class="card-dark mb-3">
    <form method="GET" class="row g-2 align-items-end p-3">
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
                <th>Slno</th>
                <th>Date</th>
                <th>Particular</th>
                <th>VType</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($entries as $e)
            <tr>
                <td><span class="badge bg-secondary font-monospace">{{ $e->slno }}</span></td>
                <td>{{ $e->tdate }}</td>
                <td>{{ $e->particular }}</td>
                <td><span class="badge bg-secondary">{{ $e->vtype }}</span></td>
                <td>
                    <a href="{{ route('salary-posting.show', $e->id) }}" class="btn btn-xs btn-outline-gold"><i class="bi bi-eye"></i></a>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center text-muted py-4">No salary postings found. Post salary from the Payroll section.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $entries->links() }}
@endsection
