<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentGatewayTxn extends Model
{
    protected $fillable = [
        'order_ref','gateway','gateway_txn_id','amount','currency','status',
        'sales_invoice_id','customer_id','response_data','settled_at',
    ];

    protected $casts = [
        'response_data' => 'array',
        'settled_at' => 'datetime',
    ];
}
