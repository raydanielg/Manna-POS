<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Purchase extends Model {
    protected $fillable = ['created_by','reference','supplier_id','purchase_date','subtotal','discount','tax','shipping','total','payment_status','status','notes'];
    protected $casts = ['purchase_date' => 'date'];
    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function items()    { return $this->hasMany(PurchaseItem::class); }
    public function scopeForCurrentUser($query, $userId = null) {
        $uid = $userId ?? auth()->id();
        return $query->where('created_by', $uid);
    }
}
