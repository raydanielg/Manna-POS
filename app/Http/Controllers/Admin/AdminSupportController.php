<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use Illuminate\Http\Request;

class AdminSupportController extends Controller
{
    public function tickets()
    {
        return view('admin.support.tickets');
    }

    public function ticketsList(Request $req)
    {
        $q = SupportTicket::with(['user:id,name,email', 'assignedTo:id,name']);
        if ($req->search) {
            $q->where(function($q) use ($req) {
                $q->where('subject','like',"%{$req->search}%")
                  ->orWhere('ticket_number','like',"%{$req->search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name','like',"%{$req->search}%"));
            });
        }
        if ($req->status) $q->where('status', $req->status);
        if ($req->priority) $q->where('priority', $req->priority);
        if ($req->category) $q->where('category', $req->category);
        return response()->json($q->latest()->get());
    }

    public function ticketsShow(SupportTicket $ticket)
    {
        return response()->json($ticket->load(['user', 'assignedTo', 'replies.user']));
    }

    public function ticketsUpdate(Request $req, SupportTicket $ticket)
    {
        $data = $req->validate([
            'status' => 'nullable|string|max:20',
            'priority' => 'nullable|string|max:20',
            'assigned_to' => 'nullable|exists:users,id',
            'category' => 'nullable|string|max:50',
        ]);
        if ($req->status === 'resolved' || $req->status === 'closed') {
            $data['resolved_at'] = now();
        }
        $ticket->update($data);
        return response()->json(['success'=>true,'ticket'=>$ticket]);
    }

    public function ticketsReply(Request $req, SupportTicket $ticket)
    {
        $data = $req->validate([
            'message' => 'required|string',
            'attachments' => 'nullable|array',
        ]);
        $data['user_id'] = auth()->id();
        $data['ticket_id'] = $ticket->id;
        $reply = TicketReply::create($data);
        $ticket->update(['status' => 'in_progress']);
        return response()->json(['success'=>true,'reply'=>$reply->load('user')], 201);
    }

    public function ticketsClose(SupportTicket $ticket)
    {
        $ticket->update(['status'=>'closed','resolved_at'=>now()]);
        return response()->json(['success'=>true]);
    }

    public function ticketsDestroy(SupportTicket $ticket)
    {
        $ticket->replies()->delete();
        $ticket->delete();
        return response()->json(['success'=>true]);
    }
}
