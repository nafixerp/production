@extends('layouts.app')
@section('title','Chart of Accounts')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-diagram-3 me-2"></i>Chart of Accounts</h5>
    <a href="{{ route('chart-of-accounts.create') }}" class="btn btn-sm btn-gold"><i class="bi bi-plus-lg me-1"></i>New Account</a>
</div>

<div class="card-dark mb-3">
    <form method="GET" class="row g-2">
        <div class="col-md-3">
            <select name="group" class="form-select form-select-sm">
                <option value="">All Groups</option>
                @foreach(['asset','liability','equity','income','expense','tax'] as $g)
                <option value="{{ $g }}" {{ request('group')===$g?'selected':'' }}>{{ ucfirst($g) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2"><button type="submit" class="btn btn-sm btn-outline-gold w-100">Filter</button></div>
    </form>
</div>

@if(session('success'))<div class="alert alert-success alert-sm">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger alert-sm">{{ session('error') }}</div>@endif

<div class="table-responsive">
<table class="table table-dark-erp table-hover">
    <thead>
        <tr><th>Code</th><th>Name</th><th>Group</th><th>Type</th><th>Parent</th><th>Bank</th><th>OB</th><th>Status</th><th>Actions</th></tr>
    </thead>
    <tbody>
        @foreach($allGroups as $grp)
        @php $grpAccounts = $accounts->where('account_group', $grp); @endphp
        @if($grpAccounts->count() > 0)
        <tr class="table-group-header">
            <td colspan="9" class="text-uppercase fw-bold" style="color:var(--gold);background:rgba(212,175,55,.08);font-size:.72rem;letter-spacing:2px;padding:6px 12px">
                {{ $grp }} ({{ $grpAccounts->count() }} accounts)
            </td>
        </tr>
        @foreach($grpAccounts as $a)
        <tr>
            <td><span class="badge bg-secondary">{{ $a->code }}</span></td>
            <td style="{{ $a->parent_id ? 'padding-left:28px' : '' }}">
                {{ $a->parent_id ? '↳ ' : '' }}{{ $a->name }}
                @if($a->is_control_account) <span class="badge bg-warning text-dark" style="font-size:.6rem">CTRL</span>@endif
            </td>
            <td><span class="badge badge-group-{{ $a->account_group }}">{{ ucfirst($a->account_group) }}</span></td>
            <td>{{ $a->account_type }}</td>
            <td>{{ $a->parent?->name ?? '-' }}</td>
            <td>{{ $a->is_bank_account ? '<i class="bi bi-bank text-info"></i>' : '-' }}</td>
            <td class="text-end">{{ $a->ob_type === 'dr' ? 'Dr' : 'Cr' }} {{ number_format($a->opening_balance,2) }}</td>
            <td><span class="badge {{ $a->status ? 'bg-success' : 'bg-danger' }}">{{ $a->status ? 'Active' : 'Inactive' }}</span></td>
            <td>
                <a href="{{ route('chart-of-accounts.show',$a->id) }}" class="btn btn-xs btn-outline-info"><i class="bi bi-eye"></i></a>
                <a href="{{ route('chart-of-accounts.edit',$a->id) }}" class="btn btn-xs btn-outline-warning"><i class="bi bi-pencil"></i></a>
                <form method="POST" action="{{ route('chart-of-accounts.destroy',$a->id) }}" class="d-inline" onsubmit="return confirm('Delete?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-xs btn-outline-danger"><i class="bi bi-trash"></i></button>
                </form>
            </td>
        </tr>
        @endforeach
        @endif
        @endforeach
    </tbody>
</table>
</div>

<style>
.badge-group-asset{background:#3498db}.badge-group-liability{background:#e74c3c}
.badge-group-equity{background:#9b59b6}.badge-group-income{background:#27ae60}
.badge-group-expense{background:#e67e22}.badge-group-tax{background:#95a5a6}
.btn-xs{padding:2px 6px;font-size:.7rem}
</style>
@endsection
