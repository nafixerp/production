@extends('layouts.app')
@section('title','Budgets')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-bar-chart-steps me-2"></i>Budget vs Actual</h5>
    <a href="{{ route('budgets.create') }}" class="btn btn-sm btn-gold"><i class="bi bi-plus-lg me-1"></i>New Budget Entry</a>
</div>

<div class="card-dark mb-3">
    <form method="GET" class="row g-2">
        <div class="col-md-2"><input type="text" name="fy" class="form-control form-control-sm" value="{{ $fy }}" placeholder="2025-2026"></div>
        <div class="col-md-3">
            <select name="cost_centre_id" class="form-select form-select-sm">
                <option value="">All Cost Centres</option>
                @foreach($costCentres as $cc)
                <option value="{{ $cc->id }}" {{ $costCentreId == $cc->id ? 'selected' : '' }}>{{ $cc->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="month" class="form-select form-select-sm">
                <option value="">All Months</option>
                @foreach($months as $m)
                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ date('M', mktime(0,0,0,$m,1)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2"><button type="submit" class="btn btn-sm btn-outline-gold w-100">Filter</button></div>
    </form>
</div>

@if(session('success'))<div class="alert alert-success alert-sm">{{ session('success') }}</div>@endif

<div class="table-responsive">
<table class="table table-dark-erp">
    <thead>
        <tr><th>Account</th><th>Cost Centre</th><th>FY</th><th>Month</th><th class="text-end">Budgeted</th><th class="text-end">Actual</th><th class="text-end">Variance</th><th>%</th><th>Actions</th></tr>
    </thead>
    <tbody>
        @forelse($budgets as $b)
        @php $pct = $b->budgeted_amount > 0 ? round($b->actual_amount/$b->budgeted_amount*100) : 0; @endphp
        <tr>
            <td>{{ $b->account_name }}</td>
            <td>{{ $b->costCentre?->name ?? '-' }}</td>
            <td>{{ $b->financial_year }}</td>
            <td>{{ date('M', mktime(0,0,0,$b->month,1)) }}</td>
            <td class="text-end text-gold">{{ number_format($b->budgeted_amount,2) }}</td>
            <td class="text-end">{{ number_format($b->actual_amount,2) }}</td>
            <td class="text-end {{ $b->variance >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($b->variance,2) }}</td>
            <td>
                <div class="progress" style="height:8px;width:80px">
                    <div class="progress-bar {{ $pct > 100 ? 'bg-danger' : ($pct > 80 ? 'bg-warning' : 'bg-success') }}" style="width:{{ min($pct,100) }}%"></div>
                </div>
                <small class="{{ $pct > 100 ? 'text-danger' : 'text-muted' }}">{{ $pct }}%</small>
            </td>
            <td>
                <a href="{{ route('budgets.edit',$b->id) }}" class="btn btn-xs btn-outline-warning"><i class="bi bi-pencil"></i></a>
                <form method="POST" action="{{ route('budgets.destroy',$b->id) }}" class="d-inline" onsubmit="return confirm('Delete?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-xs btn-outline-danger"><i class="bi bi-trash"></i></button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="9" class="text-center text-muted">No budget entries.</td></tr>
        @endforelse
    </tbody>
</table>
</div>
<style>.btn-xs{padding:2px 6px;font-size:.7rem}</style>
@endsection
