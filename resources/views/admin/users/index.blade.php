@extends('admin.layouts.app')
@section('page_title', 'User Management')
@section('content')
<div class="page-card">
    <div class="card-header">
        <div class="card-title">All Users</div>
        <div class="filters-row">
            <div class="search-wrap">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" id="searchInput" placeholder="Search users..." oninput="loadList()">
            </div>
            <select class="form-control" style="width:auto;padding:0.45rem 0.75rem;font-size:0.78rem;" id="roleFilter" onchange="loadList()">
                <option value="">All Roles</option>
                <option value="admin">Admin</option>
                <option value="user">User</option>
            </select>
            <select class="form-control" style="width:auto;padding:0.45rem 0.75rem;font-size:0.78rem;" id="statusFilter" onchange="loadList()">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="blocked">Blocked</option>
            </select>
            <a href="{{ route('admin.users.create') }}" class="btn btn-success">+ Add User</a>
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead><tr>
                <th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Status</th><th>Registered</th><th>Actions</th>
            </tr></thead>
            <tbody id="tableBody"><tr><td colspan="7" class="tbl-empty">Loading...</td></tr></tbody>
        </table>
    </div>
    <div id="paginationWrap" style="padding:1rem;display:flex;justify-content:center;gap:0.5rem;flex-wrap:wrap;"></div>
</div>

<div class="modal-overlay" id="userModal">
    <div class="modal" style="max-width:640px;">
        <div class="modal-header">
            <div class="modal-title" id="modalTitle">Edit User</div>
            <button class="modal-close" onclick="closeModal('userModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="userForm">
                <div class="form-row">
                    <div class="form-group">
                        <label>Name *</label>
                        <input type="text" class="form-control" id="edit_name" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" class="form-control" id="edit_email" required>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" class="form-control" id="edit_phone">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <select class="form-control" id="edit_role">
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" id="edit_status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>New Password (leave blank to keep)</label>
                        <input type="password" class="form-control" id="edit_password">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <input type="hidden" id="edit_id" value="">
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('userModal')">Cancel</button>
            <button class="btn btn-primary" onclick="saveUser()">Save Changes</button>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
const API = '/api/admin/users';

async function loadList(page = 1) {
    const search = document.getElementById('searchInput').value;
    const role = document.getElementById('roleFilter').value;
    const status = document.getElementById('statusFilter').value;
    const data = await apiFetch(`${API}?page=${page}&search=${search}&role=${role}&status=${status}`);
    const tbody = document.getElementById('tableBody');
    if (!data.data || !data.data.length) { tbody.innerHTML = '<tr><td colspan="7" class="tbl-empty">No users found</td></tr>'; return; }
    tbody.innerHTML = data.data.map(u => `<tr>
        <td><strong>${escapeHtml(u.name)}</strong></td>
        <td>${escapeHtml(u.email)}</td>
        <td>${u.phone || '-'}</td>
        <td><span class="badge ${u.role === 'admin' ? 'badge-warning' : 'badge-info'}">${u.role}</span></td>
        <td><span class="badge ${u.status === 'active' ? 'badge-success' : u.status === 'blocked' ? 'badge-danger' : 'badge-secondary'}">${u.status || 'active'}</span></td>
        <td>${u.created_at ? new Date(u.created_at).toLocaleDateString() : '-'}</td>
        <td class="actions-cell">
            <button class="btn btn-primary btn-xs" onclick="editUser(${u.id})">Edit</button>
            ${u.status !== 'blocked' ? `<button class="btn btn-danger btn-xs" onclick="blockUser(${u.id},'${escapeHtml(u.name)}')">Block</button>` : ''}
            <button class="btn btn-danger btn-xs" onclick="deleteUser(${u.id},'${escapeHtml(u.name)}')">Delete</button>
        </td>
    </tr>`).join('');
    const pw = document.getElementById('paginationWrap');
    if (data.last_page > 1) {
        let html = '';
        for (let i = 1; i <= data.last_page; i++) {
            html += `<button class="btn btn-sm ${i === data.current_page ? 'btn-primary' : 'btn-secondary'}" onclick="loadList(${i})">${i}</button>`;
        }
        pw.innerHTML = html;
    } else { pw.innerHTML = ''; }
}

async function editUser(id) {
    const u = await apiFetch(`${API}/${id}`, {}, 'GET');
    document.getElementById('edit_id').value = u.id;
    document.getElementById('edit_name').value = u.name;
    document.getElementById('edit_email').value = u.email;
    document.getElementById('edit_phone').value = u.phone || '';
    document.getElementById('edit_role').value = u.role;
    document.getElementById('edit_status').value = u.status || 'active';
    document.getElementById('edit_password').value = '';
    document.getElementById('modalTitle').textContent = 'Edit User';
    openModal('userModal');
}

async function saveUser() {
    const id = document.getElementById('edit_id').value;
    const body = {
        name: document.getElementById('edit_name').value,
        email: document.getElementById('edit_email').value,
        phone: document.getElementById('edit_phone').value,
        role: document.getElementById('edit_role').value,
        status: document.getElementById('edit_status').value,
    };
    const pw = document.getElementById('edit_password').value;
    if (pw) body.password = pw;
    await apiFetch(`${API}/${id}`, body, 'PUT');
    closeModal('userModal');
    await loadList();
    Swal.fire('Updated', 'User has been updated', 'success');
}

async function blockUser(id, name) {
    const result = await Swal.fire({
        title: 'Block User',
        text: `Block ${name}?`,
        input: 'textarea', inputLabel: 'Reason (optional)', inputPlaceholder: 'Enter reason...',
        showCancelButton: true, confirmButtonColor: '#dc3545', confirmButtonText: 'Block',
    });
    if (!result.isConfirmed) return;
    await apiFetch(`${API}/${id}/block`, { reason: result.value || '' }, 'POST');
    await loadList();
    Swal.fire('Blocked', `${name} has been blocked`, 'success');
}

async function deleteUser(id, name) {
    const result = await Swal.fire({ title: 'Delete User', text: `Permanently delete ${name}? This cannot be undone.`, icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc3545', confirmButtonText: 'Delete' });
    if (!result.isConfirmed) return;
    await apiFetch(`${API}/${id}`, {}, 'DELETE');
    await loadList();
    Swal.fire('Deleted', 'User has been deleted', 'success');
}

function escapeHtml(str) { if (!str) return ''; const d = document.createElement('div'); d.textContent = str; return d.innerHTML; }
loadList();
</script>
@endsection
