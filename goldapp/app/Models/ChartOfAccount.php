<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ChartOfAccount extends Model {
    protected $table = 'chart_of_accounts';
    protected $fillable = [
        'code','name','account_group','account_type','parent_id','is_control_account',
        'currency','opening_balance','ob_type','is_bank_account','bank_name',
        'bank_account_no','ifsc','allow_direct_posting','status','notes','created_by'
    ];
    protected $casts = ['opening_balance'=>'decimal:4'];

    public function parent() { return $this->belongsTo(ChartOfAccount::class, 'parent_id'); }
    public function children() { return $this->hasMany(ChartOfAccount::class, 'parent_id'); }
    public function daybookEntries() { return $this->hasMany(Daybook::class, 'account_id'); }

    public function getBalance(?string $fromDate = null, ?string $toDate = null): float {
        $q = $this->daybookEntries();
        if ($fromDate) $q->where('tdate', '>=', $fromDate);
        if ($toDate) $q->where('tdate', '<=', $toDate);
        $movement = $q->sum('amount');
        $ob = $this->ob_type === 'cr' ? $this->opening_balance : -$this->opening_balance;
        return $ob + $movement;
    }
}
