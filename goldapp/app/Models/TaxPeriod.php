<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TaxPeriod extends Model {
    protected $table = 'tax_periods';
    protected $fillable = ['period_name','from_date','to_date','tax_type','status','filed_by','filed_at'];
    protected $casts = ['from_date'=>'date','to_date'=>'date','filed_at'=>'datetime'];
}
