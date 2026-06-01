<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BatchTraceability extends Model {
    protected $table = 'batch_traceability';
    protected $fillable = [
        'batch_no','fg_id','fg_name','production_order_id','mfg_date','expiry_date',
        'qty_produced','qty_dispatched','qty_returned','qty_available','qc_status','recall_flag'
    ];
    protected $casts = ['mfg_date'=>'date','expiry_date'=>'date'];

    public function finishedGood() { return $this->belongsTo(FinishedGoods::class,'fg_id'); }
    public function dispatchItems(){ return $this->hasMany(DispatchOrderItem::class,'batch_no','batch_no'); }
}
