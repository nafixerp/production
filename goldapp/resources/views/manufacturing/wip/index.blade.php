@extends('layouts.app')
@section('title', 'WIP - Work In Progress')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="text-gold mb-0"><i class="bi bi-hourglass-split me-2"></i>Work In Progress (WIP)</h4>
</div>

<div class="card-erp mb-3">
    <form method="GET" class="row g-2 align-items-end p-2">
        <div class="col-md-2">
            <select name="priority" class="form-select form-select-sm form-erp">
                <option value="">All Priority</option>
                @foreach(['urgent','high','normal','low'] as $p)
                    <option value="{{ $p }}" {{ ($priority ?? '') === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto"><button class="btn btn-gold btn-sm"><i class="bi bi-filter"></i></button></div>
    </form>
</div>

<!-- Kanban View -->
<div class="row g-3 mb-4">
    @php
        $statusGroups = ['released' => 'Released', 'in_progress' => 'In Progress'];
    @endphp
    @foreach($statusGroups as $statusKey => $statusLabel)
    <div class="col-md-6">
        <div class="card-erp">
            <div class="p-2 border-bottom-gold d-flex align-items-center gap-2">
                <span class="badge bg-{{ $statusKey === 'in_progress' ? 'warning' : 'primary' }}">{{ $statusLabel }}</span>
                <span class="text-muted small">{{ $orders->where('status', $statusKey)->count() }} orders</span>
            </div>
            <div class="p-2">
            @forelse($orders->where('status', $statusKey) as $o)
                <div class="card mb-2" style="background:#1a1a2e;border:1px solid rgba(212,175,55,0.2)">
                    <div class="card-body p-2">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <a href="{{ route('production-orders-new.show', $o) }}" class="text-gold-light fw-bold small">{{ $o->slno }}</a>
                            <div>
                                @php $pc = ['low'=>'secondary','normal'=>'info','high'=>'warning','urgent'=>'danger']; @endphp
                                <span class="badge bg-{{ $pc[$o->priority] ?? 'secondary' }} badge-sm">{{ ucfirst($o->priority) }}</span>
                            </div>
                        </div>
                        <div class="fw-bold small mb-1">{{ $o->fg_name }}</div>
                        <div class="small text-muted mb-1">Batch: <code>{{ $o->batch_no }}</code></div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span>{{ number_format($o->order_qty, 2) }} {{ $o->unit }}</span>
                            <span class="text-muted">Stage: <strong>{{ ucwords(str_replace('_',' ',$o->current_stage)) }}</strong></span>
                        </div>
                        <!-- Progress: BOM issued -->
                        <div class="mb-1">
                            <div class="d-flex justify-content-between x-small text-muted mb-1">
                                <span>Materials Issued</span><span>{{ $o->bom_progress_pct }}%</span>
                            </div>
                            <div class="progress" style="height:5px;background:#2a2a40">
                                <div class="progress-bar {{ $o->bom_progress_pct >= 100 ? 'bg-success' : 'bg-warning' }}" style="width:{{ $o->bom_progress_pct }}%"></div>
                            </div>
                        </div>
                        @if($o->planned_end)
                            @php $daysLeft = now()->diffInDays($o->planned_end, false); @endphp
                            <div class="small {{ $daysLeft < 0 ? 'text-danger' : ($daysLeft <= 1 ? 'text-warning' : 'text-muted') }}">
                                <i class="bi bi-calendar me-1"></i>
                                @if($daysLeft < 0) Overdue by {{ abs($daysLeft) }}d
                                @elseif($daysLeft === 0) Due today
                                @else {{ $daysLeft }}d left @endif
                            </div>
                        @endif
                        @if($o->status === 'in_progress')
                        <div class="mt-2">
                            <form method="POST" action="{{ route('production-orders-new.complete', $o->id) }}" class="d-inline">
                                @csrf
                                <input type="hidden" name="produced_qty" value="{{ $o->order_qty }}">
                                <input type="hidden" name="complete_date" value="{{ now()->format('Y-m-d') }}">
                                <button type="button" class="btn btn-xs btn-outline-success" onclick="if(confirm('Mark as completed?')) this.form.submit()"><i class="bi bi-check-circle me-1"></i>Complete</button>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center text-muted small py-3">No orders</div>
            @endforelse
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Table view for all WIP -->
<div class="card-erp">
    <div class="p-3 border-bottom-gold"><h6 class="text-gold mb-0">All Open Production Orders</h6></div>
    <div class="table-responsive">
        <table class="table table-sm table-erp mb-0">
            <thead><tr><th>Order No</th><th>FG Name</th><th>Batch</th><th>Priority</th><th>Status</th><th>Current Stage</th><th class="text-end">Order Qty</th><th>BOM Progress</th><th>Planned End</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($orders as $o)
                <tr>
                    <td><a href="{{ route('production-orders-new.show', $o) }}" class="text-gold-light">{{ $o->slno }}</a></td>
                    <td>{{ $o->fg_name }}</td>
                    <td><code>{{ $o->batch_no }}</code></td>
                    <td><span class="badge bg-{{ ['low'=>'secondary','normal'=>'info','high'=>'warning','urgent'=>'danger'][$o->priority] ?? 'secondary' }}">{{ ucfirst($o->priority) }}</span></td>
                    <td><span class="badge bg-{{ $o->status === 'in_progress' ? 'warning' : 'primary' }}">{{ ucwords(str_replace('_',' ',$o->status)) }}</span></td>
                    <td>{{ ucwords(str_replace('_',' ',$o->current_stage)) }}</td>
                    <td class="text-end">{{ number_format($o->order_qty, 3) }}</td>
                    <td>
                        <div class="progress" style="height:5px;background:#2a2a40;min-width:80px">
                            <div class="progress-bar {{ $o->bom_progress_pct >= 100 ? 'bg-success' : 'bg-warning' }}" style="width:{{ $o->bom_progress_pct }}%"></div>
                        </div>
                        <small class="text-muted">{{ $o->bom_progress_pct }}%</small>
                    </td>
                    <td>
                        @if($o->planned_end)
                            @php $dl = now()->diffInDays($o->planned_end, false); @endphp
                            <span class="{{ $dl < 0 ? 'text-danger' : ($dl <= 1 ? 'text-warning' : 'text-muted') }}">
                                {{ $o->planned_end->format('d/m/Y') }}
                            </span>
                        @else—@endif
                    </td>
                    <td>
                        <a href="{{ route('production-orders-new.issueForm', $o->id) }}" class="btn btn-xs btn-outline-success"><i class="bi bi-box-arrow-right"></i></a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="10" class="text-center text-muted py-3">No open production orders.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
