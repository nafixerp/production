<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Factory extends Model {
    protected $table = 'factory';
    protected $fillable = ['branch_id','code','name','address','capacity','status'];

    public function branch() { return $this->belongsTo(Branch::class, 'branch_id'); }
}
