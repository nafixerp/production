<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PackingMaterial extends Model {
    protected $table = 'packing_material';
    protected $fillable = ['code','name','category','unit_id','hsn_id','tax_id','reorder_qty','status'];

    public function unit() { return $this->belongsTo(Unit::class, 'unit_id'); }
    public function hsn() { return $this->belongsTo(HsnSac::class, 'hsn_id'); }
    public function tax() { return $this->belongsTo(TaxGst::class, 'tax_id'); }
}
