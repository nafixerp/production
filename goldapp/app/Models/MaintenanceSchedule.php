<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class MaintenanceSchedule extends Model {
    protected $fillable = ['machine_id','scheduled_date','type','description','assigned_to','completed_date','status'];
    public function machine() { return $this->belongsTo(MachineMaster::class, 'machine_id'); }
}
