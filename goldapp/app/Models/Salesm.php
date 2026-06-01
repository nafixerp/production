<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Salesm extends Model {
    protected $table = 'salesm';
    protected $fillable = ['slno','billno','billdate','customer_id','customer_name','gross_amount','discount','sgst','cgst','igst','hmc','tcs','round_off','net_amount','received_amount','payment_mode','bank_account_id','exchange_amount','sales_return','narration','status','created_by'];
    public function details() { return $this->hasMany(Salesd::class, 'salesm_id'); }
    public function customer() { return $this->belongsTo(Account::class, 'customer_id'); }
}
