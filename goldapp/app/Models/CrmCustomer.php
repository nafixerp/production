<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CrmCustomer extends Model {
    protected $table = 'customers';
    protected $fillable = [
        'code','name','contact_person','phone','alt_phone','email','address','city','state',
        'pincode','country','gst_no','pan_no','customer_type','credit_limit','credit_days',
        'payment_terms','price_tier','outstanding_balance','loyalty_points','route_id',
        'distributor_id','assigned_to','dob','anniversary','status','notes','created_by'
    ];
    protected $casts = ['dob'=>'date','anniversary'=>'date','credit_limit'=>'decimal:2','outstanding_balance'=>'decimal:2'];

    public function complaints() { return $this->hasMany(CustomerComplaint::class, 'customer_id'); }
    public function activities() { return $this->hasMany(CrmActivity::class, 'customer_id'); }
    public function pricing() { return $this->hasMany(CustomerPricing::class, 'customer_id'); }
    public function loyaltyTransactions() { return $this->hasMany(LoyaltyTransaction::class, 'customer_id'); }
    public function opportunities() { return $this->hasMany(CrmOpportunity::class, 'customer_id'); }
}
