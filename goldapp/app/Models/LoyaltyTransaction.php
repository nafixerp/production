<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class LoyaltyTransaction extends Model {
    protected $table = 'loyalty_transactions';
    protected $fillable = [
        'customer_id','tdate','ref_type','ref_id','points_earned',
        'points_redeemed','balance_points','narration'
    ];
    protected $casts = ['tdate'=>'date'];

    public function customer() { return $this->belongsTo(CrmCustomer::class, 'customer_id'); }
}
