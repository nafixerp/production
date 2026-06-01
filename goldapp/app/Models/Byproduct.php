<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Byproduct extends Model {
    protected $fillable = ['prod_order_id','item_id','item_name','qty','unit','disposal_type'];
    public function productionOrder() { return $this->belongsTo(ProductionOrder::class, 'prod_order_id'); }
}
