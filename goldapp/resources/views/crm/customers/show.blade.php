@extends('layouts.app')
@section('title', $customer->name)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-person-circle me-2"></i>{{ $customer->name }} <span class="badge bg-secondary ms-2">{{ $customer->code }}</span></h5>
    <div class="d-flex gap-2">
        <a href="{{ route('customers.edit',$customer->id) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil me-1"></i>Edit</a>
        <a href="{{ route('customers.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
    </div>
</div>

@if($creditWarning)
<div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Credit limit exceeded! Outstanding: ₹{{ number_format($customer->outstanding_balance,2) }} / Limit: ₹{{ number_format($customer->credit_limit,2) }}</div>
@endif
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

<div class="row g-3 mb-4">
    <!-- Details Card -->
    <div class="col-md-4">
        <div class="card-dark p-3 h-100">
            <h6 class="text-gold mb-3"><i class="bi bi-info-circle me-1"></i>Customer Details</h6>
            <table class="table table-sm table-borderless text-small mb-0">
                <tr><th class="text-muted" width="40%">Type</th><td><span class="badge badge-type-{{ $customer->customer_type }}">{{ ucfirst($customer->customer_type) }}</span></td></tr>
                <tr><th class="text-muted">Phone</th><td>{{ $customer->phone }}{{ $customer->alt_phone ? ' / '.$customer->alt_phone : '' }}</td></tr>
                <tr><th class="text-muted">Email</th><td>{{ $customer->email ?? '-' }}</td></tr>
                <tr><th class="text-muted">City</th><td>{{ $customer->city }}, {{ $customer->state }}</td></tr>
                <tr><th class="text-muted">GST No.</th><td>{{ $customer->gst_no ?? '-' }}</td></tr>
                <tr><th class="text-muted">PAN</th><td>{{ $customer->pan_no ?? '-' }}</td></tr>
                <tr><th class="text-muted">Price Tier</th><td>{{ ucfirst($customer->price_tier) }}</td></tr>
                <tr><th class="text-muted">Status</th><td><span class="badge {{ $customer->status ? 'bg-success' : 'bg-danger' }}">{{ $customer->status ? 'Active' : 'Inactive' }}</span></td></tr>
            </table>
        </div>
    </div>

    <!-- Financial Summary -->
    <div class="col-md-4">
        <div class="card-dark p-3 h-100">
            <h6 class="text-gold mb-3"><i class="bi bi-currency-rupee me-1"></i>Financial Summary</h6>
            <div class="row g-2">
                <div class="col-6">
                    <div class="stat-mini">
                        <div class="stat-val {{ $customer->outstanding_balance > $customer->credit_limit && $customer->credit_limit > 0 ? 'text-danger' : 'text-warning' }}">₹{{ number_format($customer->outstanding_balance,0) }}</div>
                        <div class="stat-lbl">Outstanding</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="stat-mini">
                        <div class="stat-val text-gold">₹{{ number_format($customer->credit_limit,0) }}</div>
                        <div class="stat-lbl">Credit Limit</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="stat-mini">
                        <div class="stat-val text-info">{{ $customer->credit_days }}</div>
                        <div class="stat-lbl">Credit Days</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="stat-mini">
                        <div class="stat-val text-success">{{ number_format($customer->loyalty_points) }}</div>
                        <div class="stat-lbl">Loyalty Points</div>
                    </div>
                </div>
            </div>
            @if($customer->payment_terms)
            <div class="mt-2 text-muted text-small">Payment Terms: {{ $customer->payment_terms }}</div>
            @endif
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-md-4">
        <div class="card-dark p-3 h-100">
            <h6 class="text-gold mb-3"><i class="bi bi-lightning me-1"></i>Quick Actions</h6>
            <div class="d-grid gap-2">
                <a href="{{ route('crm-activities.create', ['customer_id' => $customer->id]) }}" class="btn btn-sm btn-outline-gold"><i class="bi bi-plus me-1"></i>Log Activity</a>
                <a href="{{ route('customer-complaints.create') }}?customer_id={{ $customer->id }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-exclamation-circle me-1"></i>New Complaint</a>
                <a href="{{ route('customer-pricing.create') }}?customer_id={{ $customer->id }}" class="btn btn-sm btn-outline-info"><i class="bi bi-tag me-1"></i>Set Pricing</a>
            </div>
            @if($customer->notes)
            <div class="mt-3 p-2 rounded" style="background:rgba(212,175,55,.05);border:1px solid var(--border-gold)">
                <small class="text-muted">{{ $customer->notes }}</small>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Tabs for related data -->
