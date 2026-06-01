<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CustomerPricing extends Model {
    protected $table = 'customer_pricing';
    protected $fillable = [
        'customer_id','fg_id','price_type','price','discount_pct','min_qty',
        'valid_from','valid_to','status'
    ];
    protected $casts = ['valid_from'=>'date','valid_to'=>'date'];

    public function customer() { return $this->belongsTo(CrmCustomer::class, 'customer_id'); }
    public function finishedGood() { return $this->belongsTo(FinishedGoods::class, 'fg_id'); }
}
