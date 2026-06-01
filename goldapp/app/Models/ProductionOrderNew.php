<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProductionOrderNew extends Model {
    protected $table = 'production_orders';
    protected $fillable = [
        'slno','po_no','order_date','planned_start','planned_end','actual_start','actual_end',
        'fg_id','fg_code','fg_name','recipe_id','batch_no','order_qty','produced_qty',
        'unit','status','priority','cost_centre','narration','branch_id','created_by'
    ];
    protected $casts = [
        'order_date' => 'date', 'planned_start' => 'date', 'planned_end' => 'date',
        'actual_start' => 'date', 'actual_end' => 'date'
    ];

    public function bom() { return $this->hasMany(ProductionBom::class, 'production_order_id'); }
    public function journal() { return $this->hasMany(ProductionJournalNew::class, 'production_order_id'); }
    public function costing() { return $this->hasOne(ProductionCostingNew::class, 'production_order_id'); }
    public function wipTracking() { return $this->hasMany(WipTracking::class, 'production_order_id'); }
    public function finishedGood() { return $this->belongsTo(FinishedGoods::class, 'fg_id'); }
    public function recipe() { return $this->belongsTo(RecipeBom::class, 'recipe_id'); }

    public function getProgressPctAttribute(): float {
        if ($this->order_qty <= 0) return 0;
        return min(100, round(($this->produced_qty / $this->order_qty) * 100, 1));
    }
}
