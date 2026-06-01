<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturn extends Model {
    protected $table = 'purchase_returns';
    protected $fillable = [
        'slno','return_no','return_date','invoice_id','grn_id','supplier_id','supplier_name',
        'reason','taxable_amount','sgst','cgst','igst','net_amount','status',
        'narration','branch_id','created_by'
    ];
    protected $casts = ['return_date' => 'date'];

    public function supplier() { return $this->belongsTo(Supplier::class, 'supplier_id'); }
    public function items() { return $this->hasMany(PurchaseReturnItem::class, 'return_id'); }
}
