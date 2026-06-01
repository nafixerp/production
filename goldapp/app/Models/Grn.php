<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Grn extends Model {
    protected $table = 'grn';
    protected $fillable = [
        'slno','grn_no','grn_date','po_id','supplier_id','supplier_name',
        'supplier_invoice_no','supplier_invoice_date','taxable_amount',
        'sgst','cgst','igst','net_amount','status','qc_result',
        'narration','branch_id','created_by'
    ];
    protected $casts = ['grn_date' => 'date', 'supplier_invoice_date' => 'date'];

    public function supplier() { return $this->belongsTo(Supplier::class, 'supplier_id'); }
    public function purchaseOrder() { return $this->belongsTo(PurchaseOrder::class, 'po_id'); }
    public function items() { return $this->hasMany(GrnItem::class, 'grn_id'); }
}
