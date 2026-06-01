<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SalesReturn extends Model {
    protected $table = 'sales_returns';
    protected $fillable = [
        'slno','return_no','return_date','invoice_id','so_id','customer_id','customer_name',
        'channel','reason','taxable_amount','sgst','cgst','igst','net_amount',
        'refund_mode','status','narration','branch_id','created_by'
    ];
    protected $casts = ['return_date'=>'date'];

    public function items()   { return $this->hasMany(SalesReturnItem::class,'return_id'); }
    public function invoice() { return $this->belongsTo(SalesInvoiceModel::class,'invoice_id'); }
    public function customer(){ return $this->belongsTo(Account::class,'customer_id'); }
}
