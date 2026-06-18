<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SellingPriceGroup extends Model {
    protected $fillable = ['created_by','name','description','percentage','type','status'];
    public function scopeForCurrentUser($query, $userId = null) {
        $uid = $userId ?? auth()->id();
        return $query->where('created_by', $uid);
    }
}
