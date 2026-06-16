@extends('admin.layouts.app')
@section('page_title', 'Email Templates')
@section('content')
<div class="page-card">
    <div class="card-header">
        <div class="card-title">Email Templates</div>
        <button class="btn btn-success" onclick="openAddModal()">+ Add Template</button>
    </div>
    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead><tr><th>Name</th><th>Subject</th><th>Code</th><th>Category</th><th>Active</th><th>Actions</th></tr></thead>
            <tbody id="tableBody"><tr><td colspan="6" class="tbl-empty">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="emailModal">
    <div class="modal" style="max-width:700px;">
        <div class="modal-header">
            <div class="modal-title" id="modalTitle">Add Template</div>
            <button class="modal-close" onclick="closeModal('emailModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="emailForm">
                <div class="form-row">
                    <div class="form-group">
                        <label>Name *</label>
                        <input type="text" class="form-control" id="name" required>
                    </div>
                    <div class="form-group">
                        <label>Code *</label>
                        <input type="text" class="form-control" id="code" placeholder="welcome_email" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Subject *</label>
                    <input type="text" class="form-control" id="subject" required>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select class="form-control" id="category">
                        <option value="">None</option>
                        <option value="welcome">Welcome</option>
                        <option value="invoice">Invoice</option>
                        <option value="notification">Notification</option>
                        <option value="promotion">Promotion</option>
                        <option value="alert">Alert</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Body *</label>
                    <textarea class="form-control" id="body" rows="8"></textarea>
                </div>
                <div class="form-group">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" id="is_active" checked>
                        <span style="font-size:0.82rem;">Active</span>
                    </label>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('emailModal')">Cancel</button>
            <button class="btn btn-primary" onclick="saveTemplate()">Save</button>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
const API = '/api/admin/communication/email-templates';
let editId = null;

async function loadList() {
    const data = await apiFetch(API);
    const tbody = document.getElementById('tableBody');
    if (!data.length) { tbody.innerHTML = '<tr><td colspan="6" class="tbl-empty">No templates</td></tr>'; return; }
    tbody.innerHTML = data.map(t => `<tr>
        <td><strong>${t.name}</strong></td>
        <td>${t.subject}</td>
        <td><code>${t.code}</code></td>
        <td>${t.category || '-'}</td>
        <td>${t.is_active ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>'}</td>
        <td class="actions-cell">
            <button class="btn btn-primary btn-xs" onclick="editTemplate(${t.id})">Edit</button>
            <button class="btn btn-danger btn-xs" onclick="deleteTemplate(${t.id},'${t.name}')">Delete</button>
        </td>
    </tr>`).join('');
}

async function openAddModal() {
    editId = null; document.getElementById('emailForm').reset();
    document.getElementById('modalTitle').textContent = 'Add Template';
    openModal('emailModal');
}

async function editTemplate(id) {
    editId = id; document.getElementById('modalTitle').textContent = 'Edit Template';
    const data = await apiFetch(`${API}/${id}`);
    document.getElementById('name').value = data.name || '';
    document.getElementById('code').value = data.code || '';
    document.getElementById('subject').value = data.subject || '';
    document.getElementById('category').value = data.category || '';
    document.getElementById('body').value = data.body || '';
    document.getElementById('is_active').checked = data.is_active;
    openModal('emailModal');
}

async function saveTemplate() {
    const body = { name: document.getElementById('name').value, code: document.getElementById('code').value, subject: document.getElementById('subject').value, category: document.getElementById('category').value, body: document.getElementById('body').value, is_active: document.getElementById('is_active').checked };
    try {
        if (editId) { await apiFetch(`${API}/${editId}`, { method: 'PUT', body }); }
        else { await apiFetch(API, { method: 'POST', body }); }
        closeModal('emailModal'); Swal.fire({ icon: 'success', title: 'Saved!', timer: 2000, showConfirmButton: false }); loadList();
    } catch (e) { Swal.fire({ icon: 'error', title: 'Error', text: e.data?.message || 'Something went wrong' }); }
}

function deleteTemplate(id, name) {
    Swal.fire({ title: 'Delete?', text: `Delete ${name}?`, icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc2626', confirmButtonText: 'Delete' })
    .then(async (r) => { if (r.isConfirmed) { await apiFetch(`${API}/${id}`, { method: 'DELETE' }); Swal.fire({ icon: 'success', title: 'Deleted!', timer: 2000, showConfirmButton: false }); loadList(); }});
}

loadList();
</script>
@endsection
