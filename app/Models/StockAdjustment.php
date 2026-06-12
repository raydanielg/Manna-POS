<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class StockAdjustment extends Model {
    protected $fillable = ['reference','adjustment_date','type','product_id','quantity','unit_cost','reason','notes'];
    protected $casts = ['adjustment_date' => 'date'];
    public function product() { return $this->belongsTo(Product::class); }
}
