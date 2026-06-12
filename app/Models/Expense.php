<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Expense extends Model {
    protected $fillable = ['expense_category_id','reference','expense_date','amount','payment_method','notes','created_by'];
    protected $casts = ['expense_date' => 'date'];
    public function category() { return $this->belongsTo(ExpenseCategory::class,'expense_category_id'); }
}
