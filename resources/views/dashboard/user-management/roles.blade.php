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
    @foreach([
      ['name'=>'admin',   'label'=>'Administrator', 'icon'=>'👑', 'bg'=>'linear-gradient(135deg,#fef2f2,#fee2e2)', 'border'=>'#fca5a5', 'color'=>'#dc2626', 'desc'=>'Full system access — manages everything including users, roles and settings.', 'perms'=>'All permissions'],
      ['name'=>'manager', 'label'=>'Manager',        'icon'=>'🏢', 'bg'=>'linear-gradient(135deg,#fffbeb,#fef3c7)', 'border'=>'#fcd34d', 'color'=>'#d97706', 'desc'=>'Day-to-day operations — sales, inventory, staff and reports.', 'perms'=>'16 permissions'],
      ['name'=>'cashier', 'label'=>'Cashier',        'icon'=>'💰', 'bg'=>'linear-gradient(135deg,#eff6ff,#dbeafe)', 'border'=>'#93c5fd', 'color'=>'#2563eb', 'desc'=>'Operates POS terminal, processes sales and handles customers at their store.', 'perms'=>'6 permissions'],
      ['name'=>'user',    'label'=>'Basic User',     'icon'=>'👤', 'bg'=>'linear-gradient(135deg,#f5f3ff,#ede9fe)', 'border'=>'#c4b5fd', 'color'=>'#7c3aed', 'desc'=>'Limited access — can view dashboard only. No operational permissions.', 'perms'=>'1 permission'],
    ] as $r)
    <div style="background:{{ $r['bg'] }};border:1.5px solid {{ $r['border'] }};border-radius:14px;padding:1.25rem 1.25rem 1rem;position:relative;">
      <div style="display:flex;align-items:flex-start;gap:.85rem;">
        <div style="width:44px;height:44px;border-radius:12px;background:white;display:flex;align-items:center;justify-content:center;font-size:1.4rem;flex-shrink:0;box-shadow:0 2px 8px rgba(0,0,0,.08);">{{ $r['icon'] }}</div>
        <div style="flex:1;min-width:0;">
          <div style="font-weight:800;font-size:.95rem;color:{{ $r['color'] }};">{{ $r['label'] }}</div>
          <div style="font-size:.72rem;color:#64748b;margin-top:.25rem;line-height:1.5;">{{ $r['desc'] }}</div>
        </div>
      </div>
      <div style="display:flex;align-items:center;justify-content:space-between;margin-top:.85rem;padding-top:.75rem;border-top:1px solid {{ $r['border'] }}40;">
        <div style="font-size:.72rem;font-weight:600;color:{{ $r['color'] }};">{{ $r['perms'] }}</div>
        <div style="display:flex;gap:.4rem;">
          <div id="sys-{{ $r['name'] }}-count" style="font-size:.72rem;font-weight:600;color:#94a3b8;">— users</div>
          <button class="btn btn-sm" style="background:white;color:{{ $r['color'] }};border:1px solid {{ $r['border'] }};font-size:.72rem;padding:.2rem .6rem;" onclick="editSystemRole('{{ $r['name'] }}')">Edit Perms</button>
        </div>
      </div>
    </div>
    @endforeach
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
