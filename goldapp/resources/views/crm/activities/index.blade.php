@extends('layouts.app')
@section('title','CRM Activities')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-calendar-check me-2"></i>CRM Activities</h5>
    <a href="{{ route('crm-activities.create') }}" class="btn btn-sm btn-gold"><i class="bi bi-plus-lg me-1"></i>Log Activity</a>
</div>

<div class="card-dark mb-3">
    <form method="GET" class="row g-2">
        <div class="col-md-2"><input type="date" name="date" class="form-control form-control-sm" value="{{ request('date') }}"></div>
        <div class="col-md-2">
            <select name="type" class="form-select form-select-sm">
                <option value="">All Types</option>
                @foreach(['call','email','visit','demo','follow_up','whatsapp','meeting'] as $t)
                <option value="{{ $t }}" {{ request('type')===$t?'selected':'' }}>{{ ucwords(str_replace('_',' ',$t)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2"><button type="submit" class="btn btn-sm btn-outline-gold w-100">Filter</button></div>
    </form>
</div>

@if(session('success'))<div class="alert alert-success alert-sm">{{ session('success') }}</div>@endif

<div class="table-responsive">
<table class="table table-dark-erp table-hover">
    <thead>
        <tr><th>Date</th><th>Type</th><th>Subject</th><th>Lead / Customer</th><th>Notes</th><th>Next Action</th><th>Actions</th></tr>
    </thead>
    <tbody>
        @forelse($activities as $a)
        <tr>
            <td>{{ $a->activity_date->format('d M Y') }}</td>
            <td><span class="badge bg-primary">{{ ucwords(str_replace('_',' ',$a->activity_type)) }}</span></td>
            <td>{{ $a->subject }}</td>
            <td>
                @if($a->lead)<a href="{{ route('crm-leads.show',$a->lead_id) }}" class="text-gold-link">Lead: {{ $a->lead->contact_name }}</a>@endif
                @if($a->customer)<a href="{{ route('customers.show',$a->customer_id) }}" class="text-gold-link">{{ $a->customer->name }}</a>@endif
            </td>
            <td class="text-small">{{ Str::limit($a->notes,60) }}</td>
            <td class="text-small">{{ $a->next_action ? $a->next_action.' '.($a->next_action_date ? '('.$a->next_action_date->format('d M').')' : '') : '-' }}</td>
            <td>
                <a href="{{ route('crm-activities.edit',$a->id) }}" class="btn btn-xs btn-outline-warning"><i class="bi bi-pencil"></i></a>
                <form method="POST" action="{{ route('crm-activities.destroy',$a->id) }}" class="d-inline" onsubmit="return confirm('Delete?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-xs btn-outline-danger"><i class="bi bi-trash"></i></button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center text-muted">No activities found.</td></tr>
        @endforelse
    </tbody>
</table>
</div>
{{ $activities->links() }}
<style>.text-gold-link{color:var(--gold);text-decoration:none}.text-small{font-size:.78rem}.btn-xs{padding:2px 6px;font-size:.7rem}</style>
@endsection
