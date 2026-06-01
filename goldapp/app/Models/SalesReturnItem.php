<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SalesReturnItem extends Model {
    protected $table = 'sales_return_items';
    protected $fillable = ['return_id','fg_id','fg_code','fg_name','unit','qty','rate','amount','batch_no'];
    public function salesReturn()  { return $this->belongsTo(SalesReturn::class,'return_id'); }
    public function finishedGood() { return $this->belongsTo(FinishedGoods::class,'fg_id'); }
}
