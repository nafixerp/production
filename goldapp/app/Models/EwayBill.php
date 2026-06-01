<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EwayBill extends Model
{
    protected $fillable = [
        'sales_invoice_id','ewb_no','ewb_date','valid_till','from_gstin','to_gstin',
        'transporter_id','vehicle_no','distance_km','status',
    ];

    protected $casts = [
        'ewb_date' => 'datetime',
        'valid_till' => 'datetime',
    ];
}
