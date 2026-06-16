<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $table = 'announcements';
    protected $fillable = ['title','content','type','status','scheduled_at','expires_at','created_by'];
    protected $casts = ['scheduled_at'=>'datetime','expires_at'=>'datetime'];

    public function creator() { return $this->belongsTo(User::class, 'created_by'); }

    public function scopeActive($q) { return $q->where('status', 'published')->where(function($q) { $q->whereNull('expires_at')->orWhere('expires_at', '>', now()); }); }
}
