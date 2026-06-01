<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class MaterialIssueDetail extends Model {
    protected $fillable = ['issue_id','item_type','item_id','item_name','unit','required_qty','issued_qty','batch_no','warehouse_id'];
    public function issue() { return $this->belongsTo(MaterialIssue::class, 'issue_id'); }
}
