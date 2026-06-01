@extends('layouts.app')
@section('title','Role Permissions — '.$role->display_name)
@section('page-title','Role Permissions')

@push('styles')
<style>
.perm-card { background: var(--bg-card); border: 1px solid var(--border-gold); border-radius: 10px; padding: 20px; }
.perm-matrix th { color: var(--text-muted-gold); font-size: .68rem; letter-spacing: 1.5px; text-transform: uppercase; border-color: var(--border-gold); background: rgba(212,175,55,.06); padding: 10px 14px; text-align: center; }
.perm-matrix th:first-child { text-align: left; min-width: 160px; }
.perm-matrix td { border-color: rgba(212,175,55,.08); padding: 10px 14px; color: #d0c8a8; vertical-align: middle; text-align: center; }
.perm-matrix td:first-child { text-align: left; font-size: .82rem; color: #e8e0c8; font-weight: 500; }
.perm-matrix tr:hover td { background: rgba(212,175,55,.03); }
.perm-cb { width: 18px; height: 18px; cursor: pointer; accent-color: var(--gold); }
.module-group-header td { background: rgba(212,175,55,.08) !important; color: var(--gold) !important; font-size: .7rem; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; }
.select-all-col { cursor: pointer; color: var(--gold); font-size: .7rem; letter-spacing: .5px; }
.select-all-col:hover { text-decoration: underline; }
.bulk-actions { display: flex; gap: 8px; align-items: center; margin-bottom: 14px; }
</style>
@endpush

@section('content')
@if(session('success'))
<div class="alert alert-dismissible fade show mb-3" style="background:rgba(81,207,102,.1);border-color:rgba(81,207,102,.3);color:#51cf66" role="alert">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h6 style="color:var(--gold);margin:0"><i class="bi bi-key me-2"></i>Permissions for: <strong>{{ $role->display_name ?? $role->name }}</strong></h6>
        <small style="color:#a09878;font-size:.75rem">Check/uncheck permissions then click Save</small>
    </div>
    <a href="{{ route('roles.index') }}" class="btn btn-sm" style="background:rgba(255,255,255,.05);color:#a09878;border:1px solid var(--border-gold);font-size:.78rem">
        <i class="bi bi-arrow-left me-1"></i>Back to Roles
    </a>
</div>

<div class="perm-card">
    <div class="bulk-actions">
        <button type="button" id="checkAll" class="btn btn-sm" style="background:rgba(81,207,102,.1);color:#51cf66;border:1px solid rgba(81,207,102,.3);font-size:.75rem">
            <i class="bi bi-check-all me-1"></i>Select All
        </button>
        <button type="button" id="uncheckAll" class="btn btn-sm" style="background:rgba(255,107,107,.1);color:#ff6b6b;border:1px solid rgba(255,107,107,.3);font-size:.75rem">
            <i class="bi bi-x-lg me-1"></i>Deselect All
        </button>
        <span style="color:#555;font-size:.72rem">|</span>
        <span id="countLabel" style="color:#a09878;font-size:.78rem">0 selected</span>
    </div>

    <form method="POST" action="{{ route('roles.save-permissions', $role->id) }}" id="permForm">
        @csrf
        <div class="table-responsive">
            <table class="table perm-matrix mb-3">
                <thead>
                    <tr>
                        <th>Module</th>
                        @foreach($actions as $action)
                        <th>
                            <div>{{ ucfirst($action) }}</div>
                            <div class="select-all-col" data-action="{{ $action }}" onclick="toggleColumn('{{ $action }}')">
                                <i class="bi bi-chevron-down" style="font-size:.55rem"></i> All
                            </div>
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($modules as $module)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:6px">
                                <i class="bi bi-puzzle" style="color:var(--text-muted-gold);font-size:.75rem"></i>
                                <strong>{{ ucfirst($module) }}</strong>
                            </div>
                            <div class="select-all-col" onclick="toggleRow('{{ $module }}')">
                                <i class="bi bi-check2-square" style="font-size:.65rem"></i> Select row
                            </div>
                        </td>
                        @foreach($actions as $action)
                        @php
                            $perm = $all_permissions->get($module.'.'.$action);
                        @endphp
                        <td>
                            @if($perm)
                            <input type="checkbox" name="permissions[]"
                                class="perm-cb perm-{{ $module }} perm-action-{{ $action }}"
                                value="{{ $perm->id }}"
                                data-module="{{ $module }}"
                                data-action="{{ $action }}"
                                {{ isset($assigned[$perm->id]) ? 'checked' : '' }}>
                            @else
                            <span style="color:#333;font-size:.7rem">—</span>
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn" style="background:var(--gold);color:#000;font-weight:700;padding:10px 28px">
                <i class="bi bi-save me-2"></i>Save Permissions
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function updateCount() {
    const checked = document.querySelectorAll('.perm-cb:checked').length;
    document.getElementById('countLabel').textContent = checked + ' selected';
}

document.getElementById('checkAll').onclick = () => {
    document.querySelectorAll('.perm-cb').forEach(cb => cb.checked = true);
    updateCount();
};
document.getElementById('uncheckAll').onclick = () => {
    document.querySelectorAll('.perm-cb').forEach(cb => cb.checked = false);
    updateCount();
};
document.querySelectorAll('.perm-cb').forEach(cb => cb.addEventListener('change', updateCount));

function toggleRow(module) {
    const cbs = document.querySelectorAll('.perm-' + module);
    const anyUnchecked = Array.from(cbs).some(cb => !cb.checked);
    cbs.forEach(cb => cb.checked = anyUnchecked);
    updateCount();
}

function toggleColumn(action) {
    const cbs = document.querySelectorAll('.perm-action-' + action);
    const anyUnchecked = Array.from(cbs).some(cb => !cb.checked);
    cbs.forEach(cb => cb.checked = anyUnchecked);
    updateCount();
}

updateCount();
</script>
@endpush
