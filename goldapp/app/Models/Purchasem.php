<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Purchasem extends Model {
    protected $table = 'purchasem';
    protected $fillable = ['slno','docno','billdate','supplier_id','supplier_name','supplier_bill_no','gross_amount','discount','sgst','cgst','igst','hmc','tcs','round_off','net_amount','paid_amount','payment_mode','bank_account_id','exchange_amount','narration','status','created_by'];
    public function details() { return $this->hasMany(Purchasedd::class, 'purchasem_id'); }
    public function supplier() { return $this->belongsTo(Account::class, 'supplier_id'); }
}
