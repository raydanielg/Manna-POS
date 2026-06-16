<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Business extends Model
{
    use SoftDeletes;
    protected $table = 'businesses';
    protected $fillable = ['user_id','business_name','business_type','business_category_id','business_address','business_city','business_country','phone','email','website','registration_number','tax_number','currency','status','is_verified','verified_at','verified_by','notes'];
    protected $casts = ['is_verified'=>'boolean','verified_at'=>'datetime'];

    public function user() { return $this->belongsTo(User::class); }
    public function category() { return $this->belongsTo(BusinessCategory::class, 'business_category_id'); }
    public function verifier() { return $this->belongsTo(User::class, 'verified_by'); }
    public function verifications() { return $this->hasMany(BusinessVerification::class); }
}
