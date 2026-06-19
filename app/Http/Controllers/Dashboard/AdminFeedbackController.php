<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\FeedbackReply;
use Illuminate\Http\Request;

class AdminFeedbackController extends Controller
{
    public function index()
    {
        $feedbacks = Feedback::with('user')
            ->latest()
            ->when(request('status'), function ($q, $status) {
                if ($status === 'open') return $q->open();
                if ($status === 'resolved') return $q->resolved();
            })
            ->when(request('type'), function ($q, $type) {
                return $q->where('type', $type);
            })
            ->paginate(15);

        $stats = [
            'total'     => Feedback::count(),
            'open'      => Feedback::open()->count(),
            'resolved'  => Feedback::resolved()->count(),
            'high'      => Feedback::where('priority', 'high')->open()->count(),
        ];

        return view('dashboard.feedback.admin-index', compact('feedbacks', 'stats'));
    }

    public function show(Feedback $feedback)
    {
        $feedback->load(['replies.user', 'user', 'responder']);
        return view('dashboard.feedback.admin-show', compact('feedback'));
    }

    public function reply(Request $request, Feedback $feedback)
    {
        $request->validate(['message' => 'required|string|min:2|max:2000']);

        FeedbackReply::create([
            'feedback_id'  => $feedback->id,
            'user_id'      => auth()->id(),
            'message'      => $request->message,
            'sender_type'  => 'admin',
        ]);

        $feedback->update([
            'status'       => 'in_progress',
            'responded_by' => auth()->id(),
            'responded_at' => now(),
        ]);

        return redirect()->route('dashboard.feedback.admin.show', $feedback)
            ->with('success', 'Reply sent.');
    }

    public function updateStatus(Request $request, Feedback $feedback)
    {
        $request->validate(['status' => 'required|in:open,in_progress,resolved,closed']);
        $feedback->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Status updated.');
    }

    public function updatePriority(Request $request, Feedback $feedback)
    {
        $request->validate(['priority' => 'required|in:low,medium,high']);
        $feedback->update(['priority' => $request->priority]);

        return redirect()->back()->with('success', 'Priority updated.');
    }
}
