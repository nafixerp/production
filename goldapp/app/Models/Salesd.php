<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Salesd extends Model {
    protected $table = 'salesd';
    protected $fillable = ['salesm_id','slno','item_code','item_name','purity','gross_wt','net_wt','rate','amount','hmc'];
}
