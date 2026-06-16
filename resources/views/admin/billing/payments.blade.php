@extends('admin.layouts.app')
@section('page_title', 'Payments')
@section('content')
<div class="page-card">
    <div class="card-header">
        <div class="card-title">Payment History</div>
        <div class="filters-row">
            <div class="search-wrap">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" id="searchInput" placeholder="Search..." oninput="loadList()">
            </div>
            <select class="form-control" style="width:auto;padding:0.45rem 0.75rem;font-size:0.78rem;" id="statusFilter" onchange="loadList()">
                <option value="">All</option>
                <option value="completed">Completed</option>
                <option value="pending">Pending</option>
                <option value="failed">Failed</option>
                <option value="refunded">Refunded</option>
            </select>
            <button class="btn btn-success" onclick="openAddModal()">+ Record Payment</button>
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead><tr><th>Transaction ID</th><th>User</th><th>Invoice</th><th>Amount</th><th>Method</th><th>Gateway</th><th>Status</th><th>Date</th></tr></thead>
            <tbody id="tableBody"><tr><td colspan="8" class="tbl-empty">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="payModal">
    <div class="modal" style="max-width:500px;">
        <div class="modal-header">
            <div class="modal-title">Record Payment</div>
            <button class="modal-close" onclick="closeModal('payModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="payForm">
                <div class="form-group">
                    <label>Invoice *</label>
                    <select class="form-control" id="invoice_id" required></select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Amount *</label>
                        <input type="number" step="0.01" class="form-control" id="amount" required>
                    </div>
                    <div class="form-group">
                        <label>Payment Method</label>
                        <select class="form-control" id="payment_method">
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="mobile_money">Mobile Money</option>
                            <option value="credit_card">Credit Card</option>
                            <option value="cash">Cash</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Transaction ID</label>
                        <input type="text" class="form-control" id="transaction_id">
                    </div>
                    <div class="form-group">
                        <label>Gateway</label>
                        <input type="text" class="form-control" id="gateway">
                    </div>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" id="status">
                        <option value="completed">Completed</option>
                        <option value="pending">Pending</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Notes</label>
                    <textarea class="form-control" id="notes" rows="2"></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('payModal')">Cancel</button>
            <button class="btn btn-primary" onclick="savePayment()">Record Payment</button>
        </div>
    </div>
</div>
@endsection
@section('scripts')
const API = '/api/admin/billing/payments';

async function init() {
    const invs = await apiFetch('/api/admin/billing/invoices?status=pending');
    document.getElementById('invoice_id').innerHTML = '<option value="">Select Invoice</option>' + invs.map(i => `<option value="${i.id}" data-user-id="${i.user_id || ''}">${i.invoice_number} - ${i.currency} ${i.total}</option>`).join('');
    loadList();
}

async function loadList() {
    const search = document.getElementById('searchInput').value;
    const status = document.getElementById('statusFilter').value;
    const data = await apiFetch(`${API}?search=${search}&status=${status}`);
    const tbody = document.getElementById('tableBody');
    if (!data.length) { tbody.innerHTML = '<tr><td colspan="8" class="tbl-empty">No payments</td></tr>'; return; }
    tbody.innerHTML = data.map(p => `<tr>
        <td><strong>${p.transaction_id || '-'}</strong></td>
        <td>${p.user?.name || 'N/A'}</td>
        <td>${p.invoice?.invoice_number || '-'}</td>
        <td>${p.currency || 'TZS'} ${(p.amount || 0).toLocaleString()}</td>
        <td>${p.payment_method || '-'}</td>
        <td>${p.gateway || '-'}</td>
        <td><span class="badge ${p.status === 'completed' ? 'badge-success' : p.status === 'pending' ? 'badge-pending' : 'badge-danger'}">${p.status}</span></td>
        <td>${p.paid_at ? new Date(p.paid_at).toLocaleDateString() : '-'}</td>
    </tr>`).join('');
}

async function openAddModal() {
    document.getElementById('payForm').reset();
    openModal('payModal');
}

async function savePayment() {
    const body = {
        invoice_id: document.getElementById('invoice_id').value,
        amount: document.getElementById('amount').value,
        payment_method: document.getElementById('payment_method').value,
        transaction_id: document.getElementById('transaction_id').value,
        gateway: document.getElementById('gateway').value,
        status: document.getElementById('status').value,
        notes: document.getElementById('notes').value,
        user_id: document.getElementById('invoice_id').selectedOptions[0]?.dataset?.userId || null,
    };
    try {
        await apiFetch(API, { method: 'POST', body });
        closeModal('payModal');
        Swal.fire({ icon: 'success', title: 'Payment recorded!', timer: 2000, showConfirmButton: false });
        loadList();
    } catch (e) { Swal.fire({ icon: 'error', title: 'Error', text: e.data?.message || 'Something went wrong' }); }
}

init();
@endsection
