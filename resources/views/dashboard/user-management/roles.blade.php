@extends('layouts.dashboard')
@section('page_title','Roles & Permissions')
@section('content')
<div class="dash-content">

{{-- ── Default Role Cards ──────────────────────────────── --}}
<div style="margin-bottom:1.5rem;">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
    <div>
      <div style="font-size:1rem;font-weight:800;color:#1e293b;">System Roles</div>
      <div style="font-size:.8rem;color:#94a3b8;margin-top:2px;">Built-in roles with predefined permission sets</div>
    </div>
    <button class="btn btn-secondary" onclick="seedDefaults()" id="seedBtn">
      <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
      Sync Default Roles
    </button>
  </div>
  <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(230px,1fr));gap:1rem;" id="defaultRolesGrid">

    <div style="background:linear-gradient(135deg,#fef2f2,#fee2e2);border:1.5px solid #fca5a5;border-radius:14px;padding:1.25rem 1.25rem 1rem;">
      <div style="display:flex;align-items:flex-start;gap:.85rem;">
        <div style="width:44px;height:44px;border-radius:12px;background:white;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 2px 8px rgba(0,0,0,.08);">
          <svg width="22" height="22" fill="none" stroke="#dc2626" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.955 11.955 0 003 10c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
        </div>
        <div style="flex:1;min-width:0;"><div style="font-weight:800;font-size:.95rem;color:#dc2626;">Administrator</div><div style="font-size:.72rem;color:#64748b;margin-top:.25rem;line-height:1.5;">Full system access — manages everything including users, roles and settings.</div></div>
      </div>
      <div style="display:flex;align-items:center;justify-content:space-between;margin-top:.85rem;padding-top:.75rem;border-top:1px solid #fca5a540;">
        <div style="font-size:.72rem;font-weight:600;color:#dc2626;">All permissions</div>
        <div style="display:flex;gap:.4rem;align-items:center;">
          <div id="sys-admin-count" style="font-size:.72rem;font-weight:600;color:#94a3b8;">— users</div>
          <button class="btn btn-sm" style="background:white;color:#dc2626;border:1px solid #fca5a5;font-size:.72rem;padding:.2rem .6rem;" onclick="editSystemRole('admin')">Edit Perms</button>
        </div>
      </div>
    </div>

    <div style="background:linear-gradient(135deg,#fffbeb,#fef3c7);border:1.5px solid #fcd34d;border-radius:14px;padding:1.25rem 1.25rem 1rem;">
      <div style="display:flex;align-items:flex-start;gap:.85rem;">
        <div style="width:44px;height:44px;border-radius:12px;background:white;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 2px 8px rgba(0,0,0,.08);">
          <svg width="22" height="22" fill="none" stroke="#d97706" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
        </div>
        <div style="flex:1;min-width:0;"><div style="font-weight:800;font-size:.95rem;color:#d97706;">Manager</div><div style="font-size:.72rem;color:#64748b;margin-top:.25rem;line-height:1.5;">Day-to-day operations — sales, inventory, staff and reports.</div></div>
      </div>
      <div style="display:flex;align-items:center;justify-content:space-between;margin-top:.85rem;padding-top:.75rem;border-top:1px solid #fcd34d40;">
        <div style="font-size:.72rem;font-weight:600;color:#d97706;">16 permissions</div>
        <div style="display:flex;gap:.4rem;align-items:center;">
          <div id="sys-manager-count" style="font-size:.72rem;font-weight:600;color:#94a3b8;">— users</div>
          <button class="btn btn-sm" style="background:white;color:#d97706;border:1px solid #fcd34d;font-size:.72rem;padding:.2rem .6rem;" onclick="editSystemRole('manager')">Edit Perms</button>
        </div>
      </div>
    </div>

    <div style="background:linear-gradient(135deg,#eff6ff,#dbeafe);border:1.5px solid #93c5fd;border-radius:14px;padding:1.25rem 1.25rem 1rem;">
      <div style="display:flex;align-items:flex-start;gap:.85rem;">
        <div style="width:44px;height:44px;border-radius:12px;background:white;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 2px 8px rgba(0,0,0,.08);">
          <svg width="22" height="22" fill="none" stroke="#2563eb" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg>
        </div>
        <div style="flex:1;min-width:0;"><div style="font-weight:800;font-size:.95rem;color:#2563eb;">Cashier</div><div style="font-size:.72rem;color:#64748b;margin-top:.25rem;line-height:1.5;">Operates POS terminal, processes sales and handles customers at their store.</div></div>
      </div>
      <div style="display:flex;align-items:center;justify-content:space-between;margin-top:.85rem;padding-top:.75rem;border-top:1px solid #93c5fd40;">
        <div style="font-size:.72rem;font-weight:600;color:#2563eb;">6 permissions</div>
        <div style="display:flex;gap:.4rem;align-items:center;">
          <div id="sys-cashier-count" style="font-size:.72rem;font-weight:600;color:#94a3b8;">— users</div>
          <button class="btn btn-sm" style="background:white;color:#2563eb;border:1px solid #93c5fd;font-size:.72rem;padding:.2rem .6rem;" onclick="editSystemRole('cashier')">Edit Perms</button>
        </div>
      </div>
    </div>

    <div style="background:linear-gradient(135deg,#f5f3ff,#ede9fe);border:1.5px solid #c4b5fd;border-radius:14px;padding:1.25rem 1.25rem 1rem;">
      <div style="display:flex;align-items:flex-start;gap:.85rem;">
        <div style="width:44px;height:44px;border-radius:12px;background:white;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 2px 8px rgba(0,0,0,.08);">
          <svg width="22" height="22" fill="none" stroke="#7c3aed" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
        </div>
        <div style="flex:1;min-width:0;"><div style="font-weight:800;font-size:.95rem;color:#7c3aed;">Basic User</div><div style="font-size:.72rem;color:#64748b;margin-top:.25rem;line-height:1.5;">Limited access — can view dashboard only. No operational permissions.</div></div>
      </div>
      <div style="display:flex;align-items:center;justify-content:space-between;margin-top:.85rem;padding-top:.75rem;border-top:1px solid #c4b5fd40;">
        <div style="font-size:.72rem;font-weight:600;color:#7c3aed;">1 permission</div>
        <div style="display:flex;gap:.4rem;align-items:center;">
          <div id="sys-user-count" style="font-size:.72rem;font-weight:600;color:#94a3b8;">— users</div>
          <button class="btn btn-sm" style="background:white;color:#7c3aed;border:1px solid #c4b5fd;font-size:.72rem;padding:.2rem .6rem;" onclick="editSystemRole('user')">Edit Perms</button>
        </div>
      </div>
    </div>

  </div>
