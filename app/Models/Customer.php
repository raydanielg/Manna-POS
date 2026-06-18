<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Customer extends Model {
    protected $fillable = ['created_by','name','email','phone','address','city','country','customer_group_id','loyalty_points','balance','credit_limit','status','notes'];
    public function group() { return $this->belongsTo(CustomerGroup::class,'customer_group_id'); }
    public function sales() { return $this->hasMany(Sale::class); }
    public function scopeForCurrentUser($query, $userId = null) {
        $uid = $userId ?? auth()->id();
        return $query->where('created_by', $uid);
    }
}
