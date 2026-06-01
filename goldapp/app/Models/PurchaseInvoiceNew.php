<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PurchaseInvoiceNew extends Model {
    protected $table = 'purchase_invoices';
    protected $fillable = [
        'slno','invoice_no','invoice_date','grn_id','po_id','supplier_id','supplier_name',
        'supplier_bill_no','supplier_bill_date','taxable_amount','discount','sgst','cgst',
        'igst','tcs','other_charges','round_off','net_amount','paid_amount','balance_amount',
        'payment_mode','bank_account_id','cheque_no','cheque_date','status',
        'narration','branch_id','created_by'
    ];
    protected $casts = ['invoice_date' => 'date', 'supplier_bill_date' => 'date', 'cheque_date' => 'date'];

    public function supplier() { return $this->belongsTo(Supplier::class, 'supplier_id'); }
    public function grn() { return $this->belongsTo(Grn::class, 'grn_id'); }
    public function purchaseOrder() { return $this->belongsTo(PurchaseOrder::class, 'po_id'); }
    public function items() { return $this->hasMany(PurchaseInvoiceItemNew::class, 'invoice_id'); }
}
