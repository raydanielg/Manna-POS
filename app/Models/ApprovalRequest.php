<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ApprovalRequest extends Model
{
    protected $fillable = [
        'user_id', 'business_id', 'module', 'action',
        'approvable_type', 'approvable_id', 'request_data',
        'reason', 'status', 'reviewed_by', 'review_notes', 'reviewed_at',
    ];

    protected $casts = [
        'request_data' => 'array',
        'reviewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function business()
    {
        return $this->belongsTo(User::class, 'business_id');
    }

    public function approvable()
    {
        return $this->morphTo();
    }

    public function scopePending($q)
    {
        return $q->where('status', 'pending');
    }

    public function scopeForBusiness($q, $businessId)
    {
        return $q->where('business_id', $businessId);
    }

    public function scopeForUser($q, $userId)
    {
        return $q->where('user_id', $userId);
    }
}
