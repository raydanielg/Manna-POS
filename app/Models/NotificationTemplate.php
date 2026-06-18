<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class NotificationTemplate extends Model {
    protected $fillable = ['created_by','type','subject','body','is_active'];
    public function scopeForCurrentUser($query, $userId = null) {
        $uid = $userId ?? auth()->id();
        return $query->where('created_by', $uid);
    }
}
