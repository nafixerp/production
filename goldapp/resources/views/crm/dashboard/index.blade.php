@extends('layouts.app')
@section('title','CRM Dashboard')
@section('content')
<h5 class="text-gold mb-4"><i class="bi bi-bar-chart-fill me-2"></i>CRM Dashboard</h5>

<!-- Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-md-2"><div class="stat-card"><div class="stat-val text-gold">{{ $totalCustomers }}</div><div class="stat-lbl">Total Customers</div><div class="stat-sub text-success">+{{ $newThisMonth }} this month</div></div></div>
    <div class="col-md-2"><div class="stat-card"><div class="stat-val text-info">{{ $openLeads }}</div><div class="stat-lbl">Open Leads</div><div class="stat-sub">of {{ $totalLeads }} total</div></div></div>
    <div class="col-md-2"><div class="stat-card"><div class="stat-val text-gold">₹{{ number_format($pipelineValue/100000,1) }}L</div><div class="stat-lbl">Pipeline Value</div></div></div>
    <div class="col-md-2"><div class="stat-card"><div class="stat-val text-success">{{ $wonThisMonth }}</div><div class="stat-lbl">Won This Month</div><div class="stat-sub">₹{{ number_format($wonValue/1000,0) }}K</div></div></div>
    <div class="col-md-2"><div class="stat-card"><div class="stat-val text-warning">{{ $activitiesToday }}</div><div class="stat-lbl">Activities Today</div><div class="stat-sub text-danger">{{ $activitiesDue }} overdue follow-ups</div></div></div>
    <div class="col-md-2"><div class="stat-card {{ $criticalComplaints > 0 ? 'border-danger' : '' }}"><div class="stat-val {{ $openComplaints > 0 ? 'text-danger' : 'text-success' }}">{{ $openComplaints }}</div><div class="stat-lbl">Open Complaints</div><div class="stat-sub text-danger">{{ $criticalComplaints }} critical</div></div></div>
</div>

<div class="row g-3 mb-4">
    <!-- Pipeline by Stage -->
    <div class="col-md-5">
        <div class="card-dark p-3">
            <h6 class="text-gold mb-3"><i class="bi bi-funnel me-1"></i>Lead Pipeline by Stage</h6>
            @php $stages = ['new','contacted','qualified','proposal_sent','negotiating','won','lost']; $colors=['new'=>'3498db','contacted'=>'9b59b6','qualified'=>'e67e22','proposal_sent'=>'2980b9','negotiating'=>'d35400','won'=>'27ae60','lost'=>'c0392b']; @endphp
            @foreach($stages as $stage)
            @php $data = $leadsByStatus[$stage] ?? null; $cnt = $data?->count ?? 0; $val = $data?->value ?? 0; @endphp
            <div class="mb-2">
                <div class="d-flex justify-content-between text-small mb-1">
                    <span>{{ ucwords(str_replace('_',' ',$stage)) }}</span>
                    <span>{{ $cnt }} leads | ₹{{ number_format($val/1000,0) }}K</span>
                </div>
                <div class="progress" style="height:6px">
                    <div class="progress-bar" style="width:{{ $totalLeads > 0 ? round($cnt/$totalLeads*100) : 0 }}%;background:#{{ $colors[$stage] }}"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Opportunity Pipeline -->
    <div class="col-md-4">
        <div class="card-dark p-3">
            <h6 class="text-gold mb-3"><i class="bi bi-trophy me-1"></i>Opportunity Pipeline</h6>
            @php $oppStages=['prospect','qualified','proposal','negotiation','closed_won','closed_lost']; @endphp
            @foreach($oppStages as $stage)
            @php $odata = $opsByStage[$stage] ?? null; @endphp
            <div class="d-flex justify-content-between mb-2 text-small">
                <span>{{ ucwords(str_replace('_',' ',$stage)) }}</span>
                <span class="text-gold">{{ $odata?->count ?? 0 }} | ₹{{ number_format(($odata?->value ?? 0)/1000,0) }}K</span>
            </div>
            @endforeach
            <hr style="border-color:var(--border-gold)">
            <div class="d-flex justify-content-between text-small">
                <span class="text-muted">Weighted Pipeline</span>
                <strong class="text-success">₹{{ number_format($weightedPipeline/1000,0) }}K</strong>
            </div>
        </div>
    </div>

    <!-- Upcoming Follow-Ups -->
    <div class="col-md-3">
        <div class="card-dark p-3">
            <h6 class="text-gold mb-3"><i class="bi bi-alarm me-1"></i>Upcoming Follow-Ups (7 days)</h6>
            @forelse($followUps as $f)
            <div class="mb-2 p-2 rounded" style="background:rgba(212,175,55,.05);border-left:2px solid var(--gold)">
                <div class="text-small text-gold">{{ $f->next_action_date?->format('d M') }}</div>
                <div class="text-small">{{ Str::limit($f->next_action,40) }}</div>
            </div>
            @empty
            <p class="text-muted text-small">No upcoming follow-ups.</p>
            @endforelse
        </div>
    </div>
</div>

<!-- Recent Activities -->
<div class="card-dark p-3">
    <h6 class="text-gold mb-3"><i class="bi bi-clock-history me-1"></i>Recent Activities</h6>
    <div class="table-responsive">
    <table class="table table-dark-erp table-sm">
        <thead><tr><th>Date</th><th>Type</th><th>Subject</th><th>Lead/Customer</th><th>Outcome</th></tr></thead>
        <tbody>
            @forelse($recentActivities as $a)
            <tr>
                <td>{{ $a->activity_date->format('d M') }}</td>
                <td><span class="badge bg-primary" style="font-size:.65rem">{{ ucfirst($a->activity_type) }}</span></td>
                <td>{{ Str::limit($a->subject,40) }}</td>
                <td class="text-gold" style="font-size:.78rem">{{ $a->customer?->name ?? ($a->lead?->contact_name ?? '-') }}</td>
                <td style="font-size:.72rem">{{ Str::limit($a->outcome,30) }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center text-muted">No recent activities.</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>

<style>
.stat-card{background:#13132a;border:1px solid var(--border-gold);border-radius:8px;padding:14px;text-align:center}
.stat-val{font-size:1.5rem;font-weight:700;line-height:1.2}
.stat-lbl{font-size:.65rem;letter-spacing:1.5px;text-transform:uppercase;color:rgba(212,175,55,.6);margin-top:2px}
.stat-sub{font-size:.7rem;margin-top:4px;color:#888}
.border-danger{border-color:#dc3545!important}
.text-small{font-size:.78rem}
</style>
@endsection
