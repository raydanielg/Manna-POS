@extends('admin.layouts.app')
@section('page_title', 'Payouts')
@section('content')
<div class="page-card">
    <div class="card-header">
        <div class="card-title">Payouts</div>
        <div class="filters-row">
            <select class="form-control" style="width:auto;padding:0.45rem 0.75rem;font-size:0.78rem;" id="statusFilter" onchange="loadList()">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="paid">Paid</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead><tr><th>Agent/User</th><th>Amount</th><th>Method</th><th>Status</th><th>Requested Date</th><th>Paid Date</th><th>Actions</th></tr></thead>
            <tbody id="tableBody"><tr><td colspan="7" class="tbl-empty">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="payoutModal">
    <div class="modal" style="max-width:500px;">
        <div class="modal-header">
            <div class="modal-title">Process Payout</div>
            <button class="modal-close" onclick="closeModal('payoutModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="payoutForm">
                <div class="form-group">
                    <label>Transaction ID *</label>
                    <input type="text" class="form-control" id="transaction_id" required placeholder="Enter transaction reference">
                </div>
                <div class="form-group">
                    <label>Notes</label>
                    <textarea class="form-control" id="payoutNotes" rows="3" placeholder="Optional notes..."></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('payoutModal')">Cancel</button>
            <button class="btn btn-success" onclick="confirmProcessPayout()">Mark as Paid</button>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
const API = '/api/admin/finance/payouts';
let payoutId = null;

async function loadList() {
    const status = document.getElementById('statusFilter').value;
    const data = await apiFetch(`${API}?status=${status}`);
    const tbody = document.getElementById('tableBody');
    if (!data.length) { tbody.innerHTML = '<tr><td colspan="7" class="tbl-empty">No payouts found</td></tr>'; return; }
    tbody.innerHTML = data.map(p => `<tr>
        <td><strong>${p.agent || p.user || p.name || '-'}</strong></td>
        <td>${(p.amount || 0).toLocaleString()}</td>
        <td>${p.method || p.payment_method || '-'}</td>
        <td><span class="badge ${p.status === 'paid' ? 'badge-success' : p.status === 'cancelled' ? 'badge-danger' : 'badge-pending'}">${p.status || 'pending'}</span></td>
        <td>${p.requested_date || p.created_at ? new Date(p.requested_date || p.created_at).toLocaleDateString() : '-'}</td>
        <td>${p.paid_date || p.paid_at ? new Date(p.paid_date || p.paid_at).toLocaleDateString() : '-'}</td>
        <td class="actions-cell">
            ${p.status === 'pending' ? `<button class="btn btn-success btn-xs" onclick="processPayout(${p.id})">Process</button>` : ''}
            ${p.status === 'pending' ? `<button class="btn btn-danger btn-xs" onclick="cancelPayout(${p.id})">Cancel</button>` : ''}
        </td>
    </tr>`).join('');
}

function processPayout(id) {
    payoutId = id;
    document.getElementById('payoutForm').reset();
    openModal('payoutModal');
}

function confirmProcessPayout() {
    const txId = document.getElementById('transaction_id').value.trim();
    if (!txId) { Swal.fire({ icon: 'error', title: 'Required', text: 'Please enter a transaction ID' }); return; }
    Swal.fire({
        title: 'Process Payout?',
        text: 'Mark this payout as paid with transaction ID: ' + txId,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#16a34a',
        confirmButtonText: 'Yes, mark as paid'
    }).then(async (r) => {
        if (!r.isConfirmed) return;
        try {
            await apiFetch(`${API}/${payoutId}/process`, {
                method: 'POST',
                body: { transaction_id: txId, notes: document.getElementById('payoutNotes').value }
            });
            closeModal('payoutModal');
            Swal.fire({ icon: 'success', title: 'Payout processed!', timer: 2000, showConfirmButton: false });
            loadList();
        } catch (e) { Swal.fire({ icon: 'error', title: 'Error', text: e.data?.message || 'Something went wrong' }); }
    });
}

function cancelPayout(id) {
    Swal.fire({
        title: 'Cancel Payout?',
        text: 'Are you sure you want to cancel this payout?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        confirmButtonText: 'Yes, cancel'
    }).then(async (r) => {
        if (!r.isConfirmed) return;
        try {
            await apiFetch(`${API}/${id}/cancel`, { method: 'POST' });
            Swal.fire({ icon: 'success', title: 'Payout cancelled!', timer: 2000, showConfirmButton: false });
            loadList();
        } catch (e) { Swal.fire({ icon: 'error', title: 'Error', text: e.data?.message || 'Something went wrong' }); }
    });
}

loadList();
</script>
@endsection
