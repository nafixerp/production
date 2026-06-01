<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model {
    protected $table = 'suppliers';
    protected $fillable = [
        'code','name','contact_person','phone','email','address','city','state',
        'pincode','country','gst_no','pan_no','credit_limit','credit_days',
        'payment_terms','bank_name','bank_account','ifsc','currency','rating',
        'status','notes','created_by'
    ];

    public function purchaseOrders() { return $this->hasMany(PurchaseOrder::class, 'supplier_id'); }
    public function grns() { return $this->hasMany(Grn::class, 'supplier_id'); }
    public function purchaseInvoicesNew() { return $this->hasMany(PurchaseInvoiceNew::class, 'supplier_id'); }
    public function apLedger() { return $this->hasMany(ApLedger::class, 'supplier_id'); }

    public function getCurrentBalance(): float {
        return (float) ApLedger::where('supplier_id', $this->id)->sum(\DB::raw('credit - debit'));
    }
}
