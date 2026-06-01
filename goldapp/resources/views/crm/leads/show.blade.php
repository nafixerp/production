@extends('layouts.app')
@section('title', 'Lead: '.$lead->contact_name)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-person-lines-fill me-2"></i>{{ $lead->company_name ?? $lead->contact_name }} <span class="badge bg-secondary">{{ $lead->slno }}</span></h5>
    <div class="d-flex gap-2">
        <a href="{{ route('crm-leads.edit',$lead->id) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil me-1"></i>Edit</a>
        <a href="{{ route('crm-activities.create', ['lead_id'=>$lead->id]) }}" class="btn btn-sm btn-outline-gold"><i class="bi bi-plus me-1"></i>Log Activity</a>
        <a href="{{ route('crm-leads.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
    </div>
</div>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card-dark p-3">
            <h6 class="text-gold mb-3">Lead Details</h6>
            <table class="table table-sm table-borderless mb-0">
                <tr><th class="text-muted" width="35%">Contact</th><td>{{ $lead->contact_name }}</td></tr>
                <tr><th class="text-muted">Phone</th><td>{{ $lead->phone }}</td></tr>
                <tr><th class="text-muted">Email</th><td>{{ $lead->email ?? '-' }}</td></tr>
                <tr><th class="text-muted">City</th><td>{{ $lead->city }}</td></tr>
                <tr><th class="text-muted">Source</th><td>{{ ucwords(str_replace('_',' ',$lead->source)) }}</td></tr>
                <tr><th class="text-muted">Status</th><td><span class="badge bg-info">{{ ucwords(str_replace('_',' ',$lead->status)) }}</span></td></tr>
                <tr><th class="text-muted">Priority</th><td><span class="badge priority-{{ $lead->priority }}">{{ ucfirst($lead->priority) }}</span></td></tr>
                <tr><th class="text-muted">Value</th><td class="text-gold">{{ $lead->estimated_value ? '₹'.number_format($lead->estimated_value,2) : '-' }}</td></tr>
                <tr><th class="text-muted">Expected Close</th><td>{{ $lead->expected_close_date ? $lead->expected_close_date->format('d M Y') : '-' }}</td></tr>
                @if($lead->converted_customer_id)
                <tr><th class="text-muted">Converted</th><td><span class="text-success"><i class="bi bi-check-circle"></i> {{ $lead->converted_at->format('d M Y') }}</span></td></tr>
                @endif
            </table>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card-dark p-3">
            <h6 class="text-gold mb-3">Product Interest & Notes</h6>
            @if($lead->product_interest)
            <p class="text-small mb-2"><strong>Products:</strong> {{ $lead->product_interest }}</p>
            @endif
            @if($lead->narration)
            <p class="text-small mb-2">{{ $lead->narration }}</p>
            @endif
            @if($lead->lost_reason)
            <div class="alert alert-danger p-2 text-small"><strong>Lost Reason:</strong> {{ $lead->lost_reason }}</div>
            @endif
        </div>
    </div>
</div>

<!-- Activity Timeline -->
<h6 class="text-gold mb-3"><i class="bi bi-clock-history me-1"></i>Activity Timeline</h6>
@forelse($activities as $a)
<div class="timeline-item d-flex gap-3 mb-3">
    <div class="tl-icon bg-{{ ['call'=>'primary','email'=>'info','visit'=>'warning','demo'=>'success','follow_up'=>'secondary','whatsapp'=>'success','meeting'=>'danger'][$a->activity_type] ?? 'secondary' }}">
        <i class="bi bi-{{ ['call'=>'telephone','email'=>'envelope','visit'=>'geo-alt','demo'=>'display','follow_up'=>'arrow-clockwise','whatsapp'=>'whatsapp','meeting'=>'people'][$a->activity_type] ?? 'activity' }}"></i>
    </div>
    <div class="tl-body card-dark p-2 flex-fill">
        <div class="d-flex justify-content-between">
            <strong>{{ ucfirst($a->activity_type) }}: {{ $a->subject }}</strong>
            <small class="text-muted">{{ $a->activity_date->format('d M Y') }}</small>
        </div>
        <p class="mb-1 text-small">{{ $a->notes }}</p>
        @if($a->outcome)<p class="mb-0 text-small text-success"><i class="bi bi-check me-1"></i>{{ $a->outcome }}</p>@endif
        @if($a->next_action)<p class="mb-0 text-small text-warning"><i class="bi bi-arrow-right me-1"></i>Next: {{ $a->next_action }} {{ $a->next_action_date ? '('.($a->next_action_date->format('d M')).')' : '' }}</p>@endif
    </div>
</div>
@empty
<p class="text-muted">No activities yet. <a href="{{ route('crm-activities.create', ['lead_id'=>$lead->id]) }}" class="text-gold">Log first activity</a></p>
@endforelse

<style>
.tl-icon{width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:.85rem;color:#fff}
.priority-high{background:#e74c3c}.priority-medium{background:#e67e22}.priority-low{background:#7f8c8d}
.text-small{font-size:.8rem}
</style>
@endsection
