<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class FinishedGoods extends Model {
    protected $table = 'finished_goods';
    protected $fillable = ['code','name','category','unit_id','hsn_id','tax_id','mrp','selling_price','cost_price','description','status'];

    public function unit() { return $this->belongsTo(Unit::class, 'unit_id'); }
    public function hsn() { return $this->belongsTo(HsnSac::class, 'hsn_id'); }
    public function tax() { return $this->belongsTo(TaxGst::class, 'tax_id'); }
    public function recipes() { return $this->hasMany(RecipeBom::class, 'fg_id'); }
}
