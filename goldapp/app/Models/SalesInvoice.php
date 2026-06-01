<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SalesInvoice extends Model {
    protected $table = 'sales_invoice';
    protected $fillable = [
        'slno','invoice_no','invoice_date','customer_id','customer_name',
        'taxable_amount','discount','sgst','cgst','igst','other_charges',
        'round_off','net_amount','received_amount','payment_mode',
        'bank_account_id','cheque_no','cheque_date','narration','status',
        'branch_id','created_by'
    ];
    public function details() { return $this->hasMany(SalesInvoiceDetail::class, 'sales_invoice_id'); }
    public function customer() { return $this->belongsTo(Account::class, 'customer_id'); }
}
