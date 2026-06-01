<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class VendorSupplier extends Model {
    protected $table = 'vendor_supplier';
    protected $fillable = ['code','name','contact_person','phone','email','address','city','state','gst_no','pan_no','credit_limit','credit_days','bank_name','bank_account','ifsc','status'];
}
