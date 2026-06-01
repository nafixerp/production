<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Company extends Model {
    protected $table = 'company';
    protected $fillable = ['code','name','address','city','state','country','pincode','phone','email','gst_no','pan_no','fssai_no','logo','status'];

    public function branches() { return $this->hasMany(Branch::class, 'company_id'); }
}
