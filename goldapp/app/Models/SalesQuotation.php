<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SalesQuotation extends Model {
    protected $table = 'sales_quotations';
    protected $fillable = [
        'slno','quot_no','quot_date','valid_till','customer_id','customer_name',
        'customer_address','channel','taxable_amount','discount','sgst','cgst','igst',
        'net_amount','status','terms','narration','created_by'
    ];
    protected $casts = ['quot_date'=>'date','valid_till'=>'date'];

    public function items()    { return $this->hasMany(SalesQuotationItem::class,'quot_id'); }
    public function customer() { return $this->belongsTo(Account::class,'customer_id'); }
}
