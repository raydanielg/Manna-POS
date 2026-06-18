<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ExpenseCategory extends Model {
    protected $fillable = ['created_by','name','description'];
    public function expenses() { return $this->hasMany(Expense::class); }
    public function scopeForCurrentUser($query, $userId = null) {
        $uid = $userId ?? auth()->id();
        return $query->where('created_by', $uid);
    }
}
