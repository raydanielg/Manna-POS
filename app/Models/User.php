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
        'name','email','password','role','phone',
        'business_name','business_type','business_address','business_city','business_country',
        'currency','tax_percentage','fiscal_year_start','owner_id',
    ];

    protected $hidden = ['password','remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'tax_percentage'    => 'decimal:2',
    ];

    public function staff() {
        return $this->hasMany(User::class, 'owner_id');
    }

    public function owner() {
        return $this->belongsTo(User::class, 'owner_id');
    }
}