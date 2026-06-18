<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ProductVariation extends Model {
    protected $fillable = ['created_by','product_id','attribute_name','attribute_value','additional_price','sku','stock_quantity','status'];
    public function product() { return $this->belongsTo(Product::class); }
    public function scopeForCurrentUser($query, $userId = null) {
        $uid = $userId ?? auth()->id();
        return $query->where('created_by', $uid);
    }
}
