<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DispatchOrder extends Model {
    protected $table = 'dispatch_orders';
    protected $fillable = [
        'slno','do_no','do_date','sales_order_id','customer_id','customer_name',
        'delivery_address','vehicle_id','driver_name','driver_phone','route_id',
        'dispatch_date','status','narration','branch_id','created_by'
    ];
    protected $casts = ['do_date'=>'date','dispatch_date'=>'date'];

    public function items()       { return $this->hasMany(DispatchOrderItem::class,'do_id'); }
    public function salesOrder()  { return $this->belongsTo(SalesOrder::class,'sales_order_id'); }
    public function customer()    { return $this->belongsTo(Account::class,'customer_id'); }
}
