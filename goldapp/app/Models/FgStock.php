<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class FgStock extends Model {
    protected $table = 'fg_stock';
    protected $fillable = [
        'fg_id','fg_code','fg_name','warehouse_id','batch_no','mfg_date','expiry_date',
        'qty_in','qty_out','qty_reserved','cost_rate','status'
    ];
    protected $casts = ['mfg_date'=>'date','expiry_date'=>'date'];

    public function finishedGood() { return $this->belongsTo(FinishedGoods::class,'fg_id'); }
    public function warehouse()    { return $this->belongsTo(Warehouse::class,'warehouse_id'); }

    public function getQtyAvailableAttribute(): float {
        return (float)$this->qty_in - (float)$this->qty_out - (float)$this->qty_reserved;
    }
    public function getExpiryStatusAttribute(): string {
        if (!$this->expiry_date) return 'ok';
        $days = now()->diffInDays($this->expiry_date, false);
        if ($days < 0) return 'expired';
        if ($days <= 30) return 'expiring_soon';
        return 'ok';
    }
}
