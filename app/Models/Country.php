<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'phone_code', 'flag_emoji', 'region', 'is_active'];

    public function scopeEastAfrica($query)
    {
        return $query->where('region', 'East Africa')->where('is_active', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
