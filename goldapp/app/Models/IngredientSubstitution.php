<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class IngredientSubstitution extends Model {
    protected $table = 'ingredient_substitution';
    protected $fillable = ['recipe_id','original_item_id','substitute_item_id','substitution_ratio','notes'];

    public function recipe() { return $this->belongsTo(RecipeBom::class, 'recipe_id'); }
}
