<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\CrmActivity;
use Illuminate\Http\Request;

class CrmActivityController extends Controller
{
    public function index(Request $req)
    {
        $q = CrmActivity::forCurrentUser($this->currentBusinessId())->with('customer:id,name,phone');

        if ($req->search) {
            $q->whereHas('customer', fn($c) => $c->where('name', 'like', "%{$req->search}%"));
        }
        if ($req->type) {
            $q->where('type', $req->type);
        }
        if ($req->status) {
            $q->where('status', $req->status);
        }

        return response()->json($q->latest()->get());
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'customer_id' => 'required|exists:customers,id',
            'type' => 'required|in:call,email,meeting,note,task,sms,visit',
            'status' => 'required|in:pending,completed,cancelled',
            'subject' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'follow_up_date' => 'nullable|date',
        ]);

        $data['created_by'] = $this->currentBusinessId();

        $activity = CrmActivity::create($data);
        $activity->load('customer:id,name,phone');

        return response()->json($activity, 201);
    }
}
