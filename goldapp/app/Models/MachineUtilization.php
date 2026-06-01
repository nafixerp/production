<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class MachineUtilization extends Model {
    protected $fillable = ['machine_id','tdate','shift','start_time','end_time','produced_qty','status'];
    public function machine() { return $this->belongsTo(MachineMaster::class, 'machine_id'); }
}
