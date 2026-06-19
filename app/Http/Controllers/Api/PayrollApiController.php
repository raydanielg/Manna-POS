<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PayrollPeriod;
use App\Models\PayrollEntry;
use App\Models\PayrollDeductionType;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayrollApiController extends Controller
{
    use UserIdTrait;

    private function uid() { return $this->userId(); }

    public function dashboard()
    {
        $uid = $this->uid();
        $periods = PayrollPeriod::where('user_id', $uid)->latest()->take(6)->get();
        $currentPeriod = PayrollPeriod::where('user_id', $uid)->where('status', 'open')->latest()->first();

        $stats = [
            'total_staff' => Staff::where('user_id', $uid)->count(),
            'total_paid' => PayrollEntry::where('user_id', $uid)->where('status', 'paid')->sum('net_salary'),
            'pending_payroll' => PayrollPeriod::where('user_id', $uid)->where('status', 'open')->count(),
            'total_entries' => PayrollEntry::where('user_id', $uid)->count(),
        ];

        $recentEntries = PayrollEntry::where('user_id', $uid)
            ->with('staff:id,name', 'period:id,name')
            ->latest()->take(5)->get();

        return response()->json([
            'stats' => $stats,
            'periods' => $periods,
            'current_period' => $currentPeriod,
            'recent_entries' => $recentEntries,
        ]);
    }

    public function periods()
    {
        $periods = PayrollPeriod::where('user_id', $this->uid())->latest()->get();
        return response()->json($periods);
    }

    public function storePeriod(Request $req)
    {
        $data = $req->validate([
            'name' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'pay_date' => 'nullable|date',
        ]);
        $data['user_id'] = $this->uid();
        $data['status'] = 'open';
        $period = PayrollPeriod::create($data);
        return response()->json(['success' => true, 'message' => 'Payroll period created', 'period' => $period]);
    }

    public function showPeriod($id)
    {
        $period = PayrollPeriod::where('user_id', $this->uid())->where('id', $id)->firstOrFail();
        $period->load(['entries.staff:id,name', 'entries.deductions']);
        $staffList = Staff::where('user_id', $this->uid())->get(['id', 'name']);
        $deductions = PayrollDeductionType::where('user_id', $this->uid())->where('is_active', true)->get();

        $summary = [
            'total_basic' => $period->entries->sum('basic_salary'),
            'total_gross' => $period->entries->sum('gross_salary'),
            'total_deductions' => $period->entries->sum('total_deduction'),
            'total_net' => $period->entries->sum('net_salary'),
            'staff_count' => $period->entries->count(),
        ];

        return response()->json([
            'period' => $period,
            'staff_list' => $staffList,
            'deductions' => $deductions,
            'summary' => $summary,
        ]);
    }

    public function storeEntry(Request $req, $periodId)
    {
        $period = PayrollPeriod::where('user_id', $this->uid())->where('id', $periodId)->firstOrFail();
        $data = $req->validate([
            'staff_id' => 'required|exists:staff,id',
            'basic_salary' => 'required|numeric|min:0',
            'overtime_hours' => 'nullable|numeric|min:0',
            'overtime_rate' => 'nullable|numeric|min:0',
            'bonus' => 'nullable|numeric|min:0',
            'allowance' => 'nullable|numeric|min:0',
            'tax_deduction' => 'nullable|numeric|min:0',
            'nssf_deduction' => 'nullable|numeric|min:0',
            'nhif_deduction' => 'nullable|numeric|min:0',
            'loan_deduction' => 'nullable|numeric|min:0',
            'other_deduction' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $data['user_id'] = $this->uid();
        $data['payroll_period_id'] = $period->id;
        $ot = ($data['overtime_hours'] ?? 0) * ($data['overtime_rate'] ?? 0);
        $data['overtime_amount'] = $ot;
        $data['gross_salary'] = $data['basic_salary'] + $ot + ($data['bonus'] ?? 0) + ($data['allowance'] ?? 0);
        $totalDed = ($data['tax_deduction'] ?? 0) + ($data['nssf_deduction'] ?? 0) + ($data['nhif_deduction'] ?? 0) + ($data['loan_deduction'] ?? 0) + ($data['other_deduction'] ?? 0);
        $data['total_deduction'] = $totalDed;
        $data['net_salary'] = $data['gross_salary'] - $totalDed;
        $data['status'] = 'draft';

        try {
            DB::beginTransaction();
            $entry = PayrollEntry::create($data);
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Payroll entry added', 'entry' => $entry]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to add entry'], 500);
        }
    }

    public function updateEntry(Request $req, $id)
    {
        $entry = PayrollEntry::where('user_id', $this->uid())->where('id', $id)->firstOrFail();
        $data = $req->validate([
            'basic_salary' => 'required|numeric|min:0',
            'overtime_hours' => 'nullable|numeric|min:0',
            'overtime_rate' => 'nullable|numeric|min:0',
            'bonus' => 'nullable|numeric|min:0',
            'allowance' => 'nullable|numeric|min:0',
            'tax_deduction' => 'nullable|numeric|min:0',
            'nssf_deduction' => 'nullable|numeric|min:0',
            'nhif_deduction' => 'nullable|numeric|min:0',
            'loan_deduction' => 'nullable|numeric|min:0',
            'other_deduction' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $ot = ($data['overtime_hours'] ?? 0) * ($data['overtime_rate'] ?? 0);
        $data['overtime_amount'] = $ot;
        $data['gross_salary'] = $data['basic_salary'] + $ot + ($data['bonus'] ?? 0) + ($data['allowance'] ?? 0);
        $totalDed = ($data['tax_deduction'] ?? 0) + ($data['nssf_deduction'] ?? 0) + ($data['nhif_deduction'] ?? 0) + ($data['loan_deduction'] ?? 0) + ($data['other_deduction'] ?? 0);
        $data['total_deduction'] = $totalDed;
        $data['net_salary'] = $data['gross_salary'] - $totalDed;

        $entry->update($data);
        return response()->json(['success' => true, 'message' => 'Payroll entry updated', 'entry' => $entry]);
    }

    public function destroyEntry($id)
    {
        $entry = PayrollEntry::where('user_id', $this->uid())->where('id', $id)->firstOrFail();
        $entry->delete();
        return response()->json(['success' => true, 'message' => 'Entry removed']);
    }

    public function updateEntryStatus(Request $req, $id)
    {
        $entry = PayrollEntry::where('user_id', $this->uid())->where('id', $id)->firstOrFail();
        $entry->update(['status' => $req->input('status')]);
        return response()->json(['success' => true, 'message' => 'Entry updated to ' . ucfirst($req->input('status')), 'entry' => $entry]);
    }

    public function deductions()
    {
        $types = PayrollDeductionType::where('user_id', $this->uid())->latest()->get();
        return response()->json($types);
    }
}
