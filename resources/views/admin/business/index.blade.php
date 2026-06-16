@extends('admin.layouts.app')
@section('page_title', 'Business Management')
@section('content')
<div class="page-card">
    <div class="card-header">
        <div class="card-title">All Businesses</div>
        <div class="filters-row">
            <div class="search-wrap">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" id="searchInput" placeholder="Search..." oninput="loadList()">
            </div>
            <select class="form-control" style="width:auto;padding:0.45rem 0.75rem;font-size:0.78rem;" id="statusFilter" onchange="loadList()">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="pending">Pending</option>
                <option value="suspended">Suspended</option>
                <option value="rejected">Rejected</option>
            </select>
            <select class="form-control" style="width:auto;padding:0.45rem 0.75rem;font-size:0.78rem;" id="verifiedFilter" onchange="loadList()">
                <option value="">All</option>
                <option value="1">Verified</option>
                <option value="0">Unverified</option>
            </select>
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead><tr><th>Business</th><th>Owner</th><th>Email</th><th>Phone</th><th>City</th><th>Status</th><th>Verified</th><th>Actions</th></tr></thead>
            <tbody id="tableBody"><tr><td colspan="8" class="tbl-empty">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="bizModal">
    <div class="modal" style="max-width:640px;">
        <div class="modal-header">
            <div class="modal-title" id="modalTitle">Business Details</div>
            <button class="modal-close" onclick="closeModal('bizModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="bizForm">
                <div class="form-row">
                    <div class="form-group">
                        <label>Business Name *</label>
                        <input type="text" class="form-control" id="business_name" required>
                    </div>
                    <div class="form-group">
                        <label>Business Type</label>
                        <input type="text" class="form-control" id="business_type">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" id="email">
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" class="form-control" id="phone">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>City</label>
                        <input type="text" class="form-control" id="business_city">
                    </div>
                    <div class="form-group">
                        <label>Country</label>
                        <input type="text" class="form-control" id="business_country">
                    </div>
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <textarea class="form-control" id="business_address" rows="2"></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Registration #</label>
                        <input type="text" class="form-control" id="registration_number">
                    </div>
                    <div class="form-group">
                        <label>Tax #</label>
                        <input type="text" class="form-control" id="tax_number">
                    </div>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" id="status">
                        <option value="active">Active</option>
                        <option value="pending">Pending</option>
                        <option value="suspended">Suspended</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Notes</label>
                    <textarea class="form-control" id="notes" rows="2"></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-success" onclick="verifyBiz()">Verify</button>
            <button class="btn btn-secondary" onclick="closeModal('bizModal')">Close</button>
            <button class="btn btn-primary" onclick="saveBiz()">Update</button>
        </div>
    </div>
</div>
@endsection
@section('scripts')
const API = '/api/admin/business';
let editId = null;

async function loadList() {
    const search = document.getElementById('searchInput').value;
    const status = document.getElementById('statusFilter').value;
    const is_verified = document.getElementById('verifiedFilter').value;
    const params = new URLSearchParams({search, status, is_verified});
    const data = await apiFetch(`${API}?${params}`);
    const tbody = document.getElementById('tableBody');
    if (!data.length) { tbody.innerHTML = '<tr><td colspan="8" class="tbl-empty">No businesses found</td></tr>'; return; }
    tbody.innerHTML = data.map(b => `<tr>
        <td><strong>${b.business_name}</strong><br><small style="color:#94a3b8;">${b.category || ''}</small></td>
        <td>${b.owner}</td>
        <td>${b.email || '-'}</td>
        <td>${b.phone || '-'}</td>
        <td>${b.city || '-'}</td>
        <td><span class="badge ${b.status === 'active' ? 'badge-success' : b.status === 'pending' ? 'badge-pending' : b.status === 'rejected' ? 'badge-danger' : 'badge-default'}">${b.status}</span></td>
        <td>${b.is_verified ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>'}</td>
        <td class="actions-cell">
            <button class="btn btn-primary btn-xs" onclick="editBiz(${b.id})">View</button>
            <button class="btn btn-danger btn-xs" onclick="deleteBiz(${b.id},'${b.business_name}')">Delete</button>
        </td>
    </tr>`).join('');
}

async function editBiz(id) {
    editId = id; document.getElementById('modalTitle').textContent = 'Edit Business';
    const data = await apiFetch(`${API}/${id}`);
    document.getElementById('business_name').value = data.business_name || '';
    document.getElementById('business_type').value = data.business_type || '';
    document.getElementById('email').value = data.email || '';
    document.getElementById('phone').value = data.phone || '';
    document.getElementById('business_city').value = data.business_city || '';
    document.getElementById('business_country').value = data.business_country || '';
    document.getElementById('business_address').value = data.business_address || '';
    document.getElementById('registration_number').value = data.registration_number || '';
    document.getElementById('tax_number').value = data.tax_number || '';
    document.getElementById('status').value = data.status || 'pending';
    document.getElementById('notes').value = data.notes || '';
    openModal('bizModal');
}

async function saveBiz() {
    if (!editId) return;
    const body = {
        business_name: document.getElementById('business_name').value,
        business_type: document.getElementById('business_type').value,
        email: document.getElementById('email').value,
        phone: document.getElementById('phone').value,
        business_city: document.getElementById('business_city').value,
        business_country: document.getElementById('business_country').value,
        business_address: document.getElementById('business_address').value,
        registration_number: document.getElementById('registration_number').value,
        tax_number: document.getElementById('tax_number').value,
        status: document.getElementById('status').value,
        notes: document.getElementById('notes').value,
    };
    try {
        await apiFetch(`${API}/${editId}`, { method: 'PUT', body });
        closeModal('bizModal');
        Swal.fire({ icon: 'success', title: 'Updated!', timer: 2000, showConfirmButton: false });
        loadList();
    } catch (e) { Swal.fire({ icon: 'error', title: 'Error', text: e.data?.message || 'Something went wrong' }); }
}

async function verifyBiz() {
    if (!editId) return;
    try {
        await apiFetch(`${API}/${editId}/verify`, { method: 'POST' });
        Swal.fire({ icon: 'success', title: 'Verified!', text: 'Business has been verified.', timer: 2000, showConfirmButton: false });
        closeModal('bizModal'); loadList();
    } catch (e) { Swal.fire({ icon: 'error', title: 'Error', text: e.data?.message }); }
}

function deleteBiz(id, name) {
    Swal.fire({ title: 'Delete Business?', text: `Delete ${name}?`, icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc2626', confirmButtonText: 'Delete' })
    .then(async (r) => { if (r.isConfirmed) { await apiFetch(`${API}/${id}`, { method: 'DELETE' }); Swal.fire({ icon: 'success', title: 'Deleted!', timer: 2000, showConfirmButton: false }); loadList(); }});
}

loadList();
@endsection
