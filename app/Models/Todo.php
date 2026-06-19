<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    use HasFactory;

    protected $table = 'user_todos';

    protected $fillable = [
        'user_id', 'title', 'description', 'date',
        'priority', 'status', 'sort_order',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForUser($query, $userId = null)
    {
        return $query->where('user_id', $userId ?? auth()->id());
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeByDate($query, $date)
    {
        return $query->where('date', $date);
    }

    public function scopeByMonth($query, $year, $month)
    {
        return $query->whereYear('date', $year)->whereMonth('date', $month);
    }
}
