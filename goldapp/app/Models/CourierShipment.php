<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourierShipment extends Model
{
    protected $fillable = [
        'slno','dispatch_id','courier_partner','awb_no','weight','dimensions',
        'charges','pickup_date','estimated_delivery','actual_delivery','status','tracking_events',
    ];

    protected $casts = [
        'tracking_events' => 'array',
        'pickup_date' => 'date',
        'estimated_delivery' => 'date',
        'actual_delivery' => 'date',
    ];
}
