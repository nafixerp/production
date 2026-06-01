@extends('layouts.app')
@section('title','Journal Vouchers')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-journal-bookmark me-2"></i>Journal Vouchers</h5>
    <a href="{{ route('journal-vouchers.create') }}" class="btn btn-sm btn-gold"><i class="bi bi-plus-lg me-1"></i>New JV</a>
</div>

<div class="card-dark mb-3">
    <form method="GET" class="row g-2">
        <div class="col-md-2">
            <select name="status" class="form-select form-select-sm">
                <option value="">All Status</option>
                @foreach(['draft','approved','posted'] as $s)
                <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="voucher_type" class="form-select form-select-sm">
                <option value="">All Types</option>
                @foreach(['JV','CO','DN','CN'] as $t)
                <option value="{{ $t }}" {{ request('voucher_type')===$t?'selected':'' }}>{{ $t }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2"><input type="date" name="from" class="form-control form-control-sm" value="{{ request('from') }}" placeholder="From"></div>
        <div class="col-md-2"><input type="date" name="to" class="form-control form-control-sm" value="{{ request('to') }}" placeholder="To"></div>
        <div class="col-md-2"><button type="submit" class="btn btn-sm btn-outline-gold w-100">Filter</button></div>
    </form>
</div>

@if(session('success'))<div class="alert alert-success alert-sm">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger alert-sm">{{ session('error') }}</div>@endif

<div class="table-responsive">
<table class="table table-dark-erp table-hover">
    <thead>
        <tr><th>Ref</th><th>Date</th><th>Type</th><th>Narration</th><th class="text-end">Total Dr</th><th class="text-end">Total Cr</th><th>Status</th><th>Actions</th></tr>
    </thead>
    <tbody>
        @forelse($vouchers as $v)
        <tr>
            <td><a href="{{ route('journal-vouchers.show',$v->id) }}" class="text-gold-link">{{ $v->slno }}</a></td>
            <td>{{ $v->jv_date->format('d M Y') }}</td>
            <td><span class="badge bg-secondary">{{ $v->voucher_type }}</span></td>
            <td>{{ Str::limit($v->narration,50) }}</td>
            <td class="text-end text-danger">{{ number_format($v->total_debit,2) }}</td>
            <td class="text-end text-success">{{ number_format($v->total_credit,2) }}</td>
            <td>
                <span class="badge bg-{{ $v->status==='posted'?'success':($v->status==='approved'?'info':'warning') }}">{{ ucfirst($v->status) }}</span>
            </td>
            <td>
                @if($v->status !== 'posted')
                    <a href="{{ route('journal-vouchers.edit',$v->id) }}" class="btn btn-xs btn-outline-warning"><i class="bi bi-pencil"></i></a>
                @endif
                @if($v->status === 'draft')
                    <form method="POST" action="/journal-vouchers/{{ $v->id }}/approve" class="d-inline">
                        @csrf <button class="btn btn-xs btn-outline-info" title="Approve"><i class="bi bi-check-circle"></i></button>
                    </form>
                @endif
                @if($v->status !== 'posted')
                    <form method="POST" action="{{ route('journal-vouchers.post',$v->id) }}" class="d-inline" onsubmit="return confirm('Post to daybook?')">
                        @csrf <button class="btn btn-xs btn-outline-success" title="Post"><i class="bi bi-send"></i></button>
                    </form>
                @endif
                @if($v->status !== 'posted')
                <form method="POST" action="{{ route('journal-vouchers.destroy',$v->id) }}" class="d-inline" onsubmit="return confirm('Delete?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-xs btn-outline-danger"><i class="bi bi-trash"></i></button>
                </form>
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="8" class="text-center text-muted">No vouchers found.</td></tr>
        @endforelse
    </tbody>
</table>
</div>
{{ $vouchers->links() }}
<style>.text-gold-link{color:var(--gold);text-decoration:none}.btn-xs{padding:2px 6px;font-size:.7rem}</style>
@endsection
