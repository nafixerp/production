@extends('layouts.app')
@section('title','Accounts')
@section('page-title','Chart of Accounts')

@section('content')
<div class="card-gold">
    <div class="card-header-gold">
        <h5>◆ Accounts List</h5>
        <a href="{{ route('accounts.create') }}" class="btn-gold">+ New Account</a>
    </div>

    <form method="GET" class="mb-3 d-flex gap-2">
        <input type="text" name="q" value="{{ $q }}" class="form-control" style="max-width:280px" placeholder="Search code or name...">
        <button class="btn-outline-gold">Search</button>
        @if($q)<a href="{{ route('accounts.index') }}" class="btn-outline-gold">Clear</a>@endif
    </form>

    <div class="table-responsive">
    <table class="table-gold w-100">
        <thead>
            <tr>
                <th>#</th>
                <th>Code</th>
                <th>Name</th>
                <th>Type</th>
                <th>Group</th>
                <th>Opening Bal</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($accounts as $acc)
            <tr>
                <td>{{ $accounts->firstItem() + $loop->index }}</td>
                <td><span class="badge-gold">{{ $acc->code }}</span></td>
                <td>{{ $acc->name }}</td>
                <td>{{ ucfirst($acc->atype ?? '-') }}</td>
                <td>{{ $acc->group->name ?? '-' }}</td>
                <td>
                    {{ number_format($acc->opening_balance,2) }}
                    <small>({{ strtoupper($acc->ob_type) }})</small>
                </td>
                <td>
                    @if($acc->status)
                        <span style="color:#4ade80; font-size:0.75rem;">Active</span>
                    @else
                        <span style="color:#f87171; font-size:0.75rem;">Inactive</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('accounts.edit',$acc->id) }}" class="btn-outline-gold btn-sm-gold">Edit</a>
                    <form method="POST" action="{{ route('accounts.destroy',$acc->id) }}" style="display:inline" onsubmit="return confirm('Delete this account?')">
                        @csrf @method('DELETE')
                        <button class="btn-outline-gold btn-sm-gold" style="color:#f87171;border-color:rgba(248,113,113,0.4)">Del</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center" style="padding:24px;color:var(--text-muted-gold)">No accounts found.</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
    <div class="mt-3">{{ $accounts->links() }}</div>
</div>
@endsection
