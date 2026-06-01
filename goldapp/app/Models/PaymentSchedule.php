<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PaymentSchedule extends Model {
    protected $table = 'payment_schedules';
    protected $fillable = [
        'party_type','party_id','ref_type','ref_id','due_date','amount','paid_amount','status'
    ];
    protected $casts = ['due_date'=>'date'];
}
