<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class YieldWastage extends Model {
    protected $fillable = ['prod_order_id','fg_id','expected_qty','actual_qty','yield_pct','waste_qty','waste_reason'];
    public function productionOrder() { return $this->belongsTo(ProductionOrder::class, 'prod_order_id'); }
}
