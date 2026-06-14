<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriptionPlan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'description',
        'price_monthly', 'price_yearly', 'currency',
        'max_users', 'max_products', 'max_locations',
        'features', 'is_active', 'is_featured',
        'sort_order', 'badge_color',
    ];

    protected $casts = [
        'features'    => 'array',
        'is_active'   => 'boolean',
        'is_featured' => 'boolean',
        'price_monthly' => 'decimal:2',
        'price_yearly'  => 'decimal:2',
    ];

    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }

    public function activeSubscriptions()
    {
        return $this->subscriptions()->where('status', 'active');
    }
}
