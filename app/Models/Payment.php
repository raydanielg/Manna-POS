<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';
    protected $fillable = ['invoice_id','user_id','amount','currency','payment_method','transaction_id','gateway','status','notes','paid_at'];
    protected $casts = ['amount'=>'decimal:2','paid_at'=>'datetime'];

    public function invoice() { return $this->belongsTo(Invoice::class); }
    public function user() { return $this->belongsTo(User::class); }
}
