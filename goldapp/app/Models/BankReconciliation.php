<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BankReconciliation extends Model {
    protected $table = 'bank_reconciliation';
    protected $fillable = [
        'bank_account_id','statement_date','statement_closing_balance',
        'book_balance','difference','status','reconciled_by','reconciled_at'
    ];
    protected $casts = ['statement_date'=>'date','reconciled_at'=>'datetime'];

    public function bankAccount() { return $this->belongsTo(ChartOfAccount::class, 'bank_account_id'); }
    public function lines() { return $this->hasMany(BankReconLine::class, 'recon_id'); }
}
