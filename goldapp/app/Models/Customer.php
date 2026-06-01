<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model {
    protected $table = 'customer';
    protected $fillable = ['code','name','contact_person','phone','email','address','city','state','gst_no','pan_no','credit_limit','credit_days','route_id','distributor_id','status'];

    public function route() { return $this->belongsTo(RouteMaster::class, 'route_id'); }
    public function distributor() { return $this->belongsTo(Distributor::class, 'distributor_id'); }
}
