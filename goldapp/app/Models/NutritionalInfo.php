<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class NutritionalInfo extends Model {
    protected $table = 'nutritional_info';
    protected $fillable = ['item_id','item_type','per_100g_calories','protein','carbs','fat','fiber','sodium','allergens_text'];
}
