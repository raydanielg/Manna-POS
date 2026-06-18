<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class BusinessLocation extends Model {
    protected $fillable = ['created_by','name','address','city','country','phone','status'];
    public function scopeForCurrentUser($query, $userId = null) {
        $uid = $userId ?? auth()->id();
        return $query->where('created_by', $uid);
    }
}
