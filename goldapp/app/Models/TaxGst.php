<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TaxGst extends Model {
    protected $table = 'tax_gst';
    protected $fillable = ['code','name','sgst_pct','cgst_pct','igst_pct','cess_pct','status'];
}
