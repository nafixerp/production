<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SalesInvoiceDetail extends Model {
    protected $table = 'sales_invoice_detail';
    protected $fillable = [
        'sales_invoice_id','slno','item_id','item_code','item_name','hsn_code',
        'unit','qty','rate','amount','discount',
        'sgst_pct','cgst_pct','igst_pct','sgst','cgst','igst'
    ];
    public function invoice() { return $this->belongsTo(SalesInvoice::class, 'sales_invoice_id'); }
}
