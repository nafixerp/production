<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model {
    protected $table = 'purchase_order_items';
    protected $fillable = [
        'po_id','item_type','item_id','item_code','item_name','hsn_code','unit',
        'qty','pending_qty','rate','amount','discount_pct','discount_amount',
        'sgst_pct','cgst_pct','igst_pct','sgst','cgst','igst','net_amount'
    ];
    public function purchaseOrder() { return $this->belongsTo(PurchaseOrder::class, 'po_id'); }
}
