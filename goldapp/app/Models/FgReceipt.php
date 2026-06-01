<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class FgReceipt extends Model {
    protected $fillable = ['slno','receipt_date','prod_order_id','fg_id','fg_name','received_qty','unit','batch_no','expiry_date','warehouse_id','cost_per_unit','total_cost'];
    public function productionOrder() { return $this->belongsTo(ProductionOrder::class, 'prod_order_id'); }
    public function finalQc() { return $this->hasOne(FinalQc::class, 'fg_receipt_id'); }
}
