@extends('layouts.app')
@section('title','Bank Reconciliation')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-arrow-left-right me-2"></i>Bank Reconciliation</h5>
    <a href="{{ route('bank-reconciliation.create') }}" class="btn btn-sm btn-gold"><i class="bi bi-plus-lg me-1"></i>New Reconciliation</a>
</div>
@if(session('success'))<div class="alert alert-success alert-sm">{{ session('success') }}</div>@endif

<div class="table-responsive">
<table class="table table-dark-erp table-hover">
    <thead>
        <tr><th>Bank Account</th><th>Statement Date</th><th>Statement Balance</th><th>Book Balance</th><th>Difference</th><th>Status</th><th>Actions</th></tr>
    </thead>
    <tbody>
        @forelse($reconciliations as $r)
        <tr>
            <td>{{ $r->bankAccount?->name ?? '-' }}</td>
            <td>{{ $r->statement_date->format('d M Y') }}</td>
            <td class="text-end">₹{{ number_format($r->statement_closing_balance,2) }}</td>
            <td class="text-end">₹{{ number_format($r->book_balance,2) }}</td>
            <td class="text-end {{ abs($r->difference) > 0.01 ? 'text-danger' : 'text-success' }}">{{ number_format($r->difference,2) }}</td>
            <td><span class="badge {{ $r->status==='reconciled'?'bg-success':'bg-warning' }}">{{ ucfirst(str_replace('_',' ',$r->status)) }}</span></td>
            <td>
                <a href="{{ route('bank-reconciliation.show',$r->id) }}" class="btn btn-xs btn-outline-info"><i class="bi bi-eye"></i></a>
                <form method="POST" action="{{ route('bank-reconciliation.destroy',$r->id) }}" class="d-inline" onsubmit="return confirm('Delete?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-xs btn-outline-danger"><i class="bi bi-trash"></i></button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center text-muted">No reconciliations.</td></tr>
        @endforelse
    </tbody>
</table>
</div>
{{ $reconciliations->links() }}
<style>.btn-xs{padding:2px 6px;font-size:.7rem}</style>
@endsection
