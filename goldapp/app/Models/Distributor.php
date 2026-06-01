<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Distributor extends Model {
    protected $table = 'distributor';
    protected $fillable = ['code','name','contact_person','phone','email','address','city','state','gst_no','area','status'];
}
