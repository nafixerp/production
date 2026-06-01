<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Rework extends Model {
    protected $fillable = ['prod_order_id','rework_date','reason','qty','status'];
    public function productionOrder() { return $this->belongsTo(ProductionOrder::class, 'prod_order_id'); }
}
