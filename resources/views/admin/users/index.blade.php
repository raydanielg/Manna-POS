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
            <select class="filter-select" id="roleFilter" onchange="loadList()">
                <option value="">All Roles</option>
                <option value="admin">Admin</option>
                <option value="user">User</option>
            </select>
            <select class="filter-select" id="statusFilter" onchange="loadList()">
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
                <th>User</th><th>Role</th><th>Status</th><th>Registered</th><th style="text-align:right">Actions</th>
            </tr></thead>
            <tbody id="tableBody"><tr><td colspan="5" class="tbl-empty">Loading...</td></tr></tbody>
        </table>
    </div>
    <div id="paginationWrap" style="padding:1rem;display:flex;justify-content:center;gap:0.5rem;flex-wrap:wrap;"></div>
</div>

{{-- ═══ EDIT MODAL ═══ --}}
<div class="modal-overlay" id="editModal">
    <div class="modal" style="max-width:640px;">
        <div class="modal-header">
            <div class="modal-title" id="modalTitle">Edit User</div>
            <button class="modal-close" onclick="closeModal('editModal')">&times;</button>
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
                        <label>New Password (leave blank)</label>
                        <input type="password" class="form-control" id="edit_password">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <input type="hidden" id="edit_id" value="">
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('editModal')">Cancel</button>
            <button class="btn btn-primary" onclick="saveUser()">Save Changes</button>
        </div>
    </div>
</div>

{{-- ═══ VIEW MODAL ═══ --}}
<div class="modal-overlay" id="viewModal">
    <div class="modal" style="max-width:560px;">
        <div class="modal-header">
            <div class="modal-title" id="viewModalTitle">User Profile</div>
            <button class="modal-close" onclick="closeModal('viewModal')">&times;</button>
        </div>
        <div class="modal-body" style="padding:0;">
            <div class="profile-hero">
                <div class="profile-avatar" id="viewAvatar">A</div>
                <div class="profile-hero-info">
                    <div class="profile-name" id="viewName">-</div>
                    <div class="profile-email" id="viewEmail">-</div>
                    <div class="profile-meta">
                        <span id="viewRoleBadge"></span>
                        <span id="viewStatusBadge"></span>
                    </div>
                </div>
            </div>
            <div class="profile-details">
                <div class="detail-row">
                    <span class="detail-label"><span class="material-icons" style="font-size:16px;vertical-align:middle;margin-right:4px">phone</span> Phone</span>
                    <span class="detail-value" id="viewPhone">-</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><span class="material-icons" style="font-size:16px;vertical-align:middle;margin-right:4px">badge</span> Role</span>
                    <span class="detail-value" id="viewRole">-</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><span class="material-icons" style="font-size:16px;vertical-align:middle;margin-right:4px">verified</span> Email Verified</span>
                    <span class="detail-value" id="viewVerified">-</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><span class="material-icons" style="font-size:16px;vertical-align:middle;margin-right:4px">calendar_today</span> Joined</span>
                    <span class="detail-value" id="viewJoined">-</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><span class="material-icons" style="font-size:16px;vertical-align:middle;margin-right:4px">login</span> Last Login</span>
                    <span class="detail-value" id="viewLastLogin">-</span>
                </div>
            </div>
            <div class="profile-stats">
                <div class="profile-stat">
                    <div class="ps-num" id="viewBizCount">0</div>
                    <div class="ps-label">Businesses</div>
                </div>
                <div class="profile-stat">
                    <div class="ps-num" id="viewSubCount">0</div>
                    <div class="ps-label">Subscriptions</div>
                </div>
                <div class="profile-stat">
                    <div class="ps-num" id="viewInvoiceCount">0</div>
                    <div class="ps-label">Invoices</div>
                </div>
                <div class="profile-stat">
                    <div class="ps-num" id="viewTicketCount">0</div>
                    <div class="ps-label">Tickets</div>
                </div>
            </div>
        </div>
        <div class="modal-footer" style="border-top:none;padding-top:0;">
            <button class="btn btn-secondary" onclick="closeModal('viewModal')">Close</button>
        </div>
    </div>
</div>

