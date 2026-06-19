<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Todo;
use App\Models\CrmActivity;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('dashboard.calendar');
    }

    public function list(Request $req)
    {
        $user = auth()->user();
        $date = $req->date;
        $status = $req->status;

        $query = Todo::forUser()->orderBy('sort_order')->orderBy('created_at', 'desc');

        if ($date) {
            $query->where('date', $date);
        }
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        $todos = $query->get();

        // Also get CRM activities for the calendar
        $activities = CrmActivity::forCurrentUser()
            ->whereDate('follow_up_date', $date ?? now()->toDateString())
            ->get()
            ->map(function ($a) {
                return [
                    'id' => $a->id,
                    'title' => $a->subject,
                    'description' => $a->description,
                    'date' => $a->follow_up_date ? $a->follow_up_date->format('Y-m-d') : null,
                    'priority' => 'medium',
                    'status' => $a->status === 'completed' ? 'completed' : 'pending',
                    'type' => 'crm',
                    'crm_type' => $a->type,
                ];
            });

        return response()->json([
            'todos' => $todos,
            'activities' => $activities,
        ]);
    }

    public function calendarData(Request $req)
    {
        $user = auth()->user();
        $year = $req->year ?? now()->year;
        $month = $req->month ?? now()->month;

        $todos = Todo::forUser()->byMonth($year, $month)->get();
        $activities = CrmActivity::forCurrentUser()
            ->whereYear('follow_up_date', $year)
            ->whereMonth('follow_up_date', $month)
            ->get();

        // Build date map: which dates have items
        $dates = [];
        foreach ($todos as $t) {
            if ($t->date) {
                $d = $t->date->format('Y-m-d');
                if (!isset($dates[$d])) $dates[$d] = ['todos' => 0, 'activities' => 0, 'total' => 0];
                $dates[$d]['todos']++;
                $dates[$d]['total']++;
            }
        }
        foreach ($activities as $a) {
            if ($a->follow_up_date) {
                $d = $a->follow_up_date->format('Y-m-d');
                if (!isset($dates[$d])) $dates[$d] = ['todos' => 0, 'activities' => 0, 'total' => 0];
                $dates[$d]['activities']++;
                $dates[$d]['total']++;
            }
        }

        return response()->json([
            'dates' => $dates,
            'total_todos' => $todos->count(),
            'pending_todos' => $todos->where('status', 'pending')->count(),
            'completed_todos' => $todos->where('status', 'completed')->count(),
        ]);
    }

    public function store(Request $req)
    {
        $req->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'nullable|date',
            'priority' => 'in:low,medium,high',
        ]);

        $todo = Todo::create([
            'user_id' => auth()->id(),
            'title' => $req->title,
            'description' => $req->description,
            'date' => $req->date ?? now()->toDateString(),
            'priority' => $req->priority ?? 'medium',
            'status' => 'pending',
            'sort_order' => Todo::forUser()->max('sort_order') + 1,
        ]);

        return response()->json(['success' => true, 'todo' => $todo]);
    }

    public function update(Request $req, Todo $todo)
    {
        if ($todo->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $req->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'nullable|date',
            'priority' => 'in:low,medium,high',
            'status' => 'in:pending,completed,cancelled',
        ]);

        $todo->update($req->only(['title', 'description', 'date', 'priority', 'status']));

        return response()->json(['success' => true, 'todo' => $todo]);
    }

    public function toggleStatus(Todo $todo)
    {
        if ($todo->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $todo->update([
            'status' => $todo->status === 'completed' ? 'pending' : 'completed',
        ]);

        return response()->json(['success' => true, 'todo' => $todo]);
    }

    public function updateSort(Request $req)
    {
        $req->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:user_todos,id',
            'items.*.sort_order' => 'required|integer',
        ]);

        foreach ($req->items as $item) {
            $todo = Todo::find($item['id']);
            if ($todo && $todo->user_id === auth()->id()) {
                $todo->update(['sort_order' => $item['sort_order']]);
            }
        }

        return response()->json(['success' => true]);
    }

    public function destroy(Todo $todo)
    {
        if ($todo->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $todo->delete();

        return response()->json(['success' => true]);
    }
}
