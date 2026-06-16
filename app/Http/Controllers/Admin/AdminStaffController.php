<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\StaffAttendance;
use App\Models\StaffSchedule;
use Illuminate\Http\Request;

class AdminStaffController extends Controller
{
    public function index()
    {
        return view('admin.staff.index');
    }

    public function list(Request $req)
    {
        $q = Staff::query();
        if ($req->search) {
            $q->where(function($q) use ($req) {
                $q->where('first_name','like',"%{$req->search}%")
                  ->orWhere('last_name','like',"%{$req->search}%")
                  ->orWhere('email','like',"%{$req->search}%")
                  ->orWhere('phone','like',"%{$req->search}%");
            });
        }
        if ($req->status) $q->where('status', $req->status);
        if ($req->department) $q->where('department', $req->department);
        return response()->json($q->with('user:id,name')->latest()->get()->map(fn($s) => [
            'id' => $s->id, 'first_name' => $s->first_name, 'last_name' => $s->last_name,
            'full_name' => $s->full_name, 'email' => $s->email, 'phone' => $s->phone,
            'department' => $s->department, 'position' => $s->position,
            'salary' => number_format($s->salary, 2), 'status' => $s->status,
            'hire_date' => $s->hire_date ? $s->hire_date->format('Y-m-d') : null,
        ]));
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'first_name'  => 'required|string|max:191',
            'last_name'   => 'required|string|max:191',
            'email'       => 'required|email|unique:staff,email',
            'phone'       => 'nullable|string|max:20',
            'department'  => 'nullable|string|max:100',
            'position'    => 'nullable|string|max:100',
            'salary'      => 'nullable|numeric|min:0',
            'pay_type'    => 'nullable|string|max:20',
            'hire_date'   => 'nullable|date',
            'status'      => 'nullable|string|max:20',
            'address'     => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:100',
            'emergency_phone'   => 'nullable|string|max:20',
        ]);
        $data['user_id'] = auth()->id();
        $staff = Staff::create($data);
        return response()->json(['success'=>true,'staff'=>$staff], 201);
    }

    public function show(Staff $staff)
    {
        return response()->json($staff->load('schedules'));
    }

    public function update(Request $req, Staff $staff)
    {
        $data = $req->validate([
            'first_name'  => 'required|string|max:191',
            'last_name'   => 'required|string|max:191',
            'email'       => "required|email|unique:staff,email,{$staff->id}",
            'phone'       => 'nullable|string|max:20',
            'department'  => 'nullable|string|max:100',
            'position'    => 'nullable|string|max:100',
            'salary'      => 'nullable|numeric|min:0',
            'pay_type'    => 'nullable|string|max:20',
            'hire_date'   => 'nullable|date',
            'status'      => 'nullable|string|max:20',
            'address'     => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:100',
            'emergency_phone'   => 'nullable|string|max:20',
        ]);
        $staff->update($data);
        return response()->json(['success'=>true,'staff'=>$staff]);
    }

    public function destroy(Staff $staff)
    {
        $staff->delete();
        return response()->json(['success'=>true,'message'=>'Staff deleted']);
    }

    public function attendance()
    {
        return view('admin.staff.attendance');
    }

    public function attendanceList(Request $req)
    {
        $q = StaffAttendance::with('staff');
        if ($req->date) $q->where('date', $req->date);
        if ($req->staff_id) $q->where('staff_id', $req->staff_id);
        if ($req->status) $q->where('status', $req->status);
        return response()->json($q->latest()->get());
    }

    public function attendanceStore(Request $req)
    {
        $data = $req->validate([
            'staff_id' => 'required|exists:staff,id',
            'date'     => 'required|date',
            'clock_in' => 'nullable',
            'clock_out'=> 'nullable',
            'status'   => 'required|string|max:20',
            'notes'    => 'nullable|string',
        ]);
        $att = StaffAttendance::updateOrCreate(
            ['staff_id' => $data['staff_id'], 'date' => $data['date']],
            $data
        );
        return response()->json(['success'=>true,'attendance'=>$att], 201);
    }

    public function attendanceDestroy(StaffAttendance $attendance)
    {
        $attendance->delete();
        return response()->json(['success'=>true]);
    }

    public function schedules()
    {
        return view('admin.staff.schedules');
    }

    public function schedulesList(Request $req)
    {
        $q = StaffSchedule::with('staff');
        if ($req->staff_id) $q->where('staff_id', $req->staff_id);
        return response()->json($q->get());
    }

    public function schedulesStore(Request $req)
    {
        $data = $req->validate([
            'staff_id'      => 'required|exists:staff,id',
            'day_of_week'   => 'required|string|max:10',
            'start_time'    => 'required',
            'end_time'      => 'required',
            'is_working_day'=> 'nullable|boolean',
        ]);
        $data['is_working_day'] = $data['is_working_day'] ?? true;
        $schedule = StaffSchedule::updateOrCreate(
            ['staff_id' => $data['staff_id'], 'day_of_week' => $data['day_of_week']],
            $data
        );
        return response()->json(['success'=>true,'schedule'=>$schedule], 201);
    }

    public function schedulesDestroy(StaffSchedule $schedule)
    {
        $schedule->delete();
        return response()->json(['success'=>true]);
    }
}
