@extends('admin.layouts.app')
@section('page_title', 'Active Subscriptions')
@section('content')
<div class="page-card">
    <div class="card-header">
        <div class="card-title">Active Subscriptions</div>
        <div class="filters-row">
            <select class="form-control" style="width:auto;padding:0.45rem 0.75rem;font-size:0.78rem;" id="statusFilter" onchange="loadList()">
                <option value="active">Active</option>
                <option value="expired">Expired</option>
                <option value="trial">Trial</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead><tr><th>User</th><th>Plan</th><th>Status</th><th>Start Date</th><th>End Date</th><th>Amount</th><th>Actions</th></tr></thead>
            <tbody id="tableBody"><tr><td colspan="7" class="tbl-empty">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="subModal">
    <div class="modal" style="max-width:500px;">
        <div class="modal-header">
            <div class="modal-title" id="modalTitle">Edit Subscription</div>
            <button class="modal-close" onclick="closeModal('subModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="subForm">
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" id="status">
                        <option value="active">Active</option>
                        <option value="expired">Expired</option>
                        <option value="trial">Trial</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>End Date</label>
                    <input type="date" class="form-control" id="end_date">
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('subModal')">Cancel</button>
            <button class="btn btn-primary" onclick="saveSub()">Save</button>
        </div>
    </div>
</div>
@endsection
@section('scripts')
const API = '/api/admin/subscriptions?status=active';
let editId = null;

async function loadList() {
    const res = await apiFetch(API);
    const data = res.data || res;
    const tbody = document.getElementById('tableBody');
    if (!data.length) { tbody.innerHTML = '<tr><td colspan="7" class="tbl-empty">No active subscriptions</td></tr>'; return; }
    tbody.innerHTML = data.map(s => `<tr>
        <td>${s.user?.name || s.user_name || 'N/A'}</td>
        <td>${s.plan?.name || s.plan_name || 'N/A'}</td>
        <td><span class="badge ${s.status === 'active' ? 'badge-success' : s.status === 'trial' ? 'badge-info' : s.status === 'expired' ? 'badge-danger' : 'badge-default'}">${s.status}</span></td>
        <td>${s.starts_at ? new Date(s.starts_at).toLocaleDateString() : '-'}</td>
        <td>${s.expires_at ? new Date(s.expires_at).toLocaleDateString() : '-'}</td>
        <td>${s.currency || 'TZS'} ${(s.amount_paid || 0).toLocaleString()}</td>
        <td class="actions-cell">
            <button class="btn btn-primary btn-xs" onclick="editSub(${s.id})">Edit</button>
            <button class="btn btn-danger btn-xs" onclick="deleteSub(${s.id})">Delete</button>
        </td>
    </tr>`).join('');
}

async function editSub(id) {
    editId = id; document.getElementById('modalTitle').textContent = 'Edit Subscription';
    const res = await apiFetch(`/api/admin/subscriptions/${id}`);
    document.getElementById('status').value = data.status || 'active';
    document.getElementById('end_date').value = data.expires_at ? data.expires_at.split('T')[0] : '';
    openModal('subModal');
}

async function saveSub() {
    const body = {
        status: document.getElementById('status').value,
        expires_at: document.getElementById('end_date').value,
    };
    try {
        await apiFetch(`/api/admin/subscriptions/${editId}`, { method: 'PUT', body });
        closeModal('subModal');
        Swal.fire({ icon: 'success', title: 'Updated!', timer: 2000, showConfirmButton: false });
        loadList();
    } catch (e) { Swal.fire({ icon: 'error', title: 'Error', text: e.data?.message || 'Something went wrong' }); }
}

function deleteSub(id) {
    Swal.fire({ title: 'Delete?', text: 'Delete this subscription?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc2626', confirmButtonText: 'Delete' })
    .then(async (r) => { if (r.isConfirmed) { await apiFetch(`/api/admin/subscriptions/${id}`, { method: 'DELETE' }); Swal.fire({ icon: 'success', title: 'Deleted!', timer: 2000, showConfirmButton: false }); loadList(); }});
}

loadList();
@endsection
