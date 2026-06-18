<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Sale extends Model {
    protected $fillable = ['created_by','reference','customer_id','sale_date','subtotal','discount','tax','total','paid','payment_status','payment_method','status','notes'];
    protected $casts = ['sale_date' => 'date'];
    public function customer() { return $this->belongsTo(Customer::class); }
    public function items()    { return $this->hasMany(SaleItem::class); }
    public function scopeForCurrentUser($query, $userId = null) {
        $uid = $userId ?? auth()->id();
        return $query->where('created_by', $uid);
    }
}
