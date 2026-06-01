@extends('layouts.app')
@section('title','My Profile')
@section('page-title','My Profile')

@push('styles')
<style>
.profile-card { background: var(--bg-card); border: 1px solid var(--border-gold); border-radius: 10px; padding: 28px; }
.section-title { color: var(--gold); font-size: .72rem; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 16px; border-bottom: 1px solid var(--border-gold); padding-bottom: 8px; }
.form-label { color: var(--text-muted-gold); font-size: .72rem; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 5px; }
.form-control { background: rgba(255,255,255,.04); border: 1px solid var(--border-gold); color: #e8e0c8; font-size: .84rem; padding: 9px 12px; }
.form-control:focus { background: rgba(255,255,255,.07); border-color: var(--gold); box-shadow: 0 0 0 3px rgba(212,175,55,.15); color: #e8e0c8; }
.form-control::placeholder { color: #555; }
.avatar-preview { width: 90px; height: 90px; border-radius: 50%; background: rgba(212,175,55,.15); border: 3px solid var(--border-gold); display: flex; align-items: center; justify-content: center; font-size: 2rem; color: var(--gold); overflow: hidden; }
.avatar-preview img { width: 100%; height: 100%; object-fit: cover; }
.info-row { display: flex; align-items: center; gap: 10px; padding: 8px 0; border-bottom: 1px solid rgba(212,175,55,.06); }
.info-label { color: var(--text-muted-gold); font-size: .72rem; letter-spacing: 1px; text-transform: uppercase; min-width: 130px; }
.info-value { color: #e8e0c8; font-size: .84rem; }
</style>
@endpush

@section('content')
@if(session('success'))
<div class="alert alert-dismissible fade show mb-3" style="background:rgba(81,207,102,.1);border-color:rgba(81,207,102,.3);color:#51cf66" role="alert">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if($errors->any())
<div class="alert alert-dismissible fade show mb-3" style="background:rgba(255,107,107,.1);border-color:rgba(255,107,107,.3);color:#ff6b6b" role="alert">
    @foreach($errors->all() as $e)
    <div><i class="bi bi-x-circle me-1"></i>{{ $e }}</div>
    @endforeach
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-3">
    {{-- Account Info --}}
    <div class="col-md-4">
        <div class="profile-card">
            <div class="section-title"><i class="bi bi-person-circle me-2"></i>Account Info</div>
            <div class="d-flex flex-column align-items-center mb-4">
                <div class="avatar-preview mb-3">
                    @if($user->avatar)
                    <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}">
                    @else
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                    @endif
                </div>
                <div style="color:var(--gold);font-weight:700;font-size:1.05rem">{{ $user->name }}</div>
                <div style="color:#a09878;font-size:.8rem">{{ $user->email }}</div>
            </div>

            <div class="info-row"><span class="info-label">User ID</span><span class="info-value">#{{ $user->id }}</span></div>
            <div class="info-row">
                <span class="info-label">Role</span>
                <span class="info-value">
                    @if($user->roleModel)
                    <span style="background:rgba(212,175,55,.15);color:var(--gold);padding:2px 8px;border-radius:4px;font-size:.8rem">{{ $user->roleModel->display_name ?? $user->roleModel->name }}</span>
                    @else
                    <span style="color:#555">No Role Assigned</span>
                    @endif
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Status</span>
                <span class="info-value">
                    @if($user->is_active)
                    <span style="color:#51cf66"><i class="bi bi-circle-fill" style="font-size:.45rem;vertical-align:middle;margin-right:4px"></i>Active</span>
                    @else
                    <span style="color:#ff6b6b"><i class="bi bi-circle-fill" style="font-size:.45rem;vertical-align:middle;margin-right:4px"></i>Inactive</span>
                    @endif
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Last Login</span>
                <span class="info-value" style="font-size:.8rem">{{ $user->last_login_at ? $user->last_login_at->format('d M Y H:i') : 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Member Since</span>
                <span class="info-value" style="font-size:.8rem">{{ $user->created_at ? $user->created_at->format('d M Y') : 'N/A' }}</span>
            </div>
        </div>
    </div>

    {{-- Edit Profile Form --}}
    <div class="col-md-8">
        <div class="profile-card">
            <div class="section-title"><i class="bi bi-pencil-square me-2"></i>Edit Profile</div>

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf @method('PUT')

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $user->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email', $user->email) }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">Profile Avatar</label>
                        <input type="file" name="avatar" class="form-control" accept="image/jpeg,image/png,image/gif">
                        <small style="color:#666;font-size:.7rem">Max 2MB. JPG, PNG or GIF.</small>
                    </div>
                </div>

                <div class="section-title" style="margin-top:8px"><i class="bi bi-lock me-2"></i>Change Password <span style="color:#666;font-size:.68rem;font-weight:400">(leave blank to keep current)</span></div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Current Password</label>
                        <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror"
                            placeholder="Enter current password">
                        @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">New Password</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                            placeholder="Min. 8 characters">
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat new password">
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn" style="background:var(--gold);color:#000;font-weight:700;padding:10px 28px">
                        <i class="bi bi-save me-2"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
