<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model {
    protected $table = 'stock_movements';
    protected $fillable = [
        'movement_date','item_type','item_id','item_code','item_name','warehouse_id',
        'batch_no','movement_type','ref_type','ref_id','qty','unit',
        'cost_rate','cost_amount','running_balance','narration','created_by'
    ];
    protected $casts = ['movement_date' => 'date'];

    public function warehouse() { return $this->belongsTo(Warehouse::class, 'warehouse_id'); }
}
