<?php
namespace App\Models;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable {
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name','email','password','role','status','phone',
        'business_name','business_type','business_address','business_city','business_country',
        'currency','tax_percentage','fiscal_year_start','owner_id','block_reason','blocked_at',
        'pos_settings','setup_completed',
    ];

    protected $hidden = ['password','remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'tax_percentage'    => 'decimal:2',
        'setup_completed'   => 'boolean',
    ];

    public function subscriptions()
    {
        return $this->hasMany(\App\Models\UserSubscription::class);
    }

    public function activeSubscription()
    {
        return $this->subscriptions()
            ->whereIn('status', ['active', 'trial'])
            ->where(function($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->latest()
            ->first();
    }

    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription() !== null;
    }

    public function staff() {
        return $this->hasMany(User::class, 'owner_id');
    }

    public function owner() {
        return $this->belongsTo(User::class, 'owner_id');
    }
}