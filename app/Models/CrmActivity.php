<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CrmActivity extends Model {
    protected $fillable = ['customer_id','created_by','type','subject','description','follow_up_date','status'];
    protected $casts = ['follow_up_date' => 'datetime'];
    public function customer() { return $this->belongsTo(Customer::class); }
    public function creator() { return $this->belongsTo(\App\Models\User::class, 'created_by'); }
    public function scopeForCurrentUser($query, $userId = null) {
        $uid = $userId ?? auth()->id();
        return $query->where('created_by', $uid);
    }
}
