@extends('layouts.app')
@section('title', isset($permission) ? 'Edit Permission' : 'Create Permission')
@section('page-title', isset($permission) ? 'Edit Permission' : 'Create Permission')

@push('styles')
<style>
.form-card { background: var(--bg-card); border: 1px solid var(--border-gold); border-radius: 10px; padding: 28px; }
.form-label { color: var(--text-muted-gold); font-size: .72rem; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 5px; }
.form-control, .form-select { background: rgba(255,255,255,.04); border: 1px solid var(--border-gold); color: #e8e0c8; font-size: .84rem; padding: 9px 12px; }
.form-control:focus, .form-select:focus { background: rgba(255,255,255,.07); border-color: var(--gold); box-shadow: 0 0 0 3px rgba(212,175,55,.15); color: #e8e0c8; }
.form-select option { background: #1a1535; }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="form-card">
            <div style="color:var(--gold);font-size:.72rem;letter-spacing:2px;text-transform:uppercase;margin-bottom:18px;border-bottom:1px solid var(--border-gold);padding-bottom:8px">
                <i class="bi bi-key me-2"></i>{{ isset($permission) ? 'Update Permission' : 'Create Permission' }}
            </div>

            @if($errors->any())
            <div class="alert mb-3" style="background:rgba(255,107,107,.1);border-color:rgba(255,107,107,.3);color:#ff6b6b">
                @foreach($errors->all() as $e)
                <div style="font-size:.82rem"><i class="bi bi-x-circle me-1"></i>{{ $e }}</div>
                @endforeach
            </div>
            @endif

            <form method="POST" action="{{ isset($permission) ? route('admin.permissions.update', $permission) : route('admin.permissions.store') }}">
                @csrf
                @if(isset($permission)) @method('PUT') @endif

                <div class="mb-3">
                    <label class="form-label">Permission Name (Code) <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $permission->name ?? '') }}" placeholder="e.g. sales.create">
                    <small style="color:#666;font-size:.7rem">Format: module.action (e.g. inventory.view)</small>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Display Name <span class="text-danger">*</span></label>
                    <input type="text" name="display_name" class="form-control @error('display_name') is-invalid @enderror"
                        value="{{ old('display_name', $permission->display_name ?? '') }}" placeholder="e.g. Sales - Create">
                    @error('display_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Module <span class="text-danger">*</span></label>
                        <select name="module" class="form-select @error('module') is-invalid @enderror">
                            <option value="">-- Select Module --</option>
                            @foreach($modules as $m)
                            <option value="{{ $m }}" {{ old('module', $permission->module ?? '') == $m ? 'selected' : '' }}>{{ ucfirst($m) }}</option>
                            @endforeach
                        </select>
                        @error('module') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Action <span class="text-danger">*</span></label>
                        <select name="action" class="form-select @error('action') is-invalid @enderror">
                            <option value="">-- Select Action --</option>
                            @foreach($actions as $a)
                            <option value="{{ $a }}" {{ old('action', $permission->action ?? '') == $a ? 'selected' : '' }}>{{ ucfirst($a) }}</option>
                            @endforeach
                        </select>
                        @error('action') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="d-flex gap-2 justify-content-end">
                    <a href="{{ route('admin.permissions.index') }}" class="btn btn-sm" style="background:rgba(255,255,255,.05);color:#a09878;border:1px solid var(--border-gold)">
                        <i class="bi bi-x me-1"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-sm" style="background:var(--gold);color:#000;font-weight:600;padding:8px 20px">
                        <i class="bi bi-check-lg me-1"></i>{{ isset($permission) ? 'Update' : 'Create' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
