@extends('layouts.app')
@section('title', 'Suppliers')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="text-gold mb-0"><i class="bi bi-people-fill me-2"></i>Suppliers</h4>
    <a href="{{ route('suppliers.create') }}" class="btn btn-gold btn-sm"><i class="bi bi-plus-lg me-1"></i>New Supplier</a>
</div>

<div class="card-erp mb-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-5">
            <input type="text" name="q" value="{{ $q ?? '' }}" class="form-control form-control-sm form-erp" placeholder="Search name / code / GST...">
        </div>
        <div class="col-auto">
            <button class="btn btn-gold btn-sm"><i class="bi bi-search me-1"></i>Search</button>
            <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary btn-sm ms-1">Clear</a>
        </div>
    </form>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="card-erp">
    <div class="table-responsive">
        <table class="table table-erp table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Contact Person</th>
                    <th>Phone</th>
                    <th>GST No</th>
                    <th>Credit Limit</th>
                    <th>Rating</th>
                    <th>AP Balance</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($suppliers as $s)
                <tr>
                    <td>{{ $s->id }}</td>
                    <td><code class="text-gold-light">{{ $s->code }}</code></td>
                    <td><strong>{{ $s->name }}</strong></td>
                    <td>{{ $s->contact_person }}</td>
                    <td>{{ $s->phone }}</td>
                    <td>{{ $s->gst_no }}</td>
                    <td class="text-end">₹{{ number_format($s->credit_limit, 2) }}</td>
                    <td>
                        @php $r = (float)$s->rating; @endphp
                        <span class="{{ $r >= 4 ? 'text-success' : ($r >= 2.5 ? 'text-warning' : 'text-danger') }}">
                            {{ $r > 0 ? $r : '—' }}
                        </span>
                    </td>
                    <td class="text-end">
                        @php $bal = $balances[$s->id] ?? 0; @endphp
                        <span class="{{ $bal > 0 ? 'text-warning' : 'text-muted' }}">₹{{ number_format($bal, 2) }}</span>
                    </td>
                    <td>
                        <span class="badge bg-{{ $s->status === 'active' ? 'success' : ($s->status === 'blacklisted' ? 'danger' : 'secondary') }}">
                            {{ ucfirst($s->status) }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('suppliers.show', $s) }}" class="btn btn-xs btn-outline-info me-1"><i class="bi bi-eye"></i></a>
                        <a href="{{ route('suppliers.edit', $s) }}" class="btn btn-xs btn-outline-warning me-1"><i class="bi bi-pencil"></i></a>
                        <form method="POST" action="{{ route('suppliers.destroy', $s) }}" class="d-inline" onsubmit="return confirm('Delete supplier?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-xs btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="11" class="text-center text-muted py-4">No suppliers found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3">{{ $suppliers->links() }}</div>
</div>
@endsection
