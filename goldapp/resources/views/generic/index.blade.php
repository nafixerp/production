@extends('layouts.app')
@section('title', $title)
@section('page-title', $title)
@section('content')
<div class="card-gold">
    <div class="card-header-gold">
        <h5>◇ {{ $title }}</h5>
        <a href="{{ route($slug.'.create') }}" class="btn-outline-gold btn-sm-gold">
            <i class="bi bi-plus-lg"></i> Add New
        </a>
    </div>
    <form class="d-flex gap-2 mb-3" method="GET">
        <input name="q" value="{{ $q ?? '' }}" class="form-control" style="max-width:280px" placeholder="Search name/code...">
        <button class="btn-gold" type="submit">Search</button>
        @if($q ?? '') <a href="{{ route($slug.'.index') }}" class="btn-outline-gold">Clear</a> @endif
    </form>
    <div style="overflow-x:auto">
    <table class="table-gold w-100">
        <thead>
            <tr>
                <th>#</th>
                <th>Code</th>
                <th>Name</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
            <tr>
                <td>{{ $row->id }}</td>
                <td>{{ $row->code ?? '—' }}</td>
                <td>{{ $row->name ?? '—' }}</td>
                <td>
                    <span class="badge-gold">{{ $row->status ?? 'active' }}</span>
                </td>
                <td>
                    <a href="{{ route($slug.'.edit', $row->id) }}" class="btn-outline-gold btn-sm-gold"><i class="bi bi-pencil"></i></a>
                    <form method="POST" action="{{ route($slug.'.destroy', $row->id) }}" style="display:inline" onsubmit="return confirm('Delete?')">
                        @csrf @method('DELETE')
                        <button class="btn-outline-gold btn-sm-gold" style="color:#f87171;border-color:#f87171"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center py-4" style="color:rgba(212,175,55,0.4)">No records found.</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
    <div class="mt-3">{{ $rows->links() }}</div>
</div>
@endsection
