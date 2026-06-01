<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class JournalVoucherLine extends Model {
    protected $table = 'journal_voucher_lines';
    protected $fillable = [
        'jv_id','account_id','account_code','account_name','amount','particular','cost_centre'
    ];
    protected $casts = ['amount'=>'decimal:4'];

    public function voucher() { return $this->belongsTo(JournalVoucher::class, 'jv_id'); }
    public function account() { return $this->belongsTo(ChartOfAccount::class, 'account_id'); }
}
