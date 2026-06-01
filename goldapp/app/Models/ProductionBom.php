<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProductionBom extends Model {
    protected $table = 'production_bom';
    protected $fillable = [
        'production_order_id','item_type','item_id','item_code','item_name','unit',
        'required_qty','issued_qty','wastage_pct','actual_wastage',
        'cost_rate','cost_amount','batch_no','warehouse_id'
    ];
    public function productionOrder() { return $this->belongsTo(ProductionOrderNew::class, 'production_order_id'); }
}
