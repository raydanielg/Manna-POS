<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\PayrollPeriod;
use App\Models\PayrollEntry;
use App\Models\PayrollDeductionType;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PayrollController extends Controller
{
    public function __construct() { $this->middleware('auth'); }

    private function jsonSuccess($message, $data = [])
    {
        return response()->json(array_merge(['success' => true, 'message' => $message], $data));
    }

    private function jsonError($message, $code = 422)
    {
        return response()->json(['success' => false, 'message' => $message], $code);
    }

    public function index()
    {
        $periods = PayrollPeriod::where('user_id', auth()->id())->latest()->take(6)->get();
        $currentPeriod = PayrollPeriod::where('user_id', auth()->id())->where('status', 'open')->latest()->first();

        $stats = [
            'total_staff' => Staff::where('user_id', auth()->id())->count(),
            'total_paid' => PayrollEntry::where('user_id', auth()->id())->where('status', 'paid')->sum('net_salary'),
            'pending_payroll' => PayrollPeriod::where('user_id', auth()->id())->where('status', 'open')->count(),
            'total_entries' => PayrollEntry::where('user_id', auth()->id())->count(),
        ];

        return view('dashboard.payroll.index', compact('periods', 'currentPeriod', 'stats'));
    }

    public function periods()
    {
        $periods = PayrollPeriod::where('user_id', auth()->id())->latest()->get();
        return view('dashboard.payroll.periods', compact('periods'));
    }

    public function storePeriod(Request $req)
    {
        $data = $req->validate([
            'name' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'pay_date' => 'nullable|date',
        ]);
        $data['user_id'] = auth()->id();
        $data['status'] = 'open';
        $period = PayrollPeriod::create($data);
        Log::info('Payroll period created', ['user_id' => auth()->id(), 'period_id' => $period->id]);
        if (request()->ajax() || request()->wantsJson()) {
            return $this->jsonSuccess('Payroll period created', ['period' => $period]);
        }
        return redirect()->route('dashboard.payroll.periods')->with('success', 'Payroll period created');
    }

    public function showPeriod(PayrollPeriod $period)
    {
        $this->guardPeriod($period);
        $period->load('entries.staff');
        $staffList = Staff::where('user_id', auth()->id())->get();
        $deductions = PayrollDeductionType::where('user_id', auth()->id())->where('is_active', true)->get();

        $summary = [
            'total_basic' => $period->entries->sum('basic_salary'),
            'total_gross' => $period->entries->sum('gross_salary'),
            'total_deductions' => $period->entries->sum('total_deduction'),
            'total_net' => $period->entries->sum('net_salary'),
            'staff_count' => $period->entries->count(),
        ];

        return view('dashboard.payroll.period-show', compact('period', 'staffList', 'deductions', 'summary'));
    }

    public function storeEntry(Request $req, PayrollPeriod $period)
    {
        $this->guardPeriod($period);
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

        $data['user_id'] = auth()->id();
        $data['payroll_period_id'] = $period->id;

        // Calculate
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
            Log::info('Payroll entry added', ['user_id' => auth()->id(), 'entry_id' => $entry->id, 'period_id' => $period->id]);
            if ($req->ajax() || $req->wantsJson()) {
                return $this->jsonSuccess('Payroll entry added', ['entry' => $entry]);
            }
            return redirect()->route('dashboard.payroll.period.show', $period)->with('success', 'Payroll entry added');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payroll entry failed', ['error' => $e->getMessage()]);
            if ($req->ajax() || $req->wantsJson()) {
                return $this->jsonError('Failed to add entry', 500);
            }
            return redirect()->route('dashboard.payroll.period.show', $period)->with('error', 'Failed to add entry');
        }
    }

    public function updateEntryStatus(Request $req, PayrollEntry $entry)
    {
        $this->guardEntry($entry);
        $entry->update(['status' => $req->status]);
        Log::info('Payroll entry status updated', ['user_id' => auth()->id(), 'entry_id' => $entry->id, 'status' => $req->status]);
        if ($req->ajax() || $req->wantsJson()) {
            return $this->jsonSuccess('Entry updated to ' . ucfirst($req->status), ['entry' => $entry]);
        }
        return redirect()->route('dashboard.payroll.period.show', $entry->payroll_period_id)->with('success', 'Entry updated');
    }

    public function destroyEntry(PayrollEntry $entry)
    {
        $this->guardEntry($entry);
        $periodId = $entry->payroll_period_id;
        $entry->delete();
        Log::info('Payroll entry deleted', ['user_id' => auth()->id(), 'entry_id' => $entry->id]);
        if (request()->ajax() || request()->wantsJson()) {
            return $this->jsonSuccess('Entry removed');
        }
        return redirect()->route('dashboard.payroll.period.show', $periodId)->with('success', 'Entry removed');
    }

    public function deductionTypes()
    {
        $types = PayrollDeductionType::where('user_id', auth()->id())->latest()->get();
        return view('dashboard.payroll.deductions', compact('types'));
    }

    public function storeDeductionType(Request $req)
    {
        $data = $req->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);
        $data['user_id'] = auth()->id();
        PayrollDeductionType::create($data);
        return redirect()->route('dashboard.payroll.deductions')->with('success', 'Deduction type added');
    }

    public function updateDeductionType(Request $req, PayrollDeductionType $type)
    {
        $this->guardType($type);
        $data = $req->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);
        $type->update($data);
        return redirect()->route('dashboard.payroll.deductions')->with('success', 'Deduction type updated');
    }

    public function destroyDeductionType(PayrollDeductionType $type)
    {
        $this->guardType($type);
        $type->delete();
        return redirect()->route('dashboard.payroll.deductions')->with('success', 'Deduction type removed');
    }

    public function payslip(PayrollEntry $entry)
    {
        $this->guardEntry($entry);
        $entry->load('staff', 'period');
        return view('dashboard.payroll.payslip', compact('entry'));
    }

    private function guardPeriod($period)
    {
        if ($period->user_id !== auth()->id()) abort(403);
    }

    private function guardEntry($entry)
    {
        if ($entry->user_id !== auth()->id()) abort(403);
    }

    private function guardType($type)
    {
        if ($type->user_id !== auth()->id()) abort(403);
    }
}
