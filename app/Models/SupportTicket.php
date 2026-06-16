<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    protected $table = 'support_tickets';
    protected $fillable = ['ticket_number','user_id','subject','description','priority','category','status','assigned_to','resolved_at'];
    protected $casts = ['resolved_at'=>'datetime'];

    public function user() { return $this->belongsTo(User::class); }
    public function assignedTo() { return $this->belongsTo(User::class, 'assigned_to'); }
    public function replies() { return $this->hasMany(TicketReply::class, 'ticket_id'); }

    public function scopeOpen($q) { return $q->whereIn('status', ['open', 'in_progress']); }
    public function scopeClosed($q) { return $q->where('status', 'closed'); }
}
