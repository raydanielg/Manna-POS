<?php
namespace App\Models;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail {
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name','email','password','role','status','phone','avatar',
        'business_name','business_type','business_address','business_city','business_country','business_region',
        'currency','tax_percentage','tax_number','fiscal_year_start','owner_id','block_reason','blocked_at',
        'pos_settings','setup_completed','email_verified_at','role_id',
        'otp_code','otp_expires_at','activation_token','activation_token_expires_at',
    ];

    public function getAvatarUrlAttribute()
    {
        return $this->avatar ? asset('storage/avatars/'.$this->avatar) : null;
    }

    protected $hidden = ['password','remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'otp_expires_at' => 'datetime',
        'activation_token_expires_at' => 'datetime',
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

    public function roleRelation() {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function staff() {
        return $this->hasMany(User::class, 'owner_id');
    }

    public function owner() {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function permissions(): array {
        if ($this->isOwner()) return ['*']; // owners have all permissions
        return $this->roleRelation?->permissions ?? [];
    }

    public function hasPermission(string $permission): bool {
        if ($this->isOwner()) return true;
        $perms = $this->permissions();
        return in_array('*', $perms) || in_array($permission, $perms);
    }

    public function hasAnyPermission(array $permissions): bool {
        if ($this->isOwner()) return true;
        return !empty(array_intersect($permissions, $this->permissions()));
    }

    public function isOwner(): bool {
        return is_null($this->owner_id);
    }

    public function isStaff(): bool {
        return !is_null($this->owner_id);
    }

    /**
     * Return the business owner ID for data scoping.
     * If this user is staff (has owner_id), return owner's ID.
     * Otherwise return own ID.
     */
    public function businessId(): int
    {
        return $this->owner_id ?? $this->id;
    }
}