<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model {
    protected $table = 'payment';
    protected $fillable = ['slno','vch_no','vch_date','party_id','party_name','amount','payment_mode','bank_account_id','cheque_no','cheque_date','bank_name','narration','status','branch_id','created_by'];
    public function party() { return $this->belongsTo(Account::class, 'party_id'); }
}
