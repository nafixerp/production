<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GstFiling extends Model
{
    protected $fillable = [
        'period_name','from_date','to_date','filing_type','total_taxable',
        'total_sgst','total_cgst','total_igst','json_payload','irn_no','status','filed_at',
    ];

    protected $casts = [
        'json_payload' => 'array',
        'filed_at' => 'datetime',
        'from_date' => 'date',
        'to_date' => 'date',
    ];
}
