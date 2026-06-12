<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Customer extends Model {
    protected $fillable = ['name','email','phone','address','city','country','customer_group_id','loyalty_points','balance','credit_limit','status','notes'];
    public function group() { return $this->belongsTo(CustomerGroup::class,'customer_group_id'); }
    public function sales() { return $this->hasMany(Sale::class); }
}
