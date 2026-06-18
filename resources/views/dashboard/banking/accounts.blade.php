@extends('layouts.dashboard')
@section('page_title','Bank Accounts')
@section('content')
<div class="dash-content">
    <div class="page-header">
        <div>
            <h1 class="page-title">Bank Accounts</h1>
            <p class="page-subtitle">Manage cash, bank, and mobile money accounts</p>
        </div>
        <button class="btn btn-primary" onclick="document.getElementById('accountModal').classList.add('open')">Add Account</button>
    </div>

    <div class="page-card">
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Account Name</th>
                        <th>Type</th>
                        <th>Bank / Provider</th>
                        <th>Account Number</th>
                        <th class="text-right">Opening Balance</th>
                        <th class="text-right">Current Balance</th>
                        <th>Status</th>
                        <th style="width:120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($accounts as $acc)
                    <tr>
                        <td style="font-weight:600;color:#0f172a;">{{ $acc->account_name }}</td>
                        <td><span class="badge badge-blue">{{ ucfirst(str_replace('_',' ',$acc->account_type)) }}</span></td>
                        <td>{{ $acc->bank_name }}</td>
                        <td>{{ $acc->account_number ?: '—' }}</td>
                        <td class="text-right">TZS {{ number_format($acc->opening_balance,2) }}</td>
                        <td class="text-right" style="font-weight:700;color:#0f172a;">TZS {{ number_format($acc->current_balance,2) }}</td>
                        <td>
                            @if($acc->is_active)
                                <span class="badge badge-green">Active</span>
                            @else
                                <span class="badge badge-gray">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-btns">
                                <button class="btn-icon" title="Edit" onclick="editAccount({{ json_encode($acc) }})"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
                                <form action="{{ route('dashboard.banking.accounts.destroy',$acc) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this account?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-icon btn-danger" title="Delete"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center" style="padding:2.5rem;color:#94a3b8;">No accounts found. Click "Add Account" to create one.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding:1rem;">{{ $accounts->links() }}</div>
    </div>
</div>

{{-- Add Account Modal --}}
<div class="modal" id="accountModal">
    <div class="modal-overlay" onclick="document.getElementById('accountModal').classList.remove('open')"></div>
    <div class="modal-content" style="max-width:520px;">
        <div class="modal-header">
            <h3 class="modal-title">Add Bank Account</h3>
            <button class="modal-close" onclick="document.getElementById('accountModal').classList.remove('open')">&times;</button>
        </div>
        <form action="{{ route('dashboard.banking.accounts.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-grid" style="grid-template-columns:1fr 1fr;gap:1rem;">
                    <div class="form-group">
                        <label class="form-label">Account Name *</label>
                        <input type="text" name="account_name" class="form-control" required placeholder="e.g. Main Business Account">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Account Type *</label>
                        <select name="account_type" class="form-control" required>
                            <option value="cash">Cash</option>
                            <option value="bank">Bank</option>
                            <option value="mobile_money">Mobile Money</option>
                            <option value="savings">Savings</option>
                            <option value="checking">Checking</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Bank / Provider Name *</label>
                        <input type="text" name="bank_name" class="form-control" required placeholder="e.g. CRDB Bank">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Account Number</label>
                        <input type="text" name="account_number" class="form-control" placeholder="Optional">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Branch</label>
                        <input type="text" name="branch" class="form-control" placeholder="Optional">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Opening Balance</label>
                        <input type="number" step="0.01" name="opening_balance" class="form-control" value="0">
                    </div>
                    <div class="form-group" style="grid-column:span 2;">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Optional notes..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('accountModal').classList.remove('open')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Account</button>
            </div>
        </form>
    </div>
</div>
@endsection
