<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model {
    protected $table = 'unit';
    protected $fillable = ['code','name','base_unit','conversion_factor','status'];
}
