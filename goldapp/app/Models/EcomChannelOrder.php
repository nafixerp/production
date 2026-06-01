<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EcomChannelOrder extends Model
{
    protected $fillable = [
        'channel','channel_order_id','order_date','customer_name','customer_email',
        'customer_phone','total_amount','status','mapped_so_id','raw_data',
    ];

    protected $casts = [
        'raw_data' => 'array',
        'order_date' => 'datetime',
    ];
}
