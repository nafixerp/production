@extends('layouts.app')
@section('title', isset($user) ? 'Edit User' : 'Add User')
@section('page-title', isset($user) ? 'Edit User' : 'Add User')

@push('styles')
<style>
.form-card { background: var(--bg-card); border: 1px solid var(--border-gold); border-radius: 10px; padding: 28px; }
.form-section-title { color: var(--gold); font-size: .72rem; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 14px; border-bottom: 1px solid var(--border-gold); padding-bottom: 8px; }
.form-label { color: var(--text-muted-gold); font-size: .72rem; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 5px; }
.form-control, .form-select { background: rgba(255,255,255,.04); border: 1px solid var(--border-gold); color: #e8e0c8; font-size: .84rem; padding: 9px 12px; }
.form-control:focus, .form-select:focus { background: rgba(255,255,255,.07); border-color: var(--gold); box-shadow: 0 0 0 3px rgba(212,175,55,.15); color: #e8e0c8; }
.form-control::placeholder { color: #666; }
.form-select option { background: #1a1535; color: #e8e0c8; }
.invalid-feedback { font-size: .75rem; }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="form-card">
            <div class="form-section-title">
                <i class="bi bi-person{{ isset($user) ? '-gear' : '-plus' }} me-2"></i>
                {{ isset($user) ? 'Update User Account' : 'Create New User' }}
            </div>

            @if($errors->any())
            <div class="alert mb-3" style="background:rgba(255,107,107,.1);border-color:rgba(255,107,107,.3);color:#ff6b6b">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $e)
                    <li style="font-size:.82rem">{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ isset($user) ? route('users.update', $user) : route('users.store') }}" enctype="multipart/form-data">
                @csrf
                @if(isset($user)) @method('PUT') @endif

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $user->name ?? '') }}" placeholder="Enter full name" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email', $user->email ?? '') }}" placeholder="user@company.com" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    @if(!isset($user))
                    <div class="col-md-6">
                        <label class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Min. 8 characters" required minlength="8">
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat password" required>
                    </div>
                    @endif

                    <div class="col-md-6">
                        <label class="form-label">Assign Role</label>
                        <select name="role_id" class="form-select @error('role_id') is-invalid @enderror">
                            <option value="">-- Select Role --</option>
                            @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id', $user->role_id ?? '') == $role->id ? 'selected' : '' }}>
                                {{ $role->display_name ?? $role->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('role_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Avatar (Optional)</label>
                        <input type="file" name="avatar" class="form-control" accept="image/*">
                        @if(isset($user) && $user->avatar)
                        <small class="text-muted d-block mt-1">Current: <a href="{{ Storage::url($user->avatar) }}" target="_blank" style="color:var(--gold)">View</a></small>
                        @endif
                    </div>

                    <div class="col-12">
                        <div class="form-check" style="padding-left:0">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                value="1" {{ old('is_active', $user->is_active ?? 1) ? 'checked' : '' }}
                                style="background:rgba(212,175,55,.2);border-color:var(--border-gold);cursor:pointer">
                            <label class="form-check-label ms-2" for="is_active" style="color:#a09878;font-size:.84rem;cursor:pointer">
                                Account Active
                            </label>
                        </div>
                    </div>
                </div>

                <hr style="border-color:var(--border-gold);margin:20px 0">
                <div class="d-flex gap-2 justify-content-end">
                    <a href="{{ route('users.index') }}" class="btn btn-sm" style="background:rgba(255,255,255,.05);color:#a09878;border:1px solid var(--border-gold)">
                        <i class="bi bi-x me-1"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-sm" style="background:var(--gold);color:#000;font-weight:600;padding:8px 20px">
                        <i class="bi bi-check-lg me-1"></i>{{ isset($user) ? 'Update User' : 'Create User' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
