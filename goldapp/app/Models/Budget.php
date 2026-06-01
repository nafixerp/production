<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model {
    protected $table = 'budgets';
    protected $fillable = [
        'cost_centre_id','account_id','account_name','financial_year',
        'month','budgeted_amount','actual_amount','variance'
    ];

    public function costCentre() { return $this->belongsTo(CostCentre::class, 'cost_centre_id'); }
    public function account() { return $this->belongsTo(ChartOfAccount::class, 'account_id'); }
}
