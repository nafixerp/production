@extends('layouts.app')
@section('title','Role Management')
@section('page-title','Role Management')

@push('styles')
<style>
.card-gold { background: var(--bg-card); border: 1px solid var(--border-gold); border-radius: 10px; padding: 20px; }
.role-table th { color: var(--text-muted-gold); font-size: .68rem; letter-spacing: 1px; text-transform: uppercase; border-color: var(--border-gold); background: rgba(212,175,55,.04); padding: 10px 12px; }
.role-table td { border-color: rgba(212,175,55,.08); padding: 10px 12px; color: #d0c8a8; font-size: .83rem; vertical-align: middle; }
.role-table tr:hover td { background: rgba(212,175,55,.04); }
.system-badge { font-size: .65rem; padding: 2px 7px; border-radius: 4px; background: rgba(160,130,28,.3); color: var(--gold-light); border: 1px solid rgba(212,175,55,.2); }
</style>
@endpush

@section('content')
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mb-3" style="background:rgba(81,207,102,.1);border-color:rgba(81,207,102,.3);color:#51cf66" role="alert">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-dismissible fade show mb-3" style="background:rgba(255,107,107,.1);border-color:rgba(255,107,107,.3);color:#ff6b6b" role="alert">
    <i class="bi bi-x-circle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="d-flex justify-content-between align-items-center mb-3">
    <h6 style="color:var(--gold);margin:0"><i class="bi bi-shield-check me-2"></i>Roles ({{ $roles->count() }})</h6>
    <a href="{{ route('admin.roles.create') }}" class="btn" style="background:var(--gold);color:#000;font-weight:600;font-size:.82rem">
        <i class="bi bi-plus-lg me-1"></i>Add Role
    </a>
</div>

<div class="card-gold">
    <div class="table-responsive">
        <table class="table role-table mb-0">
            <thead>
                <tr><th>Role Name</th><th>Display Name</th><th>Description</th><th class="text-center">Users</th><th class="text-center">Type</th><th class="text-center">Actions</th></tr>
            </thead>
            <tbody>
                @forelse($roles as $role)
                <tr>
                    <td><code style="color:var(--gold);background:rgba(212,175,55,.08);padding:2px 6px;border-radius:3px">{{ $role->name }}</code></td>
                    <td><strong>{{ $role->display_name ?? $role->name }}</strong></td>
                    <td style="color:#a09878;font-size:.78rem">{{ Str::limit($role->description, 60) }}</td>
                    <td class="text-center">
                        <span style="background:rgba(212,175,55,.15);color:var(--gold);padding:3px 10px;border-radius:10px;font-size:.78rem;font-weight:600">
                            {{ $role->user_count ?? 0 }}
                        </span>
                    </td>
                    <td class="text-center">
                        @if($role->is_system)
                        <span class="system-badge"><i class="bi bi-lock-fill me-1" style="font-size:.6rem"></i>System</span>
                        @else
                        <span style="color:#666;font-size:.75rem">Custom</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            <a href="{{ route('roles.permissions', $role->id) }}" class="btn btn-sm" style="background:rgba(81,207,102,.1);color:#51cf66;border:1px solid rgba(81,207,102,.3);font-size:.72rem" title="Permissions">
                                <i class="bi bi-key"></i>
                            </a>
                            <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm" style="background:rgba(212,175,55,.15);color:var(--gold);border:1px solid var(--border-gold);font-size:.72rem" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @if(!$role->is_system)
                            <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" onsubmit="return confirm('Delete role {{ $role->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm" style="background:rgba(255,107,107,.1);color:#ff6b6b;border:1px solid rgba(255,107,107,.3);font-size:.72rem" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No roles found. <a href="{{ route('admin.roles.create') }}" style="color:var(--gold)">Create one.</a></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
