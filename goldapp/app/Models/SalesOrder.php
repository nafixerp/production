<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model {
    protected $table = 'sales_orders';
    protected $fillable = [
        'slno','so_no','so_date','quot_id','customer_id','customer_name',
        'delivery_address','delivery_date','channel','payment_terms',
        'taxable_amount','discount','sgst','cgst','igst','net_amount',
        'advance_received','balance_amount','status','narration','branch_id','created_by'
    ];
    protected $casts = ['so_date'=>'date','delivery_date'=>'date'];

    public function items()      { return $this->hasMany(SalesOrderItem::class,'so_id'); }
    public function quotation()  { return $this->belongsTo(SalesQuotation::class,'quot_id'); }
    public function customer()   { return $this->belongsTo(Account::class,'customer_id'); }
    public function dispatches() { return $this->hasMany(DispatchOrder::class,'sales_order_id'); }
    public function invoices()   { return $this->hasMany(SalesInvoiceModel::class,'so_id'); }
}
