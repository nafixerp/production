<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PurchaseInvoice extends Model {
    protected $table = 'purchase_invoice';
    protected $fillable = [
        'slno','doc_no','invoice_date','supplier_id','supplier_name','supplier_bill_no',
        'taxable_amount','discount','sgst','cgst','igst','tcs','other_charges',
        'round_off','net_amount','paid_amount','payment_mode',
        'bank_account_id','cheque_no','cheque_date','narration','status',
        'branch_id','created_by'
    ];
    public function details() { return $this->hasMany(PurchaseInvoiceDetail::class, 'purchase_invoice_id'); }
    public function supplier() { return $this->belongsTo(Account::class, 'supplier_id'); }
}
