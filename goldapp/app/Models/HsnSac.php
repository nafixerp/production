<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class HsnSac extends Model {
    protected $table = 'hsn_sac';
    protected $fillable = ['code','description','type','tax_id','status'];

    public function tax() { return $this->belongsTo(TaxGst::class, 'tax_id'); }
}
