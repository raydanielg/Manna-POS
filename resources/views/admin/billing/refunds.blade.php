@extends('admin.layouts.app')
@section('page_title', 'Refunds')
@section('content')
<div class="page-card">
    <div class="card-header">
        <div class="card-title">Refund Requests</div>
        <div class="filters-row">
            <select class="form-control" style="width:auto;padding:0.45rem 0.75rem;font-size:0.78rem;" id="statusFilter" onchange="loadList()">
                <option value="">All</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead><tr><th>Transaction ID</th><th>Invoice</th><th>User</th><th>Amount</th><th>Reason</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
            <tbody id="tableBody"><tr><td colspan="8" class="tbl-empty">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="refModal">
    <div class="modal" style="max-width:500px;">
        <div class="modal-header">
            <div class="modal-title">Process Refund</div>
            <button class="modal-close" onclick="closeModal('refModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="refForm">
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" id="refStatus">
                        <option value="approved">Approve</option>
                        <option value="rejected">Reject</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Admin Note</label>
                    <textarea class="form-control" id="admin_note" rows="3" placeholder="Reason for approval/rejection"></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('refModal')">Cancel</button>
            <button class="btn btn-primary" onclick="processRefund()">Process</button>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
const API = '/api/admin/billing/refunds';
let editId = null;

async function loadList() {
    const status = document.getElementById('statusFilter').value;
    const params = new URLSearchParams();
    if (status) params.set('status', status);
    const data = await apiFetch(`${API}?${params}`);
    const tbody = document.getElementById('tableBody');
    if (!data.length) { tbody.innerHTML = '<tr><td colspan="8" class="tbl-empty">No refund requests</td></tr>'; return; }
    tbody.innerHTML = data.map(r => `<tr>
        <td><strong>${r.transaction_id || '-'}</strong></td>
        <td>${r.invoice?.invoice_number || r.invoice_number || '-'}</td>
        <td>${r.user?.name || r.user_name || 'N/A'}</td>
        <td>${r.currency || 'TZS'} ${(r.amount || 0).toLocaleString()}</td>
        <td>${r.reason || '-'}</td>
        <td><span class="badge ${r.status === 'approved' ? 'badge-success' : r.status === 'rejected' ? 'badge-danger' : 'badge-pending'}">${r.status}</span></td>
        <td>${r.created_at ? new Date(r.created_at).toLocaleDateString() : '-'}</td>
        <td class="actions-cell">
            ${r.status === 'pending' ? `<button class="btn btn-primary btn-xs" onclick="openRefModal(${r.id})">Process</button>` : ''}
            <button class="btn btn-danger btn-xs" onclick="deleteRefund(${r.id})">Delete</button>
        </td>
    </tr>`).join('');
}

function openRefModal(id) {
    editId = id; document.getElementById('refForm').reset();
    openModal('refModal');
}

async function processRefund() {
    const body = {
        status: document.getElementById('refStatus').value,
        admin_note: document.getElementById('admin_note').value,
    };
    try {
        await apiFetch(`${API}/${editId}/process`, { method: 'POST', body });
        closeModal('refModal');
        Swal.fire({ icon: 'success', title: 'Refund processed!', timer: 2000, showConfirmButton: false });
        loadList();
    } catch (e) { Swal.fire({ icon: 'error', title: 'Error', text: e.data?.message || 'Something went wrong' }); }
}

function deleteRefund(id) {
    Swal.fire({ title: 'Delete?', text: 'Delete this refund record?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc2626', confirmButtonText: 'Delete' })
    .then(async (r) => { if (r.isConfirmed) { await apiFetch(`${API}/${id}`, { method: 'DELETE' }); Swal.fire({ icon: 'success', title: 'Deleted!', timer: 2000, showConfirmButton: false }); loadList(); }});
}

loadList();
</script>
@endsection
