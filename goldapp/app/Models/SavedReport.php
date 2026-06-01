<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavedReport extends Model {
    protected $fillable = ['template_id', 'name', 'parameters', 'schedule', 'last_run_at', 'created_by'];

    protected $casts = [
        'parameters'  => 'array',
        'last_run_at' => 'datetime',
    ];

    public function template() {
        return $this->belongsTo(ReportTemplate::class);
    }
}
