<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BusinessVerification extends Model
{
    protected $table = 'business_verifications';
    protected $fillable = ['business_id','document_type','document_path','status','notes','reviewed_by','reviewed_at'];
    protected $casts = ['reviewed_at'=>'datetime'];

    public function business() { return $this->belongsTo(Business::class); }
    public function reviewer() { return $this->belongsTo(User::class, 'reviewed_by'); }
}
