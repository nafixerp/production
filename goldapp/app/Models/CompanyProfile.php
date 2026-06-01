<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyProfile extends Model
{
    protected $table = 'company_profile';
    protected $fillable = [
        'name','legal_name','address','city','state','pincode','country',
        'phone','email','website','gst_no','pan_no','cin_no','fssai_no',
        'logo_path','financial_year_start','currency','currency_symbol',
        'date_format','decimal_places',
    ];
}
