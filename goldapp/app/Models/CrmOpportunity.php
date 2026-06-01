<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CrmOpportunity extends Model {
    protected $table = 'crm_opportunities';
    protected $fillable = [
        'lead_id','customer_id','title','value','probability','expected_close',
        'stage','product_ids','assigned_to','narration','created_by'
    ];
    protected $casts = ['expected_close'=>'date','product_ids'=>'array','value'=>'decimal:2'];

    public function lead() { return $this->belongsTo(CrmLead::class, 'lead_id'); }
    public function customer() { return $this->belongsTo(CrmCustomer::class, 'customer_id'); }
}
