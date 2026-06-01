@extends('layouts.app')
@section('title','Reconcile Bank Statement')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-arrow-left-right me-2"></i>Reconcile: {{ $recon->bankAccount?->name }} — {{ $recon->statement_date->format('d M Y') }}</h5>
    <a href="{{ route('bank-reconciliation.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
</div>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

<div class="row g-3 mb-3">
    <div class="col-md-3"><div class="stat-mini"><div class="stat-val text-gold">₹{{ number_format($recon->statement_closing_balance,2) }}</div><div class="stat-lbl">Statement Balance</div></div></div>
    <div class="col-md-3"><div class="stat-mini"><div class="stat-val text-info">₹{{ number_format($recon->book_balance,2) }}</div><div class="stat-lbl">Book Balance</div></div></div>
    <div class="col-md-3"><div class="stat-mini"><div class="stat-val {{ abs($recon->difference) > 0.01 ? 'text-danger' : 'text-success' }}">₹{{ number_format($recon->difference,2) }}</div><div class="stat-lbl">Difference</div></div></div>
    <div class="col-md-3"><div class="stat-mini"><div class="stat-val {{ $recon->status==='reconciled'?'text-success':'text-warning' }}">{{ ucfirst(str_replace('_',' ',$recon->status)) }}</div><div class="stat-lbl">Status</div></div></div>
</div>

<form method="POST" action="{{ route('bank-reconciliation.update',$recon->id) }}">
    @csrf @method('PUT')

<div class="row g-3">
    <div class="col-md-7">
        <div class="card-dark p-3">
            <h6 class="text-gold mb-3">Book Entries (Daybook)</h6>
            <div class="table-responsive">
            <table class="table table-dark-erp table-sm">
                <thead><tr><th><input type="checkbox" id="checkAll"></th><th>Date</th><th>Description</th><th class="text-end">Dr</th><th class="text-end">Cr</th><th>Matched</th></tr></thead>
                <tbody>
                    @foreach($bookLines as $line)
                    <tr class="{{ $line->is_matched ? 'matched-row' : '' }}">
                        <td><input type="checkbox" name="match_ids[]" value="{{ $line->id }}" {{ $line->is_matched ? 'checked' : '' }}></td>
                        <td>{{ $line->tdate }}</td>
                        <td>{{ $line->description }}</td>
                        <td class="text-end text-danger">{{ $line->dr_cr==='dr' ? number_format($line->amount,2) : '-' }}</td>
                        <td class="text-end text-success">{{ $line->dr_cr==='cr' ? number_format($line->amount,2) : '-' }}</td>
                        <td>{{ $line->is_matched ? '<span class="text-success"><i class="bi bi-check-circle-fill"></i></span>' : '<span class="text-muted">—</span>' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card-dark p-3">
            <h6 class="text-gold mb-3">Add Statement Lines</h6>
            <div id="stmtLines">
                <div class="stmt-line row g-2 mb-2">
                    <div class="col-3"><input type="date" name="statement_lines[0][tdate]" class="form-control form-control-sm"></div>
                    <div class="col-5"><input type="text" name="statement_lines[0][description]" class="form-control form-control-sm" placeholder="Description"></div>
                    <div class="col-2"><input type="number" step="0.01" name="statement_lines[0][amount]" class="form-control form-control-sm" placeholder="Amount"></div>
                    <div class="col-2">
                        <select name="statement_lines[0][dr_cr]" class="form-select form-select-sm">
                            <option value="cr">Cr</option><option value="dr">Dr</option>
                        </select>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-xs btn-outline-gold" onclick="addStmtLine()"><i class="bi bi-plus me-1"></i>Add Line</button>
        </div>
    </div>
</div>

<div class="mt-3">
    <button type="submit" class="btn btn-gold">Save & Match</button>
</div>
</form>

<script>
let stmtIdx = 1;
document.getElementById('checkAll')?.addEventListener('change', function() {
    document.querySelectorAll('[name="match_ids[]"]').forEach(cb => cb.checked = this.checked);
});
function addStmtLine() {
    const div = document.getElementById('stmtLines');
    div.insertAdjacentHTML('beforeend', `
        <div class="stmt-line row g-2 mb-2">
            <div class="col-3"><input type="date" name="statement_lines[${stmtIdx}][tdate]" class="form-control form-control-sm"></div>
            <div class="col-5"><input type="text" name="statement_lines[${stmtIdx}][description]" class="form-control form-control-sm" placeholder="Description"></div>
            <div class="col-2"><input type="number" step="0.01" name="statement_lines[${stmtIdx}][amount]" class="form-control form-control-sm" placeholder="Amount"></div>
            <div class="col-2"><select name="statement_lines[${stmtIdx}][dr_cr]" class="form-select form-select-sm"><option value="cr">Cr</option><option value="dr">Dr</option></select></div>
        </div>`);
    stmtIdx++;
}
</script>
<style>
.matched-row{background:rgba(39,174,96,.08)}
.stat-mini{text-align:center;padding:10px;background:#13132a;border:1px solid var(--border-gold);border-radius:6px}
.stat-val{font-size:1rem;font-weight:700}.stat-lbl{font-size:.65rem;color:rgba(212,175,55,.6);letter-spacing:1px}
.btn-xs{padding:2px 6px;font-size:.7rem}
</style>
@endsection
