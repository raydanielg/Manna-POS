<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProductBatch extends Model {
    protected $fillable = ['product_id','purchase_id','supplier_id','batch_number','quantity','unit_cost','expiry_date','manufacture_date','status'];
    protected $casts = ['expiry_date' => 'date', 'manufacture_date' => 'date'];
    public function product()  { return $this->belongsTo(Product::class); }
    public function purchase()   { return $this->belongsTo(Purchase::class); }
    public function supplier() { return $this->belongsTo(Supplier::class); }

    public function scopeExpiringSoon($q, $days = 30) {
        return $q->where('expiry_date', '<=', now()->addDays($days))
                   ->where('expiry_date', '>=', now())
                   ->where('status', 'active');
    }
    public function scopeExpired($q) {
        return $q->where('expiry_date', '<', now())->where('status', 'active');
    }
}
