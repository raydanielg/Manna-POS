@extends('admin.layouts.app')
@section('page_title', 'Staff Roles')
@section('content')
<div class="page-card">
    <div class="card-header">
        <div class="card-title">Staff Roles</div>
        <button class="btn btn-success" onclick="openRoleModal()">+ Add Role</button>
    </div>
    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead><tr><th>Role Name</th><th>Description</th><th>Staff Count</th><th>Actions</th></tr></thead>
            <tbody id="tableBody"><tr><td colspan="4" class="tbl-empty">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="roleModal">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title" id="modalTitle">Add Role</div>
            <button class="modal-close" onclick="closeModal('roleModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="roleForm">
                <div class="form-group">
                    <label>Role Name *</label>
                    <input type="text" class="form-control" id="name" required>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea class="form-control" id="description" rows="3"></textarea>
                    <div class="invalid-feedback"></div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('roleModal')">Cancel</button>
            <button class="btn btn-primary" id="saveBtn" onclick="saveRole()">Save Role</button>
        </div>
    </div>
</div>
@endsection
@section('scripts')
const API = '/api/admin/staff/roles';
let editId = null;

async function loadList() {
    const data = await apiFetch(API);
    const tbody = document.getElementById('tableBody');
    if (!data.length) { tbody.innerHTML = '<tr><td colspan="4" class="tbl-empty">No roles found</td></tr>'; return; }
    tbody.innerHTML = data.map(r => `<tr>
        <td><strong>${r.name}</strong></td>
        <td>${r.description || '-'}</td>
        <td><span class="badge badge-info">${r.staff_count ?? 0}</span></td>
        <td class="actions-cell">
            <button class="btn btn-primary btn-xs" onclick="editRole(${r.id})">Edit</button>
            <button class="btn btn-danger btn-xs" onclick="deleteRole(${r.id},'${r.name}')">Delete</button>
        </td>
    </tr>`).join('');
}

function openRoleModal() {
    editId = null; document.getElementById('modalTitle').textContent = 'Add Role';
    document.getElementById('roleForm').reset();
    document.getElementById('saveBtn').textContent = 'Save Role';
    openModal('roleModal');
}

async function editRole(id) {
    editId = id; document.getElementById('modalTitle').textContent = 'Edit Role';
    document.getElementById('saveBtn').textContent = 'Update Role';
    const data = await apiFetch(`${API}/${id}`);
    document.getElementById('name').value = data.name || '';
    document.getElementById('description').value = data.description || '';
    openModal('roleModal');
}

async function saveRole() {
    const body = {
        name: document.getElementById('name').value,
        description: document.getElementById('description').value,
    };
    try {
        if (editId) { await apiFetch(`${API}/${editId}`, { method: 'PUT', body }); }
        else { await apiFetch(API, { method: 'POST', body }); }
        closeModal('roleModal');
        Swal.fire({ icon: 'success', title: 'Success!', text: editId ? 'Role updated' : 'Role created', timer: 2000, showConfirmButton: false });
        loadList();
    } catch (e) {
        if (e.data && e.data.errors) {
            for (const [field, msgs] of Object.entries(e.data.errors)) {
                const el = document.getElementById(field);
                if (el) { el.classList.add('is-invalid'); el.nextElementSibling.textContent = msgs[0]; }
            }
        }
        Swal.fire({ icon: 'error', title: 'Error', text: e.data?.message || 'Something went wrong' });
    }
}

function deleteRole(id, name) {
    Swal.fire({
        title: 'Delete Role?', text: `Delete role "${name}"?`, icon: 'warning',
        showCancelButton: true, confirmButtonColor: '#dc2626', confirmButtonText: 'Yes, delete!'
    }).then(async (r) => {
        if (r.isConfirmed) {
            await apiFetch(`${API}/${id}`, { method: 'DELETE' });
            Swal.fire({ icon: 'success', title: 'Deleted!', timer: 2000, showConfirmButton: false });
            loadList();
        }
    });
}

loadList();
@endsection