@extends('layouts.app')
@section('title','Audit Log Detail')
@section('page-title','Audit Log Detail')

@push('styles')
<style>
.detail-card { background: var(--bg-card); border: 1px solid var(--border-gold); border-radius: 10px; padding: 22px; margin-bottom: 16px; }
.detail-label { color: var(--text-muted-gold); font-size: .68rem; letter-spacing: 1.5px; text-transform: uppercase; margin-bottom: 4px; }
.detail-value { color: #e8e0c8; font-size: .88rem; }
.diff-card { background: rgba(0,0,0,.25); border: 1px solid var(--border-gold); border-radius: 8px; padding: 16px; }
.diff-label { color: var(--text-muted-gold); font-size: .68rem; letter-spacing: 1.5px; text-transform: uppercase; margin-bottom: 10px; font-weight: 600; }
.diff-row { display: flex; gap: 12px; padding: 6px 0; border-bottom: 1px solid rgba(212,175,55,.06); align-items: flex-start; }
.diff-row:last-child { border-bottom: none; }
.diff-key { color: var(--gold); font-size: .78rem; font-weight: 600; min-width: 160px; flex-shrink: 0; }
.diff-old { color: #ff6b6b; font-size: .8rem; text-decoration: line-through; flex: 1; word-break: break-all; }
.diff-new { color: #51cf66; font-size: .8rem; flex: 1; word-break: break-all; }
.diff-unchanged { color: #a09878; font-size: .8rem; flex: 1; }
.action-badge { display: inline-block; padding: 4px 12px; border-radius: 5px; font-size: .78rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; }
.action-create  { background: rgba(81,207,102,.15);  color: #51cf66; border: 1px solid rgba(81,207,102,.3); }
.action-update  { background: rgba(212,175,55,.15);  color: var(--gold); border: 1px solid var(--border-gold); }
.action-delete  { background: rgba(255,107,107,.15); color: #ff6b6b; border: 1px solid rgba(255,107,107,.3); }
.action-login   { background: rgba(81,150,255,.15);  color: #74b0ff; border: 1px solid rgba(81,150,255,.3); }
.action-logout  { background: rgba(150,150,150,.15); color: #aaa; border: 1px solid rgba(150,150,150,.2); }
.action-export  { background: rgba(240,208,96,.15);  color: #f0d060; border: 1px solid rgba(240,208,96,.3); }
.action-approve { background: rgba(81,207,102,.2);   color: #51cf66; border: 1px solid rgba(81,207,102,.3); }
.action-view    { background: rgba(100,180,255,.1);  color: #80c0ff; border: 1px solid rgba(100,180,255,.2); }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h6 style="color:var(--gold);margin:0"><i class="bi bi-shield-lock me-2"></i>Audit Event #{{ $log->id }}</h6>
    <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-sm" style="background:rgba(255,255,255,.05);color:#a09878;border:1px solid var(--border-gold);font-size:.78rem">
        <i class="bi bi-arrow-left me-1"></i>Back to Logs
    </a>
</div>

{{-- Event Summary --}}
<div class="detail-card">
    <div class="row g-3">
        <div class="col-md-2">
            <div class="detail-label">Action</div>
            <div><span class="action-badge action-{{ $log->action }}">{{ $log->action }}</span></div>
        </div>
        <div class="col-md-2">
            <div class="detail-label">Module</div>
            <div class="detail-value fw-bold">{{ $log->module ?? '—' }}</div>
        </div>
        <div class="col-md-2">
            <div class="detail-label">Record ID</div>
            <div class="detail-value">{{ $log->record_id ?? '—' }}</div>
        </div>
        <div class="col-md-3">
            <div class="detail-label">User</div>
            <div class="detail-value">{{ $log->user_name ?? '—' }} <small style="color:#666">(ID: {{ $log->user_id }})</small></div>
        </div>
        <div class="col-md-3">
            <div class="detail-label">Timestamp</div>
            <div class="detail-value">{{ $log->created_at->format('d M Y H:i:s') }}</div>
            <div style="color:#666;font-size:.72rem">{{ $log->created_at->diffForHumans() }}</div>
        </div>
        <div class="col-md-3">
            <div class="detail-label">IP Address</div>
            <div class="detail-value" style="font-family:monospace">{{ $log->ip_address ?? '—' }}</div>
        </div>
        <div class="col-md-9">
            <div class="detail-label">User Agent</div>
            <div class="detail-value" style="font-size:.75rem;color:#888;word-break:break-all">{{ $log->user_agent ?? '—' }}</div>
        </div>
    </div>
</div>

{{-- Diff View --}}
@if($log->old_values || $log->new_values)
<div class="row g-3">
    @if($log->old_values)
    <div class="{{ $log->new_values ? 'col-md-6' : 'col-12' }}">
        <div class="diff-card">
            <div class="diff-label" style="color:#ff6b6b"><i class="bi bi-dash-circle me-2"></i>Before Change (Old Values)</div>
            @foreach($log->old_values as $key => $val)
            @php $newVal = $log->new_values[$key] ?? null; @endphp
            <div class="diff-row">
                <span class="diff-key">{{ $key }}</span>
                <span class="{{ ($newVal !== null && $newVal != $val) ? 'diff-old' : 'diff-unchanged' }}">
                    {{ is_array($val) ? json_encode($val) : ($val ?? 'null') }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if($log->new_values)
    <div class="{{ $log->old_values ? 'col-md-6' : 'col-12' }}">
        <div class="diff-card">
            <div class="diff-label" style="color:#51cf66"><i class="bi bi-plus-circle me-2"></i>After Change (New Values)</div>
            @foreach($log->new_values as $key => $val)
            @php $oldVal = $log->old_values[$key] ?? null; @endphp
            <div class="diff-row">
                <span class="diff-key">{{ $key }}</span>
                <span class="{{ ($oldVal !== null && $oldVal != $val) ? 'diff-new' : 'diff-unchanged' }}">
                    {{ is_array($val) ? json_encode($val) : ($val ?? 'null') }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@else
<div class="detail-card" style="text-align:center;color:#666;padding:30px">
    <i class="bi bi-info-circle me-2"></i>No data changes recorded for this event
</div>
@endif
@endsection
