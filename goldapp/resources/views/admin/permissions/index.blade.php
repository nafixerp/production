@extends('layouts.app')
@section('title','Permissions')
@section('page-title','Permission Management')

@push('styles')
<style>
.card-gold { background: var(--bg-card); border: 1px solid var(--border-gold); border-radius: 10px; padding: 20px; margin-bottom: 16px; }
.perm-module-title { color: var(--gold); font-size: .72rem; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 10px; border-bottom: 1px solid var(--border-gold); padding-bottom: 6px; display: flex; align-items: center; gap: 8px; }
.perm-badge { display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: .72rem; font-weight: 600; margin: 3px; background: rgba(212,175,55,.12); color: var(--gold); border: 1px solid rgba(212,175,55,.2); }
.perm-badge.action-view    { background: rgba(100,180,255,.1); color: #80c0ff; border-color: rgba(100,180,255,.2); }
.perm-badge.action-create  { background: rgba(81,207,102,.1); color: #51cf66; border-color: rgba(81,207,102,.2); }
.perm-badge.action-edit    { background: rgba(212,175,55,.12); color: var(--gold); }
.perm-badge.action-delete  { background: rgba(255,107,107,.1); color: #ff6b6b; border-color: rgba(255,107,107,.2); }
.perm-badge.action-approve { background: rgba(81,207,102,.15); color: #51cf66; }
.perm-badge.action-export  { background: rgba(240,208,96,.12); color: #f0d060; }
</style>
@endpush

@section('content')
@if(session('success'))
<div class="alert alert-dismissible fade show mb-3" style="background:rgba(81,207,102,.1);border-color:rgba(81,207,102,.3);color:#51cf66" role="alert">
    {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="d-flex justify-content-between align-items-center mb-3">
    <h6 style="color:var(--gold);margin:0"><i class="bi bi-key me-2"></i>Permission Registry</h6>
    <div class="d-flex gap-2">
        <form method="POST" action="{{ route('permissions.auto-generate') }}">
            @csrf
            <button type="submit" class="btn btn-sm" style="background:rgba(81,207,102,.15);color:#51cf66;border:1px solid rgba(81,207,102,.3);font-size:.78rem"
                onclick="return confirm('Auto-generate missing permissions for all modules?')">
                <i class="bi bi-magic me-1"></i>Auto-Generate
            </button>
        </form>
        <a href="{{ route('admin.permissions.create') }}" class="btn btn-sm" style="background:var(--gold);color:#000;font-weight:600;font-size:.78rem">
            <i class="bi bi-plus-lg me-1"></i>Add Permission
        </a>
    </div>
</div>

@forelse($permissions as $module => $perms)
<div class="card-gold">
    <div class="perm-module-title">
        <i class="bi bi-puzzle"></i>
        {{ ucfirst($module) }}
        <span style="color:#555;font-size:.65rem;font-weight:400">({{ $perms->count() }} permissions)</span>
    </div>
    <div>
        @foreach($perms as $perm)
        <span class="perm-badge action-{{ $perm->action }}" title="{{ $perm->name }}">
            {{ ucfirst($perm->action) }}
        </span>
        @endforeach
    </div>
    <div class="mt-2">
        @foreach($perms as $perm)
        <small style="color:#555;margin-right:8px;font-size:.68rem;font-family:monospace">{{ $perm->name }}</small>
        @endforeach
    </div>
</div>
@empty
<div class="card-gold text-center" style="padding:40px">
    <i class="bi bi-key" style="font-size:2rem;color:#333;display:block;margin-bottom:12px"></i>
    <p style="color:#666;margin:0">No permissions defined yet.</p>
    <form method="POST" action="{{ route('permissions.auto-generate') }}" class="mt-3">
        @csrf
        <button type="submit" class="btn" style="background:var(--gold);color:#000;font-weight:600">
            <i class="bi bi-magic me-2"></i>Auto-Generate All Permissions
        </button>
    </form>
</div>
@endforelse
@endsection
