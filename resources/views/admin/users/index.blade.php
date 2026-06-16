@extends('admin.layouts.app')
@section('page_title', 'User Management')
@section('content')
<div class="dash-content">
<style>
.stat-cards{display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:1.5rem;}
.stat-card{background:#fff;border-radius:14px;border:1px solid #e9edf5;padding:1.1rem 1.25rem;display:flex;align-items:center;gap:1rem;}
.stat-icon{width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.stat-icon svg{width:22px;height:22px;}
.stat-num{font-size:1.5rem;font-weight:800;color:#0f172a;line-height:1;}
.stat-label{font-size:0.72rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin-top:2px;}
.u-avatar{width:34px;height:34px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.8rem;color:#fff;flex-shrink:0;}
.actions-cell{display:flex;gap:0.3rem;flex-wrap:nowrap;}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;}
.form-group{margin-bottom:0.9rem;}
.form-group label{display:block;font-size:0.78rem;font-weight:600;color:#374151;margin-bottom:0.35rem;}
.form-control{width:100%;padding:0.55rem 0.75rem;border:1.5px solid #e2e8f0;border-radius:10px;font-size:0.82rem;color:#0f172a;background:#f8fafc;outline:none;transition:all .2s;font-family:inherit;}
.form-control:focus{border-color:#e03057;background:#fff;box-shadow:0 0 0 3px rgba(224,48,87,.08);}
.invalid-feedback{display:none;font-size:0.72rem;color:#dc2626;margin-top:0.25rem;}
.modal-section-title{font-size:0.72rem;font-weight:700;color:#e03057;text-transform:uppercase;letter-spacing:.08em;margin:1rem 0 0.5rem;padding-bottom:0.4rem;border-bottom:1px solid #f1f5f9;}
.view-row{display:flex;justify-content:space-between;align-items:flex-start;padding:0.6rem 0;border-bottom:1px solid #f8fafc;}
.view-label{font-size:0.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;}
.view-val{font-size:0.85rem;font-weight:600;color:#0f172a;text-align:right;max-width:60%;}
.view-avatar{width:64px;height:64px;border-radius:16px;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:1.4rem;color:#fff;margin:0 auto 1rem;}
.sub-info{background:#f8fafc;border:1px solid #e9edf5;border-radius:10px;padding:0.75rem;margin-top:0.75rem;}
.sub-info-row{display:flex;justify-content:space-between;font-size:0.8rem;padding:0.25rem 0;}
.sub-info-row .key{color:#64748b;}
.sub-info-row .val{font-weight:600;color:#0f172a;}
@media(max-width:900px){.stat-cards{grid-template-columns:repeat(2,1fr);}}
@media(max-width:600px){.stat-cards{grid-template-columns:1fr;}.form-row{grid-template-columns:1fr;}}
</style>

<!-- Stats -->
<div class="stat-cards" id="statsRow">
  <div class="stat-card"><div class="stat-icon" style="background:#eff6ff;"><svg fill="none" stroke="#2563eb" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div><div><div class="stat-num" id="stat-total">—</div><div class="stat-label">Total Users</div></div></div>
  <div class="stat-card"><div class="stat-icon" style="background:#dcfce7;"><svg fill="none" stroke="#16a34a" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div><div><div class="stat-num" id="stat-active">—</div><div class="stat-label">Active</div></div></div>
  <div class="stat-card"><div class="stat-icon" style="background:#dbeafe;"><svg fill="none" stroke="#2563eb" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div><div><div class="stat-num" id="stat-trial">—</div><div class="stat-label">On Trial</div></div></div>
  <div class="stat-card"><div class="stat-icon" style="background:#fee2e2;"><svg fill="none" stroke="#dc2626" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg></div><div><div class="stat-num" id="stat-blocked">—</div><div class="stat-label">Blocked</div></div></div>
</div>

<div class="page-card">
  <div class="card-header">
    <div class="card-title">All Users</div>
    <div class="filters-row">
      <div class="search-wrap"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg><input type="text" id="searchInput" placeholder="Search users..." oninput="debouncedLoad()"></div>
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
      <button class="btn btn-primary" onclick="openAddModal()">+ Add User</button>
    </div>
  </div>
  <div style="overflow-x:auto;">
    <table class="tbl">
      <thead><tr>
        <th>User</th><th>Email</th><th>Phone</th><th>Role</th><th>Status</th><th>Subscription</th><th>Registered</th><th>Actions</th>
      </tr></thead>
      <tbody id="tableBody"><tr><td colspan="8" class="tbl-empty">Loading...</td></tr></tbody>
    </table>
  </div>
  <div id="paginationWrap" style="padding:1rem;display:flex;justify-content:center;gap:0.5rem;flex-wrap:wrap;"></div>
</div>

<!-- Add/Edit User Modal -->
<div class="modal-overlay" id="userModal">
  <div class="modal" style="max-width:640px;">
    <div class="modal-header">
      <div class="modal-title" id="modalTitle">Add User</div>
      <button class="modal-close" onclick="closeModal('userModal')">&times;</button>
    </div>
    <div class="modal-body">
      <form id="userForm">
        <div class="modal-section-title">Account Details</div>
        <div class="form-row">
          <div class="form-group"><label>Full Name *</label><input type="text" class="form-control" id="f_name" name="name" required><div class="invalid-feedback"></div></div>
          <div class="form-group"><label>Email *</label><input type="email" class="form-control" id="f_email" name="email" required><div class="invalid-feedback"></div></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Phone *</label><input type="tel" class="form-control" id="f_phone" name="phone" placeholder="+255 7xx xxx xxx"><div class="invalid-feedback"></div></div>
          <div class="form-group"><label>Password <span id="pwdHint" style="color:#94a3b8;font-weight:400;">(required for new)</span></label><input type="password" class="form-control" id="f_password" name="password"><div class="invalid-feedback"></div></div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Role</label>
            <select class="form-control" id="f_role" name="role">
              <option value="user">User</option>
              <option value="admin">Admin</option>
            </select>
          </div>
          <div class="form-group">
            <label>Status</label>
            <select class="form-control" id="f_status" name="status">
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
        </div>
        <div class="modal-section-title">Business Details</div>
        <div class="form-row">
          <div class="form-group"><label>Business Name</label><input type="text" class="form-control" id="f_business_name" name="business_name"><div class="invalid-feedback"></div></div>
          <div class="form-group">
            <label>Business Type</label>
            <select class="form-control" id="f_business_type" name="business_type">
              <option value="">Select type</option>
              <option value="retail">Retail</option>
              <option value="wholesale">Wholesale</option>
              <option value="restaurant">Restaurant</option>
              <option value="supermarket">Supermarket</option>
              <option value="pharmacy">Pharmacy</option>
              <option value="services">Services</option>
              <option value="other">Other</option>
            </select>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Country</label><input type="text" class="form-control" id="f_country" name="business_country" placeholder="Tanzania"><div class="invalid-feedback"></div></div>
          <div class="form-group">
            <label>Currency</label>
            <select class="form-control" id="f_currency" name="currency">
              <option value="TZS">TZS — Tanzanian Shilling</option>
              <option value="KES">KES — Kenyan Shilling</option>
              <option value="USD">USD — US Dollar</option>
              <option value="EUR">EUR — Euro</option>
              <option value="UGX">UGX — Ugandan Shilling</option>
            </select>
          </div>
        </div>
        <input type="hidden" id="f_id">
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('userModal')">Cancel</button>
      <button class="btn btn-primary" id="saveBtn" onclick="saveUser()">Save User</button>
    </div>
  </div>
</div>

<!-- View User Modal -->
<div class="modal-overlay" id="viewModal">
  <div class="modal" style="max-width:520px;">
    <div class="modal-header">
      <div class="modal-title">User Profile</div>
      <button class="modal-close" onclick="closeModal('viewModal')">&times;</button>
    </div>
    <div class="modal-body" id="viewModalBody">
      <div style="text-align:center;padding:1rem 0;">
        <div class="view-avatar" id="viewAvatar" style="background:linear-gradient(135deg,#2563eb,#7c3aed);">U</div>
        <div style="font-size:1.1rem;font-weight:800;color:#0f172a;" id="viewName">—</div>
        <div style="font-size:0.8rem;color:#64748b;margin-top:0.2rem;" id="viewEmail">—</div>
        <div style="margin-top:0.5rem;" id="viewBadges"></div>
      </div>
      <div id="viewDetails"></div>
      <div id="viewSubInfo" class="sub-info" style="display:none;">
        <div style="font-size:0.75rem;font-weight:700;color:#e03057;text-transform:uppercase;letter-spacing:.06em;margin-bottom:0.5rem;">Subscription</div>
        <div id="viewSubRows"></div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('viewModal')">Close</button>
      <button class="btn btn-primary" id="viewEditBtn" onclick="">Edit User</button>
    </div>
  </div>
</div>

<!-- Block User Modal -->
<div class="modal-overlay" id="blockModal">
  <div class="modal" style="max-width:420px;">
    <div class="modal-header">
      <div class="modal-title" id="blockModalTitle">Block User</div>
      <button class="modal-close" onclick="closeModal('blockModal')">&times;</button>
    </div>
    <div class="modal-body">
      <p style="font-size:0.85rem;color:#64748b;margin-bottom:1rem;" id="blockModalDesc">Are you sure you want to block this user?</p>
      <div class="form-group">
        <label>Reason (optional)</label>
        <textarea class="form-control" id="blockReason" rows="3" placeholder="Enter reason for blocking..."></textarea>
      </div>
      <input type="hidden" id="blockUserId">
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('blockModal')">Cancel</button>
      <button class="btn btn-danger" id="blockConfirmBtn" onclick="confirmBlock()">Block User</button>
    </div>
  </div>
</div>
</div>
@endsection
@section('scripts')
const API = '/api/admin/users';
let currentPage = 1;
let searchTimeout = null;

function debouncedLoad(){clearTimeout(searchTimeout);searchTimeout=setTimeout(()=>loadList(1),300);}

async function loadStats() {
  try {
    const [all, active, blocked] = await Promise.all([
      apiFetch(`${API}?per_page=1`),
      apiFetch(`${API}?status=active&per_page=1`),
      apiFetch(`${API}?status=blocked&per_page=1`),
    ]);
    document.getElementById('stat-total').textContent = all.total || 0;
    document.getElementById('stat-active').textContent = active.total || 0;
    document.getElementById('stat-blocked').textContent = blocked.total || 0;
    // Trial: fetch subscriptions count
    try {
      const subs = await apiFetch('/api/admin/subscriptions?status=trial&per_page=1');
      document.getElementById('stat-trial').textContent = subs.total || (Array.isArray(subs.data) ? subs.data.length : 0);
    } catch(e) { document.getElementById('stat-trial').textContent = '—'; }
  } catch(e) {}
}

async function loadList(page = 1) {
  currentPage = page;
  const search = document.getElementById('searchInput').value;
  const role   = document.getElementById('roleFilter').value;
  const status = document.getElementById('statusFilter').value;
  const tbody  = document.getElementById('tableBody');
  tbody.innerHTML = '<tr><td colspan="8" class="tbl-empty" style="padding:2rem;">Loading...</td></tr>';
  try {
    const data = await apiFetch(`${API}?page=${page}&search=${encodeURIComponent(search)}&role=${role}&status=${status}`);
    const rows = data.data || [];
    if (!rows.length) { tbody.innerHTML = '<tr><td colspan="8" class="tbl-empty">No users found</td></tr>'; renderPagination(data); return; }
    tbody.innerHTML = rows.map(u => {
      const initials = u.name ? u.name.split(' ').map(w=>w[0]).join('').slice(0,2).toUpperCase() : 'U';
      const colors = ['#2563eb','#7c3aed','#059669','#dc2626','#d97706','#0891b2'];
      const color  = colors[u.id % colors.length];
      const statusBadge = u.status === 'active' ? 'badge-success' : u.status === 'blocked' ? 'badge-danger' : 'badge-default';
      const roleBadge   = u.role === 'admin' ? 'badge-warning' : 'badge-info';
      let subBadge = '<span class="badge badge-default">None</span>';
      if (u.subscription) {
        const sb = u.subscription.status === 'active' ? 'badge-success' : u.subscription.status === 'trial' ? 'badge-info' : 'badge-danger';
        const days = u.subscription.expires_at ? Math.max(0, Math.floor((new Date(u.subscription.expires_at) - new Date()) / 86400000)) : null;
        subBadge = `<span class="badge ${sb}" title="${u.subscription.plan_name||''}">${u.subscription.status}${days!==null?' ('+days+'d)':''}</span>`;
      }
      return `<tr>
        <td style="display:flex;align-items:center;gap:0.65rem;padding:0.65rem 1.25rem;">
          <div class="u-avatar" style="background:${color};">${initials}</div>
          <div><div style="font-weight:700;font-size:0.82rem;color:#0f172a;">${escHtml(u.name)}</div><div style="font-size:0.72rem;color:#94a3b8;">${u.business_name ? escHtml(u.business_name) : 'No business'}</div></div>
        </td>
        <td>${escHtml(u.email)}</td>
        <td>${u.phone || '<span style="color:#c4c4c4;">—</span>'}</td>
        <td><span class="badge ${roleBadge}">${u.role}</span></td>
        <td><span class="badge ${statusBadge}">${u.status || 'active'}</span></td>
        <td>${subBadge}</td>
        <td style="font-size:0.75rem;color:#94a3b8;">${u.created_at ? new Date(u.created_at).toLocaleDateString() : '—'}</td>
        <td><div class="actions-cell">
          <button class="btn btn-secondary btn-xs" onclick="viewUser(${u.id})" title="View">
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
          </button>
          <button class="btn btn-primary btn-xs" onclick="editUser(${u.id})" title="Edit">
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
          </button>
          ${u.status !== 'blocked'
            ? `<button class="btn btn-warning btn-xs" onclick="openBlockModal(${u.id},'${escHtml(u.name).replace(/'/g,"\\'")}','block')" title="Block">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
               </button>`
            : `<button class="btn btn-success btn-xs" onclick="unblockUser(${u.id},'${escHtml(u.name).replace(/'/g,"\\'")}'" title="Unblock">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
               </button>`
          }
          <button class="btn btn-danger btn-xs" onclick="deleteUser(${u.id},'${escHtml(u.name).replace(/'/g,"\\'")}'" title="Delete">
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
          </button>
        </div></td>
      </tr>`;
    }).join('');
    renderPagination(data);
  } catch(e) {
    tbody.innerHTML = '<tr><td colspan="8" class="tbl-empty">Failed to load users. Please refresh.</td></tr>';
    console.error(e);
  }
}

function renderPagination(data) {
  const pw = document.getElementById('paginationWrap');
  if (!data || !data.last_page || data.last_page <= 1) { pw.innerHTML=''; return; }
  let html = '';
  for (let i = 1; i <= data.last_page; i++) {
    html += `<button class="btn btn-sm ${i===data.current_page?'btn-primary':'btn-secondary'}" onclick="loadList(${i})">${i}</button>`;
  }
  pw.innerHTML = html;
}

function openAddModal() {
  document.getElementById('f_id').value = '';
  document.getElementById('userForm').reset();
  document.getElementById('modalTitle').textContent = 'Add New User';
  document.getElementById('saveBtn').textContent = 'Create User';
  document.getElementById('pwdHint').textContent = '(required)';
  clearErrors();
  openModal('userModal');
}

async function editUser(id) {
  try {
    const u = await apiFetch(`${API}/${id}`);
    document.getElementById('f_id').value = u.id;
    document.getElementById('f_name').value = u.name || '';
    document.getElementById('f_email').value = u.email || '';
    document.getElementById('f_phone').value = u.phone || '';
    document.getElementById('f_password').value = '';
    document.getElementById('f_role').value = u.role || 'user';
    document.getElementById('f_status').value = u.status || 'active';
    document.getElementById('f_business_name').value = u.business_name || '';
    document.getElementById('f_business_type').value = u.business_type || '';
    document.getElementById('f_country').value = u.business_country || '';
    document.getElementById('f_currency').value = u.currency || 'TZS';
    document.getElementById('modalTitle').textContent = 'Edit User';
    document.getElementById('saveBtn').textContent = 'Update User';
    document.getElementById('pwdHint').textContent = '(leave blank to keep)';
    clearErrors();
    openModal('userModal');
  } catch(e) { Swal.fire('Error', 'Could not load user', 'error'); }
}

async function saveUser() {
  clearErrors();
  const id  = document.getElementById('f_id').value;
  const pwd = document.getElementById('f_password').value;
  const body = {
    name:             document.getElementById('f_name').value.trim(),
    email:            document.getElementById('f_email').value.trim(),
    phone:            document.getElementById('f_phone').value.trim(),
    role:             document.getElementById('f_role').value,
    status:           document.getElementById('f_status').value,
    business_name:    document.getElementById('f_business_name').value.trim(),
    business_type:    document.getElementById('f_business_type').value,
    business_country: document.getElementById('f_country').value.trim(),
    currency:         document.getElementById('f_currency').value,
  };
  if (pwd) { body.password = pwd; body.password_confirmation = pwd; }

  const btn = document.getElementById('saveBtn');
  btn.disabled = true; btn.textContent = 'Saving...';
  try {
    if (id) {
      await apiFetch(`${API}/${id}`, { method: 'PUT', body: JSON.stringify(body) });
      Swal.fire({ icon: 'success', title: 'Updated!', text: 'User updated successfully.', timer: 2000, showConfirmButton: false });
    } else {
      await apiFetch(API, { method: 'POST', body: JSON.stringify(body) });
      Swal.fire({ icon: 'success', title: 'Created!', text: 'User created successfully.', timer: 2000, showConfirmButton: false });
    }
    closeModal('userModal');
    loadList(currentPage);
    loadStats();
  } catch(e) {
    const errs = e.data?.errors;
    if (errs) {
      for (const [field, msgs] of Object.entries(errs)) {
        const el = document.getElementById('f_' + field) || document.querySelector(`[name="${field}"]`);
        if (el) { el.classList.add('is-invalid'); el.style.borderColor='#dc2626'; const fb=el.nextElementSibling; if(fb&&fb.classList.contains('invalid-feedback')){fb.textContent=Array.isArray(msgs)?msgs[0]:msgs;fb.style.display='block';} }
      }
    } else Swal.fire('Error', e.data?.message || 'Save failed. Please check the form.', 'error');
  } finally { btn.disabled=false; btn.textContent=id?'Update User':'Create User'; }
}

async function viewUser(id) {
  try {
    const u = await apiFetch(`${API}/${id}`);
    const initials = u.name ? u.name.split(' ').map(w=>w[0]).join('').slice(0,2).toUpperCase() : 'U';
    const colors = ['#2563eb','#7c3aed','#059669','#dc2626','#d97706','#0891b2'];
    const color  = colors[u.id % colors.length];
    document.getElementById('viewAvatar').textContent = initials;
    document.getElementById('viewAvatar').style.background = `linear-gradient(135deg,${color},${color}cc)`;
    document.getElementById('viewName').textContent = u.name || '—';
    document.getElementById('viewEmail').textContent = u.email || '—';
    const rb = u.role==='admin'?'badge-warning':'badge-info';
    const sb = u.status==='active'?'badge-success':u.status==='blocked'?'badge-danger':'badge-default';
    document.getElementById('viewBadges').innerHTML = `<span class="badge ${rb}" style="margin-right:.35rem;">${u.role}</span><span class="badge ${sb}">${u.status||'active'}</span>`;
    document.getElementById('viewDetails').innerHTML = `
      <div class="view-row"><span class="view-label">Phone</span><span class="view-val">${u.phone||'—'}</span></div>
      <div class="view-row"><span class="view-label">Business</span><span class="view-val">${u.business_name||'—'}</span></div>
      <div class="view-row"><span class="view-label">Type</span><span class="view-val">${u.business_type||'—'}</span></div>
      <div class="view-row"><span class="view-label">Country</span><span class="view-val">${u.business_country||'—'}</span></div>
      <div class="view-row"><span class="view-label">Currency</span><span class="view-val">${u.currency||'—'}</span></div>
      <div class="view-row"><span class="view-label">Registered</span><span class="view-val">${u.created_at?new Date(u.created_at).toLocaleString():'—'}</span></div>
      ${u.block_reason?`<div class="view-row"><span class="view-label">Block Reason</span><span class="view-val" style="color:#dc2626;">${escHtml(u.block_reason)}</span></div>`:''}
    `;
    if (u.subscription) {
      const ss = u.subscription.status==='active'?'badge-success':u.subscription.status==='trial'?'badge-info':'badge-danger';
      const days = u.subscription.expires_at ? Math.max(0, Math.floor((new Date(u.subscription.expires_at) - new Date()) / 86400000)) : null;
      document.getElementById('viewSubInfo').style.display='block';
      document.getElementById('viewSubRows').innerHTML = `
        <div class="sub-info-row"><span class="key">Plan</span><span class="val">${u.subscription.plan_name||'—'}</span></div>
        <div class="sub-info-row"><span class="key">Status</span><span class="val"><span class="badge ${ss}">${u.subscription.status}</span></span></div>
        ${days!==null?`<div class="sub-info-row"><span class="key">Days Left</span><span class="val">${days} days</span></div>`:''}
        <div class="sub-info-row"><span class="key">Expires</span><span class="val">${u.subscription.expires_at?new Date(u.subscription.expires_at).toLocaleDateString():'Never'}</span></div>
      `;
    } else { document.getElementById('viewSubInfo').style.display='none'; }
    document.getElementById('viewEditBtn').onclick = () => { closeModal('viewModal'); editUser(id); };
    openModal('viewModal');
  } catch(e) { Swal.fire('Error', 'Could not load user details', 'error'); }
}

function openBlockModal(id, name, action) {
  document.getElementById('blockUserId').value = id;
  document.getElementById('blockModalTitle').textContent = 'Block User';
  document.getElementById('blockModalDesc').textContent = `Block ${name}? They will not be able to access the system.`;
  document.getElementById('blockReason').value = '';
  document.getElementById('blockConfirmBtn').textContent = 'Block User';
  document.getElementById('blockConfirmBtn').className = 'btn btn-danger';
  openModal('blockModal');
}

async function confirmBlock() {
  const id     = document.getElementById('blockUserId').value;
  const reason = document.getElementById('blockReason').value;
  const btn    = document.getElementById('blockConfirmBtn');
  btn.disabled = true; btn.textContent = 'Blocking...';
  try {
    await apiFetch(`${API}/${id}/block`, { method: 'POST', body: JSON.stringify({ reason }) });
    closeModal('blockModal');
    Swal.fire({ icon: 'success', title: 'Blocked!', text: 'User has been blocked.', timer: 2000, showConfirmButton: false });
    loadList(currentPage); loadStats();
  } catch(e) { Swal.fire('Error', e.data?.message || 'Failed to block user', 'error'); }
  finally { btn.disabled=false; btn.textContent='Block User'; }
}

async function unblockUser(id, name) {
  const r = await Swal.fire({ title: 'Unblock User', text: `Restore access for ${name}?`, icon: 'question', showCancelButton: true, confirmButtonColor: '#16a34a', confirmButtonText: 'Yes, Unblock' });
  if (!r.isConfirmed) return;
  try {
    await apiFetch(`${API}/${id}/unblock`, { method: 'POST' });
    Swal.fire({ icon: 'success', title: 'Unblocked!', text: `${name} can now access the system.`, timer: 2000, showConfirmButton: false });
    loadList(currentPage); loadStats();
  } catch(e) { Swal.fire('Error', e.data?.message || 'Failed to unblock user', 'error'); }
}

async function deleteUser(id, name) {
  const r = await Swal.fire({ title: 'Delete User', text: `Permanently delete ${name}? This cannot be undone.`, icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc2626', confirmButtonText: 'Yes, Delete' });
  if (!r.isConfirmed) return;
  try {
    await apiFetch(`${API}/${id}`, { method: 'DELETE' });
    Swal.fire({ icon: 'success', title: 'Deleted!', text: 'User has been permanently deleted.', timer: 2000, showConfirmButton: false });
    loadList(currentPage); loadStats();
  } catch(e) { Swal.fire('Error', e.data?.message || 'Failed to delete user', 'error'); }
}

function clearErrors() {
  document.querySelectorAll('#userForm .form-control').forEach(el => { el.classList.remove('is-invalid'); el.style.borderColor=''; });
  document.querySelectorAll('#userForm .invalid-feedback').forEach(el => { el.textContent=''; el.style.display='none'; });
}

function escHtml(str) { if(!str) return ''; const d=document.createElement('div'); d.textContent=str; return d.innerHTML; }

// Update admin users API to include subscription info
// This relies on the API returning subscription data
loadList();
loadStats();
@endsection
