<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class JournalVoucher extends Model {
    protected $table = 'journal_vouchers';
    protected $fillable = [
        'slno','jv_no','jv_date','voucher_type','narration','total_debit',
        'total_credit','status','approved_by','branch_id','created_by'
    ];
    protected $casts = ['jv_date'=>'date'];

    public function lines() { return $this->hasMany(JournalVoucherLine::class, 'jv_id'); }
    public function approvedBy() { return $this->belongsTo(User::class, 'approved_by'); }
}
