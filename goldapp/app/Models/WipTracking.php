<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class WipTracking extends Model {
    protected $table = 'wip_tracking';
    protected $fillable = [
        'production_order_id','stage','started_at','completed_at','operator_id',
        'input_qty','output_qty','loss_qty','remarks'
    ];
    protected $casts = ['started_at' => 'datetime', 'completed_at' => 'datetime'];
    public function productionOrder() { return $this->belongsTo(ProductionOrderNew::class, 'production_order_id'); }
}
