<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model {
    protected $table = 'purchase_orders';
    protected $fillable = [
        'slno','po_no','po_date','supplier_id','supplier_name','delivery_date',
        'currency','exchange_rate','payment_terms','shipping_address',
        'taxable_amount','discount_amount','sgst','cgst','igst','tcs',
        'round_off','net_amount','status','approved_by','approved_at',
        'narration','branch_id','created_by'
    ];
    protected $casts = ['po_date' => 'date', 'delivery_date' => 'date', 'approved_at' => 'datetime'];

    public function supplier() { return $this->belongsTo(Supplier::class, 'supplier_id'); }
    public function items() { return $this->hasMany(PurchaseOrderItem::class, 'po_id'); }
    public function grns() { return $this->hasMany(Grn::class, 'po_id'); }
}
