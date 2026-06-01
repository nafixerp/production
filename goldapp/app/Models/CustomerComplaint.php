<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CustomerComplaint extends Model {
    protected $table = 'customer_complaints';
    protected $fillable = [
        'slno','complaint_date','customer_id','customer_name','invoice_id','complaint_type',
        'subject','description','priority','status','resolution','resolved_by','resolved_at',
        'escalated','created_by'
    ];
    protected $casts = ['complaint_date'=>'date','resolved_at'=>'datetime'];

    public function customer() { return $this->belongsTo(CrmCustomer::class, 'customer_id'); }
    public function resolvedBy() { return $this->belongsTo(User::class, 'resolved_by'); }
}
