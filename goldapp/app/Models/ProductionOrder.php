<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ProductionOrder extends Model {
    protected $fillable = ['slno','po_no','plan_id','order_date','fg_id','fg_name','order_qty','unit','recipe_id','batch_no','status','narration'];
    public function plan() { return $this->belongsTo(ProductionPlan::class, 'plan_id'); }
    public function materialIssues() { return $this->hasMany(MaterialIssue::class, 'prod_order_id'); }
    public function wipRecords() { return $this->hasMany(Wip::class, 'prod_order_id'); }
    public function stageLogs() { return $this->hasMany(ProductionStageLog::class, 'prod_order_id'); }
    public function fgReceipts() { return $this->hasMany(FgReceipt::class, 'prod_order_id'); }
    public function yieldWastage() { return $this->hasOne(YieldWastage::class, 'prod_order_id'); }
    public function byproducts() { return $this->hasMany(Byproduct::class, 'prod_order_id'); }
    public function reworks() { return $this->hasMany(Rework::class, 'prod_order_id'); }
    public function costing() { return $this->hasOne(ProductionCosting::class, 'prod_order_id'); }
}
