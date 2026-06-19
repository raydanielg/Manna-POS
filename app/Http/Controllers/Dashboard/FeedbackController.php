<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\FeedbackReply;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index()
    {
        $feedbacks = Feedback::forUser(auth()->id())
            ->latest()
            ->paginate(10);
        return view('dashboard.feedback.index', compact('feedbacks'));
    }

    public function create()
    {
        return view('dashboard.feedback.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:120',
            'email'   => 'required|email|max:120',
            'subject' => 'required|string|max:200',
            'type'    => 'required|in:feedback,complaint,feature_request,bug_report,general',
            'message' => 'required|string|min:10|max:5000',
        ]);

        $data['user_id'] = auth()->id();
        $data['priority']  = 'medium';
        $data['status']    = 'open';

        $feedback = Feedback::create($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Your message has been submitted successfully.',
                'feedback' => $feedback
            ]);
        }

        return redirect()->route('dashboard.feedback.index')
            ->with('success', 'Your message has been submitted successfully.');
    }

    public function show(Feedback $feedback)
    {
        if ($feedback->user_id !== auth()->id()) {
            abort(403);
        }

        $feedback->load(['replies.user']);
        return view('dashboard.feedback.show', compact('feedback'));
    }

    public function reply(Request $request, Feedback $feedback)
    {
        if ($feedback->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate(['message' => 'required|string|min:2|max:2000']);

        FeedbackReply::create([
            'feedback_id'  => $feedback->id,
            'user_id'      => auth()->id(),
            'message'      => $request->message,
            'sender_type'  => 'customer',
        ]);

        $feedback->update(['status' => 'open']);

        return redirect()->route('dashboard.feedback.show', $feedback)
            ->with('success', 'Reply sent.');
    }
}
