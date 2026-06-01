<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class AccountGroup extends Model {
    protected $table = 'account_group';
    protected $fillable = ['code','name','nature'];
    public function accounts() { return $this->hasMany(Account::class, 'group_id'); }
}
