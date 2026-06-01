<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model {
    protected $table = 'vehicle';
    protected $fillable = ['code','registration_no','type','capacity','driver_name','driver_phone','status'];
}
