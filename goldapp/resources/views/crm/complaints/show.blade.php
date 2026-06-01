@extends('layouts.app')
@section('title','Complaint: '.$complaint->slno)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-exclamation-circle me-2"></i>{{ $complaint->slno }} — {{ $complaint->subject }}</h5>
    <div class="d-flex gap-2">
        @if(!in_array($complaint->status,['resolved','closed']))
        <form method="POST" action="{{ route('customer-complaints.escalate',$complaint->id) }}" class="d-inline">
            @csrf <button class="btn btn-sm btn-outline-danger"><i class="bi bi-arrow-up me-1"></i>Escalate</button>
        </form>
        @endif
        <a href="{{ route('customer-complaints.edit',$complaint->id) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil me-1"></i>Edit</a>
        <a href="{{ route('customer-complaints.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
    </div>
</div>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

<div class="row g-3">
    <div class="col-md-6">
        <div class="card-dark p-3">
            <h6 class="text-gold mb-3">Complaint Details</h6>
            <table class="table table-sm table-borderless mb-0">
                <tr><th class="text-muted">Ref</th><td>{{ $complaint->slno }}</td></tr>
                <tr><th class="text-muted">Date</th><td>{{ $complaint->complaint_date->format('d M Y') }}</td></tr>
                <tr><th class="text-muted">Customer</th><td><a href="{{ route('customers.show',$complaint->customer_id) }}" class="text-gold-link">{{ $complaint->customer_name }}</a></td></tr>
                <tr><th class="text-muted">Type</th><td>{{ ucfirst($complaint->complaint_type) }}</td></tr>
                <tr><th class="text-muted">Priority</th><td><span class="badge bg-{{ $complaint->priority==='critical'?'danger':($complaint->priority==='high'?'warning':($complaint->priority==='medium'?'info':'secondary')) }}">{{ ucfirst($complaint->priority) }}</span></td></tr>
                <tr><th class="text-muted">Status</th><td><span class="badge bg-{{ $complaint->status==='resolved'||$complaint->status==='closed'?'success':($complaint->status==='open'?'danger':'warning') }}">{{ ucfirst($complaint->status) }}</span></td></tr>
                <tr><th class="text-muted">Escalated</th><td>{{ $complaint->escalated ? '<span class="text-danger">Yes</span>' : 'No' }}</td></tr>
            </table>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card-dark p-3">
            <h6 class="text-gold mb-3">Description</h6>
            <p class="text-small">{{ $complaint->description }}</p>
            @if($complaint->resolution)
            <hr style="border-color:var(--border-gold)">
            <h6 class="text-success mb-2">Resolution</h6>
            <p class="text-small text-success">{{ $complaint->resolution }}</p>
            @if($complaint->resolved_at)
            <p class="text-small text-muted">Resolved: {{ $complaint->resolved_at->format('d M Y H:i') }}</p>
            @endif
            @endif
        </div>
    </div>
</div>

@if(!in_array($complaint->status,['resolved','closed']))
<div class="card-dark p-3 mt-3">
    <h6 class="text-gold mb-3">Resolve Complaint</h6>
    <form method="POST" action="{{ route('customer-complaints.resolve',$complaint->id) }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Resolution *</label>
            <textarea name="resolution" class="form-control" rows="3" required placeholder="Describe how the complaint was resolved..."></textarea>
        </div>
        <button type="submit" class="btn btn-success"><i class="bi bi-check-circle me-1"></i>Mark as Resolved</button>
    </form>
</div>
@endif
<style>.text-small{font-size:.82rem}</style>
@endsection
