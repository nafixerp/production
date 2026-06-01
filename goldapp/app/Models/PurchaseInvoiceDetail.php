<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PurchaseInvoiceDetail extends Model {
    protected $table = 'purchase_invoice_detail';
    protected $fillable = [
        'purchase_invoice_id','slno','item_id','item_code','item_name','hsn_code',
        'unit','qty','rate','amount','discount',
        'sgst_pct','cgst_pct','igst_pct','sgst','cgst','igst'
    ];
    public function invoice() { return $this->belongsTo(PurchaseInvoice::class, 'purchase_invoice_id'); }
}
