<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\CrmActivity;
use App\Models\Customer;
use Illuminate\Http\Request;

class CrmActivityApiController extends Controller {
    use UserIdTrait;

    public function index(Request $req) {
        $q = CrmActivity::with('customer:id,name,phone,email')
            ->whereHas('customer', fn($cq) => $cq->where('created_by', $this->userId()));
        if ($req->customer_id) $q->where('customer_id', $req->customer_id);
        if ($req->type) $q->where('type', $req->type);
        if ($req->status) $q->where('status', $req->status);
        if ($req->from) $q->whereDate('created_at', '>=', $req->from);
        if ($req->to) $q->whereDate('created_at', '<=', $req->to);
        if ($req->upcoming) $q->where('follow_up_date', '>=', now())->where('follow_up_date', '<=', now()->addDays(7));
        return response()->json($q->orderByDesc('created_at')->get());
    }

    public function store(Request $req) {
        $data = $req->validate([
            'customer_id' => 'required|exists:customers,id',
            'type' => 'in:call,email,meeting,note,task,sms,visit',
            'subject' => 'nullable|string|max:191',
            'description' => 'nullable|string',
            'follow_up_date' => 'nullable|date',
            'status' => 'in:pending,completed,cancelled',
        ]);
        $data['created_by'] = $this->userId();
        $activity = CrmActivity::create($data);
        // Update customer's last_contact_date
        Customer::where('id', $data['customer_id'])->update(['last_contact_date' => now()]);
        return response()->json($activity->load('customer'), 201);
    }

    public function show($id) {
        $activity = CrmActivity::with('customer')
            ->whereHas('customer', fn($cq) => $cq->where('created_by', $this->userId()))
            ->findOrFail($id);
        return response()->json($activity);
    }

    public function update(Request $req, $id) {
        $activity = CrmActivity::whereHas('customer', fn($cq) => $cq->where('created_by', $this->userId()))->findOrFail($id);
        $data = $req->validate([
            'type' => 'sometimes|in:call,email,meeting,note,task,sms,visit',
            'subject' => 'nullable|string|max:191',
            'description' => 'nullable|string',
            'follow_up_date' => 'nullable|date',
            'status' => 'sometimes|in:pending,completed,cancelled',
        ]);
        $activity->update($data);
        return response()->json($activity->load('customer'));
    }

    public function destroy($id) {
        $activity = CrmActivity::whereHas('customer', fn($cq) => $cq->where('created_by', $this->userId()))->findOrFail($id);
        $activity->delete();
        return response()->json(['message' => 'Activity deleted']);
    }

    public function dashboard(Request $req) {
        $uid = $this->userId();
        $today = now()->toDateString();
        $weekLater = now()->addDays(7)->toDateString();

        $totalActivities = CrmActivity::whereHas('customer', fn($cq) => $cq->where('created_by', $uid))->count();
        $pendingFollowUps = CrmActivity::whereHas('customer', fn($cq) => $cq->where('created_by', $uid))
            ->where('status', 'pending')
            ->whereBetween('follow_up_date', [$today, $weekLater])
            ->count();
        $overdueTasks = CrmActivity::whereHas('customer', fn($cq) => $cq->where('created_by', $uid))
            ->where('status', 'pending')
            ->where('follow_up_date', '<', $today)
            ->count();
        $recentInteractions = CrmActivity::whereHas('customer', fn($cq) => $cq->where('created_by', $uid))
            ->whereDate('created_at', $today)
            ->count();

        $activitiesByType = CrmActivity::whereHas('customer', fn($cq) => $cq->where('created_by', $uid))
            ->selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type');

        $upcomingFollowUps = CrmActivity::with('customer:id,name,phone')
            ->whereHas('customer', fn($cq) => $cq->where('created_by', $uid))
            ->where('status', 'pending')
            ->where('follow_up_date', '>=', now())
            ->where('follow_up_date', '<=', now()->addDays(7))
            ->orderBy('follow_up_date')
            ->limit(10)
            ->get();

        return response()->json([
            'total_activities' => $totalActivities,
            'pending_followups' => $pendingFollowUps,
            'overdue_tasks' => $overdueTasks,
            'recent_interactions' => $recentInteractions,
            'activities_by_type' => $activitiesByType,
            'upcoming_followups' => $upcomingFollowUps,
        ]);
    }
}
