<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Einvoice extends Model
{
    protected $fillable = [
        'sales_invoice_id','irn','ack_no','ack_date','qr_code','signed_invoice',
        'cancel_irn','cancel_date','status',
    ];

    protected $casts = [
        'ack_date' => 'datetime',
        'cancel_date' => 'datetime',
    ];
}
