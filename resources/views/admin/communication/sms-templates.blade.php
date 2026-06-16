@extends('admin.layouts.app')
@section('page_title', 'SMS Templates')
@section('content')
<div class="page-card">
    <div class="card-header">
        <div class="card-title">SMS Templates</div>
        <button class="btn btn-success" onclick="openSmsAddModal()">+ Add Template</button>
    </div>
    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead><tr><th>Name</th><th>Code</th><th>Category</th><th>Active</th><th>Actions</th></tr></thead>
            <tbody id="smsTableBody"><tr><td colspan="5" class="tbl-empty">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="smsModal">
    <div class="modal" style="max-width:600px;">
        <div class="modal-header">
            <div class="modal-title" id="smsModalTitle">Add SMS Template</div>
            <button class="modal-close" onclick="closeModal('smsModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="smsForm">
                <div class="form-row">
                    <div class="form-group">
                        <label>Name *</label>
                        <input type="text" class="form-control" id="sms_name" required>
                    </div>
                    <div class="form-group">
                        <label>Code *</label>
                        <input type="text" class="form-control" id="sms_code" placeholder="welcome_sms" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select class="form-control" id="sms_category">
                        <option value="">None</option>
                        <option value="welcome">Welcome</option>
                        <option value="notification">Notification</option>
                        <option value="alert">Alert</option>
                        <option value="promotion">Promotion</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Message *</label>
                    <textarea class="form-control" id="sms_message" rows="5"></textarea>
                </div>
                <div class="form-group">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" id="sms_is_active" checked>
                        <span style="font-size:0.82rem;">Active</span>
                    </label>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('smsModal')">Cancel</button>
            <button class="btn btn-primary" onclick="saveSmsTemplate()">Save</button>
        </div>
    </div>
</div>
@endsection
@section('scripts')
const API = '/api/admin/communication/sms-templates';
let smsEditId = null;

async function loadSmsList() {
    const data = await apiFetch(API);
    const tbody = document.getElementById('smsTableBody');
    if (!data.length) { tbody.innerHTML = '<tr><td colspan="5" class="tbl-empty">No templates</td></tr>'; return; }
    tbody.innerHTML = data.map(t => `<tr>
        <td><strong>${t.name}</strong></td>
        <td><code>${t.code}</code></td>
        <td>${t.category || '-'}</td>
        <td>${t.is_active ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>'}</td>
        <td class="actions-cell">
            <button class="btn btn-primary btn-xs" onclick="editSms(${t.id})">Edit</button>
            <button class="btn btn-danger btn-xs" onclick="deleteSms(${t.id},'${t.name}')">Delete</button>
        </td>
    </tr>`).join('');
}

function openSmsAddModal() { smsEditId = null; document.getElementById('smsForm').reset(); document.getElementById('smsModalTitle').textContent = 'Add SMS Template'; openModal('smsModal'); }

async function editSms(id) {
    smsEditId = id; document.getElementById('smsModalTitle').textContent = 'Edit SMS Template';
    const data = await apiFetch(`${API}/${id}`);
    document.getElementById('sms_name').value = data.name || '';
    document.getElementById('sms_code').value = data.code || '';
    document.getElementById('sms_category').value = data.category || '';
    document.getElementById('sms_message').value = data.message || '';
    document.getElementById('sms_is_active').checked = data.is_active;
    openModal('smsModal');
}

async function saveSmsTemplate() {
    const body = { name: document.getElementById('sms_name').value, code: document.getElementById('sms_code').value, category: document.getElementById('sms_category').value, message: document.getElementById('sms_message').value, is_active: document.getElementById('sms_is_active').checked };
    try {
        if (smsEditId) { await apiFetch(`${API}/${smsEditId}`, { method: 'PUT', body }); }
        else { await apiFetch(API, { method: 'POST', body }); }
        closeModal('smsModal'); Swal.fire({ icon: 'success', title: 'Saved!', timer: 2000, showConfirmButton: false }); loadSmsList();
    } catch (e) { Swal.fire({ icon: 'error', title: 'Error', text: e.data?.message || 'Something went wrong' }); }
}

function deleteSms(id, name) {
    Swal.fire({ title: 'Delete?', text: `Delete ${name}?`, icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc2626', confirmButtonText: 'Delete' })
    .then(async (r) => { if (r.isConfirmed) { await apiFetch(`${API}/${id}`, { method: 'DELETE' }); Swal.fire({ icon: 'success', title: 'Deleted!', timer: 2000, showConfirmButton: false }); loadSmsList(); }});
}

loadSmsList();
@endsection
