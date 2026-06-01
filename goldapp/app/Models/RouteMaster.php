<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class RouteMaster extends Model {
    protected $table = 'route_master';
    protected $fillable = ['code','name','description','status'];
}
