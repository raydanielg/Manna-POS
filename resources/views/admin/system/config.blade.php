@extends('admin.layouts.app')
@section('page_title', 'System Configuration')
@section('content')
<div class="page-card">
    <div class="card-header">
        <div class="card-title">System Configuration</div>
        <button class="btn btn-success" onclick="openAddModal()">+ Add Config</button>
    </div>
    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead><tr><th>Key</th><th>Value</th><th>Group</th><th>Actions</th></tr></thead>
            <tbody id="tableBody"><tr><td colspan="4" class="tbl-empty">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="configModal">
    <div class="modal" style="max-width:600px;">
        <div class="modal-header">
            <div class="modal-title" id="modalTitle">Add Config</div>
            <button class="modal-close" onclick="closeModal('configModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="configForm">
                <div class="form-row">
                    <div class="form-group">
                        <label>Key *</label>
                        <input type="text" class="form-control" id="config_key" placeholder="app_name" required>
                    </div>
                    <div class="form-group">
                        <label>Group</label>
                        <select class="form-control" id="group">
                            <option value="general">General</option>
                            <option value="email">Email</option>
                            <option value="payment">Payment</option>
                            <option value="security">Security</option>
                            <option value="features">Features</option>
                            <option value="localization">Localization</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Value *</label>
                    <textarea class="form-control" id="config_value" rows="4" required></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('configModal')">Cancel</button>
            <button class="btn btn-primary" onclick="saveConfig()">Save</button>
        </div>
    </div>
</div>
@endsection
@section('scripts')
const API = '/api/admin/system/config';
let editId = null;

async function loadList() {
    const data = await apiFetch(API);
    const tbody = document.getElementById('tableBody');
    if (!data.length) { tbody.innerHTML = '<tr><td colspan="4" class="tbl-empty">No config</td></tr>'; return; }
    tbody.innerHTML = data.map(c => `<tr>
        <td><code>${c.key}</code></td>
        <td><code>${c.value ? (c.value.length > 60 ? c.value.substring(0,60)+'...' : c.value) : '-'}</code></td>
        <td><span class="badge badge-info">${c.group}</span></td>
        <td class="actions-cell">
            <button class="btn btn-primary btn-xs" onclick="editConfig(${c.id})">Edit</button>
            <button class="btn btn-danger btn-xs" onclick="deleteConfig(${c.id},'${c.key}')">Delete</button>
        </td>
    </tr>`).join('');
}

function openAddModal() { editId = null; document.getElementById('configForm').reset(); document.getElementById('modalTitle').textContent = 'Add Config'; openModal('configModal'); }

async function editConfig(id) {
    editId = id; document.getElementById('modalTitle').textContent = 'Edit Config';
    const data = await apiFetch(`${API}/${id}`);
    document.getElementById('config_key').value = data.key || '';
    document.getElementById('config_key').disabled = true;
    document.getElementById('config_value').value = data.value || '';
    document.getElementById('group').value = data.group || 'general';
    openModal('configModal');
}

async function saveConfig() {
    const body = { key: document.getElementById('config_key').value, value: document.getElementById('config_value').value, group: document.getElementById('group').value };
    try {
        if (editId) { await apiFetch(`${API}/${editId}`, { method: 'PUT', body }); }
        else { await apiFetch(API, { method: 'POST', body }); }
        closeModal('configModal'); document.getElementById('config_key').disabled = false;
        Swal.fire({ icon: 'success', title: 'Saved!', timer: 2000, showConfirmButton: false }); loadList();
    } catch (e) { Swal.fire({ icon: 'error', title: 'Error', text: e.data?.message || 'Something went wrong' }); }
}

function deleteConfig(id, key) {
    Swal.fire({ title: 'Delete?', text: `Delete "${key}"?`, icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc2626', confirmButtonText: 'Delete' })
    .then(async (r) => { if (r.isConfirmed) { await apiFetch(`${API}/${id}`, { method: 'DELETE' }); Swal.fire({ icon: 'success', title: 'Deleted!', timer: 2000, showConfirmButton: false }); loadList(); }});
}

loadList();
@endsection
