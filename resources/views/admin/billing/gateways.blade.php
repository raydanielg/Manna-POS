@extends('admin.layouts.app')
@section('page_title', 'Payment Gateways')
@section('content')
<div class="page-card">
    <div class="card-header">
        <div class="card-title">Payment Gateways</div>
        <button class="btn btn-success" onclick="openAddModal()">+ Add Gateway</button>
    </div>
    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead><tr><th>Name</th><th>Code</th><th>Description</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody id="tableBody"><tr><td colspan="5" class="tbl-empty">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="gwModal">
    <div class="modal" style="max-width:560px;">
        <div class="modal-header">
            <div class="modal-title" id="modalTitle">Add Gateway</div>
            <button class="modal-close" onclick="closeModal('gwModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="gwForm">
                <div class="form-row">
                    <div class="form-group">
                        <label>Name *</label>
                        <input type="text" class="form-control" id="name" required>
                    </div>
                    <div class="form-group">
                        <label>Code *</label>
                        <input type="text" class="form-control" id="code" placeholder="e.g. stripe, paypal" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea class="form-control" id="description" rows="2"></textarea>
                </div>
                <div class="form-group">
                    <label>Credentials (JSON)</label>
                    <textarea class="form-control" id="credentials" rows="3" placeholder='{"api_key":"","secret":""}'></textarea>
                </div>
                <div class="form-group">
                    <label>Settings (JSON)</label>
                    <textarea class="form-control" id="settings" rows="3" placeholder='{"sandbox":true}'></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Sort Order</label>
                        <input type="number" class="form-control" id="sort_order" value="0">
                    </div>
                    <div class="form-group">
                        <label class="flex items-center gap-2 mt-6">
                            <input type="checkbox" id="is_active">
                            <span style="font-size:0.82rem;">Active</span>
                        </label>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('gwModal')">Cancel</button>
            <button class="btn btn-primary" onclick="saveGateway()">Save</button>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
const API = '/api/admin/billing/gateways';
let editId = null;

async function loadList() {
    const data = await apiFetch(API);
    const tbody = document.getElementById('tableBody');
    if (!data.length) { tbody.innerHTML = '<tr><td colspan="5" class="tbl-empty">No gateways</td></tr>'; return; }
    tbody.innerHTML = data.map(g => `<tr>
        <td><strong>${g.name}</strong></td>
        <td><code>${g.code}</code></td>
        <td>${g.description || '-'}</td>
        <td>${g.is_active ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>'}</td>
        <td class="actions-cell">
            <button class="btn btn-primary btn-xs" onclick="editGw(${g.id})">Edit</button>
            <button class="btn btn-warning btn-xs" onclick="toggleGw(${g.id},'${g.name}')">${g.is_active ? 'Deactivate' : 'Activate'}</button>
            <button class="btn btn-danger btn-xs" onclick="deleteGw(${g.id},'${g.name}')">Delete</button>
        </td>
    </tr>`).join('');
}

async function openAddModal() {
    editId = null; document.getElementById('gwForm').reset();
    document.getElementById('modalTitle').textContent = 'Add Gateway';
    openModal('gwModal');
}

async function editGw(id) {
    editId = id; document.getElementById('modalTitle').textContent = 'Edit Gateway';
    const data = await apiFetch(`${API}/${id}`);
    document.getElementById('name').value = data.name || '';
    document.getElementById('code').value = data.code || '';
    document.getElementById('description').value = data.description || '';
    document.getElementById('credentials').value = data.credentials ? JSON.stringify(data.credentials, null, 2) : '';
    document.getElementById('settings').value = data.settings ? JSON.stringify(data.settings, null, 2) : '';
    document.getElementById('sort_order').value = data.sort_order || 0;
    document.getElementById('is_active').checked = data.is_active;
    openModal('gwModal');
}

async function saveGateway() {
    let credentials = null, settings = null;
    try { if (document.getElementById('credentials').value) credentials = JSON.parse(document.getElementById('credentials').value); } catch(e) {}
    try { if (document.getElementById('settings').value) settings = JSON.parse(document.getElementById('settings').value); } catch(e) {}
    const body = { name: document.getElementById('name').value, code: document.getElementById('code').value, description: document.getElementById('description').value, credentials, settings, sort_order: document.getElementById('sort_order').value, is_active: document.getElementById('is_active').checked };
    try {
        if (editId) { await apiFetch(`${API}/${editId}`, { method: 'PUT', body }); }
        else { await apiFetch(API, { method: 'POST', body }); }
        closeModal('gwModal'); Swal.fire({ icon: 'success', title: 'Saved!', timer: 2000, showConfirmButton: false }); loadList();
    } catch (e) { Swal.fire({ icon: 'error', title: 'Error', text: e.data?.message || 'Invalid JSON format' }); }
}

async function toggleGw(id, name) {
    await apiFetch(`${API}/${id}/toggle`, { method: 'POST' });
    Swal.fire({ icon: 'success', title: 'Updated!', text: `${name} status toggled`, timer: 2000, showConfirmButton: false });
    loadList();
}

function deleteGw(id, name) {
    Swal.fire({ title: 'Delete?', text: `Delete ${name}?`, icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc2626', confirmButtonText: 'Delete' })
    .then(async (r) => { if (r.isConfirmed) { await apiFetch(`${API}/${id}`, { method: 'DELETE' }); Swal.fire({ icon: 'success', title: 'Deleted!', timer: 2000, showConfirmButton: false }); loadList(); }});
}

loadList();
</script>
@endsection
