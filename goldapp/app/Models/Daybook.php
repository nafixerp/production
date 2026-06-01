<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Daybook extends Model {
    protected $table = 'daybook';
    protected $fillable = [
        'slno','account_id','account_code','account_name','amount',
        'particular','tdate','vtype','branch_id'
    ];
    public function account() { return $this->belongsTo(Account::class, 'account_id'); }
}
