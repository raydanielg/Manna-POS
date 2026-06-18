<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\BankTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BankingController extends Controller
{
    public function index()
    {
        $accounts = BankAccount::where('user_id', auth()->id())->get();
        $totalBalance = $accounts->sum('current_balance');
        $recentTransactions = BankTransaction::whereIn('bank_account_id', $accounts->pluck('id'))
            ->with('bankAccount')
            ->orderBy('transaction_date','desc')
            ->take(10)
            ->get();
        return view('dashboard.banking.index', compact('accounts','totalBalance','recentTransactions'));
    }

    public function accounts()
    {
        $accounts = BankAccount::where('user_id', $this->currentBusinessId())->paginate(15);
        return view('dashboard.banking.accounts', compact('accounts'));
    }

    public function storeAccount(Request $request)
    {
        $data = $request->validate([
            'account_name' => 'required|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'bank_name' => 'required|string|max:255',
            'branch' => 'nullable|string|max:255',
            'account_type' => 'required|in:cash,bank,mobile_money,savings,checking',
            'opening_balance' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'description' => 'nullable|string',
        ]);
        $data['user_id'] = auth()->id();
        $data['current_balance'] = $data['opening_balance'] ?? 0;
        BankAccount::create($data);
        return redirect()->route('dashboard.banking.accounts')->with('success','Account created successfully');
    }

    public function updateAccount(Request $request, BankAccount $account)
    {
        $this->authorizeAccount($account);
        $data = $request->validate([
            'account_name' => 'required|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'bank_name' => 'required|string|max:255',
            'branch' => 'nullable|string|max:255',
            'account_type' => 'required|in:cash,bank,mobile_money,savings,checking',
            'currency' => 'nullable|string|max:10',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        $account->update($data);
        return redirect()->route('dashboard.banking.accounts')->with('success','Account updated successfully');
    }

    public function destroyAccount(BankAccount $account)
    {
        $this->authorizeAccount($account);
        $account->delete();
        return redirect()->route('dashboard.banking.accounts')->with('success','Account deleted');
    }

    public function transactions(Request $request)
    {
        $accounts = BankAccount::where('user_id', auth()->id())->get();
        $query = BankTransaction::whereIn('bank_account_id', $accounts->pluck('id'))
            ->with('bankAccount');

        if ($request->filled('account_id')) {
            $query->where('bank_account_id', $request->account_id);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('from_date')) {
            $query->whereDate('transaction_date','>=',$request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('transaction_date','<=',$request->to_date);
        }

        $transactions = $query->orderBy('transaction_date','desc')->paginate(25);
        $summary = [
            'total_in' => (clone $query)->whereIn('type',['deposit','transfer_in','payment','refund'])->sum('amount'),
            'total_out' => (clone $query)->whereIn('type',['withdrawal','transfer_out','adjustment'])->sum('amount'),
        ];
        return view('dashboard.banking.transactions', compact('transactions','accounts','summary'));
    }

    public function storeTransaction(Request $request)
    {
        $data = $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'type' => 'required|in:deposit,withdrawal,transfer_in,transfer_out,payment,refund,adjustment',
            'amount' => 'required|numeric|min:0.01',
            'reference_number' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'contact_type' => 'nullable|string|max:255',
            'transaction_date' => 'required|date',
        ]);

        $account = BankAccount::where('id', $data['bank_account_id'])->where('user_id', auth()->id())->firstOrFail();
        $data['user_id'] = auth()->id();
        $data['balance_after'] = $this->calculateNewBalance($account, $data['type'], $data['amount']);

        DB::transaction(function() use ($account, $data) {
            BankTransaction::create($data);
            $account->current_balance = $data['balance_after'];
            $account->save();
        });

        return redirect()->route('dashboard.banking.transactions')->with('success','Transaction recorded successfully');
    }

    private function calculateNewBalance(BankAccount $account, string $type, float $amount): float
    {
        $in = ['deposit','transfer_in','payment','refund'];
        $out = ['withdrawal','transfer_out','adjustment'];
        if (in_array($type, $in)) return $account->current_balance + $amount;
        if (in_array($type, $out)) return $account->current_balance - $amount;
        return $account->current_balance;
    }

    private function authorizeAccount(BankAccount $account)
    {
        if ($account->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
