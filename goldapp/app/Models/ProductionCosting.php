<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ProductionCosting extends Model {
    protected $fillable = ['prod_order_id','fg_id','fg_name','batch_no','rm_cost','pm_cost','labour_cost','overhead_cost','total_cost','cost_per_unit'];
    public function productionOrder() { return $this->belongsTo(ProductionOrder::class, 'prod_order_id'); }
}
