@extends('layouts.app')
@section('title','Opportunity: '.$opportunity->title)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-trophy me-2"></i>{{ $opportunity->title }}</h5>
    <div class="d-flex gap-2">
        <a href="{{ route('crm-opportunities.edit',$opportunity->id) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil me-1"></i>Edit</a>
        <a href="{{ route('crm-opportunities.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
    </div>
</div>
<div class="card-dark p-3">
    <div class="row g-3">
        <div class="col-md-6">
            <table class="table table-sm table-borderless mb-0">
                <tr><th class="text-muted">Stage</th><td><span class="badge bg-info">{{ ucwords(str_replace('_',' ',$opportunity->stage)) }}</span></td></tr>
                <tr><th class="text-muted">Value</th><td class="text-gold fw-bold fs-5">₹{{ number_format($opportunity->value,2) }}</td></tr>
                <tr><th class="text-muted">Probability</th><td>{{ $opportunity->probability }}%</td></tr>
                <tr><th class="text-muted">Expected Close</th><td>{{ $opportunity->expected_close->format('d M Y') }}</td></tr>
                @if($opportunity->customer)<tr><th class="text-muted">Customer</th><td>{{ $opportunity->customer->name }}</td></tr>@endif
                @if($opportunity->lead)<tr><th class="text-muted">Lead</th><td>{{ $opportunity->lead->contact_name }}</td></tr>@endif
            </table>
        </div>
        <div class="col-md-6">
            @if($opportunity->narration)
            <h6 class="text-muted">Notes</h6>
            <p>{{ $opportunity->narration }}</p>
            @endif
            <div class="mt-2">
                <div class="text-muted text-small mb-1">Probability: {{ $opportunity->probability }}%</div>
                <div class="progress" style="height:12px">
                    <div class="progress-bar bg-success" style="width:{{ $opportunity->probability }}%">{{ $opportunity->probability }}%</div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>.text-small{font-size:.78rem}</style>
@endsection
