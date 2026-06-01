<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Department extends Model {
    protected $table = 'department';
    protected $fillable = ['branch_id','code','name','description','status'];

    public function branch() { return $this->belongsTo(Branch::class, 'branch_id'); }
    public function staff() { return $this->hasMany(Staff::class, 'department_id'); }
}
