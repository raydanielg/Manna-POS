<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsCampaign extends Model
{
    protected $fillable = [
        'user_id', 'name', 'message', 'status', 'recipient_count',
        'sent_count', 'failed_count', 'scheduled_at', 'sent_at'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function recipients()
    {
        return $this->hasMany(SmsCampaignRecipient::class);
    }
}
