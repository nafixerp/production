<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ApLedger extends Model {
    protected $table = 'ap_ledger';
    protected $fillable = [
        'supplier_id','tdate','slno','vtype','ref_no','debit','credit','balance','narration'
    ];
    protected $casts = ['tdate' => 'date'];

    public function supplier() { return $this->belongsTo(Supplier::class, 'supplier_id'); }
}
