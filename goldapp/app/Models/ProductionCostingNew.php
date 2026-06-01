<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProductionCostingNew extends Model {
    protected $table = 'production_costing';
    protected $fillable = [
        'production_order_id','fg_id','fg_name','batch_no','order_qty','produced_qty',
        'rm_cost','pm_cost','labour_cost','overhead_cost','total_cost','cost_per_unit','variance'
    ];
    public function productionOrder() { return $this->belongsTo(ProductionOrderNew::class, 'production_order_id'); }
}
