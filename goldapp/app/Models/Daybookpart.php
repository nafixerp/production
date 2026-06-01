<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Daybookpart extends Model {
    protected $table = 'daybookpart';
    protected $fillable = [
        'slno','vchno','particular','tdate','vtype','staff_id',
        'cheque_no','cheque_date','bank_name','narration','branch_id','created_by'
    ];
    public function entries() { return $this->hasMany(Daybook::class, 'slno', 'slno'); }
}
