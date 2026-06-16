@extends('admin.layouts.app')
@section('page_title', 'Notification Templates')
@section('content')
<div class="page-card">
    <div class="card-header">
        <div class="card-title">Notification Templates</div>
        <div class="filters-row">
            <div class="search-wrap">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                <input type="text" id="searchInput" placeholder="Search templates..." oninput="loadList()">
            </div>
            <button class="btn btn-success" onclick="openModal()">+ Add Template</button>
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead><tr><th>#</th><th>Type</th><th>Subject</th><th>Active</th><th>Actions</th></tr></thead>
            <tbody id="tableBody"><tr><td colspan="5" class="tbl-empty">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="modal">
    <div class="modal" style="max-width:640px;">
        <div class="modal-header">
            <div class="modal-title" id="modalTitle">Add Template</div>
            <button class="modal-close" onclick="closeModal('modal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="itemForm">
                <div class="form-row">
                    <div class="form-group"><label>Type *</label>
                        <select name="type" class="form-control" required>
                            <option value="email">Email</option>
                            <option value="sms">SMS</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer;margin-top:1.5rem;">
                            <input type="checkbox" name="is_active" value="1" checked style="width:16px;height:16px;"> Active
                        </label>
                    </div>
                </div>
                <div class="form-group"><label>Subject *</label><input name="subject" class="form-control" required><div class="invalid-feedback"></div></div>
                <div class="form-group"><label>Body</label><textarea name="body" class="form-control" rows="4"></textarea><div class="invalid-feedback"></div></div>
                <input type="hidden" name="edit_id" id="editId" value="">
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('modal')">Cancel</button>
            <button class="btn btn-primary" id="saveBtn" onclick="saveItem()">Save</button>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
const API = '/api/dashboard/notification-templates';
let editId = null;
async function loadList() {
    const s = document.getElementById('searchInput').value;
    const tbody = document.getElementById('tableBody');
    try {
        const items = await apiFetch(`${API}?search=${encodeURIComponent(s)}`);
        if (!items.length) { tbody.innerHTML = '<tr><td colspan="5" class="tbl-empty">No templates found</td></tr>'; return; }
        tbody.innerHTML = items.map((r, i) => `<tr>
            <td>${i+1}</td>
            <td><span class="badge badge-info">${r.type}</span></td>
            <td>${r.subject || '-'}</td>
            <td><span class="badge ${r.is_active ? 'badge-success' : 'badge-danger'}">${r.is_active ? 'Yes' : 'No'}</span></td>
            <td class="actions-cell">
                <button class="btn btn-primary btn-xs" onclick="editItem(${r.id})">Edit</button>
                <button class="btn btn-danger btn-xs" onclick="deleteItem(${r.id})">Delete</button>
            </td>
        </tr>`).join('');
    } catch (e) { tbody.innerHTML = '<tr><td colspan="5" class="tbl-empty">Error loading data</td></tr>'; }
}

function openModal() {
    editId = null; document.getElementById('modalTitle').textContent = 'Add Template';
    document.getElementById('itemForm').reset();
    document.querySelector('#itemForm [name="is_active"]').checked = true;
    clearFormErrors('itemForm'); openModal('modal');
}

async function editItem(id) {
    try {
        const r = await apiFetch(`${API}/${id}`);
        editId = id; document.getElementById('modalTitle').textContent = 'Edit Template';
        const form = document.getElementById('itemForm');
        form.querySelector('[name="type"]').value = r.type || 'email';
        form.querySelector('[name="subject"]').value = r.subject || '';
        form.querySelector('[name="body"]').value = r.body || '';
        form.querySelector('[name="is_active"]').checked = r.is_active !== false;
        clearFormErrors('itemForm'); openModal('modal');
    } catch (e) { Swal.fire('Error', 'Failed to load template', 'error'); }
}

async function saveItem() {
    clearFormErrors('itemForm');
    const form = document.getElementById('itemForm');
    const data = {
        type: form.querySelector('[name="type"]').value,
        subject: form.querySelector('[name="subject"]').value,
        body: form.querySelector('[name="body"]').value,
        is_active: form.querySelector('[name="is_active"]').checked,
    };
    const btn = document.getElementById('saveBtn'); btn.disabled = true; btn.textContent = 'Saving...';
    try {
        if (editId) await apiFetch(`${API}/${editId}`, { method: 'PUT', body: JSON.stringify(data) });
        else await apiFetch(API, { method: 'POST', body: JSON.stringify(data) });
        closeModal('modal'); Swal.fire('Success', editId ? 'Updated!' : 'Created!', 'success'); loadList();
    } catch (e) {
        if (e.errors) showFormErrors('itemForm', e.errors);
        else Swal.fire('Error', e.message || 'Save failed', 'error');
    } finally { btn.disabled = false; btn.textContent = 'Save'; }
}

function deleteItem(id) {
    Swal.fire({ title: 'Delete', text: 'Delete this template?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc3545', confirmButtonText: 'Delete' })
    .then(async (r) => {
        if (!r.isConfirmed) return;
        try { await apiFetch(`${API}/${id}`, { method: 'DELETE' }); Swal.fire('Deleted', 'Template deleted!', 'success'); loadList(); }
        catch (e) { Swal.fire('Error', e.message || 'Delete failed', 'error'); }
    });
}

loadList();
</script>
@endsection
