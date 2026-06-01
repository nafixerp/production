<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CostCentre extends Model {
    protected $table = 'cost_centres';
    protected $fillable = ['code','name','parent_id','budget','status'];

    public function parent() { return $this->belongsTo(CostCentre::class, 'parent_id'); }
    public function children() { return $this->hasMany(CostCentre::class, 'parent_id'); }
}