</div>

{{-- ── Custom Roles Table ──────────────────────────────── --}}
<div class="page-card">
  <div class="card-header">
    <div class="card-title">Custom Roles</div>
    <div class="filters-row">
      <div class="search-wrap">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
        <input type="text" id="searchInput" placeholder="Search roles…" oninput="loadList()">
      </div>
      <button class="btn btn-success" onclick="openAddModal()">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Create Custom Role
      </button>
    </div>
  </div>
  <div style="overflow-x:auto;">
    <table class="tbl">
      <thead>
        <tr><th>#</th><th>Role Name</th><th>Description</th><th>Permissions</th><th>Users</th><th>Actions</th></tr>
      </thead>
      <tbody id="tableBody">
        <tr><td colspan="6" class="tbl-empty">Loading…</td></tr>
      </tbody>
    </table>
  </div>
</div>
</div>

{{-- ── Add/Edit Role Modal ─────────────────────────────── --}}
<div class="modal-overlay" id="modal">
  <div class="modal modal-lg" style="max-width:720px;">
    <div class="modal-header">
      <div class="modal-title" id="modal-title">Create Role</div>
      <button class="modal-close" onclick="closeModal('modal')">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <div class="modal-body" style="max-height:78vh;overflow-y:auto;">
      <form id="itemForm" onsubmit="return false;">

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Role Name *</label>
            <input name="name" class="form-control" required placeholder="e.g. Warehouse Manager">
            <div class="invalid-feedback"></div>
          </div>
          <div class="form-group">
            <label class="form-label">Description</label>
            <input name="description" class="form-control" placeholder="Brief description of this role">
            <div class="invalid-feedback"></div>
          </div>
        </div>

        <div class="form-group">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem;">
            <label class="form-label" style="margin:0;">Permissions</label>
            <div style="display:flex;gap:.5rem;">
              <button type="button" class="btn btn-sm btn-secondary" onclick="setAll(true)">Select All</button>
              <button type="button" class="btn btn-sm btn-secondary" onclick="setAll(false)">Clear All</button>
            </div>
          </div>
          <div id="permGrid" style="display:flex;flex-direction:column;gap:.75rem;"></div>
        </div>

      </form>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal')">Cancel</button>
      <button class="btn btn-primary" id="saveBtn" onclick="saveItem()">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        Save Role
      </button>
    </div>
  </div>
