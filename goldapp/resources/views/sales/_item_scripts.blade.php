<script>
let siRowIdx = document.querySelectorAll('.sales-item-row').length;

function calcSiRow(row) {
  const qty      = parseFloat(row.querySelector('.si-qty')?.value) || 0;
  const rate     = parseFloat(row.querySelector('.si-rate')?.value) || 0;
  const discPct  = parseFloat(row.querySelector('.si-discpct')?.value) || 0;
  const sgstPct  = parseFloat(row.querySelector('.si-sgstpct')?.value) || 0;
  const cgstPct  = parseFloat(row.querySelector('.si-cgstpct')?.value) || 0;
  const igstPct  = parseFloat(row.querySelector('.si-igstpct')?.value) || 0;

  const amount   = qty * rate;
  const discAmt  = amount * discPct / 100;
  const taxBase  = amount - discAmt;
  const sgst     = taxBase * sgstPct / 100;
  const cgst     = taxBase * cgstPct / 100;
  const igst     = taxBase * igstPct / 100;
  const net      = taxBase + sgst + cgst + igst;

  const set = (sel, val) => { const el = row.querySelector(sel); if (el) el.value = val.toFixed(2); };
  set('.si-amount', amount);
  set('.si-discamt', discAmt);
  set('.si-sgst', sgst);
  set('.si-cgst', cgst);
  set('.si-igst', igst);
  set('.si-net', net);

  updateTotals();
}

function updateTotals() {
  let taxable=0, disc=0, sgst=0, cgst=0, igst=0;
  document.querySelectorAll('.sales-item-row').forEach(row => {
    taxable += parseFloat(row.querySelector('.si-amount')?.value)||0;
    disc    += parseFloat(row.querySelector('.si-discamt')?.value)||0;
    sgst    += parseFloat(row.querySelector('.si-sgst')?.value)||0;
    cgst    += parseFloat(row.querySelector('.si-cgst')?.value)||0;
    igst    += parseFloat(row.querySelector('.si-igst')?.value)||0;
  });
  const net = taxable - disc + sgst + cgst + igst;
  const set = (id, v) => { const el = document.getElementById(id); if (el) el.textContent = v.toFixed(2); };
  set('sumTaxable', taxable); set('sumDiscount', disc);
  set('sumSgst', sgst); set('sumCgst', cgst); set('sumIgst', igst);
  set('sumNet', net);
}

function bindSiRow(row) {
  row.querySelectorAll('.si-qty,.si-rate,.si-discpct,.si-sgstpct,.si-cgstpct,.si-igstpct').forEach(el => {
    el.addEventListener('input', () => calcSiRow(row));
  });
  row.querySelector('.fg-sel')?.addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    const r = (sel, val) => { const el = row.querySelector(sel); if (el && val !== undefined) el.value = val; };
    r('.si-rate', opt.dataset.sell);
    r('.si-unit', opt.dataset.unit);
    r('[name*="[hsn_code]"]', opt.dataset.hsn);
    r('.si-sgstpct', opt.dataset.sgst);
    r('.si-cgstpct', opt.dataset.cgst);
    r('.si-igstpct', opt.dataset.igst);
    calcSiRow(row);
  });
  row.querySelector('.remove-si-row')?.addEventListener('click', () => { row.remove(); updateTotals(); });
}

document.querySelectorAll('.sales-item-row').forEach(bindSiRow);
updateTotals();

document.getElementById('addItemRow')?.addEventListener('click', function() {
  const tbody = document.getElementById('salesItemsBody');
  const tmpl  = tbody.querySelector('.sales-item-row');
  if (!tmpl) return;
  const newRow = tmpl.cloneNode(true);
  newRow.querySelectorAll('input').forEach(i => i.value = i.readOnly ? '0' : '');
  newRow.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
  newRow.querySelectorAll('[name]').forEach(el => {
    el.name = el.name.replace(/\[\d+\]/, '[' + siRowIdx + ']');
  });
  // set default tax pcts
  const sgstPct = newRow.querySelector('.si-sgstpct'); if (sgstPct) sgstPct.value = '9';
  const cgstPct = newRow.querySelector('.si-cgstpct'); if (cgstPct) cgstPct.value = '9';
  siRowIdx++;
  tbody.appendChild(newRow);
  bindSiRow(newRow);
});

function fillCustName(sel) {
  const opt = sel.options[sel.selectedIndex];
  const el = document.getElementById('custName');
  if (el && opt.dataset.name) el.value = opt.dataset.name;
}
</script>
