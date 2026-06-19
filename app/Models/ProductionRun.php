<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionRun extends Model
{
    protected $fillable = [
        'user_id', 'recipe_id', 'batch_number', 'planned_quantity',
        'actual_quantity', 'start_date', 'end_date', 'total_cost', 'status', 'notes'
    ];

    protected $casts = [
        'planned_quantity' => 'decimal:4',
        'actual_quantity' => 'decimal:4',
        'total_cost' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function recipe()
    {
        return $this->belongsTo(Recipe::class)->with('product');
    }

    public function usages()
    {
        return $this->hasMany(ProductionUsage::class)->with('product');
    }
}
