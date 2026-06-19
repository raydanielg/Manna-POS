<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'symbol', 'country', 'is_active'];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
