<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Purchasedd extends Model {
    protected $table = 'purchasedd';
    protected $fillable = ['purchasem_id','slno','item_code','item_name','purity','gross_wt','net_wt','rate','amount','hmc'];
}
