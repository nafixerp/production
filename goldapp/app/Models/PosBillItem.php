<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PosBillItem extends Model {
    protected $table = 'pos_bill_items';
    protected $fillable = [
        'bill_id','fg_id','fg_code','fg_name','qty','rate','discount_pct','amount','sgst','cgst','net_amount','batch_no'
    ];
    public function bill()         { return $this->belongsTo(PosBill::class,'bill_id'); }
    public function finishedGood() { return $this->belongsTo(FinishedGoods::class,'fg_id'); }
}
