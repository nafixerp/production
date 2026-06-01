<div class="card bg-dark border-secondary p-3 mb-3">
  <div class="d-flex justify-content-between align-items-center mb-2">
    <h6 class="text-warning mb-0">Items</h6>
    <button type="button" class="btn btn-sm btn-outline-warning" id="addItemRow"><i class="bi bi-plus-lg me-1"></i>Add Item</button>
  </div>
  <div class="table-responsive">
    <table class="table table-dark table-sm" id="salesItemsTable">
      <thead class="text-warning">
        <tr>
          <th style="min-width:200px">FG Item</th>
          <th>HSN</th><th>Unit</th>
          <th class="text-end">Qty</th><th class="text-end">Rate</th>
          <th class="text-end">Amount</th><th class="text-end">Disc%</th>
          <th class="text-end">Disc Amt</th>
          <th class="text-end">SGST%</th><th class="text-end">SGST</th>
          <th class="text-end">CGST%</th><th class="text-end">CGST</th>
          <th class="text-end">IGST%</th><th class="text-end">IGST</th>
          <th class="text-end">Net Amt</th>
          <th></th>
        </tr>
      </thead>
      <tbody id="salesItemsBody">
        @foreach($items ?? [[]] as $i => $item)
        <tr class="sales-item-row">
          <td>
            <select name="items[{{ $i }}][fg_id]" class="form-select form-select-sm bg-dark text-light border-secondary fg-sel">
              <option value="">-- FG --</option>
              @foreach($fgList as $fg)
              <option value="{{ $fg->id }}" data-hsn="{{ $fg->hsn_code }}" data-sell="{{ $fg->selling_price }}" data-unit="{{ $fg->unit?->name }}" data-sgst="9" data-cgst="9" data-igst="0" {{ ($item['fg_id']??'')==$fg->id?'selected':'' }}>{{ $fg->code }} – {{ $fg->name }}</option>
              @endforeach
            </select>
          </td>
          <td><input type="text" name="items[{{ $i }}][hsn_code]" class="form-control form-control-sm bg-dark text-light border-secondary" style="width:80px" value="{{ $item['hsn_code']??'' }}"></td>
          <td><input type="text" name="items[{{ $i }}][unit]" class="form-control form-control-sm bg-dark text-light border-secondary si-unit" style="width:60px" value="{{ $item['unit']??'' }}"></td>
          <td><input type="number" step="0.0001" name="items[{{ $i }}][qty]" class="form-control form-control-sm bg-dark text-light border-secondary si-qty text-end" style="width:80px" value="{{ $item['qty']??'' }}"></td>
          <td><input type="number" step="0.0001" name="items[{{ $i }}][rate]" class="form-control form-control-sm bg-dark text-light border-secondary si-rate text-end" style="width:90px" value="{{ $item['rate']??'' }}"></td>
          <td><input type="number" step="0.01" name="items[{{ $i }}][amount]" class="form-control form-control-sm bg-dark text-light border-secondary si-amount text-end" style="width:90px" value="{{ $item['amount']??'' }}" readonly></td>
          <td><input type="number" step="0.01" name="items[{{ $i }}][discount_pct]" class="form-control form-control-sm bg-dark text-light border-secondary si-discpct text-end" style="width:60px" value="{{ $item['discount_pct']??0 }}"></td>
          <td><input type="number" step="0.01" name="items[{{ $i }}][discount_amount]" class="form-control form-control-sm bg-dark text-light border-secondary si-discamt text-end" style="width:80px" value="{{ $item['discount_amount']??0 }}" readonly></td>
          <td><input type="number" step="0.01" name="items[{{ $i }}][sgst_pct]" class="form-control form-control-sm bg-dark text-light border-secondary si-sgstpct text-end" style="width:55px" value="{{ $item['sgst_pct']??9 }}"></td>
          <td><input type="number" step="0.01" name="items[{{ $i }}][sgst]" class="form-control form-control-sm bg-dark text-light border-secondary si-sgst text-end" style="width:75px" value="{{ $item['sgst']??0 }}" readonly></td>
          <td><input type="number" step="0.01" name="items[{{ $i }}][cgst_pct]" class="form-control form-control-sm bg-dark text-light border-secondary si-cgstpct text-end" style="width:55px" value="{{ $item['cgst_pct']??9 }}"></td>
          <td><input type="number" step="0.01" name="items[{{ $i }}][cgst]" class="form-control form-control-sm bg-dark text-light border-secondary si-cgst text-end" style="width:75px" value="{{ $item['cgst']??0 }}" readonly></td>
          <td><input type="number" step="0.01" name="items[{{ $i }}][igst_pct]" class="form-control form-control-sm bg-dark text-light border-secondary si-igstpct text-end" style="width:55px" value="{{ $item['igst_pct']??0 }}"></td>
          <td><input type="number" step="0.01" name="items[{{ $i }}][igst]" class="form-control form-control-sm bg-dark text-light border-secondary si-igst text-end" style="width:75px" value="{{ $item['igst']??0 }}" readonly></td>
          <td><input type="number" step="0.01" name="items[{{ $i }}][net_amount]" class="form-control form-control-sm bg-dark text-light border-secondary si-net text-end" style="width:90px" value="{{ $item['net_amount']??0 }}" readonly></td>
          <td><button type="button" class="btn btn-xs btn-outline-danger remove-si-row"><i class="bi bi-x"></i></button></td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
