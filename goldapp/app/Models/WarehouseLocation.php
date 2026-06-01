<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class WarehouseLocation extends Model {
    protected $table = 'warehouse_locations';
    protected $fillable = ['warehouse_id','zone','rack','shelf','bin','capacity','current_stock'];

    public function warehouse() { return $this->belongsTo(Warehouse::class,'warehouse_id'); }
}
