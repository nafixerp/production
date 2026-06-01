<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PosSession extends Model {
    protected $table = 'pos_sessions';
    protected $fillable = [
        'session_no','cashier_id','terminal','opened_at','closed_at',
        'opening_cash','closing_cash','total_sales','status'
    ];
    protected $casts = ['opened_at'=>'datetime','closed_at'=>'datetime'];

    public function bills()   { return $this->hasMany(PosBill::class,'session_id'); }
    public function cashier() { return $this->belongsTo(User::class,'cashier_id'); }
}
