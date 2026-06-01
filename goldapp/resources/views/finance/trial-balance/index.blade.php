@extends('layouts.app')
@section('title','Trial Balance')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-balance-scale me-2"></i>Trial Balance</h5>
    <button onclick="window.print()" class="btn btn-sm btn-outline-secondary"><i class="bi bi-printer me-1"></i>Print</button>
</div>

<div class="card-dark mb-3">
    <form method="GET" class="row g-2">
        <div class="col-md-3"><label class="form-label text-small">From Date</label><input type="date" name="from" class="form-control form-control-sm" value="{{ $fromDate }}"></div>
        <div class="col-md-3"><label class="form-label text-small">To Date</label><input type="date" name="to" class="form-control form-control-sm" value="{{ $toDate }}"></div>
        <div class="col-md-2 d-flex align-items-end"><button type="submit" class="btn btn-sm btn-outline-gold w-100">Generate</button></div>
    </form>
</div>

@if(!$balanced)
<div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Trial Balance is NOT balanced! Dr Total ≠ Cr Total. Difference: ₹{{ number_format(abs($totalDr - $totalCr),2) }}</div>
@else
<div class="alert alert-success"><i class="bi bi-check-circle-fill me-2"></i>Trial Balance is balanced.</div>
@endif

<div class="table-responsive">
<table class="table table-dark-erp">
    <thead>
        <tr>
            <th>Code</th><th>Account Name</th><th>Group</th>
            <th class="text-end">Opening</th>
            <th class="text-end">Period Dr</th><th class="text-end">Period Cr</th>
            <th class="text-end">Closing Dr</th><th class="text-end">Closing Cr</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $row)
        <tr>
            <td><span class="badge bg-secondary">{{ $row['account']->code }}</span></td>
            <td>{{ $row['account']->name }}</td>
            <td><span class="badge badge-group-{{ $row['account']->account_group }}" style="font-size:.65rem">{{ ucfirst($row['account']->account_group) }}</span></td>
            <td class="text-end text-small {{ $row['opening'] < 0 ? 'text-danger' : 'text-success' }}">{{ $row['opening'] != 0 ? number_format(abs($row['opening']),2).' '.($row['opening'] < 0 ? 'Dr' : 'Cr') : '-' }}</td>
            <td class="text-end text-danger">{{ $row['period_dr'] > 0 ? number_format($row['period_dr'],2) : '-' }}</td>
            <td class="text-end text-success">{{ $row['period_cr'] > 0 ? number_format($row['period_cr'],2) : '-' }}</td>
            <td class="text-end text-danger fw-bold">{{ $row['closing_dr'] > 0 ? number_format($row['closing_dr'],2) : '-' }}</td>
            <td class="text-end text-success fw-bold">{{ $row['closing_cr'] > 0 ? number_format($row['closing_cr'],2) : '-' }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="fw-bold" style="background:rgba(212,175,55,.1)">
            <td colspan="6" class="text-end text-gold">TOTALS</td>
            <td class="text-end text-danger">{{ number_format($totalDr,2) }}</td>
            <td class="text-end text-success">{{ number_format($totalCr,2) }}</td>
        </tr>
    </tfoot>
</table>
</div>

<style>
.badge-group-asset{background:#3498db;font-size:.65rem}.badge-group-liability{background:#e74c3c;font-size:.65rem}
.badge-group-equity{background:#9b59b6;font-size:.65rem}.badge-group-income{background:#27ae60;font-size:.65rem}
.badge-group-expense{background:#e67e22;font-size:.65rem}.badge-group-tax{background:#95a5a6;font-size:.65rem}
.text-small{font-size:.78rem}
</style>
@endsection
