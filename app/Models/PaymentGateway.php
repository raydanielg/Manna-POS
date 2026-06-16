<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    protected $table = 'payment_gateways';
    protected $fillable = ['name','code','description','credentials','settings','is_active','is_default','sort_order'];
    protected $casts = ['credentials'=>'array','settings'=>'array','is_active'=>'boolean','is_default'=>'boolean'];

    public function scopeActive($q) { return $q->where('is_active', true); }
    public function scopeDefault($q) { return $q->where('is_default', true); }
}
