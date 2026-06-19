<?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\ApprovalRequest;
use Illuminate\Http\Request;

class ApprovalRequestController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $bizId = $this->currentBusinessId();

        if ($user->isOwner()) {
            $requests = ApprovalRequest::forBusiness($bizId)->latest()->paginate(20);
        } else {
            $requests = ApprovalRequest::forBusiness($bizId)->forUser($user->id)->latest()->paginate(20);
        }

        return view('dashboard.approvals.index', compact('requests'));
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'module' => 'required|string|max:50',
            'action' => 'required|string|max:50',
            'reason' => 'required|string|max:500',
            'request_data' => 'nullable|array',
        ]);

        $data['user_id'] = auth()->id();
        $data['business_id'] = $this->currentBusinessId();
        $data['status'] = 'pending';

        ApprovalRequest::create($data);

        return response()->json(['success' => true, 'message' => 'Approval request submitted.']);
    }

    public function approve(Request $req, ApprovalRequest $approval)
    {
        $bizId = $this->currentBusinessId();
        if ($approval->business_id != $bizId) abort(403);
        if ($approval->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Already ' . $approval->status], 422);
        }

        $validated = $req->validate(['notes' => 'nullable|string|max:500']);

        $approval->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'review_notes' => $validated['notes'] ?? null,
            'reviewed_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Request approved.']);
    }

    public function reject(Request $req, ApprovalRequest $approval)
    {
        $bizId = $this->currentBusinessId();
        if ($approval->business_id != $bizId) abort(403);
        if ($approval->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Already ' . $approval->status], 422);
        }

        $validated = $req->validate(['notes' => 'required|string|max:500']);

        $approval->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'review_notes' => $validated['notes'],
            'reviewed_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Request rejected.']);
    }

    public function pendingCount()
    {
        $bizId = $this->currentBusinessId();
        $count = ApprovalRequest::forBusiness($bizId)->pending()->count();
        return response()->json(['count' => $count]);
    }
}
