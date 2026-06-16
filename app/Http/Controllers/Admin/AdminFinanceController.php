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

            $todayTotal = Invoice::where('status', 'paid')
                ->whereDate('paid_at', $today)
                ->sum('total');

            $monthTotal = Invoice::where('status', 'paid')
                ->whereDate('paid_at', '>=', $monthStart)
                ->sum('total');

            $yearTotal = Invoice::where('status', 'paid')
                ->whereDate('paid_at', '>=', $yearStart)
                ->sum('total');

            $pendingPayouts = Payment::where('status', 'pending')->sum('amount');

            $avgTransaction = Invoice::where('status', 'paid')
                ->avg('total') ?? 0;

            $chartData = Invoice::where('status', 'paid')
                ->whereYear('paid_at', now()->year)
                ->selectRaw("strftime('%Y-%m', paid_at) as month, SUM(total) as total")
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month')
                ->toArray();

            return response()->json([
                'today_total' => number_format($todayTotal, 2),
                'month_total' => number_format($monthTotal, 2),
                'year_total' => number_format($yearTotal, 2),
                'pending_payouts' => number_format($pendingPayouts, 2),
                'avg_transaction' => number_format($avgTransaction, 2),
                'chart_data' => $chartData,
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
