<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BusinessLocation extends Model {
    protected $fillable = ['owner_id','name','code','address','city','phone','email','is_active','notes'];
    protected $casts = ['is_active' => 'boolean'];

    public function owner() { return $this->belongsTo(User::class, 'owner_id'); }
    public function staff() { return $this->hasMany(User::class, 'location_id'); }
}
