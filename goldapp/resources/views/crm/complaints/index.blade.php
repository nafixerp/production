@extends('layouts.app')
@section('title','Customer Complaints')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Customer Complaints</h5>
    <a href="{{ route('customer-complaints.create') }}" class="btn btn-sm btn-gold"><i class="bi bi-plus-lg me-1"></i>New Complaint</a>
</div>

<div class="card-dark mb-3">
    <form method="GET" class="row g-2">
        <div class="col-md-3"><input type="text" name="search" class="form-control form-control-sm" placeholder="Search..." value="{{ request('search') }}"></div>
        <div class="col-md-2">
            <select name="status" class="form-select form-select-sm">
                <option value="">All Status</option>
                @foreach(['open','acknowledged','investigating','resolved','closed'] as $s)
                <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="priority" class="form-select form-select-sm">
                <option value="">All Priority</option>
                @foreach(['low','medium','high','critical'] as $p)
                <option value="{{ $p }}" {{ request('priority')===$p?'selected':'' }}>{{ ucfirst($p) }}</option>
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
        <tr><th>Ref</th><th>Date</th><th>Customer</th><th>Type</th><th>Subject</th><th>Priority</th><th>Status</th><th>Escalated</th><th>Actions</th></tr>
    </thead>
    <tbody>
        @forelse($complaints as $c)
        <tr>
            <td>{{ $c->slno }}</td>
            <td>{{ $c->complaint_date->format('d M Y') }}</td>
            <td><a href="{{ route('customers.show',$c->customer_id) }}" class="text-gold-link">{{ $c->customer_name }}</a></td>
            <td>{{ ucfirst($c->complaint_type) }}</td>
            <td>{{ Str::limit($c->subject,40) }}</td>
            <td><span class="badge bg-{{ $c->priority==='critical'?'danger':($c->priority==='high'?'warning':($c->priority==='medium'?'info':'secondary')) }}">{{ ucfirst($c->priority) }}</span></td>
            <td><span class="badge bg-{{ $c->status==='resolved'||$c->status==='closed'?'success':($c->status==='open'?'danger':'warning') }}">{{ ucfirst($c->status) }}</span></td>
            <td class="text-center">{{ $c->escalated ? '<span class="text-danger"><i class="bi bi-arrow-up-circle-fill"></i></span>' : '-' }}</td>
            <td>
                <a href="{{ route('customer-complaints.edit',$c->id) }}" class="btn btn-xs btn-outline-warning"><i class="bi bi-pencil"></i></a>
                @if(!in_array($c->status,['resolved','closed']))
                <form method="POST" action="/customer-complaints/{{ $c->id }}/escalate" class="d-inline">
                    @csrf <button class="btn btn-xs btn-outline-danger" title="Escalate"><i class="bi bi-arrow-up"></i></button>
                </form>
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="9" class="text-center text-muted">No complaints.</td></tr>
        @endforelse
    </tbody>
</table>
</div>
{{ $complaints->links() }}
<style>.text-gold-link{color:var(--gold);text-decoration:none}.btn-xs{padding:2px 6px;font-size:.7rem}</style>
@endsection
