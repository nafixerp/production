<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SalesOrderItem extends Model {
    protected $table = 'sales_order_items';
    protected $fillable = [
        'so_id','fg_id','fg_code','fg_name','hsn_code','unit',
        'ordered_qty','dispatched_qty','pending_qty','rate','amount',
        'discount_pct','discount_amount','sgst','cgst','igst','net_amount'
    ];
    public function salesOrder()   { return $this->belongsTo(SalesOrder::class,'so_id'); }
    public function finishedGood() { return $this->belongsTo(FinishedGoods::class,'fg_id'); }
}
