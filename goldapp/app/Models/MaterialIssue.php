<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class MaterialIssue extends Model {
    protected $fillable = ['slno','issue_date','prod_order_id','issued_by','status','narration'];
    public function productionOrder() { return $this->belongsTo(ProductionOrder::class, 'prod_order_id'); }
    public function details() { return $this->hasMany(MaterialIssueDetail::class, 'issue_id'); }
}
