<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

/**
 * Module-4 Sales Invoice — uses table 'sales_invoices' (full-schema version).
 * Named SalesInvoiceModel to avoid clash with legacy SalesInvoice.
 */
class SalesInvoiceModel extends Model {
    protected $table = 'sales_invoices';
    protected $fillable = [
        'slno','invoice_no','invoice_date','so_id','customer_id','customer_name',
        'billing_address','shipping_address','channel',
        'taxable_amount','discount','sgst','cgst','igst','tcs',
        'other_charges','round_off','net_amount','received_amount','balance_amount',
        'payment_mode','bank_account_id','cheque_no','utr_no','irn_no','eway_bill_no',
        'status','narration','branch_id','created_by'
    ];
    protected $casts = ['invoice_date'=>'date'];

    public function items()       { return $this->hasMany(SalesInvoiceItem::class,'invoice_id'); }
    public function customer()    { return $this->belongsTo(Account::class,'customer_id'); }
    public function salesOrder()  { return $this->belongsTo(SalesOrder::class,'so_id'); }
    public function bankAccount() { return $this->belongsTo(Account::class,'bank_account_id'); }
    public function daybookEntries() { return $this->hasMany(Daybook::class,'slno','slno'); }
}
