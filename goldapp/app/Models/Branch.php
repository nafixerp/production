<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model {
    protected $table = 'branch';
    protected $fillable = ['company_id','code','name','address','city','state','phone','email','status'];

    public function company() { return $this->belongsTo(Company::class, 'company_id'); }
    public function warehouses() { return $this->hasMany(Warehouse::class, 'branch_id'); }
    public function factories() { return $this->hasMany(Factory::class, 'branch_id'); }
    public function departments() { return $this->hasMany(Department::class, 'branch_id'); }
}
