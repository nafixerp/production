<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class FgQualityCheck extends Model {
    protected $table = 'fg_quality_checks';
    protected $fillable = [
        'fg_id','batch_no','check_date','checked_by','result',
        'temperature','moisture_pct','visual_check','taste_check','micro_check',
        'release_date','hold_reason'
    ];
    protected $casts = ['check_date'=>'date','release_date'=>'date'];

    public function finishedGood() { return $this->belongsTo(FinishedGoods::class,'fg_id'); }
    public function checker()      { return $this->belongsTo(User::class,'checked_by'); }
}
