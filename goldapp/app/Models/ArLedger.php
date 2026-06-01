<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ArLedger extends Model {
    protected $table = 'ar_ledger';
    protected $fillable = ['customer_id','tdate','slno','vtype','ref_no','debit','credit','balance','narration'];
    protected $casts = ['tdate'=>'date'];

    public function customer() { return $this->belongsTo(Account::class,'customer_id'); }
}
