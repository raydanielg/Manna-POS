<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CustomerGroup extends Model {
    protected $fillable = ['created_by','name','discount','description'];
    public function customers() { return $this->hasMany(Customer::class); }
    public function scopeForCurrentUser($query, $userId = null) {
        $uid = $userId ?? auth()->id();
        return $query->where('created_by', $uid);
    }
}
