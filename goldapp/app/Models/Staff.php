<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model {
    protected $table = 'staff';
    protected $fillable = ['department_id','branch_id','code','name','designation','phone','email','address','dob','doj','salary','status'];

    public function department() { return $this->belongsTo(Department::class, 'department_id'); }
    public function branch() { return $this->belongsTo(Branch::class, 'branch_id'); }
}
