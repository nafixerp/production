<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class RawMaterialNew extends Model {
    protected $table = 'raw_materials';
    protected $fillable = [
        'code','name','category','sub_category','unit_id','hsn_code','tax_id',
        'reorder_qty','min_stock','max_stock','lead_time_days','shelf_life_days',
        'storage_temp','allergen_ids','is_fssai_regulated','description','status','created_by'
    ];
    protected $casts = ['allergen_ids' => 'array'];

    public function unit() { return $this->belongsTo(Unit::class, 'unit_id'); }
    public function tax() { return $this->belongsTo(TaxGst::class, 'tax_id'); }

    public function getCurrentStock(): float {
        return (float) InventoryStock::where('item_type', 'RM')
            ->where('item_id', $this->id)
            ->selectRaw('SUM(qty_in - qty_out) as balance')
            ->value('balance');
    }
}
