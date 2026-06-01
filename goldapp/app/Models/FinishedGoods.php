<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class FinishedGoods extends Model {
    protected $table = 'finished_goods';
    protected $fillable = [
        'code','name','category','sub_category','unit_id','hsn_code','tax_id',
        'mrp','selling_price','wholesale_price','distributor_price','online_price','cost_price',
        'reorder_qty','shelf_life_days','storage_temp','barcode','sku',
        'weight','weight_unit','is_perishable','allergen_ids',
        'status','description','image_path','created_by'
    ];
    protected $casts = ['allergen_ids'=>'array'];

    public function unit()      { return $this->belongsTo(Unit::class,'unit_id'); }
    public function tax()       { return $this->belongsTo(TaxGst::class,'tax_id'); }
    public function stocks()    { return $this->hasMany(FgStock::class,'fg_id'); }
    public function qualityChecks() { return $this->hasMany(FgQualityCheck::class,'fg_id'); }

    public function getCurrentStockAttribute(): float {
        return (float)$this->stocks()->selectRaw('SUM(qty_in) - SUM(qty_out) - SUM(qty_reserved) as net')->value('net');
    }
}
