@extends('layouts.app')
@section('title', isset($voucher) ? 'Edit Journal Voucher' : 'New Journal Voucher')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-journal-plus me-2"></i>{{ isset($voucher) ? 'Edit JV: '.$voucher->slno : 'New Journal Voucher' }}</h5>
    <a href="{{ route('journal-vouchers.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
</div>

@if($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form method="POST" action="{{ isset($voucher) ? route('journal-vouchers.update',$voucher->id) : route('journal-vouchers.store') }}" id="jvForm">
    @csrf @if(isset($voucher)) @method('PUT') @endif

    <div class="card-dark p-3 mb-3">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Voucher No. *</label>
                <input type="text" name="slno" class="form-control" value="{{ old('slno', $voucher->slno ?? $slno ?? '') }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Date *</label>
                <input type="date" name="jv_date" class="form-control" value="{{ old('jv_date', isset($voucher) ? $voucher->jv_date->format('Y-m-d') : today()->format('Y-m-d')) }}" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Type *</label>
                <select name="voucher_type" class="form-select" required>
                    @foreach(['JV'=>'Journal Voucher','CO'=>'Contra','DN'=>'Debit Note','CN'=>'Credit Note'] as $val=>$label)
                    <option value="{{ $val }}" {{ old('voucher_type', $voucher->voucher_type ?? 'JV') === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Branch</label>
                <select name="branch_id" class="form-select">
                    <option value="">-- Select --</option>
                    @foreach($branches as $b)
                    <option value="{{ $b->id }}" {{ old('branch_id', $voucher->branch_id ?? '') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">Narration</label>
                <textarea name="narration" class="form-control" rows="1">{{ old('narration', $voucher->narration ?? '') }}</textarea>
            </div>
        </div>
    </div>

    <!-- Journal Lines -->
    <div class="card-dark p-3 mb-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="text-gold mb-0">Debit / Credit Lines</h6>
            <div class="d-flex gap-3 align-items-center">
                <span class="text-small text-muted">Dr Sum: <span id="dr_total" class="text-danger fw-bold">0.00</span></span>
                <span class="text-small text-muted">Cr Sum: <span id="cr_total" class="text-success fw-bold">0.00</span></span>
                <span class="text-small text-muted">Balance: <span id="balance_chk" class="fw-bold">0.00</span></span>
                <button type="button" class="btn btn-sm btn-outline-gold" onclick="addLine()"><i class="bi bi-plus me-1"></i>Add Line</button>
            </div>
        </div>

        <div class="table-responsive">
        <table class="table table-dark-erp table-sm" id="linesTable">
            <thead>
                <tr><th width="35%">Account</th><th width="15%">Dr Amount</th><th width="15%">Cr Amount</th><th width="20%">Particular</th><th width="12%">Cost Centre</th><th width="3%"></th></tr>
            </thead>
            <tbody id="linesBody">
                @if(isset($voucher) && $voucher->lines->count() > 0)
                    @foreach($voucher->lines as $i => $line)
                    <tr class="line-row">
                        <td>
                            <select name="lines[{{ $i }}][account_id]" class="form-select form-select-sm account-sel" required>
                                <option value="">-- Account --</option>
                                @foreach($accounts as $a)
                                <option value="{{ $a->id }}" {{ $line->account_id == $a->id ? 'selected' : '' }}>{{ $a->code }} - {{ $a->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="number" step="0.0001" class="form-control form-control-sm dr-input" placeholder="0.00" value="{{ $line->amount < 0 ? abs($line->amount) : '' }}" oninput="calcTotals()"></td>
                        <td><input type="number" step="0.0001" class="form-control form-control-sm cr-input" placeholder="0.00" value="{{ $line->amount > 0 ? $line->amount : '' }}" oninput="calcTotals()"></td>
                        <td><input type="text" name="lines[{{ $i }}][particular]" class="form-control form-control-sm" value="{{ $line->particular }}"></td>
                        <td><input type="text" name="lines[{{ $i }}][cost_centre]" class="form-control form-control-sm" value="{{ $line->cost_centre }}"></td>
                        <td><button type="button" class="btn btn-xs btn-outline-danger" onclick="removeLine(this)"><i class="bi bi-x"></i></button></td>
                        <input type="hidden" name="lines[{{ $i }}][amount]" class="amount-hidden" value="{{ $line->amount }}">
                    </tr>
                    @endforeach
                @else
                <tr class="line-row">
                    <td><select name="lines[0][account_id]" class="form-select form-select-sm account-sel" required><option value="">-- Account --</option>@foreach($accounts as $a)<option value="{{ $a->id }}">{{ $a->code }} - {{ $a->name }}</option>@endforeach</select></td>
                    <td><input type="number" step="0.0001" class="form-control form-control-sm dr-input" placeholder="0.00" oninput="calcTotals()"></td>
                    <td><input type="number" step="0.0001" class="form-control form-control-sm cr-input" placeholder="0.00" oninput="calcTotals()"></td>
                    <td><input type="text" name="lines[0][particular]" class="form-control form-control-sm"></td>
                    <td><input type="text" name="lines[0][cost_centre]" class="form-control form-control-sm"></td>
                    <td></td>
                    <input type="hidden" name="lines[0][amount]" class="amount-hidden" value="">
                </tr>
                <tr class="line-row">
                    <td><select name="lines[1][account_id]" class="form-select form-select-sm account-sel" required><option value="">-- Account --</option>@foreach($accounts as $a)<option value="{{ $a->id }}">{{ $a->code }} - {{ $a->name }}</option>@endforeach</select></td>
                    <td><input type="number" step="0.0001" class="form-control form-control-sm dr-input" placeholder="0.00" oninput="calcTotals()"></td>
                    <td><input type="number" step="0.0001" class="form-control form-control-sm cr-input" placeholder="0.00" oninput="calcTotals()"></td>
                    <td><input type="text" name="lines[1][particular]" class="form-control form-control-sm"></td>
                    <td><input type="text" name="lines[1][cost_centre]" class="form-control form-control-sm"></td>
                    <td></td>
                    <input type="hidden" name="lines[1][amount]" class="amount-hidden" value="">
                </tr>
                @endif
            </tbody>
        </table>
        </div>
        <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Convention: Dr = negative amount, Cr = positive amount. Sum of all lines must = 0 for a balanced entry.</small>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-gold" id="submitBtn">{{ isset($voucher) ? 'Update' : 'Save as Draft' }}</button>
        <a href="{{ route('journal-vouchers.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>

<script>
let lineIdx = {{ isset($voucher) ? $voucher->lines->count() : 2 }};
const accountOptions = `@foreach($accounts as $a)<option value="{{ $a->id }}">{{ $a->code }} - {{ addslashes($a->name) }}</option>@endforeach`;

function addLine() {
    const tbody = document.getElementById('linesBody');
    const row = document.createElement('tr');
    row.className = 'line-row';
    row.innerHTML = `
        <td><select name="lines[${lineIdx}][account_id]" class="form-select form-select-sm account-sel" required><option value="">-- Account --</option>${accountOptions}</select></td>
        <td><input type="number" step="0.0001" class="form-control form-control-sm dr-input" placeholder="0.00" oninput="calcTotals()"></td>
        <td><input type="number" step="0.0001" class="form-control form-control-sm cr-input" placeholder="0.00" oninput="calcTotals()"></td>
        <td><input type="text" name="lines[${lineIdx}][particular]" class="form-control form-control-sm"></td>
        <td><input type="text" name="lines[${lineIdx}][cost_centre]" class="form-control form-control-sm"></td>
        <td><button type="button" class="btn btn-xs btn-outline-danger" onclick="removeLine(this)"><i class="bi bi-x"></i></button></td>
        <input type="hidden" name="lines[${lineIdx}][amount]" class="amount-hidden" value="">
    `;
    tbody.appendChild(row);
    lineIdx++;
}

function removeLine(btn) {
    const rows = document.querySelectorAll('.line-row');
    if (rows.length <= 2) { alert('Minimum 2 lines required.'); return; }
    btn.closest('tr').remove();
    reindexLines();
    calcTotals();
}

function reindexLines() {
    document.querySelectorAll('.line-row').forEach((row, i) => {
        row.querySelectorAll('[name]').forEach(el => {
            el.name = el.name.replace(/lines\[\d+\]/, `lines[${i}]`);
        });
    });
    lineIdx = document.querySelectorAll('.line-row').length;
}

function calcTotals() {
    // Update hidden amount fields
    document.querySelectorAll('.line-row').forEach(row => {
        const dr = parseFloat(row.querySelector('.dr-input')?.value) || 0;
        const cr = parseFloat(row.querySelector('.cr-input')?.value) || 0;
        const hidden = row.querySelector('.amount-hidden');
        if (hidden) {
            // Dr = negative, Cr = positive
            if (dr > 0) hidden.value = -dr;
            else if (cr > 0) hidden.value = cr;
            else hidden.value = '';
        }
    });

    let totalDr = 0, totalCr = 0;
    document.querySelectorAll('.dr-input').forEach(i => totalDr += parseFloat(i.value) || 0);
    document.querySelectorAll('.cr-input').forEach(i => totalCr += parseFloat(i.value) || 0);
    const balance = totalCr - totalDr;

    document.getElementById('dr_total').textContent = totalDr.toFixed(2);
    document.getElementById('cr_total').textContent = totalCr.toFixed(2);
    const balEl = document.getElementById('balance_chk');
    balEl.textContent = balance.toFixed(2);
    balEl.className = 'fw-bold ' + (Math.abs(balance) < 0.01 ? 'text-success' : 'text-danger');
}

document.getElementById('jvForm').addEventListener('submit', function(e) {
    calcTotals();
    const balance = parseFloat(document.getElementById('balance_chk').textContent);
    if (Math.abs(balance) > 0.01) {
        e.preventDefault();
        alert('Journal entry is NOT balanced. Difference: ' + balance.toFixed(4) + '\nTotal Dr must equal Total Cr.');
    }
});
</script>
<style>.btn-xs{padding:2px 6px;font-size:.7rem}.text-small{font-size:.78rem}</style>
@endsection
