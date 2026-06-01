@extends('layouts.app')
@section('title', 'Payroll')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-wallet2 me-2"></i>Payroll Months</h5>
    <button class="btn btn-sm btn-gold" data-bs-toggle="modal" data-bs-target="#processModal">
        <i class="bi bi-play-circle me-1"></i>Process Payroll
    </button>
</div>

@foreach(['success','error'] as $t)
@if(session($t))<div class="alert alert-{{ $t==='error'?'danger':'success' }} alert-dismissible fade show">{{ session($t) }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
@endforeach

<div class="table-responsive">
    <table class="table table-dark-gold table-hover table-sm">
        <thead>
            <tr>
                <th>#</th>
                <th>Month/Year</th>
                <th class="text-center">Employees</th>
                <th class="text-end">Total Gross</th>
                <th class="text-end">Total Deductions</th>
                <th class="text-end">Total Net</th>
                <th>Status</th>
                <th>Processed</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($months as $m)
            <tr>
                <td>{{ $months->firstItem() + $loop->index }}</td>
                <td><strong class="text-gold">{{ date('F', mktime(0,0,0,$m->month,1)) }} {{ $m->year }}</strong></td>
                <td class="text-center">{{ $m->total_employees }}</td>
                <td class="text-end">₹{{ number_format($m->total_gross, 2) }}</td>
                <td class="text-end text-danger">₹{{ number_format($m->total_deductions, 2) }}</td>
                <td class="text-end text-success fw-bold">₹{{ number_format($m->total_net, 2) }}</td>
                <td>
                    @php $badgeClass = ['draft'=>'bg-secondary','processed'=>'bg-info','approved'=>'bg-warning text-dark','paid'=>'bg-success']; @endphp
                    <span class="badge {{ $badgeClass[$m->status] ?? 'bg-secondary' }}">{{ ucfirst($m->status) }}</span>
                </td>
                <td><small>{{ $m->processed_at?->format('d M Y H:i') ?? '—' }}</small></td>
                <td>
                    <a href="{{ route('payroll.show', $m->id) }}" class="btn btn-xs btn-outline-gold me-1"><i class="bi bi-eye"></i></a>
                    @if(in_array($m->status, ['processed','approved']))
                    <form action="{{ route('payroll.postSalary', $m->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Post salary to daybook?')">
                        @csrf
                        <button class="btn btn-xs btn-outline-success me-1" title="Post Salary"><i class="bi bi-journal-check"></i></button>
                    </form>
                    @endif
                    @if($m->status !== 'paid')
                    <form action="{{ route('payroll.destroy', $m->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete payroll month?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-xs btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="9" class="text-center text-muted py-4">No payroll months processed yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $months->links() }}

{{-- Process Modal --}}
<div class="modal fade" id="processModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background:var(--bg-card);border:1px solid var(--border-gold)">
            <form method="POST" action="{{ route('payroll.process') }}">
                @csrf
                <div class="modal-header" style="border-color:var(--border-gold)">
                    <h5 class="modal-title text-gold">Process Payroll</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label-gold">Month *</label>
                            <select name="month" class="form-select form-control-dark" required>
                                @for($m=1;$m<=12;$m++)
                                    <option value="{{ $m }}" @selected($m == now()->month)>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-gold">Year *</label>
                            <input type="number" name="year" class="form-control form-control-dark" value="{{ now()->year }}" required min="2020">
                        </div>
                    </div>
                    <div class="alert alert-warning mt-3 small">
                        This will calculate salaries for all active employees based on attendance records.
                    </div>
                </div>
                <div class="modal-footer" style="border-color:var(--border-gold)">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-gold btn-sm"><i class="bi bi-play-circle me-1"></i>Process</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
