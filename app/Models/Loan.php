<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = [
        'user_id', 'loan_product_id', 'loan_number', 'customer_id', 'principal_amount',
        'interest_rate', 'interest_type', 'duration_months', 'total_interest',
        'total_amount', 'paid_amount', 'balance', 'start_date', 'end_date',
        'status', 'purpose', 'notes', 'approved_by', 'approved_at'
    ];

    protected $casts = [
        'principal_amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'total_interest' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function loanProduct()
    {
        return $this->belongsTo(LoanProduct::class);
    }

    public function schedules()
    {
        return $this->hasMany(LoanSchedule::class)->orderBy('installment_number');
    }

    public function repayments()
    {
        return $this->hasMany(LoanRepayment::class);
    }

    public function guarantors()
    {
        return $this->hasMany(Guarantor::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
