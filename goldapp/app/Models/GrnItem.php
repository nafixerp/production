<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class GrnItem extends Model {
    protected $table = 'grn_items';
    protected $fillable = [
        'grn_id','po_item_id','item_type','item_id','item_code','item_name','unit',
        'ordered_qty','received_qty','accepted_qty','rejected_qty','rate','amount',
        'batch_no','mfg_date','expiry_date','warehouse_id'
    ];
    protected $casts = ['mfg_date' => 'date', 'expiry_date' => 'date'];
    public function grn() { return $this->belongsTo(Grn::class, 'grn_id'); }
}
