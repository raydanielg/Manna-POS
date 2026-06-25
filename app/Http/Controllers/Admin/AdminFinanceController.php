<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminFinanceController extends Controller
{
    public function revenue()
    {
        return view('admin.finance.revenue');
    }

    public function revenueData(Request $req)
    {
        try {
            $today = now()->startOfDay();
            $monthStart = now()->startOfMonth();
            $yearStart = now()->startOfYear();
            $lastMonthStart = now()->subMonth()->startOfMonth();
            $lastMonthEnd = now()->subMonth()->endOfMonth();

            // ── KPI: Revenue ──
            $todayTotal = (float) Invoice::where('status', 'paid')
                ->whereDate('paid_at', $today)->sum('total');

            $monthTotal = (float) Invoice::where('status', 'paid')
                ->whereDate('paid_at', '>=', $monthStart)->sum('total');

            $lastMonthTotal = (float) Invoice::where('status', 'paid')
                ->whereBetween('paid_at', [$lastMonthStart, $lastMonthEnd])->sum('total');

            $yearTotal = (float) Invoice::where('status', 'paid')
                ->whereDate('paid_at', '>=', $yearStart)->sum('total');

            $allTimeTotal = (float) Invoice::where('status', 'paid')->sum('total');

            // ── KPI: Counts ──
            $todayCount = Invoice::where('status', 'paid')->whereDate('paid_at', $today)->count();
            $monthCount = Invoice::where('status', 'paid')->whereDate('paid_at', '>=', $monthStart)->count();
            $yearCount = Invoice::where('status', 'paid')->whereDate('paid_at', '>=', $yearStart)->count();

            // ── KPI: Pending ──
            $pendingInvoices = Invoice::where('status', 'pending')->count();
            $pendingAmount = (float) Invoice::where('status', 'pending')->sum('total');
            $pendingPayouts = (float) Payment::where('status', 'pending')->sum('amount');

            // ── KPI: Avg ──
            $avgTransaction = (float) Invoice::where('status', 'paid')->avg('total') ?? 0;

            // ── KPI: Expenses ──
            $monthExpenses = (float) Expense::where('expense_date', '>=', $monthStart)->sum('amount');
            $yearExpenses = (float) Expense::where('expense_date', '>=', $yearStart)->sum('amount');

            // ── Net Revenue ──
            $monthNet = $monthTotal - $monthExpenses;
            $yearNet = $yearTotal - $yearExpenses;

            // ── Month-over-month change ──
            $momChange = $lastMonthTotal > 0
                ? round((($monthTotal - $lastMonthTotal) / $lastMonthTotal) * 100, 1)
                : 0;

            // ── Monthly chart (last 12 months) ──
            $monthlyLabels = [];
            $monthlyRevenue = [];
            $monthlyExpenses = [];
            for ($i = 11; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $monthlyLabels[] = $date->format('M Y');
                $monthlyRevenue[] = (float) Invoice::where('status', 'paid')
                    ->whereYear('paid_at', $date->year)
                    ->whereMonth('paid_at', $date->month)->sum('total');
                $monthlyExpenses[] = (float) Expense::whereYear('expense_date', $date->year)
                    ->whereMonth('expense_date', $date->month)->sum('amount');
            }

            // ── Weekly chart (last 8 weeks) ──
            $weeklyLabels = [];
            $weeklyRevenue = [];
            for ($i = 7; $i >= 0; $i--) {
                $weekStart = now()->subWeeks($i)->startOfWeek();
                $weekEnd = now()->subWeeks($i)->endOfWeek();
                $weeklyLabels[] = $weekStart->format('M d');
                $weeklyRevenue[] = (float) Invoice::where('status', 'paid')
                    ->whereBetween('paid_at', [$weekStart, $weekEnd])->sum('total');
            }

            // ── Payment methods breakdown ──
            $paymentMethods = Payment::where('status', 'completed')
                ->whereDate('paid_at', '>=', $monthStart)
                ->select('payment_method', DB::raw('sum(amount) as total'), DB::raw('count(*) as count'))
                ->groupBy('payment_method')
                ->get();

            // ── Recent transactions ──
            $recent = Invoice::with('user:id,name')
                ->where('status', 'paid')
                ->latest('paid_at')
                ->limit(15)
                ->get()
                ->map(fn($i) => [
                    'id' => $i->id,
                    'invoice_number' => $i->invoice_number,
                    'user' => $i->user?->name ?? '—',
                    'amount' => number_format($i->total, 2),
                    'currency' => $i->currency,
                    'status' => $i->status,
                    'paid_at' => $i->paid_at?->format('Y-m-d'),
                ]);

            return response()->json([
                'kpis' => [
                    'today_total' => $todayTotal,
                    'today_count' => $todayCount,
                    'month_total' => $monthTotal,
                    'month_count' => $monthCount,
                    'month_expenses' => $monthExpenses,
                    'month_net' => $monthNet,
                    'year_total' => $yearTotal,
                    'year_count' => $yearCount,
                    'year_expenses' => $yearExpenses,
                    'year_net' => $yearNet,
                    'all_time_total' => $allTimeTotal,
                    'avg_transaction' => $avgTransaction,
                    'pending_invoices' => $pendingInvoices,
                    'pending_amount' => $pendingAmount,
                    'pending_payouts' => $pendingPayouts,
                    'mom_change' => $momChange,
                ],
                'monthly_chart' => [
                    'labels' => $monthlyLabels,
                    'revenue' => $monthlyRevenue,
                    'expenses' => $monthlyExpenses,
                ],
                'weekly_chart' => [
                    'labels' => $weeklyLabels,
                    'revenue' => $weeklyRevenue,
                ],
                'payment_methods' => $paymentMethods,
                'recent_transactions' => $recent,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function taxReports()
    {
        return view('admin.finance.tax-reports');
    }

    public function taxReportsList(Request $req)
    {
        try {
            $q = Invoice::with('user:id,name')
                ->where('status', 'paid')
                ->where('tax', '>', 0);
            if ($req->search) {
                $q->where(function($q) use ($req) {
                    $q->where('invoice_number', 'like', "%{$req->search}%")
                      ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$req->search}%"));
                });
            }
            if ($req->from) $q->whereDate('paid_at', '>=', $req->from);
            if ($req->to) $q->whereDate('paid_at', '<=', $req->to);
            return response()->json($q->latest()->paginate(20)->through(fn($i) => [
                'id' => $i->id,
                'invoice_number' => $i->invoice_number,
                'user' => $i->user?->name,
                'subtotal' => number_format($i->subtotal, 2),
                'tax' => number_format($i->tax, 2),
                'total' => number_format($i->total, 2),
                'currency' => $i->currency,
                'paid_at' => $i->paid_at?->format('Y-m-d'),
            ]));
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function commissions()
    {
        return view('admin.finance.commissions');
    }

    public function commissionsList(Request $req)
    {
        try {
            $q = Payment::with(['user:id,name', 'invoice:id,invoice_number'])
                ->where('status', 'completed');
            if ($req->search) {
                $q->where(function($q) use ($req) {
                    $q->where('transaction_id', 'like', "%{$req->search}%")
                      ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$req->search}%"));
                });
            }
            return response()->json($q->latest()->paginate(20)->through(fn($p) => [
                'id' => $p->id,
                'user' => $p->user?->name,
                'invoice_number' => $p->invoice?->invoice_number,
                'amount' => number_format($p->amount, 2),
                'currency' => $p->currency,
                'payment_method' => $p->payment_method,
                'transaction_id' => $p->transaction_id,
                'paid_at' => $p->paid_at?->format('Y-m-d'),
            ]));
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function payouts()
    {
        return view('admin.finance.payouts');
    }

    public function payoutsList(Request $req)
    {
        try {
            $q = Payment::with(['user:id,name', 'invoice:id,invoice_number'])
                ->where('status', 'pending');
            if ($req->search) {
                $q->where(function($q) use ($req) {
                    $q->where('transaction_id', 'like', "%{$req->search}%")
                      ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$req->search}%"));
                });
            }
            return response()->json($q->latest()->paginate(20)->through(fn($p) => [
                'id' => $p->id,
                'user' => $p->user?->name,
                'invoice_number' => $p->invoice?->invoice_number,
                'amount' => number_format($p->amount, 2),
                'currency' => $p->currency,
                'payment_method' => $p->payment_method,
                'transaction_id' => $p->transaction_id,
                'created_at' => $p->created_at->format('Y-m-d'),
            ]));
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function payoutsProcess($id)
    {
        try {
            $payment = Payment::findOrFail($id);
            $payment->update(['status' => 'completed', 'paid_at' => now()]);
            Invoice::where('id', $payment->invoice_id)->update(['status' => 'paid', 'paid_at' => now()]);
            return response()->json(['success' => true, 'payment' => $payment]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
