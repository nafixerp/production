@extends('layouts.app')
@section('title','Trial Balance')
@section('page-title','Trial Balance')

@section('content')
<div class="card-gold">
    <div class="card-header-gold"><h5>◆ Trial Balance</h5></div>

    <form method="GET" class="mb-3 d-flex gap-2 align-items-end">
        <div><label class="form-label">As of Date</label><input type="date" name="as_of" value="{{ $asOfDate }}" class="form-control" style="width:155px"></div>
        <button class="btn-outline-gold">Generate</button>
    </form>

    <div class="table-responsive">
    <table class="table-gold w-100">
        <thead>
            <tr>
                <th>Account Code</th>
                <th>Account Name</th>
                <th>Type</th>
                <th class="text-end text-debit">Debit</th>
                <th class="text-end text-credit">Credit</th>
            </tr>
        </thead>
        <tbody>
        @forelse($rows as $row)
        <tr>
            <td>{{ $row['account']->code }}</td>
            <td>{{ $row['account']->name }}</td>
            <td>{{ $row['account']->atype }}</td>
            <td class="text-end text-debit">{{ $row['debit'] > 0 ? number_format($row['debit'], 2) : '' }}</td>
            <td class="text-end text-credit">{{ $row['credit'] > 0 ? number_format($row['credit'], 2) : '' }}</td>
        </tr>
        @empty
        <tr><td colspan="5" class="text-center" style="color:#666;padding:20px">No data.</td></tr>
        @endforelse
        </tbody>
        <tfoot>
            <tr style="border-top:2px solid var(--gold)">
                <td colspan="3" class="text-gold form-label">TOTALS</td>
                <td class="text-end text-debit"><strong>{{ number_format($totalDebit, 2) }}</strong></td>
                <td class="text-end text-credit"><strong>{{ number_format($totalCredit, 2) }}</strong></td>
            </tr>
            @if(abs($totalDebit - $totalCredit) > 0.01)
            <tr>
                <td colspan="5" style="color:#f87171;font-size:0.8rem;padding:8px 12px">
                    ⚠ Trial balance difference: {{ number_format(abs($totalDebit - $totalCredit), 2) }}
                </td>
            </tr>
            @else
            <tr>
                <td colspan="5" style="color:#4ade80;font-size:0.8rem;padding:8px 12px">
                    ✓ Trial balance is balanced
                </td>
            </tr>
            @endif
        </tfoot>
    </table>
    </div>
</div>
@endsection
