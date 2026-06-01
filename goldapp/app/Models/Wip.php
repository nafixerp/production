<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Wip extends Model {
    protected $table = 'wip';
    protected $fillable = ['prod_order_id','stage','start_time','end_time','qty_in','qty_out','waste_qty','status'];
    public function productionOrder() { return $this->belongsTo(ProductionOrder::class, 'prod_order_id'); }
}
