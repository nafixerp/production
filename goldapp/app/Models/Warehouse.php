<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model {
    protected $table = 'warehouses';
    protected $fillable = [
        'code','name','type','address','city','state',
        'capacity','capacity_unit','temperature_min','temperature_max',
        'manager_id','branch_id','status'
    ];

    public function branch()    { return $this->belongsTo(Branch::class,'branch_id'); }
    public function locations() { return $this->hasMany(WarehouseLocation::class,'warehouse_id'); }
    public function fgStocks()  { return $this->hasMany(FgStock::class,'warehouse_id'); }
}
