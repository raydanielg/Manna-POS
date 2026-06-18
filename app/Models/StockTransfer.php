<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class StockTransfer extends Model {
    protected $fillable = ['created_by','reference','from_location','to_location','transfer_date','status','notes'];
    protected $casts = ['transfer_date' => 'date'];
    public function scopeForCurrentUser($query, $userId = null) {
        $uid = $userId ?? auth()->id();
        return $query->where('created_by', $uid);
    }
}
