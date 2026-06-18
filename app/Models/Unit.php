<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Unit extends Model {
    protected $fillable = ['created_by','name','short_name','allow_decimal'];
    public function products() { return $this->hasMany(Product::class); }
    public function scopeForCurrentUser($query, $userId = null) {
        $uid = $userId ?? auth()->id();
        return $query->where('created_by', $uid);
    }
}
