@extends('layouts.app')
@section('title','CRM Pipeline')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-funnel-fill me-2"></i>Lead Pipeline</h5>
    <div class="d-flex gap-2 align-items-center">
        <span class="text-muted text-small">Pipeline: <strong class="text-gold">₹{{ number_format($totalValue,0) }}</strong></span>
        <a href="{{ route('crm-leads.create') }}" class="btn btn-sm btn-gold"><i class="bi bi-plus-lg me-1"></i>New Lead</a>
    </div>
</div>

@if(session('success'))<div class="alert alert-success alert-sm">{{ session('success') }}</div>@endif

<div class="kanban-board">
@php
$stageLabels = ['new'=>'New','contacted'=>'Contacted','qualified'=>'Qualified','proposal_sent'=>'Proposal Sent','negotiating'=>'Negotiating','won'=>'Won','lost'=>'Lost'];
$stageColors = ['new'=>'#3498db','contacted'=>'#9b59b6','qualified'=>'#e67e22','proposal_sent'=>'#2980b9','negotiating'=>'#d35400','won'=>'#27ae60','lost'=>'#c0392b'];
@endphp

@foreach($stageLabels as $stage => $label)
<div class="kanban-col">
    <div class="kanban-header" style="border-top:3px solid {{ $stageColors[$stage] }}">
        <span class="kanban-title">{{ $label }}</span>
        <span class="badge" style="background:{{ $stageColors[$stage] }}">{{ $pipeline[$stage]->count() }}</span>
    </div>
    <div class="kanban-cards">
        @forelse($pipeline[$stage] as $lead)
        <div class="kanban-card">
            <div class="kc-header">
                <a href="{{ route('crm-leads.show',$lead->id) }}" class="kc-title">{{ $lead->company_name ?? $lead->contact_name }}</a>
                <span class="badge priority-{{ $lead->priority }}">{{ ucfirst($lead->priority) }}</span>
            </div>
            <div class="kc-name text-muted">{{ $lead->contact_name }}</div>
            @if($lead->estimated_value)
            <div class="kc-value text-gold">₹{{ number_format($lead->estimated_value,0) }}</div>
            @endif
            <div class="kc-meta">
                <span class="text-muted"><i class="bi bi-geo-alt"></i> {{ $lead->city }}</span>
                @if($lead->expected_close_date)
                <span class="text-muted"><i class="bi bi-calendar"></i> {{ $lead->expected_close_date->format('d M') }}</span>
                @endif
            </div>
            <div class="kc-actions mt-2">
                <a href="{{ route('crm-leads.edit',$lead->id) }}" class="btn btn-xs btn-outline-warning"><i class="bi bi-pencil"></i></a>
                @if(!$lead->converted_customer_id && $lead->status !== 'lost')
                <button class="btn btn-xs btn-outline-success" onclick="showConvert({{ $lead->id }})"><i class="bi bi-arrow-right-circle"></i> Convert</button>
                @elseif($lead->converted_customer_id)
                <span class="text-success text-small"><i class="bi bi-check-circle"></i> Converted</span>
                @endif
            </div>
        </div>
        @empty
        <div class="text-center text-muted text-small p-3">No leads</div>
        @endforelse
    </div>
</div>
@endforeach
</div>

<!-- Convert Modal -->
<div class="modal fade" id="convertModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background:#13132a;border:1px solid var(--border-gold)">
            <div class="modal-header"><h5 class="modal-title text-gold">Convert Lead to Customer</h5></div>
            <form id="convertForm" method="POST">
                @csrf
                <div class="modal-body">
                    <label class="form-label">Customer Code *</label>
                    <input type="text" name="code" class="form-control" placeholder="e.g., CUST-00123" required>
                    <small class="text-muted">Unique customer code for the new account</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Convert</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.kanban-board{display:flex;gap:12px;overflow-x:auto;padding-bottom:12px;min-height:70vh}
.kanban-col{min-width:200px;max-width:220px;flex-shrink:0;background:#0e0e1f;border-radius:8px;border:1px solid var(--border-gold)}
.kanban-header{padding:10px 12px;display:flex;justify-content:space-between;align-items:center}
.kanban-title{font-size:.75rem;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:#d4af37}
.kanban-cards{padding:8px;display:flex;flex-direction:column;gap:8px;max-height:65vh;overflow-y:auto}
.kanban-card{background:#13132a;border:1px solid rgba(212,175,55,.2);border-radius:6px;padding:10px}
.kc-title{color:#d4af37;font-size:.8rem;font-weight:600;text-decoration:none;display:block}
.kc-title:hover{color:#f0d060}
.kc-name{font-size:.73rem;margin-top:2px}
.kc-value{font-size:.85rem;font-weight:700;margin-top:4px}
.kc-meta{display:flex;gap:8px;margin-top:4px;font-size:.68rem}
.kc-actions{display:flex;gap:4px;flex-wrap:wrap}
.priority-high{background:#e74c3c}.priority-medium{background:#e67e22}.priority-low{background:#7f8c8d}
.btn-xs{padding:2px 6px;font-size:.7rem}
.text-small{font-size:.78rem}
</style>
<script>
function showConvert(id) {
    document.getElementById('convertForm').action = `/crm-leads/${id}/convert`;
    new bootstrap.Modal(document.getElementById('convertModal')).show();
}
</script>
@endsection
