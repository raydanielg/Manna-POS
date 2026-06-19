<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanProduct extends Model
{
    protected $fillable = [
        'user_id', 'name', 'description', 'min_amount', 'max_amount',
        'interest_rate', 'interest_type', 'duration_min', 'duration_max', 'status'
    ];

    protected $casts = [
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }
}
