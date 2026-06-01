<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturnItem extends Model {
    protected $table = 'purchase_return_items';
    protected $fillable = [
        'return_id','item_type','item_id','item_code','item_name','unit',
        'qty','rate','amount','batch_no'
    ];
    public function purchaseReturn() { return $this->belongsTo(PurchaseReturn::class, 'return_id'); }
}
