@extends('layouts.app')
@section('title', 'Production Order: '.$productionOrderNew->slno)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="text-gold mb-0"><i class="bi bi-gear-fill me-2"></i>{{ $productionOrderNew->slno }}</h4>
    <div class="d-flex gap-2">
        @if(in_array($productionOrderNew->status, ['released','in_progress']))
            <a href="{{ route('production-orders-new.issueForm', $productionOrderNew->id) }}" class="btn btn-sm btn-success"><i class="bi bi-box-arrow-right me-1"></i>Issue Materials</a>
        @endif
        @if($productionOrderNew->status === 'in_progress')
            <a href="#completeModal" data-bs-toggle="modal" class="btn btn-sm btn-gold"><i class="bi bi-check-circle me-1"></i>Complete Production</a>
        @endif
        <a href="{{ route('production-orders-new.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
</div>

@if(session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif

<div class="row g-3 mb-3">
    <div class="col-md-8">
        <div class="card-erp p-3">
            <div class="row g-2 small">
                <div class="col-4"><span class="text-muted">Order No:</span> <strong class="text-gold-light">{{ $productionOrderNew->slno }}</strong></div>
                <div class="col-4"><span class="text-muted">Order Date:</span> {{ $productionOrderNew->order_date->format('d/m/Y') }}</div>
                <div class="col-4">
                    @php $sc = ['draft'=>'secondary','released'=>'primary','in_progress'=>'warning','completed'=>'success','cancelled'=>'danger']; @endphp
                    <span class="badge bg-{{ $sc[$productionOrderNew->status] ?? 'secondary' }}">{{ ucwords(str_replace('_',' ',$productionOrderNew->status)) }}</span>
                    <span class="badge bg-{{ ['low'=>'secondary','normal'=>'info','high'=>'warning','urgent'=>'danger'][$productionOrderNew->priority] ?? 'secondary' }} ms-1">{{ ucfirst($productionOrderNew->priority) }}</span>
                </div>
                <div class="col-6"><span class="text-muted">Finished Good:</span> <strong>{{ $productionOrderNew->fg_name }}</strong> ({{ $productionOrderNew->fg_code }})</div>
                <div class="col-3"><span class="text-muted">Batch No:</span> <code>{{ $productionOrderNew->batch_no }}</code></div>
                <div class="col-3"><span class="text-muted">Order Qty:</span> {{ number_format($productionOrderNew->order_qty, 3) }} {{ $productionOrderNew->unit }}</div>
                <div class="col-3"><span class="text-muted">Planned Start:</span> {{ $productionOrderNew->planned_start?->format('d/m/Y') ?? '—' }}</div>
                <div class="col-3"><span class="text-muted">Planned End:</span> {{ $productionOrderNew->planned_end?->format('d/m/Y') ?? '—' }}</div>
                <div class="col-3"><span class="text-muted">Actual Start:</span> {{ $productionOrderNew->actual_start?->format('d/m/Y') ?? '—' }}</div>
                <div class="col-3"><span class="text-muted">Actual End:</span> {{ $productionOrderNew->actual_end?->format('d/m/Y') ?? '—' }}</div>
                @if($productionOrderNew->narration)<div class="col-12"><span class="text-muted">Narration:</span> {{ $productionOrderNew->narration }}</div>@endif
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-erp p-3">
            @php $pct = $productionOrderNew->order_qty > 0 ? min(100, round(($productionOrderNew->produced_qty / $productionOrderNew->order_qty)*100)) : 0; @endphp
            <div class="text-center mb-2">
                <div class="text-muted small">Production Progress</div>
                <div class="text-gold fs-3 fw-bold">{{ $pct }}%</div>
            </div>
            <div class="progress mb-2" style="height:12px;background:#2a2a40">
                <div class="progress-bar {{ $pct >= 100 ? 'bg-success' : 'bg-gold' }}" style="width:{{ $pct }}%"></div>
            </div>
            <div class="d-flex justify-content-between small">
                <span class="text-muted">Produced: {{ number_format($productionOrderNew->produced_qty, 3) }}</span>
                <span class="text-muted">Target: {{ number_format($productionOrderNew->order_qty, 3) }}</span>
            </div>
            @if($productionOrderNew->costing)
                <hr class="border-gold opacity-25">
                <div class="small">
                    <div class="d-flex justify-content-between"><span class="text-muted">RM Cost:</span><span>₹{{ number_format($productionOrderNew->costing->rm_cost, 2) }}</span></div>
                    <div class="d-flex justify-content-between"><span class="text-muted">PM Cost:</span><span>₹{{ number_format($productionOrderNew->costing->pm_cost, 2) }}</span></div>
                    <div class="d-flex justify-content-between"><span class="text-muted">Labour:</span><span>₹{{ number_format($productionOrderNew->costing->labour_cost, 2) }}</span></div>
                    <div class="d-flex justify-content-between fw-bold text-gold"><span>Total Cost:</span><span>₹{{ number_format($productionOrderNew->costing->total_cost, 2) }}</span></div>
                    <div class="d-flex justify-content-between"><span class="text-muted">Cost/Unit:</span><span>₹{{ number_format($productionOrderNew->costing->cost_per_unit, 4) }}</span></div>
                    <div class="d-flex justify-content-between {{ $productionOrderNew->costing->variance > 0 ? 'text-danger' : 'text-success' }}">
                        <span>Variance:</span><span>₹{{ number_format($productionOrderNew->costing->variance, 2) }}</span>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="card-erp p-3 mb-3">
    <h6 class="text-gold mb-2">Bill of Materials</h6>
    <div class="table-responsive">
        <table class="table table-sm table-erp mb-0">
            <thead><tr><th>Type</th><th>Code</th><th>Item Name</th><th>Unit</th><th class="text-end">Required</th><th class="text-end">Issued</th><th class="text-end">Wastage%</th><th class="text-end">Cost Rate</th><th class="text-end">Cost Amt</th><th>Status</th></tr></thead>
            <tbody>
            @foreach($productionOrderNew->bom as $b)
                @php $issuePct = $b->required_qty > 0 ? round(($b->issued_qty / $b->required_qty)*100) : 0; @endphp
                <tr>
                    <td><span class="badge bg-secondary">{{ $b->item_type }}</span></td>
                    <td>{{ $b->item_code }}</td>
                    <td>{{ $b->item_name }}</td>
                    <td>{{ $b->unit }}</td>
                    <td class="text-end">{{ number_format($b->required_qty, 4) }}</td>
                    <td class="text-end">{{ number_format($b->issued_qty, 4) }}</td>
                    <td class="text-end">{{ $b->wastage_pct }}%</td>
                    <td class="text-end">₹{{ number_format($b->cost_rate, 4) }}</td>
                    <td class="text-end">₹{{ number_format($b->cost_amount, 2) }}</td>
                    <td>
                        <div class="progress" style="height:4px;background:#2a2a40;width:60px">
                            <div class="progress-bar {{ $issuePct >= 100 ? 'bg-success' : 'bg-warning' }}" style="width:{{ $issuePct }}%"></div>
                        </div>
                        <small class="text-muted">{{ $issuePct }}%</small>
                    </td>
                </tr>
            @endforeach
            </tbody>
            <tfoot><tr><td colspan="8" class="text-end fw-bold">Total Planned Cost:</td><td class="text-end fw-bold text-gold">₹{{ number_format($totalIssuedCost, 2) }}</td><td></td></tr></tfoot>
        </table>
    </div>
</div>

@if($productionOrderNew->wipTracking->count() > 0)
<div class="card-erp p-3 mb-3">
    <h6 class="text-gold mb-2">WIP Stage History</h6>
    <div class="table-responsive">
        <table class="table table-sm table-erp mb-0">
            <thead><tr><th>Stage</th><th>Started At</th><th>Completed</th><th class="text-end">Input Qty</th><th class="text-end">Output Qty</th><th class="text-end">Loss</th><th>Remarks</th></tr></thead>
            <tbody>
            @foreach($productionOrderNew->wipTracking as $wip)
                <tr>
                    <td><span class="badge bg-info">{{ ucwords(str_replace('_',' ',$wip->stage)) }}</span></td>
                    <td>{{ $wip->started_at?->format('d/m/Y H:i') ?? '—' }}</td>
                    <td>{{ $wip->completed_at?->format('d/m/Y H:i') ?? '—' }}</td>
                    <td class="text-end">{{ number_format($wip->input_qty, 3) }}</td>
                    <td class="text-end">{{ number_format($wip->output_qty, 3) }}</td>
                    <td class="text-end {{ $wip->loss_qty > 0 ? 'text-danger' : '' }}">{{ number_format($wip->loss_qty, 3) }}</td>
                    <td class="small text-muted">{{ $wip->remarks }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@if($productionOrderNew->journal->count() > 0)
<div class="card-erp p-3">
    <h6 class="text-gold mb-2">Production Journal</h6>
    <div class="table-responsive">
        <table class="table table-sm table-erp mb-0">
            <thead><tr><th>Date</th><th>Stage</th><th class="text-end">Qty</th><th>Unit</th><th>Remarks</th></tr></thead>
            <tbody>
            @foreach($productionOrderNew->journal as $j)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($j->journal_date)->format('d/m/Y') }}</td>
                    <td><span class="badge bg-secondary">{{ ucwords(str_replace('_',' ',$j->stage)) }}</span></td>
                    <td class="text-end">{{ number_format($j->qty, 3) }}</td>
                    <td>{{ $j->unit }}</td>
                    <td class="small text-muted">{{ $j->remarks }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- Complete Production Modal -->
@if($productionOrderNew->status === 'in_progress')
<div class="modal fade" id="completeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background:#13132a;border:1px solid rgba(212,175,55,.3)">
            <div class="modal-header border-0">
                <h5 class="modal-title text-gold">Complete Production</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('production-orders-new.complete', $productionOrderNew->id) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label-erp">Produced Qty</label>
                        <input type="number" name="produced_qty" value="{{ $productionOrderNew->order_qty }}" class="form-control form-erp" step="0.0001" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-erp">Completion Date</label>
                        <input type="date" name="complete_date" value="{{ now()->format('Y-m-d') }}" class="form-control form-erp">
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label-erp">Labour Cost (₹)</label>
                            <input type="number" name="labour_cost" value="0" class="form-control form-erp" step="0.01">
                        </div>
                        <div class="col-6">
                            <label class="form-label-erp">Overhead Cost (₹)</label>
                            <input type="number" name="overhead_cost" value="0" class="form-control form-erp" step="0.01">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-gold"><i class="bi bi-check-circle me-1"></i>Complete Production</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
