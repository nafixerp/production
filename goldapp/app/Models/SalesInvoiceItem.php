<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SalesInvoiceItem extends Model {
    protected $table = 'sales_invoice_items';
    protected $fillable = [
        'invoice_id','fg_id','fg_code','fg_name','hsn_code','unit','qty','rate','amount',
        'discount_pct','discount_amount','sgst_pct','cgst_pct','igst_pct',
        'sgst','cgst','igst','net_amount','batch_no','cost_rate'
    ];
    public function invoice()      { return $this->belongsTo(SalesInvoiceModel::class,'invoice_id'); }
    public function finishedGood() { return $this->belongsTo(FinishedGoods::class,'fg_id'); }
}
