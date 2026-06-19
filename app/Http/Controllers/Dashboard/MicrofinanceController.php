<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\LoanProduct;
use App\Models\Loan;
use App\Models\LoanSchedule;
use App\Models\LoanRepayment;
use App\Models\Guarantor;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MicrofinanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ================== DASHBOARD ==================
    public function index()
    {
        $user = auth()->user();
        $stats = [
            'total_loans' => Loan::where('user_id', $user->id)->count(),
            'active_loans' => Loan::where('user_id', $user->id)->whereIn('status', ['active', 'disbursed'])->count(),
            'pending_loans' => Loan::where('user_id', $user->id)->where('status', 'pending')->count(),
            'total_principal' => Loan::where('user_id', $user->id)->sum('principal_amount'),
            'total_repaid' => Loan::where('user_id', $user->id)->sum('paid_amount'),
            'total_outstanding' => Loan::where('user_id', $user->id)->sum('balance'),
            'overdue_schedules' => LoanSchedule::whereHas('loan', fn($q) => $q->where('user_id', $user->id))
                ->where('status', 'overdue')->count(),
        ];
        $recentLoans = Loan::where('user_id', $user->id)->with('customer')->latest()->take(5)->get();
        $overdueSchedules = LoanSchedule::whereHas('loan', fn($q) => $q->where('user_id', $user->id))
            ->where('status', 'overdue')->with('loan.customer')->orderBy('due_date')->take(5)->get();

        return view('dashboard.microfinance.index', compact('stats', 'recentLoans', 'overdueSchedules'));
    }

    // ================== LOAN PRODUCTS ==================
    public function products()
    {
        $products = LoanProduct::where('user_id', auth()->id())->latest()->get();
        return view('dashboard.microfinance.products', compact('products'));
    }

    public function storeProduct(Request $req)
    {
        $data = $req->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'required|numeric|min:0|gte:min_amount',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'interest_type' => 'required|in:flat,reducing_balance',
            'duration_min' => 'required|integer|min:1',
            'duration_max' => 'required|integer|min:1|gte:duration_min',
            'status' => 'required|in:active,inactive',
        ]);
        $data['user_id'] = auth()->id();
        LoanProduct::create($data);
        return redirect()->route('dashboard.microfinance.products')->with('success', 'Loan product created successfully');
    }

    public function updateProduct(Request $req, LoanProduct $product)
    {
        $this->authorizeAccess($product);
        $data = $req->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'required|numeric|min:0|gte:min_amount',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'interest_type' => 'required|in:flat,reducing_balance',
            'duration_min' => 'required|integer|min:1',
            'duration_max' => 'required|integer|min:1|gte:duration_min',
            'status' => 'required|in:active,inactive',
        ]);
        $product->update($data);
        return redirect()->route('dashboard.microfinance.products')->with('success', 'Loan product updated');
    }

    public function destroyProduct(LoanProduct $product)
    {
        $this->authorizeAccess($product);
        $product->delete();
        return redirect()->route('dashboard.microfinance.products')->with('success', 'Loan product deleted');
    }

    // ================== LOANS ==================
    public function loans()
    {
        $loans = Loan::where('user_id', auth()->id())->with('customer', 'loanProduct')->latest()->get();
        return view('dashboard.microfinance.loans', compact('loans'));
    }

    public function createLoan()
    {
        $products = LoanProduct::where('user_id', auth()->id())->where('status', 'active')->get();
        $customers = Customer::where('created_by', auth()->id())->orWhere('user_id', auth()->id())->get();
        return view('dashboard.microfinance.loan-create', compact('products', 'customers'));
    }

    public function storeLoan(Request $req)
    {
        $data = $req->validate([
            'loan_product_id' => 'nullable|exists:loan_products,id',
            'customer_id' => 'required|exists:customers,id',
            'principal_amount' => 'required|numeric|min:1',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'interest_type' => 'required|in:flat,reducing_balance',
            'duration_months' => 'required|integer|min:1|max:120',
            'purpose' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $user = auth()->user();
        $data['user_id'] = $user->id;
        $data['loan_number'] = 'LN-' . strtoupper(Str::random(8));
        $data['status'] = 'pending';

        // Calculate totals
        $principal = $data['principal_amount'];
        $rate = $data['interest_rate'] / 100;
        $months = $data['duration_months'];

        if ($data['interest_type'] === 'flat') {
            $data['total_interest'] = $principal * $rate * ($months / 12);
        } else {
            // Reducing balance - simple approximation
            $data['total_interest'] = $principal * $rate * ($months / 12) * 0.6;
        }
        $data['total_amount'] = $principal + $data['total_interest'];
        $data['balance'] = $data['total_amount'];

        $loan = Loan::create($data);

        // Generate schedule
        $this->generateSchedule($loan);

        return redirect()->route('dashboard.microfinance.loans')->with('success', 'Loan application submitted');
    }

    public function showLoan(Loan $loan)
    {
        $this->authorizeAccess($loan);
        $loan->load('customer', 'loanProduct', 'schedules', 'repayments', 'guarantors');
        return view('dashboard.microfinance.loan-show', compact('loan'));
    }

    public function updateLoanStatus(Request $req, Loan $loan)
    {
        $this->authorizeAccess($loan);
        $data = $req->validate(['status' => 'required|in:pending,approved,rejected,disbursed,active,completed,defaulted']);
        $loan->update($data);

        if ($req->status === 'disbursed') {
            $loan->update([
                'start_date' => now(),
                'end_date' => now()->addMonths($loan->duration_months),
                'status' => 'active'
            ]);
        }
        if ($req->status === 'approved') {
            $loan->update(['approved_by' => auth()->id(), 'approved_at' => now()]);
        }

        return redirect()->route('dashboard.microfinance.loans')->with('success', 'Loan status updated');
    }

    // ================== REPAYMENTS ==================
    public function storeRepayment(Request $req, Loan $loan)
    {
        $this->authorizeAccess($loan);
        $data = $req->validate([
            'amount' => 'required|numeric|min:1',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
            'reference_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $data['loan_id'] = $loan->id;
        $data['recorded_by'] = auth()->id();

        // Find schedule to apply payment
        $schedule = LoanSchedule::where('loan_id', $loan->id)
            ->whereIn('status', ['pending', 'partial'])
            ->orderBy('installment_number')
            ->first();

        if ($schedule) {
            $data['loan_schedule_id'] = $schedule->id;
            $schedule->paid_amount += $data['amount'];
            $schedule->balance = $schedule->total_amount - $schedule->paid_amount;
            $schedule->status = $schedule->balance <= 0 ? 'paid' : 'partial';
            if ($schedule->status === 'paid') $schedule->paid_date = $data['payment_date'];
            $schedule->save();
        }

        LoanRepayment::create($data);

        // Update loan totals
        $loan->paid_amount = $loan->repayments()->sum('amount');
        $loan->balance = max(0, $loan->total_amount - $loan->paid_amount);
        if ($loan->balance <= 0) $loan->status = 'completed';
        $loan->save();

        return redirect()->route('dashboard.microfinance.loans')->with('success', 'Repayment recorded');
    }

    // ================== GUARANTORS ==================
    public function guarantors()
    {
        $guarantors = Guarantor::where('user_id', auth()->id())->with('loan.customer')->latest()->get();
        return view('dashboard.microfinance.guarantors', compact('guarantors'));
    }

    public function storeGuarantor(Request $req)
    {
        $data = $req->validate([
            'loan_id' => 'nullable|exists:loans,id',
            'name' => 'required|string|max:150',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email',
            'id_number' => 'nullable|string',
            'address' => 'nullable|string',
            'relationship' => 'nullable|string',
            'pledged_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);
        $data['user_id'] = auth()->id();
        Guarantor::create($data);
        return redirect()->route('dashboard.microfinance.guarantors')->with('success', 'Guarantor added');
    }

    public function updateGuarantor(Request $req, Guarantor $guarantor)
    {
        $this->authorizeAccess($guarantor);
        $data = $req->validate([
            'loan_id' => 'nullable|exists:loans,id',
            'name' => 'required|string|max:150',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email',
            'id_number' => 'nullable|string',
            'address' => 'nullable|string',
            'relationship' => 'nullable|string',
            'pledged_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);
        $guarantor->update($data);
        return redirect()->route('dashboard.microfinance.guarantors')->with('success', 'Guarantor updated');
    }

    public function destroyGuarantor(Guarantor $guarantor)
    {
        $this->authorizeAccess($guarantor);
        $guarantor->delete();
        return redirect()->route('dashboard.microfinance.guarantors')->with('success', 'Guarantor removed');
    }

    // ================== HELPERS ==================
    private function authorizeAccess($model)
    {
        if ($model->user_id !== auth()->id()) {
            abort(403);
        }
    }

    private function generateSchedule(Loan $loan)
    {
        $principal = $loan->principal_amount;
        $months = $loan->duration_months;
        $monthlyPrincipal = $principal / $months;

        if ($loan->interest_type === 'flat') {
            $totalInterest = $loan->total_interest;
            $monthlyInterest = $totalInterest / $months;
        } else {
            $monthlyInterest = 0; // Will be calculated per installment
        }

        $balance = $loan->total_amount;
        $remainingPrincipal = $principal;

        for ($i = 1; $i <= $months; $i++) {
            if ($loan->interest_type === 'reducing_balance') {
                $monthlyInterest = $remainingPrincipal * ($loan->interest_rate / 100 / 12);
                $monthlyPrincipal = $principal / $months;
            }

            $total = $monthlyPrincipal + $monthlyInterest;
            $balance -= $total;
            $remainingPrincipal -= $monthlyPrincipal;

            LoanSchedule::create([
                'loan_id' => $loan->id,
                'installment_number' => $i,
                'due_date' => $loan->start_date ? $loan->start_date->copy()->addMonths($i) : now()->addMonths($i),
                'principal_amount' => round($monthlyPrincipal, 2),
                'interest_amount' => round($monthlyInterest, 2),
                'total_amount' => round($total, 2),
                'balance' => round(max(0, $balance), 2),
            ]);
        }
    }
}
