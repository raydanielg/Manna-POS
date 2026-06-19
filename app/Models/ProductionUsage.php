<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionUsage extends Model
{
    protected $fillable = [
        'production_run_id', 'product_id', 'planned_quantity', 'actual_quantity', 'unit_cost'
    ];

    protected $casts = [
        'planned_quantity' => 'decimal:4',
        'actual_quantity' => 'decimal:4',
        'unit_cost' => 'decimal:2',
    ];

    public function productionRun()
    {
        return $this->belongsTo(ProductionRun::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
