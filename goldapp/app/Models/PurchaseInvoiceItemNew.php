<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PurchaseInvoiceItemNew extends Model {
    protected $table = 'purchase_invoice_items';
    protected $fillable = [
        'invoice_id','item_type','item_id','item_code','item_name','hsn_code',
        'unit','qty','rate','amount','sgst','cgst','igst','net_amount'
    ];
    public function invoice() { return $this->belongsTo(PurchaseInvoiceNew::class, 'invoice_id'); }
}
