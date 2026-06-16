<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentGateway;
use App\Models\UserSubscription;
use App\Models\User;
use Illuminate\Http\Request;

class AdminBillingController extends Controller
{
    public function invoices()
    {
        return view('admin.billing.invoices');
    }

    public function invoicesList(Request $req)
    {
        $q = Invoice::with(['user:id,name,email', 'subscription']);
        if ($req->search) {
            $q->where(function($q) use ($req) {
                $q->where('invoice_number','like',"%{$req->search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name','like',"%{$req->search}%"));
            });
        }
        if ($req->status) $q->where('status', $req->status);
        return response()->json($q->latest()->get()->map(fn($i) => [
            'id' => $i->id, 'invoice_number' => $i->invoice_number,
            'user' => $i->user->name ?? 'N/A', 'user_id' => $i->user_id,
            'total' => number_format($i->total, 2),
            'currency' => $i->currency, 'status' => $i->status,
            'due_date' => $i->due_date ? $i->due_date->format('Y-m-d') : null,
            'created_at' => $i->created_at->format('Y-m-d'),
        ]));
    }

    public function invoicesShow(Invoice $invoice)
    {
        return response()->json($invoice->load(['user', 'payments']));
    }

    public function invoicesStore(Request $req)
    {
        $data = $req->validate([
            'user_id' => 'required|exists:users,id',
            'subscription_id' => 'nullable|exists:user_subscriptions,id',
            'subtotal' => 'required|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'status' => 'nullable|string|max:20',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);
        $data['invoice_number'] = 'INV-' . strtoupper(\Illuminate\Support\Str::random(8));
        $data['currency'] = $data['currency'] ?? 'TZS';
        $data['billing_cycle'] = $data['billing_cycle'] ?? 'monthly';
        return response()->json(['success'=>true,'invoice'=>Invoice::create($data)], 201);
    }

    public function invoicesUpdate(Request $req, Invoice $invoice)
    {
        $data = $req->validate([
            'status' => 'nullable|string|max:20',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'total' => 'nullable|numeric|min:0',
        ]);
        if ($req->status === 'paid') $data['paid_at'] = now();
        $invoice->update($data);
        return response()->json(['success'=>true,'invoice'=>$invoice]);
    }

    public function invoicesDestroy(Invoice $invoice)
    {
        $invoice->payments()->delete();
        $invoice->delete();
        return response()->json(['success'=>true]);
    }

    public function users()
    {
        return response()->json(User::select('id','name','email')->orderBy('name')->get());
    }

    // Payments
    public function payments()
    {
        return view('admin.billing.payments');
    }

    public function paymentsList(Request $req)
    {
        $q = Payment::with(['user:id,name,email', 'invoice:id,invoice_number']);
        if ($req->search) {
            $q->where(function($q) use ($req) {
                $q->where('transaction_id','like',"%{$req->search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name','like',"%{$req->search}%"));
            });
        }
        if ($req->status) $q->where('status', $req->status);
        if ($req->gateway) $q->where('gateway', $req->gateway);
        return response()->json($q->latest()->get());
    }

    public function paymentsStore(Request $req)
    {
        $data = $req->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'nullable|string|max:50',
            'transaction_id' => 'nullable|string|max:100',
            'gateway' => 'nullable|string|max:50',
            'status' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
        ]);
        $data['currency'] = $data['currency'] ?? 'TZS';
        $data['paid_at'] = now();
        $payment = Payment::create($data);
        if ($data['status'] === 'completed') {
            Invoice::where('id', $data['invoice_id'])->update(['status'=>'paid','paid_at'=>now()]);
        }
        return response()->json(['success'=>true,'payment'=>$payment], 201);
    }

    // Gateways
    public function gateways()
    {
        return view('admin.billing.gateways');
    }

    public function gatewaysList()
    {
        return response()->json(PaymentGateway::orderBy('sort_order')->get());
    }

    public function gatewaysStore(Request $req)
    {
        $data = $req->validate([
            'name' => 'required|string|max:191|unique:payment_gateways,name',
            'code' => 'required|string|max:50|unique:payment_gateways,code',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);
        $data['is_active'] = $data['is_active'] ?? false;
        return response()->json(['success'=>true,'gateway'=>PaymentGateway::create($data)], 201);
    }

    public function gatewaysUpdate(Request $req, PaymentGateway $gateway)
    {
        $data = $req->validate([
            'name' => "required|string|max:191|unique:payment_gateways,name,{$gateway->id}",
            'description' => 'nullable|string',
            'credentials' => 'nullable|array',
            'settings' => 'nullable|array',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);
        $gateway->update($data);
        return response()->json(['success'=>true,'gateway'=>$gateway]);
    }

    public function gatewaysShow(PaymentGateway $gateway)
    {
        return response()->json($gateway);
    }

    public function gatewaysToggle(PaymentGateway $gateway)
    {
        $gateway->update(['is_active' => !$gateway->is_active]);
        return response()->json(['success'=>true,'is_active'=>$gateway->is_active]);
    }

    public function gatewaysDestroy(PaymentGateway $gateway)
    {
        $gateway->delete();
        return response()->json(['success'=>true]);
    }

    // Refunds
    public function refunds()
    {
        return view('admin.billing.refunds');
    }

    public function refundsList(Request $req)
    {
        $q = Payment::whereIn('status', ['refunded', 'refund_requested'])->with(['user:id,name,email','invoice:id,invoice_number']);
        if ($req->status) $q->where('status', $req->status);
        return response()->json($q->latest()->get()->map(fn($p) => [
            'id' => $p->id,
            'transaction_id' => $p->transaction_id ?? 'N/A',
            'invoice_number' => $p->invoice->invoice_number ?? 'N/A',
            'user_name' => $p->user->name ?? 'N/A',
            'amount' => number_format($p->amount, 2),
            'currency' => $p->currency ?? 'TZS',
            'reason' => $p->notes ?? 'No reason provided',
            'status' => $p->status,
            'date' => $p->paid_at ? $p->paid_at->format('Y-m-d') : $p->created_at->format('Y-m-d'),
        ]));
    }

    public function refundsProcess(Request $req, Payment $payment)
    {
        $data = $req->validate(['status' => 'required|in:approved,rejected', 'notes' => 'nullable|string']);
        $payment->update(['status' => $data['status'] === 'approved' ? 'refunded' : 'completed', 'notes' => $data['notes'] ?? $payment->notes]);
        return response()->json(['success'=>true]);
    }

    // Transactions
    public function transactions()
    {
        return view('admin.billing.transactions');
    }

    public function transactionsList(Request $req)
    {
        $q = Payment::with(['user:id,name,email','invoice:id,invoice_number']);
        if ($req->search) {
            $q->where(function($q) use ($req) {
                $q->where('transaction_id','like',"%{$req->search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name','like',"%{$req->search}%"));
            });
        }
        return response()->json($q->latest()->paginate(50)->through(fn($p) => [
            'id' => $p->id,
            'transaction_id' => $p->transaction_id ?? 'N/A',
            'invoice_number' => $p->invoice->invoice_number ?? 'N/A',
            'user_name' => $p->user->name ?? 'N/A',
            'gateway' => $p->gateway ?? '-',
            'amount' => number_format($p->amount, 2),
            'currency' => $p->currency ?? 'TZS',
            'status' => $p->status,
            'date' => $p->paid_at ? $p->paid_at->format('Y-m-d H:i') : $p->created_at->format('Y-m-d H:i'),
        ]));
    }
}