<ul class="nav nav-tabs mb-3">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-txn">Transactions ({{ count($transactions) }})</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-activities">Activities ({{ count($activities) }})</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-complaints">Complaints ({{ count($complaints) }})</a></li>
</ul>

<div class="tab-content">
    <div class="tab-pane fade show active" id="tab-txn">
        <div class="table-responsive">
        <table class="table table-dark-erp table-sm">
            <thead><tr><th>Date</th><th>Voucher</th><th>Particular</th><th>Type</th><th class="text-end">Dr</th><th class="text-end">Cr</th></tr></thead>
            <tbody>
                @forelse($transactions as $t)
                <tr>
                    <td>{{ $t->tdate }}</td>
                    <td>{{ $t->slno }}</td>
                    <td>{{ $t->particular }}</td>
                    <td>{{ $t->vtype }}</td>
                    <td class="text-end text-danger">{{ $t->amount < 0 ? number_format(abs($t->amount),2) : '-' }}</td>
                    <td class="text-end text-success">{{ $t->amount > 0 ? number_format($t->amount,2) : '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted">No transactions found.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    <div class="tab-pane fade" id="tab-activities">
        @forelse($activities as $a)
        <div class="timeline-item mb-2 p-3 card-dark">
            <div class="d-flex justify-content-between">
                <strong class="text-gold">{{ ucfirst($a->activity_type) }}: {{ $a->subject }}</strong>
                <small class="text-muted">{{ $a->activity_date->format('d M Y') }}</small>
            </div>
            <p class="mb-1 text-small">{{ $a->notes }}</p>
            @if($a->outcome)<p class="mb-1 text-small text-success"><strong>Outcome:</strong> {{ $a->outcome }}</p>@endif
            @if($a->next_action)<p class="mb-0 text-small text-warning"><strong>Next:</strong> {{ $a->next_action }} {{ $a->next_action_date ? '('.$a->next_action_date->format('d M').')' : '' }}</p>@endif
        </div>
        @empty
        <p class="text-muted">No activities logged.</p>
        @endforelse
    </div>

    <div class="tab-pane fade" id="tab-complaints">
        <div class="table-responsive">
        <table class="table table-dark-erp table-sm">
            <thead><tr><th>Date</th><th>Ref</th><th>Subject</th><th>Priority</th><th>Status</th></tr></thead>
            <tbody>
                @forelse($complaints as $c)
                <tr>
                    <td>{{ $c->complaint_date->format('d M Y') }}</td>
                    <td>{{ $c->slno }}</td>
                    <td>{{ $c->subject }}</td>
                    <td><span class="badge bg-{{ $c->priority === 'critical' ? 'danger' : ($c->priority === 'high' ? 'warning' : 'secondary') }}">{{ ucfirst($c->priority) }}</span></td>
                    <td><span class="badge bg-{{ $c->status === 'resolved' || $c->status === 'closed' ? 'success' : 'warning' }}">{{ ucfirst($c->status) }}</span></td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted">No complaints.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>

<style>
.stat-mini{text-align:center;padding:8px;background:rgba(212,175,55,.05);border-radius:6px;border:1px solid var(--border-gold)}
.stat-val{font-size:1.1rem;font-weight:700}.stat-lbl{font-size:.65rem;color:rgba(212,175,55,.6);letter-spacing:1px}
.badge-type-retail{background:#3498db}.badge-type-wholesale{background:#2ecc71}.badge-type-distributor{background:#9b59b6}
.badge-type-online{background:#e67e22}.badge-type-export{background:#e74c3c}
.text-small{font-size:.8rem}
</style>
@endsection
