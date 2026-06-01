<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PosBill extends Model {
    protected $table = 'pos_bills';
    protected $fillable = [
        'slno','bill_no','bill_date','session_id','customer_id','customer_name',
        'taxable_amount','discount','sgst','cgst','net_amount',
        'received_amount','change_amount','payment_mode','branch_id','cashier_id'
    ];
    protected $casts = ['bill_date'=>'date'];

    public function items()   { return $this->hasMany(PosBillItem::class,'bill_id'); }
    public function session() { return $this->belongsTo(PosSession::class,'session_id'); }
}
