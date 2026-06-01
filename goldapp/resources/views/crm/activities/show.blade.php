@extends('layouts.app')
@section('title','Activity Detail')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-calendar-event me-2"></i>{{ $activity->subject }}</h5>
    <div class="d-flex gap-2">
        <a href="{{ route('crm-activities.edit',$activity->id) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil me-1"></i>Edit</a>
        <a href="{{ route('crm-activities.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
    </div>
</div>

<div class="card-dark p-3">
    <div class="row g-3">
        <div class="col-md-6">
            <table class="table table-sm table-borderless mb-0">
                <tr><th class="text-muted" width="35%">Date</th><td>{{ $activity->activity_date->format('d M Y') }}</td></tr>
                <tr><th class="text-muted">Type</th><td><span class="badge bg-primary">{{ ucwords(str_replace('_',' ',$activity->activity_type)) }}</span></td></tr>
                <tr><th class="text-muted">Subject</th><td>{{ $activity->subject }}</td></tr>
                @if($activity->lead)
                <tr><th class="text-muted">Lead</th><td><a href="{{ route('crm-leads.show',$activity->lead_id) }}" class="text-gold-link">{{ $activity->lead->contact_name }}</a></td></tr>
                @endif
                @if($activity->customer)
                <tr><th class="text-muted">Customer</th><td><a href="{{ route('customers.show',$activity->customer_id) }}" class="text-gold-link">{{ $activity->customer->name }}</a></td></tr>
                @endif
                @if($activity->next_action)
                <tr><th class="text-muted">Next Action</th><td class="text-warning">{{ $activity->next_action }} {{ $activity->next_action_date ? '('.$activity->next_action_date->format('d M Y').')' : '' }}</td></tr>
                @endif
            </table>
        </div>
        <div class="col-md-6">
            <h6 class="text-muted mb-2">Notes</h6>
            <p>{{ $activity->notes }}</p>
            @if($activity->outcome)
            <h6 class="text-muted mb-2">Outcome</h6>
            <p class="text-success">{{ $activity->outcome }}</p>
            @endif
        </div>
    </div>
</div>
@endsection
