<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ProductionStageLog extends Model {
    protected $fillable = ['prod_order_id','stage','operator_id','start_time','end_time','temperature','notes','status'];
    public function productionOrder() { return $this->belongsTo(ProductionOrder::class, 'prod_order_id'); }
    public function operator() { return $this->belongsTo(User::class, 'operator_id'); }
}
