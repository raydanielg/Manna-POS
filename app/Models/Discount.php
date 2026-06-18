<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Discount extends Model {
    protected $fillable = ['created_by','name','amount','type','starts_at','ends_at','status'];
    protected $casts = ['starts_at' => 'date', 'ends_at' => 'date'];
    public function scopeForCurrentUser($query, $userId = null) {
        $uid = $userId ?? auth()->id();
        return $query->where('created_by', $uid);
    }
}
