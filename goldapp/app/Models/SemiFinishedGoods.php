<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SemiFinishedGoods extends Model {
    protected $table = 'semi_finished_goods';
    protected $fillable = ['code','name','category','unit_id','description','status'];

    public function unit() { return $this->belongsTo(Unit::class, 'unit_id'); }
}
