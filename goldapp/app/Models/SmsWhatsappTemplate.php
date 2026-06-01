<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsWhatsappTemplate extends Model
{
    protected $fillable = ['name','channel','template_id','message','variables_list','module','is_active'];
}
