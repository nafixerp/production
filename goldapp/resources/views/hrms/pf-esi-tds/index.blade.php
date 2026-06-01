@extends('layouts.app')
@section('title', 'PF / ESI / TDS')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-shield-check me-2"></i>PF / ESI / TDS Compliance</h5>
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
                <th class="text-end">PF Employee</th>
                <th class="text-end">PF Employer</th>
                <th class="text-end">ESI Employee</th>
                <th class="text-end">ESI Employer</th>
                <th class="text-end">TDS</th>
                <th class="text-end">Total</th>
                <th>Status</th>
                <th>Challan</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $r)
            @php $total = $r->pf_employee_total + $r->pf_employer_total + $r->esi_employee_total + $r->esi_employer_total + $r->tds_total; @endphp
            <tr>
                <td>{{ $records->firstItem() + $loop->index }}</td>
                <td><strong class="text-gold">{{ date('F', mktime(0,0,0,$r->month,1)) }} {{ $r->year }}</strong></td>
                <td class="text-end">₹{{ number_format($r->pf_employee_total, 2) }}</td>
                <td class="text-end">₹{{ number_format($r->pf_employer_total, 2) }}</td>
                <td class="text-end">₹{{ number_format($r->esi_employee_total, 2) }}</td>
                <td class="text-end">₹{{ number_format($r->esi_employer_total, 2) }}</td>
                <td class="text-end">₹{{ number_format($r->tds_total, 2) }}</td>
                <td class="text-end fw-bold text-gold">₹{{ number_format($total, 2) }}</td>
                <td><span class="badge {{ $r->status === 'paid' ? 'bg-success' : 'bg-warning text-dark' }}">{{ ucfirst($r->status) }}</span></td>
                <td><small>{{ $r->challan_number ?? '—' }}</small></td>
                <td>
                    <a href="{{ route('pf-esi-tds.show', $r->id) }}" class="btn btn-xs btn-outline-gold me-1"><i class="bi bi-eye"></i></a>
                    @if($r->status === 'pending')
                        <button class="btn btn-xs btn-outline-success" data-bs-toggle="modal" data-bs-target="#paidModal{{ $r->id }}" title="Mark Paid"><i class="bi bi-check-circle"></i></button>
                    @endif
                </td>
            </tr>
            {{-- Mark Paid Modal --}}
            <div class="modal fade" id="paidModal{{ $r->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content" style="background:var(--bg-card);border:1px solid var(--border-gold)">
                        <form method="POST" action="{{ route('pf-esi-tds.markPaid', $r->id) }}">
                            @csrf
                            <div class="modal-header" style="border-color:var(--border-gold)">
                                <h5 class="modal-title text-gold">Mark PF/ESI/TDS Paid — {{ date('F', mktime(0,0,0,$r->month,1)) }} {{ $r->year }}</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label-gold">Challan Number</label>
                                        <input type="text" name="challan_number" class="form-control form-control-dark">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-gold">Challan Date</label>
                                        <input type="date" name="challan_date" class="form-control form-control-dark">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-gold">Payment Date</label>
                                        <input type="date" name="payment_date" class="form-control form-control-dark" value="{{ today()->toDateString() }}">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer" style="border-color:var(--border-gold)">
                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-success btn-sm">Mark as Paid</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <tr><td colspan="11" class="text-center text-muted py-4">No PF/ESI/TDS records found. Process payroll first.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $records->links() }}
@endsection
