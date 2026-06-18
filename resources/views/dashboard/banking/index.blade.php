@extends('layouts.dashboard')
@section('page_title','Banking Dashboard')
@section('content')
<div class="dash-content">
    <div class="page-header">
        <div>
            <h1 class="page-title">Banking & Cashflow</h1>
            <p class="page-subtitle">Manage accounts, track transactions, and monitor cashflow</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard.banking.transactions') }}" class="btn btn-primary">Record Transaction</a>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="summary-grid" style="grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem;margin-bottom:1.5rem;">
        <div class="summary-card" style="background:linear-gradient(135deg,#0a192f,#1e3a8a);color:#fff;">
            <div class="summary-label" style="color:#93c5fd;">Total Balance</div>
            <div class="summary-value" style="font-size:1.8rem;">TZS {{ number_format($totalBalance,2) }}</div>
        </div>
        <div class="summary-card" style="border-left:4px solid #16a34a;">
            <div class="summary-label" style="color:#16a34a;">Active Accounts</div>
            <div class="summary-value" style="font-size:1.8rem;color:#15803d;">{{ $accounts->count() }}</div>
        </div>
        <div class="summary-card" style="border-left:4px solid #2563eb;">
            <div class="summary-label" style="color:#2563eb;">Recent Transactions</div>
            <div class="summary-value" style="font-size:1.8rem;color:#1d4ed8;">{{ $recentTransactions->count() }}</div>
        </div>
    </div>

    <div class="page-grid" style="display:grid;grid-template-columns:2fr 1fr;gap:1.5rem;">
        {{-- Accounts --}}
        <div class="page-card">
            <div class="card-header">
                <div class="card-title">Accounts Overview</div>
                <a href="{{ route('dashboard.banking.accounts') }}" class="btn btn-sm btn-outline">Manage</a>
            </div>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr><th>Account</th><th>Type</th><th>Bank</th><th class="text-right">Balance</th></tr>
                    </thead>
                    <tbody>
                        @forelse($accounts as $acc)
                        <tr>
                            <td>
                                <div style="font-weight:600;color:#0f172a;">{{ $acc->account_name }}</div>
                                <div style="font-size:0.75rem;color:#64748b;">{{ $acc->account_number ?: '—' }}</div>
                            </td>
                            <td><span class="badge badge-blue">{{ ucfirst(str_replace('_',' ',$acc->account_type)) }}</span></td>
                            <td>{{ $acc->bank_name }}</td>
                            <td class="text-right" style="font-weight:700;color:#0f172a;">TZS {{ number_format($acc->current_balance,2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center" style="padding:2rem;color:#94a3b8;">No accounts yet. <a href="{{ route('dashboard.banking.accounts') }}">Create one</a>.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Recent Transactions --}}
        <div class="page-card">
            <div class="card-header">
                <div class="card-title">Recent Transactions</div>
                <a href="{{ route('dashboard.banking.transactions') }}" class="btn btn-sm btn-outline">View All</a>
            </div>
            <div style="padding:0.75rem 1rem;">
                @forelse($recentTransactions as $tx)
                <div style="display:flex;justify-content:space-between;align-items:center;padding:0.75rem 0;border-bottom:1px solid #f1f5f9;">
                    <div>
                        <div style="font-weight:600;font-size:0.85rem;color:#1e293b;">{{ $tx->description ?: ucfirst($tx->type) }}</div>
                        <div style="font-size:0.72rem;color:#94a3b8;">{{ $tx->bankAccount->account_name ?? '—' }} &middot; {{ $tx->transaction_date->format('M d, Y') }}</div>
                    </div>
                    <div style="font-weight:700;font-size:0.9rem;{{ in_array($tx->type,['deposit','transfer_in','payment','refund']) ? 'color:#16a34a;' : 'color:#e11d48;' }}">
                        {{ in_array($tx->type,['deposit','transfer_in','payment','refund']) ? '+' : '-' }} TZS {{ number_format($tx->amount,2) }}
                    </div>
                </div>
                @empty
                <div style="text-align:center;padding:2rem;color:#94a3b8;font-size:0.85rem;">No transactions recorded yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
