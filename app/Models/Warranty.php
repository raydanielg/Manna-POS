<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Warranty extends Model {
    protected $fillable = ['created_by','name','duration','duration_unit','description'];
    public function scopeForCurrentUser($query, $userId = null) {
        $uid = $userId ?? auth()->id();
        return $query->where('created_by', $uid);
    }
}
