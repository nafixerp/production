<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Account extends Model {
    protected $table = 'account';
    protected $fillable = [
        'code','name','group_id','atype','opening_balance','ob_type',
        'phone','address','gst','status','branch_id'
    ];
    public function group() { return $this->belongsTo(AccountGroup::class, 'group_id'); }
    public function daybookEntries() { return $this->hasMany(Daybook::class, 'account_id'); }
}
