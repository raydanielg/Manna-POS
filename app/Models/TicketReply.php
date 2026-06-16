<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TicketReply extends Model
{
    protected $table = 'ticket_replies';
    protected $fillable = ['ticket_id','user_id','message','attachments'];
    protected $casts = ['attachments'=>'array'];

    public function ticket() { return $this->belongsTo(SupportTicket::class, 'ticket_id'); }
    public function user() { return $this->belongsTo(User::class); }
}
