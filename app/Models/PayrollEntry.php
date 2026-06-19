<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollEntry extends Model
{
    protected $fillable = [
        'user_id', 'payroll_period_id', 'staff_id', 'basic_salary', 'overtime_hours',
        'overtime_rate', 'overtime_amount', 'bonus', 'allowance', 'gross_salary',
        'tax_deduction', 'nssf_deduction', 'nhif_deduction', 'loan_deduction',
        'other_deduction', 'total_deduction', 'net_salary', 'notes', 'status'
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'overtime_rate' => 'decimal:2',
        'overtime_amount' => 'decimal:2',
        'bonus' => 'decimal:2',
        'allowance' => 'decimal:2',
        'gross_salary' => 'decimal:2',
        'tax_deduction' => 'decimal:2',
        'nssf_deduction' => 'decimal:2',
        'nhif_deduction' => 'decimal:2',
        'loan_deduction' => 'decimal:2',
        'other_deduction' => 'decimal:2',
        'total_deduction' => 'decimal:2',
        'net_salary' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function period()
    {
        return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id');
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
