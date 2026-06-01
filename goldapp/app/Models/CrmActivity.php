<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CrmActivity extends Model {
    protected $table = 'crm_activities';
    protected $fillable = [
        'lead_id','customer_id','activity_date','activity_type','subject','notes',
        'outcome','next_action','next_action_date','done_by'
    ];
    protected $casts = ['activity_date'=>'date','next_action_date'=>'date'];

    public function lead() { return $this->belongsTo(CrmLead::class, 'lead_id'); }
    public function customer() { return $this->belongsTo(CrmCustomer::class, 'customer_id'); }
    public function doneBy() { return $this->belongsTo(User::class, 'done_by'); }
}
