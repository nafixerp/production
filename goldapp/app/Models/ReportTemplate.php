<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportTemplate extends Model {
    protected $fillable = ['name', 'module', 'description', 'query_config', 'columns_config', 'filters_config', 'is_system', 'created_by'];

    protected $casts = [
        'query_config'   => 'array',
        'columns_config' => 'array',
        'filters_config' => 'array',
    ];
}
