@extends('layouts.app')
@section('title','Opportunities')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-trophy me-2"></i>Opportunities Pipeline</h5>
    <div class="d-flex gap-3 align-items-center">
        <small class="text-muted">Open: <strong class="text-gold">₹{{ number_format($totalOpen,0) }}</strong> | Weighted: <strong class="text-success">₹{{ number_format($weighted,0) }}</strong></small>
        <a href="{{ route('crm-opportunities.create') }}" class="btn btn-sm btn-gold"><i class="bi bi-plus-lg me-1"></i>New</a>
    </div>
</div>

@if(session('success'))<div class="alert alert-success alert-sm">{{ session('success') }}</div>@endif

<div class="kanban-board">
@php
$stageLabels=['prospect'=>'Prospect','qualified'=>'Qualified','proposal'=>'Proposal','negotiation'=>'Negotiation','closed_won'=>'Closed Won','closed_lost'=>'Closed Lost'];
$stageColors=['prospect'=>'#3498db','qualified'=>'#9b59b6','proposal'=>'#e67e22','negotiation'=>'#d35400','closed_won'=>'#27ae60','closed_lost'=>'#c0392b'];
@endphp

@foreach($stageLabels as $stage => $label)
<div class="kanban-col">
    <div class="kanban-header" style="border-top:3px solid {{ $stageColors[$stage] }}">
        <span class="kanban-title">{{ $label }}</span>
        <span class="badge" style="background:{{ $stageColors[$stage] }}">{{ $pipeline[$stage]->count() }}</span>
    </div>
    <div class="kanban-cards">
        @forelse($pipeline[$stage] as $opp)
        <div class="kanban-card">
            <div class="kc-title">{{ $opp->title }}</div>
            <div class="kc-value text-gold">₹{{ number_format($opp->value,0) }}</div>
            <div class="text-small text-muted">{{ $opp->probability }}% probability</div>
            <div class="text-small text-muted">Close: {{ $opp->expected_close->format('d M Y') }}</div>
            @if($opp->customer)<div class="text-small">{{ $opp->customer->name }}</div>@endif
            <div class="kc-actions mt-2">
                <a href="{{ route('crm-opportunities.edit',$opp->id) }}" class="btn btn-xs btn-outline-warning"><i class="bi bi-pencil"></i></a>
                <form method="POST" action="{{ route('crm-opportunities.destroy',$opp->id) }}" class="d-inline" onsubmit="return confirm('Delete?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-xs btn-outline-danger"><i class="bi bi-trash"></i></button>
                </form>
            </div>
        </div>
        @empty
        <div class="text-center text-muted text-small p-3">Empty</div>
        @endforelse
    </div>
</div>
@endforeach
</div>

<style>
.kanban-board{display:flex;gap:12px;overflow-x:auto;padding-bottom:12px;min-height:60vh}
.kanban-col{min-width:185px;max-width:200px;flex-shrink:0;background:#0e0e1f;border-radius:8px;border:1px solid var(--border-gold)}
.kanban-header{padding:10px 12px;display:flex;justify-content:space-between;align-items:center}
.kanban-title{font-size:.7rem;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:#d4af37}
.kanban-cards{padding:8px;display:flex;flex-direction:column;gap:8px;max-height:60vh;overflow-y:auto}
.kanban-card{background:#13132a;border:1px solid rgba(212,175,55,.2);border-radius:6px;padding:10px}
.kc-title{color:#e8e0c8;font-size:.8rem;font-weight:600;margin-bottom:4px}
.kc-value{font-size:.9rem;font-weight:700}
.kc-actions{display:flex;gap:4px}
.btn-xs{padding:2px 6px;font-size:.7rem}.text-small{font-size:.75rem}
</style>
@endsection
