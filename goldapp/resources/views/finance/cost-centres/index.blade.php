@extends('layouts.app')
@section('title','Cost Centres')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-diagram-2 me-2"></i>Cost Centres</h5>
    <a href="{{ route('cost-centres.create') }}" class="btn btn-sm btn-gold"><i class="bi bi-plus-lg me-1"></i>New</a>
</div>
@if(session('success'))<div class="alert alert-success alert-sm">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger alert-sm">{{ session('error') }}</div>@endif

<div class="table-responsive">
<table class="table table-dark-erp table-hover">
    <thead><tr><th>Code</th><th>Name</th><th>Parent</th><th>Budget</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody>
        @forelse($costCentres as $cc)
        <tr>
            <td>{{ $cc->code }}</td>
            <td>{{ $cc->parent_id ? '↳ ' : '' }}{{ $cc->name }}</td>
            <td>{{ $cc->parent?->name ?? '-' }}</td>
            <td class="text-end">₹{{ number_format($cc->budget,2) }}</td>
            <td><span class="badge {{ $cc->status ? 'bg-success' : 'bg-danger' }}">{{ $cc->status ? 'Active' : 'Inactive' }}</span></td>
            <td>
                <a href="{{ route('cost-centres.edit',$cc->id) }}" class="btn btn-xs btn-outline-warning"><i class="bi bi-pencil"></i></a>
                <form method="POST" action="{{ route('cost-centres.destroy',$cc->id) }}" class="d-inline" onsubmit="return confirm('Delete?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-xs btn-outline-danger"><i class="bi bi-trash"></i></button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center text-muted">No cost centres.</td></tr>
        @endforelse
    </tbody>
</table>
</div>
{{ $costCentres->links() }}
<style>.btn-xs{padding:2px 6px;font-size:.7rem}</style>
@endsection