<style>
    .user-cell { display:flex;align-items:center;gap:0.7rem; }
    .user-avatar-sm { width:32px;height:32px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.75rem;color:#fff;flex-shrink:0; }
    .user-name { font-size:0.85rem;font-weight:600;color:#0f172a; }
    .user-email { font-size:0.7rem;color:#94a3b8; }

    .action-btns { display:flex;gap:0.25rem;justify-content:flex-end; }
    .action-btn { width:30px;height:30px;border-radius:8px;border:none;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;transition:all 0.15s;background:transparent; }
    .action-btn .material-icons { font-size:18px; }
    .action-btn-view:hover { background:#eff6ff; }
    .action-btn-view .material-icons { color:#2563eb; }
    .action-btn-edit:hover { background:#fffbeb; }
    .action-btn-edit .material-icons { color:#d97706; }
    .action-btn-block:hover { background:#fef2f2; }
    .action-btn-block .material-icons { color:#dc2626; }
    .action-btn-delete:hover { background:#fef2f2; }
    .action-btn-delete .material-icons { color:#dc2626; }

    .profile-hero { background:linear-gradient(135deg,#0f172a,#1e293b);padding:1.5rem;display:flex;align-items:center;gap:1.25rem; }
    .profile-avatar { width:56px;height:56px;border-radius:16px;background:linear-gradient(135deg,#e03057,#7c3aed);display:flex;align-items:center;justify-content:center;font-size:1.3rem;font-weight:800;color:#fff;flex-shrink:0;box-shadow:0 4px 12px rgba(0,0,0,0.2); }
    .profile-hero-info { min-width:0; }
    .profile-name { font-size:1.15rem;font-weight:700;color:#fff; }
    .profile-email { font-size:0.78rem;color:#94a3b8;margin-top:0.15rem; }
    .profile-meta { display:flex;gap:0.4rem;margin-top:0.4rem; }

    .profile-details { padding:1.15rem 1.5rem; }
    .detail-row { display:flex;align-items:center;justify-content:space-between;padding:0.5rem 0;border-bottom:1px solid #f8fafc; }
    .detail-row:last-child { border-bottom:none; }
    .detail-label { font-size:0.78rem;font-weight:500;color:#64748b; }
    .detail-value { font-size:0.82rem;font-weight:600;color:#0f172a; }

    .profile-stats { display:grid;grid-template-columns:repeat(4,1fr);gap:0;border-top:1px solid #e9edf5;border-bottom:1px solid #e9edf5;margin:0 1.5rem; }
    .profile-stat { text-align:center;padding:0.9rem 0.5rem;border-right:1px solid #f1f5f9; }
    .profile-stat:last-child { border-right:none; }
    .ps-num { font-size:1.2rem;font-weight:800;color:#0f172a;line-height:1; }
    .ps-label { font-size:0.65rem;font-weight:600;color:#94a3b8;margin-top:0.2rem;text-transform:uppercase;letter-spacing:0.04em; }
</style>
@endsection
@section('scripts')
const API = '/api/admin/users';

async function loadList(page = 1) {
    const search = document.getElementById('searchInput').value;
    const role = document.getElementById('roleFilter').value;
    const status = document.getElementById('statusFilter').value;
    const data = await apiFetch(`${API}?page=${page}&search=${search}&role=${role}&status=${status}`);
    const tbody = document.getElementById('tableBody');
    if (!data.data || !data.data.length) { tbody.innerHTML = '<tr><td colspan="5" class="tbl-empty">No users found</td></tr>'; return; }
    const colors = ['#2563eb','#7c3aed','#e03057','#059669','#d97706','#0891b2','#dc2626'];
    tbody.innerHTML = data.data.map((u,i) => {
        const bg = colors[i % colors.length];
        const letter = (u.name || '?')[0].toUpperCase();
        return `<tr>
            <td>
                <div class="user-cell">
                    <div class="user-avatar-sm" style="background:${bg}">${letter}</div>
                    <div>
                        <div class="user-name">${escapeHtml(u.name)}</div>
                        <div class="user-email">${escapeHtml(u.email)}</div>
                    </div>
                </div>
            </td>
            <td><span class="badge ${u.role === 'admin' ? 'badge-warning' : 'badge-info'}">${u.role}</span></td>
            <td><span class="badge ${u.status === 'active' ? 'badge-success' : u.status === 'blocked' ? 'badge-danger' : 'badge-default'}">${u.status || 'active'}</span></td>
            <td style="font-size:0.78rem;color:#64748b">${u.created_at ? new Date(u.created_at).toLocaleDateString() : '-'}</td>
            <td>
                <div class="action-btns">
                    <button class="action-btn action-btn-view" onclick="viewUser(${u.id})" title="View"><span class="material-icons">visibility</span></button>
                    <button class="action-btn action-btn-edit" onclick="editUser(${u.id})" title="Edit"><span class="material-icons">edit</span></button>
                    ${u.status !== 'blocked'
                        ? `<button class="action-btn action-btn-block" onclick="blockUser(${u.id},'${escapeHtml(u.name)}')" title="Block"><span class="material-icons">block</span></button>`
                        : `<button class="action-btn action-btn-block" onclick="unblockUser(${u.id},'${escapeHtml(u.name)}')" title="Unblock"><span class="material-icons">check_circle</span></button>`}
                    <button class="action-btn action-btn-delete" onclick="deleteUser(${u.id},'${escapeHtml(u.name)}')" title="Delete"><span class="material-icons">delete</span></button>
                </div>
            </td>
        </tr>`;
    }).join('');
    const pw = document.getElementById('paginationWrap');
    if (data.last_page > 1) {
        pw.innerHTML = Array.from({length: data.last_page}, (_, i) =>
            `<button class="btn btn-sm ${i+1 === data.current_page ? 'btn-primary' : 'btn-secondary'}" onclick="loadList(${i+1})">${i+1}</button>`
        ).join('');
    } else { pw.innerHTML = ''; }
}

async function editUser(id) {
    const u = await apiFetch(`${API}/${id}`);
    document.getElementById('edit_id').value = u.id;
    document.getElementById('edit_name').value = u.name;
    document.getElementById('edit_email').value = u.email;
    document.getElementById('edit_phone').value = u.phone || '';
    document.getElementById('edit_role').value = u.role;
    document.getElementById('edit_status').value = u.status || 'active';
    document.getElementById('edit_password').value = '';
    document.getElementById('modalTitle').textContent = 'Edit User';
    openModal('editModal');
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
    closeModal('editModal');
    await loadList();
    Swal.fire('Updated', 'User has been updated', 'success');
}

async function viewUser(id) {
    const u = await apiFetch(`${API}/${id}`);
    document.getElementById('viewAvatar').textContent = u.avatar_letter || (u.name || '?')[0].toUpperCase();
    document.getElementById('viewName').textContent = u.name;
    document.getElementById('viewEmail').textContent = u.email;
    document.getElementById('viewPhone').textContent = u.phone || '-';
    document.getElementById('viewRole').textContent = u.role;
    document.getElementById('viewJoined').textContent = u.joined;
    document.getElementById('viewLastLogin').textContent = u.last_login || 'Never';
    document.getElementById('viewBizCount').textContent = u.businesses_count ?? 0;
    document.getElementById('viewSubCount').textContent = u.subscriptions_count ?? 0;
    document.getElementById('viewInvoiceCount').textContent = u.invoices_count ?? 0;
    document.getElementById('viewTicketCount').textContent = u.tickets_count ?? 0;
    document.getElementById('viewRoleBadge').innerHTML =
        `<span class="badge ${u.role === 'admin' ? 'badge-warning' : 'badge-info'}">${u.role}</span>`;
    document.getElementById('viewStatusBadge').innerHTML =
        `<span class="badge ${u.status === 'active' ? 'badge-success' : u.status === 'blocked' ? 'badge-danger' : 'badge-default'}">${u.status || 'active'}</span>`;
    document.getElementById('viewVerified').textContent =
        u.email_verified_at ? 'Yes (' + new Date(u.email_verified_at).toLocaleDateString() + ')' : 'No';
    openModal('viewModal');
}

async function blockUser(id, name) {
    const result = await Swal.fire({
        title: 'Block User',
        text: `Block ${name}?`,
        input: 'textarea', inputLabel: 'Reason (optional)', inputPlaceholder: 'Enter reason...',
        showCancelButton: true, confirmButtonColor: '#dc2626', confirmButtonText: 'Block',
    });
    if (!result.isConfirmed) return;
    await apiFetch(`${API}/${id}/block`, { reason: result.value || '' }, 'POST');
    await loadList();
    Swal.fire('Blocked', `${name} has been blocked`, 'success');
}

async function unblockUser(id, name) {
    const result = await Swal.fire({
        title: 'Unblock User?',
        text: `Restore access for ${name}?`,
        icon: 'question', showCancelButton: true, confirmButtonColor: '#16a34a', confirmButtonText: 'Unblock',
    });
    if (!result.isConfirmed) return;
    await apiFetch(`${API}/${id}/unblock`, {}, 'POST');
    await loadList();
    Swal.fire('Unblocked', `${name} can now log in`, 'success');
}

async function deleteUser(id, name) {
    const result = await Swal.fire({
        title: 'Delete User',
        text: `Permanently delete ${name}?`,
        icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc2626', confirmButtonText: 'Delete',
    });
    if (!result.isConfirmed) return;
    await apiFetch(`${API}/${id}`, {}, 'DELETE');
    await loadList();
    Swal.fire('Deleted', 'User has been deleted', 'success');
}

function escapeHtml(str) { if (!str) return ''; const d = document.createElement('div'); d.textContent = str; return d.innerHTML; }
loadList();
@endsection