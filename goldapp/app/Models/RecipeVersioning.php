<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class RecipeVersioning extends Model {
    protected $table = 'recipe_versioning';
    protected $fillable = ['recipe_id','version','change_notes','changed_by'];

    public function recipe() { return $this->belongsTo(RecipeBom::class, 'recipe_id'); }
}
