<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KpiSnapshot extends Model {
    protected $fillable = ['snapshot_date', 'metric_name', 'metric_value', 'metric_unit', 'period'];

    protected $casts = ['snapshot_date' => 'date'];
}
