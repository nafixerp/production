<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class FssaiCompliance extends Model {
    protected $table = 'fssai_compliance';
    protected $fillable = ['branch_id','license_no','license_type','issue_date','expiry_date','status'];

    public function branch() { return $this->belongsTo(Branch::class, 'branch_id'); }
}
