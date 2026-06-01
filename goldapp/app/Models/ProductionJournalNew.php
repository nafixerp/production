<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProductionJournalNew extends Model {
    protected $table = 'production_journal';
    protected $fillable = [
        'production_order_id','journal_date','stage','qty','unit','remarks','done_by'
    ];
    protected $casts = ['journal_date' => 'date'];
    public function productionOrder() { return $this->belongsTo(ProductionOrderNew::class, 'production_order_id'); }
}
