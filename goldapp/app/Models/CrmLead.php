<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CrmLead extends Model {
    protected $table = 'crm_leads';
    protected $fillable = [
        'slno','lead_date','company_name','contact_name','phone','email','city','source',
        'product_interest','estimated_value','assigned_to','status','priority',
        'lost_reason','expected_close_date','converted_customer_id','converted_at',
        'narration','created_by'
    ];
    protected $casts = ['lead_date'=>'date','expected_close_date'=>'date','converted_at'=>'datetime'];

    public function activities() { return $this->hasMany(CrmActivity::class, 'lead_id'); }
    public function opportunities() { return $this->hasMany(CrmOpportunity::class, 'lead_id'); }
    public function convertedCustomer() { return $this->belongsTo(CrmCustomer::class, 'converted_customer_id'); }
}
