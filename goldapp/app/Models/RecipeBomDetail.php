<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class RecipeBomDetail extends Model {
    protected $table = 'recipe_bom_detail';
    protected $fillable = ['recipe_id','item_type','item_id','item_name','qty','unit','wastage_pct'];

    public function recipe() { return $this->belongsTo(RecipeBom::class, 'recipe_id'); }
}
