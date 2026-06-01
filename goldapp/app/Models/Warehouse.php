<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model {
    protected $table = 'warehouse';
    protected $fillable = ['branch_id','code','name','type','address','capacity','status'];

    public function branch() { return $this->belongsTo(Branch::class, 'branch_id'); }
}
