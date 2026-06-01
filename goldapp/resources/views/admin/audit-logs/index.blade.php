@extends('layouts.app')
@section('title','Audit Logs')
@section('page-title','Audit Logs')

@push('styles')
<style>
.card-gold { background: var(--bg-card); border: 1px solid var(--border-gold); border-radius: 10px; padding: 20px; }
.filter-bar { background: var(--bg-card); border: 1px solid var(--border-gold); border-radius: 8px; padding: 14px 18px; margin-bottom: 18px; }
.filter-bar .form-label { color: var(--text-muted-gold); font-size: .68rem; letter-spacing: 1px; text-transform: uppercase; }
.filter-bar .form-control, .filter-bar .form-select { background: rgba(255,255,255,.04); border-color: var(--border-gold); color: #e8e0c8; font-size: .82rem; }
.filter-bar .form-control:focus, .filter-bar .form-select:focus { background: rgba(255,255,255,.07); border-color: var(--gold); box-shadow: 0 0 0 2px rgba(212,175,55,.15); color: #e8e0c8; }
.audit-table th { color: var(--text-muted-gold); font-size: .68rem; letter-spacing: 1px; text-transform: uppercase; border-color: var(--border-gold); background: rgba(212,175,55,.04); padding: 10px 10px; }
.audit-table td { border-color: rgba(212,175,55,.08); padding: 9px 10px; color: #d0c8a8; font-size: .8rem; vertical-align: middle; }
.audit-table tr:hover td { background: rgba(212,175,55,.04); }
.action-badge { display: inline-block; padding: 2px 9px; border-radius: 4px; font-size: .67rem; font-weight: 600; letter-spacing: .5px; }
.action-create  { background: rgba(81,207,102,.15);  color: #51cf66; }
.action-update  { background: rgba(212,175,55,.15);  color: var(--gold); }
.action-delete  { background: rgba(255,107,107,.15); color: #ff6b6b; }
.action-login   { background: rgba(81,150,255,.15);  color: #74b0ff; }
.action-logout  { background: rgba(150,150,150,.15); color: #aaa; }
.action-export  { background: rgba(240,208,96,.15);  color: #f0d060; }
.action-approve { background: rgba(81,207,102,.2);   color: #51cf66; }
.action-view    { background: rgba(100,180,255,.1);  color: #80c0ff; }
</style>
@endpush

@section('content')
<div class="filter-bar">
    <form method="GET" class="row g-3 align-items-end">
        <div class="col-md-2">
            <label class="form-label">User</label>
            <input type="text" name="user_name" class="form-control" value="{{ request('user_name') }}" placeholder="Search user...">
        </div>
        <div class="col-md-2">
            <label class="form-label">Module</label>
            <select name="module" class="form-select">
                <option value="">All Modules</option>
                @foreach($modules as $m)
                <option value="{{ $m }}" {{ request('module') == $m ? 'selected' : '' }}>{{ $m }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Action</label>
            <select name="action" class="form-select">
                <option value="">All Actions</option>
                @foreach($actions as $a)
                <option value="{{ $a }}" {{ request('action') == $a ? 'selected' : '' }}>{{ ucfirst($a) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">From Date</label>
            <input type="date" name="from" class="form-control" value="{{ request('from') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label">To Date</label>
            <input type="date" name="to" class="form-control" value="{{ request('to') }}">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn w-100" style="background:var(--gold);color:#000;font-weight:600">
                <i class="bi bi-search me-1"></i>Filter
            </button>
        </div>
    </form>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h6 style="color:var(--gold);margin:0"><i class="bi bi-shield-lock me-2"></i>Activity Log ({{ $logs->total() }} records)</h6>
    <small style="color:#666">Showing {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ $logs->total() }}</small>
</div>

<div class="card-gold">
    <div class="table-responsive">
        <table class="table audit-table mb-0">
            <thead>
                <tr><th>Time</th><th>User</th><th>Action</th><th>Module</th><th>Record ID</th><th>IP Address</th><th class="text-center">Detail</th></tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td>
                        <div style="font-size:.78rem">{{ $log->created_at->format('d M Y') }}</div>
                        <div style="font-size:.7rem;color:#666">{{ $log->created_at->format('H:i:s') }}</div>
                    </td>
                    <td>
                        <div style="font-weight:600;color:#e8e0c8">{{ $log->user_name ?? '—' }}</div>
                        @if($log->user_id)
                        <div style="font-size:.7rem;color:#666">ID: {{ $log->user_id }}</div>
                        @endif
                    </td>
                    <td><span class="action-badge action-{{ $log->action }}">{{ strtoupper($log->action) }}</span></td>
                    <td><span style="color:var(--gold);font-size:.78rem">{{ $log->module ?? '—' }}</span></td>
                    <td>{{ $log->record_id ?? '—' }}</td>
                    <td style="font-size:.75rem;color:#888">{{ $log->ip_address }}</td>
                    <td class="text-center">
                        <a href="{{ route('audit-logs.show', $log->id) }}" class="btn btn-sm" style="background:rgba(212,175,55,.15);color:var(--gold);border:1px solid var(--border-gold);font-size:.7rem">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No audit log entries found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="mt-3 d-flex justify-content-center">
        {{ $logs->links() }}
    </div>
    @endif
</div>
@endsection