</div>

@endsection
@section('scripts')
<style>
.perm-group-box { background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:10px;overflow:hidden; }
.perm-group-header { display:flex;align-items:center;justify-content:space-between;padding:.6rem 1rem;background:#f1f5f9;cursor:pointer;user-select:none; }
.perm-group-header:hover { background:#e9eef5; }
.perm-group-title { font-size:.7rem;font-weight:800;letter-spacing:.12em;text-transform:uppercase;color:#475569; }
.perm-group-body { display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:.4rem;padding:.75rem 1rem; }
.perm-check-label { display:flex;align-items:center;gap:.45rem;cursor:pointer;padding:.3rem .5rem;border-radius:6px;transition:background .1s; }
.perm-check-label:hover { background:#e2e8f0; }
.perm-check-label input[type=checkbox] { width:15px;height:15px;cursor:pointer;accent-color:#6366f1; }
.perm-name { font-size:.78rem;font-weight:600;color:#374151; }
</style>
<script>
const API = '/api/dashboard/roles';
const PERMS_API = '/api/dashboard/roles/permissions';
const SYSTEM_ROLES = ['admin','manager','cashier','user'];
let editId = null;
let permGroups = {};
let isSystemEdit = false;

const groupIcons = {
  Dashboard:'🏠', Sales:'💰', Purchases:'🛒', Inventory:'📦',
  Customers:'👥', Suppliers:'🏭', Reports:'📊', Expenses:'💸',
  Stock:'🔄', Settings:'⚙️', Users:'👤', Roles:'🔑', Plans:'📋'
};

async function loadPermGroups() {
  try { permGroups = await apiFetch(PERMS_API); } catch(e) {}
}

function renderPermGrid(selectedPerms=[]) {
  const grid = document.getElementById('permGrid');
  grid.innerHTML = Object.entries(permGroups).map(([group, perms]) => {
    const allChecked = perms.every(p => selectedPerms.includes(p));
    const checks = perms.map(p => {
      const short = p.split('.')[1];
      return `<label class="perm-check-label">
        <input type="checkbox" name="permissions[]" value="${p}" ${selectedPerms.includes(p)?'checked':''} onchange="updateGroupCheck('${group}')">
        <span class="perm-name">${short}</span>
      </label>`;
    }).join('');
    return `<div class="perm-group-box">
      <div class="perm-group-header" onclick="toggleGroup('${group}')">
        <div style="display:flex;align-items:center;gap:.5rem;">
          <span>${groupIcons[group]||'•'}</span>
          <span class="perm-group-title">${group}</span>
          <span id="cnt-${group}" style="font-size:.7rem;color:#6366f1;font-weight:700;">(${selectedPerms.filter(p=>perms.includes(p)).length}/${perms.length})</span>
        </div>
        <label class="perm-check-label" onclick="event.stopPropagation();" style="margin:0;">
          <input type="checkbox" id="all-${group}" ${allChecked?'checked':''} onchange="toggleGroupAll('${group}',this.checked)" style="accent-color:#6366f1;width:15px;height:15px;">
          <span style="font-size:.72rem;font-weight:600;color:#64748b;">All</span>
        </label>
      </div>
      <div class="perm-group-body" id="grp-${group}">${checks}</div>
    </div>`;
  }).join('');
}

function toggleGroup(group) {
  const body = document.getElementById('grp-'+group);
  body.style.display = body.style.display === 'none' ? '' : 'none';
}

function toggleGroupAll(group, checked) {
  const perms = permGroups[group] || [];
  perms.forEach(p => {
    const cb = document.querySelector(`input[name="permissions[]"][value="${p}"]`);
    if (cb) cb.checked = checked;
  });
  updateGroupCheck(group);
}

function updateGroupCheck(group) {
  const perms = permGroups[group] || [];
  const checked = perms.filter(p => {
    const cb = document.querySelector(`input[name="permissions[]"][value="${p}"]`);
    return cb && cb.checked;
  }).length;
  const allCb = document.getElementById('all-'+group);
  if (allCb) allCb.checked = checked === perms.length;
  const cnt = document.getElementById('cnt-'+group);
  if (cnt) cnt.textContent = `(${checked}/${perms.length})`;
}

function setAll(val) {
  document.querySelectorAll('input[name="permissions[]"]').forEach(cb => cb.checked = val);
  Object.keys(permGroups).forEach(group => updateGroupCheck(group));
}

async function loadList() {
  const s = document.getElementById('searchInput').value;
  const tbody = document.getElementById('tableBody');
  tbody.innerHTML = '<tr><td colspan="6" class="tbl-empty">Loading…</td></tr>';
  try {
    const items = await apiFetch(`${API}?search=${encodeURIComponent(s)}`);
    const custom = items.filter(r => !SYSTEM_ROLES.includes(r.name.toLowerCase()));
    // Update system role user counts
    items.forEach(r => {
      const el = document.getElementById(`sys-${r.name.toLowerCase()}-count`);
      if (el) el.textContent = `${r.user_count||0} user${r.user_count===1?'':'s'}`;
    });
    if (!custom.length) {
      tbody.innerHTML = '<tr><td colspan="6" class="tbl-empty">No custom roles yet. Create one above.</td></tr>';
      return;
    }
    tbody.innerHTML = custom.map((r,i) => {
      const perms = r.permissions || [];
      return `<tr>
        <td class="text-slate-400" style="font-size:.8rem;">${i+1}</td>
        <td>
          <div style="font-weight:700;font-size:.875rem;color:#1e293b;">${r.name}</div>
        </td>
        <td style="font-size:.8rem;color:#64748b;max-width:200px;">${r.description||'—'}</td>
        <td>
          <div style="display:flex;flex-wrap:wrap;gap:.2rem;max-width:280px;">
            ${perms.slice(0,4).map(p=>`<span style="font-size:.68rem;background:#f1f5f9;border:1px solid #e2e8f0;color:#475569;padding:.1rem .4rem;border-radius:4px;font-weight:600;">${p.split('.')[1]}</span>`).join('')}
            ${perms.length>4?`<span style="font-size:.68rem;background:#ede9fe;border:1px solid #c4b5fd;color:#7c3aed;padding:.1rem .4rem;border-radius:4px;font-weight:600;">+${perms.length-4} more</span>`:''}
            ${perms.length===0?'<span style="font-size:.75rem;color:#cbd5e1;">No permissions</span>':''}
          </div>
        </td>
        <td><span style="font-size:.82rem;font-weight:700;color:#6366f1;">${r.user_count||0}</span></td>
        <td>
          <div style="display:flex;gap:.35rem;">
            <button class="btn btn-sm btn-edit btn-icon" title="Edit" onclick="editItem(${r.id})">
              <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </button>
            <button class="btn btn-sm btn-delete btn-icon" title="Delete" onclick="deleteItem(${r.id},'${r.name.replace(/'/g,"\\'")}')">
              <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
          </div>
        </td>
      </tr>`;
    }).join('');
  } catch(e) { tbody.innerHTML='<tr><td colspan="6" class="tbl-empty" style="color:#ef4444;">Error loading roles.</td></tr>'; }
}

async function seedDefaults() {
  const btn = document.getElementById('seedBtn');
  btn.disabled = true; btn.textContent = 'Syncing…';
  try {
    const res = await apiFetch('/api/dashboard/roles/seed-defaults', { method:'POST' });
    if (res.success) { showToast(res.message, 'success'); loadList(); }
    else showToast(res.message||'Failed.','error');
  } catch(e) { showToast(e.message||'Failed.','error'); }
  finally { btn.disabled=false; btn.innerHTML=`<svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg> Sync Default Roles`; }
}

function openAddModal() {
  editId = null; isSystemEdit = false;
  document.getElementById('modal-title').textContent = 'Create Custom Role';
  document.getElementById('itemForm').reset();
  renderPermGrid([]);
  document.querySelectorAll('.invalid-feedback').forEach(el => el.style.display='none');
  openModal('modal');
}

async function editItem(id) {
  try {
    const r = await apiFetch(`${API}/${id}`);
    editId = id; isSystemEdit = false;
    document.getElementById('modal-title').textContent = `Edit Role — ${r.name}`;
    document.getElementById('itemForm').elements['name'].value = r.name;
    document.getElementById('itemForm').elements['description'].value = r.description||'';
    renderPermGrid(r.permissions||[]);
    document.querySelectorAll('.invalid-feedback').forEach(el => el.style.display='none');
    openModal('modal');
  } catch(e) { showToast('Failed to load role.','error'); }
}

async function editSystemRole(roleName) {
  try {
    const roles = await apiFetch(`${API}?search=${encodeURIComponent(roleName)}`);
    const r = roles.find(x => x.name.toLowerCase() === roleName.toLowerCase());
    if (!r) { showToast('Role not found. Click "Sync Default Roles" first.','info'); return; }
    editId = r.id; isSystemEdit = true;
    document.getElementById('modal-title').textContent = `Edit Permissions — ${r.name.charAt(0).toUpperCase()+r.name.slice(1)}`;
    document.getElementById('itemForm').elements['name'].value = r.name;
    document.getElementById('itemForm').elements['description'].value = r.description||'';
    renderPermGrid(r.name.toLowerCase()==='admin' ? Object.values(permGroups).flat() : (r.permissions||[]));
    document.querySelectorAll('.invalid-feedback').forEach(el => el.style.display='none');
    openModal('modal');
  } catch(e) { showToast('Failed to load role.','error'); }
}

async function saveItem() {
  const btn = document.getElementById('saveBtn');
  const f   = document.getElementById('itemForm');
  document.querySelectorAll('.invalid-feedback').forEach(el => { el.style.display='none'; el.textContent=''; });
  const name = f.elements['name'].value.trim();
  if (!name) {
    const fb = f.elements['name'].nextElementSibling;
    if (fb) { fb.textContent='Role name is required'; fb.style.display='block'; }
    return;
  }
  const perms = [...document.querySelectorAll('input[name="permissions[]"]:checked')].map(c => c.value);
  const data = { name, description: f.elements['description'].value.trim(), permissions: perms };
  btn.disabled=true; btn.innerHTML='<svg class="spin" width="15" height="15" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-opacity=".25" stroke-width="3"/><path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg> Saving…';
  try {
    const res = editId
      ? await apiFetch(`${API}/${editId}`, { method:'PUT', body:JSON.stringify(data) })
      : await apiFetch(API, { method:'POST', body:JSON.stringify(data) });
    if (res.success) { showToast(editId?'Role updated.':'Role created.','success'); closeModal('modal'); loadList(); }
    else showToast(res.message||'Failed.','error');
  } catch(e) {
    if (e.errors) Object.entries(e.errors).forEach(([k,v]) => {
      const inp = f.elements[k]; if (!inp) return;
      const fb = inp.nextElementSibling; if (fb) { fb.textContent=v[0]; fb.style.display='block'; }
    });
    else showToast(e.message||'Failed.','error');
  } finally {
    btn.disabled=false;
    btn.innerHTML=`<svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Save Role`;
  }
}

async function deleteItem(id, name) {
  const ok = await showConfirm(`Delete role "${name}"?`, 'Users with this role will no longer have any permissions.');
  if (!ok) return;
  try {
    const res = await apiFetch(`${API}/${id}`, { method:'DELETE' });
    if (res.success) { showToast('Role deleted.','success'); loadList(); }
    else showToast(res.message||'Failed.','error');
  } catch(e) { showToast(e.message||'Failed.','error'); }
}

document.addEventListener('DOMContentLoaded', async () => {
  await loadPermGroups();
  loadList();
});
</script>
@endsection
