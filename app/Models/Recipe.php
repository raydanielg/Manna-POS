<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $fillable = [
        'user_id', 'name', 'description', 'product_id', 'output_quantity',
        'output_unit', 'labor_cost', 'overhead_cost', 'status'
    ];

    protected $casts = [
        'output_quantity' => 'decimal:4',
        'labor_cost' => 'decimal:2',
        'overhead_cost' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function items()
    {
        return $this->hasMany(RecipeItem::class)->with('product');
    }

    public function productionRuns()
    {
        return $this->hasMany(ProductionRun::class);
    }

    public function getTotalMaterialCostAttribute()
    {
        return $this->items->sum(function($i) {
            return ($i->cost ?? 0) * $i->quantity;
        });
    }
}
