<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BusinessCategory extends Model
{
    protected $table = 'business_categories';
    protected $fillable = ['name','slug','description','icon','is_active','sort_order'];
    protected $casts = ['is_active'=>'boolean'];

    public function businesses() { return $this->hasMany(Business::class); }
}
