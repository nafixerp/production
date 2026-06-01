<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class DowntimeLog extends Model {
    protected $fillable = ['machine_id','start_time','end_time','duration_mins','reason','reported_by'];
    public function machine() { return $this->belongsTo(MachineMaster::class, 'machine_id'); }
}
