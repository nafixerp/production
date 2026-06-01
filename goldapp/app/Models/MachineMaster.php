<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class MachineMaster extends Model {
    protected $fillable = ['code','name','type','location','capacity','purchase_date','warranty_expiry','status'];
    public function utilizations() { return $this->hasMany(MachineUtilization::class, 'machine_id'); }
    public function downtimeLogs() { return $this->hasMany(DowntimeLog::class, 'machine_id'); }
    public function maintenanceSchedules() { return $this->hasMany(MaintenanceSchedule::class, 'machine_id'); }
}
