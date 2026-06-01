@extends('layouts.app')
@section('title', isset($role) ? 'Edit Role' : 'Create Role')
@section('page-title', isset($role) ? 'Edit Role' : 'Create Role')

@push('styles')
<style>
.form-card { background: var(--bg-card); border: 1px solid var(--border-gold); border-radius: 10px; padding: 28px; }
.form-label { color: var(--text-muted-gold); font-size: .72rem; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 5px; }
.form-control, .form-select { background: rgba(255,255,255,.04); border: 1px solid var(--border-gold); color: #e8e0c8; font-size: .84rem; padding: 9px 12px; }
.form-control:focus, .form-select:focus { background: rgba(255,255,255,.07); border-color: var(--gold); box-shadow: 0 0 0 3px rgba(212,175,55,.15); color: #e8e0c8; }
.form-control::placeholder { color: #666; }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="form-card">
            <div style="color:var(--gold);font-size:.72rem;letter-spacing:2px;text-transform:uppercase;margin-bottom:18px;border-bottom:1px solid var(--border-gold);padding-bottom:8px">
                <i class="bi bi-shield{{ isset($role) ? '-check' : '-plus' }} me-2"></i>
                {{ isset($role) ? 'Update Role' : 'Create New Role' }}
            </div>

            @if($errors->any())
            <div class="alert mb-3" style="background:rgba(255,107,107,.1);border-color:rgba(255,107,107,.3);color:#ff6b6b">
                @foreach($errors->all() as $e)
                <div style="font-size:.82rem"><i class="bi bi-x-circle me-1"></i>{{ $e }}</div>
                @endforeach
            </div>
            @endif

            <form method="POST" action="{{ isset($role) ? route('admin.roles.update', $role) : route('admin.roles.store') }}">
                @csrf
                @if(isset($role)) @method('PUT') @endif

                <div class="mb-3">
                    <label class="form-label">Role Code / Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $role->name ?? '') }}"
                        placeholder="e.g. store_manager" pattern="[a-z_]+" title="Lowercase letters and underscores only">
                    <small style="color:#666;font-size:.7rem">Lowercase letters and underscores only (e.g. sales_manager)</small>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Display Name <span class="text-danger">*</span></label>
                    <input type="text" name="display_name" class="form-control @error('display_name') is-invalid @enderror"
                        value="{{ old('display_name', $role->display_name ?? '') }}" placeholder="e.g. Store Manager">
                    @error('display_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3"
                        placeholder="Brief description of this role's responsibilities">{{ old('description', $role->description ?? '') }}</textarea>
                </div>

                <div class="d-flex gap-2 justify-content-end">
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-sm" style="background:rgba(255,255,255,.05);color:#a09878;border:1px solid var(--border-gold)">
                        <i class="bi bi-x me-1"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-sm" style="background:var(--gold);color:#000;font-weight:600;padding:8px 20px">
                        <i class="bi bi-check-lg me-1"></i>{{ isset($role) ? 'Update Role' : 'Create & Set Permissions' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
