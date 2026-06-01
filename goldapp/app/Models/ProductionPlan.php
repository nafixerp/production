<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ProductionPlan extends Model {
    protected $fillable = ['slno','plan_date','plan_for_date','fg_id','fg_name','planned_qty','unit','recipe_id','status','narration'];
    public function productionOrders() { return $this->hasMany(ProductionOrder::class, 'plan_id'); }
}
