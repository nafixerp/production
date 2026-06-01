<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BankReconLine extends Model {
    protected $table = 'bank_recon_lines';
    protected $fillable = [
        'recon_id','tdate','description','amount','dr_cr','daybook_id','is_matched','statement_line'
    ];
    protected $casts = ['tdate'=>'date'];

    public function reconciliation() { return $this->belongsTo(BankReconciliation::class, 'recon_id'); }
    public function daybookEntry() { return $this->belongsTo(Daybook::class, 'daybook_id'); }
}
