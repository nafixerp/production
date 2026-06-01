<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class InventoryStock extends Model {
    protected $table = 'inventory_stock';
    protected $fillable = [
        'item_type','item_id','item_code','item_name','warehouse_id','batch_no',
        'mfg_date','expiry_date','qty_in','qty_out','qty_reserved','unit',
        'cost_rate','status'
    ];
    protected $casts = ['mfg_date' => 'date', 'expiry_date' => 'date'];

    public function warehouse() { return $this->belongsTo(Warehouse::class, 'warehouse_id'); }

    public function getBalanceQtyAttribute(): float {
        return (float)$this->qty_in - (float)$this->qty_out;
    }
}
