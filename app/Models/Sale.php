<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Sale extends Model {
    protected $fillable = ['reference','customer_id','sale_date','subtotal','discount','tax','total','paid','payment_status','payment_method','status','notes'];
    protected $casts = ['sale_date' => 'date'];
    public function customer() { return $this->belongsTo(Customer::class); }
    public function items()    { return $this->hasMany(SaleItem::class); }
}
