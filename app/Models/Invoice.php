<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'invoices';
    protected $fillable = ['invoice_number','user_id','subscription_id','billing_cycle','subtotal','tax','discount','total','currency','status','due_date','paid_at','notes'];
    protected $casts = ['subtotal'=>'decimal:2','tax'=>'decimal:2','discount'=>'decimal:2','total'=>'decimal:2','due_date'=>'datetime','paid_at'=>'datetime'];

    public function user() { return $this->belongsTo(User::class); }
    public function subscription() { return $this->belongsTo(UserSubscription::class, 'subscription_id'); }
    public function payments() { return $this->hasMany(Payment::class); }

    public function isPaid() { return $this->status === 'paid'; }
    public function isOverdue() { return $this->status === 'pending' && $this->due_date && $this->due_date->isPast(); }
}
