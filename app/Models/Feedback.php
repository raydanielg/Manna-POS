<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $fillable = [
        'user_id', 'name', 'email', 'subject', 'type', 'priority',
        'message', 'status', 'admin_response', 'responded_by', 'responded_at'
    ];

    protected $casts = [
        'responded_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function responder()
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    public function replies()
    {
        return $this->hasMany(FeedbackReply::class)->orderBy('created_at');
    }

    public function scopeOpen($q)
    {
        return $q->whereIn('status', ['open', 'in_progress']);
    }

    public function scopeResolved($q)
    {
        return $q->whereIn('status', ['resolved', 'closed']);
    }

    public function scopeForUser($q, $userId)
    {
        return $q->where('user_id', $userId);
    }
}
