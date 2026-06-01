@extends('layouts.app')
@section('title','User Management')
@section('page-title','User Management')

@push('styles')
<style>
.user-table th { color: var(--text-muted-gold); font-size: .68rem; letter-spacing: 1px; text-transform: uppercase; border-color: var(--border-gold); background: rgba(212,175,55,.04); padding: 10px 12px; }
.user-table td { border-color: rgba(212,175,55,.08); padding: 10px 12px; color: #d0c8a8; font-size: .83rem; vertical-align: middle; }
.user-table tr:hover td { background: rgba(212,175,55,.04); }
.avatar-circle { width: 34px; height: 34px; border-radius: 50%; background: rgba(212,175,55,.2); border: 2px solid var(--border-gold); display: flex; align-items: center; justify-content: center; color: var(--gold); font-weight: 700; font-size: .82rem; flex-shrink: 0; }
.role-badge { font-size: .68rem; padding: 3px 9px; border-radius: 12px; background: rgba(212,175,55,.15); color: var(--gold); border: 1px solid rgba(212,175,55,.25); }
.status-active { color: #51cf66; }
.status-inactive { color: #ff6b6b; }
.card-gold { background: var(--bg-card); border: 1px solid var(--border-gold); border-radius: 10px; padding: 20px; }
.page-actions { display: flex; gap: 8px; align-items: center; margin-bottom: 18px; justify-content: space-between; }
</style>
@endpush

@section('content')
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mb-3" style="background:rgba(81,207,102,.1);border-color:rgba(81,207,102,.3);color:#51cf66" role="alert">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="page-actions">
    <h6 style="color:var(--gold);margin:0"><i class="bi bi-people me-2"></i>All Users ({{ $users->total() }})</h6>
    <a href="{{ route('users.create') }}" class="btn" style="background:var(--gold);color:#000;font-weight:600;font-size:.82rem">
        <i class="bi bi-plus-lg me-1"></i>Add User
    </a>
</div>

<div class="card-gold">
    <div class="table-responsive">
        <table class="table user-table mb-0">
            <thead>
                <tr>
                    <th style="width:40px">#</th>
                    <th>User</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Last Login</th>
                    <th>Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-circle">
                                @if($user->avatar)
                                <img src="{{ Storage::url($user->avatar) }}" style="width:100%;height:100%;border-radius:50%;object-fit:cover">
                                @else
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                @endif
                            </div>
                            <div>
                                <div style="font-weight:600;color:#e8e0c8">{{ $user->name }}</div>
                                <div style="font-size:.7rem;color:var(--text-muted-gold)">ID: {{ $user->id }}</div>
                            </div>
                        </div>
                    </td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @if($user->roleModel)
                        <span class="role-badge">{{ $user->roleModel->display_name ?? $user->roleModel->name }}</span>
                        @else
                        <span style="color:#666;font-size:.75rem">No Role</span>
                        @endif
                    </td>
                    <td>
                        @if($user->last_login_at)
                        <span title="{{ $user->last_login_at }}">{{ $user->last_login_at->diffForHumans() }}</span>
                        @else
                        <span style="color:#555">Never</span>
                        @endif
                    </td>
                    <td>
                        @if($user->is_active)
                        <span class="status-active"><i class="bi bi-circle-fill" style="font-size:.5rem;vertical-align:middle"></i> Active</span>
                        @else
                        <span class="status-inactive"><i class="bi bi-circle-fill" style="font-size:.5rem;vertical-align:middle"></i> Inactive</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm" style="background:rgba(212,175,55,.15);color:var(--gold);border:1px solid var(--border-gold);font-size:.72rem" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button type="button" class="btn btn-sm" style="background:rgba(81,207,102,.1);color:#51cf66;border:1px solid rgba(81,207,102,.3);font-size:.72rem"
                                data-bs-toggle="modal" data-bs-target="#resetModal" data-uid="{{ $user->id }}" data-name="{{ $user->name }}" title="Reset Password">
                                <i class="bi bi-key"></i>
                            </button>
                            @if(auth()->id() !== $user->id)
                            <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Deactivate {{ $user->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm" style="background:rgba(255,107,107,.1);color:#ff6b6b;border:1px solid rgba(255,107,107,.3);font-size:.72rem" title="Deactivate">
                                    <i class="bi bi-person-dash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No users found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="mt-3 d-flex justify-content-end">
        {{ $users->links() }}
    </div>
    @endif
</div>

{{-- Reset Password Modal --}}
<div class="modal fade" id="resetModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content" style="background:var(--bg-card);border:1px solid var(--border-gold)">
            <div class="modal-header" style="border-color:var(--border-gold)">
                <h6 class="modal-title" style="color:var(--gold)"><i class="bi bi-key me-2"></i>Reset Password</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="resetForm">
                @csrf
                <div class="modal-body">
                    <p style="color:#a09878;font-size:.82rem">Reset password for: <strong id="resetUserName" style="color:var(--gold)"></strong></p>
                    <div class="mb-2">
                        <label style="color:var(--text-muted-gold);font-size:.7rem;text-transform:uppercase;letter-spacing:1px">New Password</label>
                        <input type="password" name="new_password" class="form-control" style="background:rgba(255,255,255,.04);border-color:var(--border-gold);color:#e8e0c8" required minlength="8">
                    </div>
                    <div>
                        <label style="color:var(--text-muted-gold);font-size:.7rem;text-transform:uppercase;letter-spacing:1px">Confirm Password</label>
                        <input type="password" name="new_password_confirmation" class="form-control" style="background:rgba(255,255,255,.04);border-color:var(--border-gold);color:#e8e0c8" required>
                    </div>
                </div>
                <div class="modal-footer" style="border-color:var(--border-gold)">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm" style="background:var(--gold);color:#000;font-weight:600">Reset Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('resetModal').addEventListener('show.bs.modal', function(e) {
    const btn = e.relatedTarget;
    const uid = btn.getAttribute('data-uid');
    const name = btn.getAttribute('data-name');
    document.getElementById('resetUserName').textContent = name;
    document.getElementById('resetForm').action = '/users/' + uid + '/reset-password';
});
</script>
@endpush
