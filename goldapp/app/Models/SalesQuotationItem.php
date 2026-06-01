<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SalesQuotationItem extends Model {
    protected $table = 'sales_quotation_items';
    protected $fillable = [
        'quot_id','fg_id','fg_code','fg_name','hsn_code','unit','qty','rate','amount',
        'discount_pct','discount_amount','sgst_pct','cgst_pct','igst_pct','sgst','cgst','igst','net_amount'
    ];
    public function quotation()    { return $this->belongsTo(SalesQuotation::class,'quot_id'); }
    public function finishedGood() { return $this->belongsTo(FinishedGoods::class,'fg_id'); }
}
