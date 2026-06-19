@extends('layouts.dashboard')
@section('page_title','Transactions')
@section('content')
<div class="dash-content">
    <div class="page-header">
        <div>
            <h1 class="page-title">Transactions</h1>
            <p class="page-subtitle">Record and track all account transactions</p>
        </div>
        <button class="btn btn-primary" onclick="document.getElementById('txModal').classList.add('open')">Record Transaction</button>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('dashboard.banking.transactions') }}" style="display:flex;gap:0.75rem;flex-wrap:wrap;align-items:flex-end;margin-bottom:1rem;background:#fff;padding:1rem;border-radius:12px;border:1px solid #e2e8f0;">
        <div class="form-group" style="margin:0;min-width:160px;">
            <label class="form-label" style="font-size:0.72rem;">Account</label>
            <select name="account_id" class="form-control" style="min-width:160px;" onchange="this.form.submit()">
                <option value="">All Accounts</option>
                @foreach($accounts as $acc)
                <option value="{{ $acc->id }}" {{ request('account_id')==$acc->id ? 'selected' : '' }}>{{ $acc->account_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group" style="margin:0;min-width:140px;">
            <label class="form-label" style="font-size:0.72rem;">Type</label>
            <select name="type" class="form-control" style="min-width:140px;" onchange="this.form.submit()">
                <option value="">All Types</option>
                <option value="deposit" {{ request('type')=='deposit' ? 'selected' : '' }}>Deposit</option>
                <option value="withdrawal" {{ request('type')=='withdrawal' ? 'selected' : '' }}>Withdrawal</option>
                <option value="transfer_in" {{ request('type')=='transfer_in' ? 'selected' : '' }}>Transfer In</option>
                <option value="transfer_out" {{ request('type')=='transfer_out' ? 'selected' : '' }}>Transfer Out</option>
                <option value="payment" {{ request('type')=='payment' ? 'selected' : '' }}>Payment</option>
                <option value="refund" {{ request('type')=='refund' ? 'selected' : '' }}>Refund</option>
                <option value="adjustment" {{ request('type')=='adjustment' ? 'selected' : '' }}>Adjustment</option>
            </select>
        </div>
        <div class="form-group" style="margin:0;">
            <label class="form-label" style="font-size:0.72rem;">From</label>
            <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}" style="width:140px;">
        </div>
        <div class="form-group" style="margin:0;">
            <label class="form-label" style="font-size:0.72rem;">To</label>
            <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}" style="width:140px;">
        </div>
        <button type="submit" class="btn btn-primary" style="height:40px;">Filter</button>
        <a href="{{ route('dashboard.banking.transactions') }}" class="btn btn-outline" style="height:40px;">Reset</a>
    </form>

    {{-- Summary --}}
    <div class="summary-grid" style="grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem;margin-bottom:1rem;">
        <div class="summary-card" style="border-left:4px solid #16a34a;">
            <div class="summary-label" style="color:#16a34a;">Total In</div>
            <div class="summary-value" style="font-size:1.5rem;color:#15803d;">{{ $userCurrency }} {{ number_format($summary['total_in'] ?? 0,2) }}</div>
        </div>
        <div class="summary-card" style="border-left:4px solid #e11d48;">
            <div class="summary-label" style="color:#e11d48;">Total Out</div>
            <div class="summary-value" style="font-size:1.5rem;color:#be123c;">{{ $userCurrency }} {{ number_format($summary['total_out'] ?? 0,2) }}</div>
        </div>
        <div class="summary-card" style="border-left:4px solid #2563eb;">
            <div class="summary-label" style="color:#2563eb;">Net Flow</div>
            <div class="summary-value" style="font-size:1.5rem;color:#1d4ed8;">{{ $userCurrency }} {{ number_format(($summary['total_in'] ?? 0) - ($summary['total_out'] ?? 0),2) }}</div>
        </div>
    </div>

    <div class="page-card">
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Account</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Reference</th>
                        <th class="text-right">Amount</th>
                        <th class="text-right">Balance After</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $tx)
                    <tr>
                        <td style="white-space:nowrap;">{{ $tx->transaction_date->format('M d, Y') }}</td>
                        <td>{{ $tx->bankAccount->account_name ?? '—' }}</td>
                        <td>
                            <span class="badge {{ in_array($tx->type,['deposit','transfer_in','payment','refund']) ? 'badge-green' : 'badge-red' }}">
                                {{ ucfirst(str_replace('_',' ',$tx->type)) }}
                            </span>
                        </td>
                        <td>{{ $tx->description ?: '—' }}</td>
                        <td>{{ $tx->reference_number ?: '—' }}</td>
                        <td class="text-right" style="font-weight:700;{{ in_array($tx->type,['deposit','transfer_in','payment','refund']) ? 'color:#16a34a;' : 'color:#e11d48;' }}">
                            {{ in_array($tx->type,['deposit','transfer_in','payment','refund']) ? '+' : '-' }} {{ $userCurrency }} {{ number_format($tx->amount,2) }}
                        </td>
                        <td class="text-right" style="font-weight:600;color:#0f172a;">{{ $userCurrency }} {{ number_format($tx->balance_after,2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center" style="padding:2.5rem;color:#94a3b8;">No transactions found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding:1rem;">{{ $transactions->links() }}</div>
    </div>
</div>

{{-- Record Transaction Modal --}}
<div class="modal" id="txModal">
    <div class="modal-overlay" onclick="document.getElementById('txModal').classList.remove('open')"></div>
    <div class="modal-content" style="max-width:520px;">
        <div class="modal-header">
            <h3 class="modal-title">Record Transaction</h3>
            <button class="modal-close" onclick="document.getElementById('txModal').classList.remove('open')">&times;</button>
        </div>
        <form action="{{ route('dashboard.banking.transactions.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-grid" style="grid-template-columns:1fr 1fr;gap:1rem;">
                    <div class="form-group" style="grid-column:span 2;">
                        <label class="form-label">Account *</label>
                        <select name="bank_account_id" class="form-control" required>
                            @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->account_name }} ({{ ucfirst($acc->account_type) }} — {{ $userCurrency }} {{ number_format($acc->current_balance,2) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Transaction Type *</label>
                        <select name="type" class="form-control" required>
                            <option value="deposit">Deposit</option>
                            <option value="withdrawal">Withdrawal</option>
                            <option value="transfer_in">Transfer In</option>
                            <option value="transfer_out">Transfer Out</option>
                            <option value="payment">Payment</option>
                            <option value="refund">Refund</option>
                            <option value="adjustment">Adjustment</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Amount *</label>
                        <input type="number" step="0.01" name="amount" class="form-control" required min="0.01">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Transaction Date *</label>
                        <input type="date" name="transaction_date" class="form-control" required value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Reference Number</label>
                        <input type="text" name="reference_number" class="form-control" placeholder="e.g. INV-001">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Category</label>
                        <input type="text" name="category" class="form-control" placeholder="e.g. Sales, Rent">
                    </div>
                    <div class="form-group" style="grid-column:span 2;">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Optional notes..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('txModal').classList.remove('open')">Cancel</button>
                <button type="submit" class="btn btn-primary">Record Transaction</button>
            </div>
        </form>
    </div>
</div>
@endsection
