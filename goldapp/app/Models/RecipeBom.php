<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class RecipeBom extends Model {
    protected $table = 'recipe_bom';
    protected $fillable = ['fg_id','code','name','version','yield_qty','yield_unit','description','status'];

    public function finishedGood() { return $this->belongsTo(FinishedGoods::class, 'fg_id'); }
    public function details() { return $this->hasMany(RecipeBomDetail::class, 'recipe_id'); }
    public function versions() { return $this->hasMany(RecipeVersioning::class, 'recipe_id'); }
    public function substitutions() { return $this->hasMany(IngredientSubstitution::class, 'recipe_id'); }
}
