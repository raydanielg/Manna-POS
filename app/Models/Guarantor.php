<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guarantor extends Model
{
    protected $fillable = [
        'user_id', 'loan_id', 'name', 'phone', 'email', 'id_number',
        'address', 'relationship', 'pledged_amount', 'notes'
    ];

    protected $casts = [
        'pledged_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}
