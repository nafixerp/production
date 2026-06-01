<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DispatchOrderItem extends Model {
    protected $table = 'dispatch_order_items';
    protected $fillable = [
        'do_id','fg_id','fg_code','fg_name','batch_no','warehouse_id',
        'qty','unit','cost_rate','cost_amount','selling_rate','selling_amount'
    ];

    public function dispatchOrder()  { return $this->belongsTo(DispatchOrder::class,'do_id'); }
    public function finishedGood()   { return $this->belongsTo(FinishedGoods::class,'fg_id'); }
}
