@extends('layouts.dashboard')
@section('page_title','Business Locations')
@section('content')
<div class="dash-content">

<div class="page-card">
  <div class="card-header">
    <div>
      <div class="card-title">Business Locations / Stores</div>
      <div style="font-size:.78rem;color:#94a3b8;margin-top:.2rem;">Manage store branches and assign staff to specific locations</div>
    </div>
    <div class="filters-row">
      <div class="search-wrap">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
        <input type="text" id="searchInput" placeholder="Search locations…" oninput="debouncedLoad()">
      </div>
      <button class="btn btn-success" onclick="openAddModal()">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Add Location
      </button>
    </div>
  </div>

  {{-- Location cards grid --}}
  <div id="locGrid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1rem;padding:1rem 0;">
    <div style="grid-column:1/-1;text-align:center;padding:3rem 1rem;color:#94a3b8;">
      <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 1rem;opacity:.4;display:block;"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
      Loading locations…
    </div>
  </div>
</div>
</div>

{{-- ── Add/Edit Modal ──────────────────────────────────── --}}
<div class="modal-overlay" id="modal">
  <div class="modal" style="max-width:580px;">
    <div class="modal-header">
      <div class="modal-title" id="modal-title">Add Location</div>
      <button class="modal-close" onclick="closeModal('modal')">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <div class="modal-body">
      <form id="itemForm" onsubmit="return false;">
        <div class="form-row">
          <div class="form-group" style="flex:2;">
            <label class="form-label">Location Name *</label>
            <input name="name" class="form-control" required placeholder="e.g. Main Branch, City Center Store">
            <div class="invalid-feedback"></div>
          </div>
          <div class="form-group">
            <label class="form-label">Code / Short ID</label>
            <input name="code" class="form-control" placeholder="e.g. BRN-001">
            <div class="invalid-feedback"></div>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">City</label>
            <input name="city" class="form-control" placeholder="Dar es Salaam">
            <div class="invalid-feedback"></div>
          </div>
          <div class="form-group">
            <label class="form-label">Phone</label>
            <input name="phone" class="form-control" placeholder="+255 7XX XXX XXX">
            <div class="invalid-feedback"></div>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Address</label>
          <input name="address" class="form-control" placeholder="Street, area, building name…">
          <div class="invalid-feedback"></div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Email</label>
            <input name="email" type="email" class="form-control" placeholder="branch@company.com">
            <div class="invalid-feedback"></div>
          </div>
          <div class="form-group" style="flex:0 0 auto;min-width:130px;">
            <label class="form-label">Status</label>
            <label style="display:flex;align-items:center;gap:.6rem;cursor:pointer;margin-top:.5rem;">
              <div class="loc-toggle-wrap" onclick="toggleLocStatus(this)">
                <input type="hidden" name="is_active" id="inp-is_active" value="1">
                <div class="toggle-track active-track"><div class="toggle-thumb"></div></div>
              </div>
              <span id="locStatusLabel" style="font-size:.82rem;font-weight:600;color:#10b981;">Active</span>
            </label>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Notes</label>
          <textarea name="notes" class="form-control" rows="2" placeholder="Optional notes about this location…" style="resize:none;"></textarea>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal')">Cancel</button>
      <button class="btn btn-primary" id="saveBtn" onclick="saveItem()">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        Save Location
      </button>
    </div>
  </div>
</div>

@endsection
@section('scripts')
<style>
.toggle-track { width:42px;height:22px;border-radius:99px;background:#e2e8f0;position:relative;transition:background .2s;display:flex;align-items:center;padding:2px; }
.toggle-track.active-track { background:#10b981; }
.toggle-thumb { width:18px;height:18px;border-radius:50%;background:#fff;box-shadow:0 1px 4px rgba(0,0,0,.2);transition:transform .2s; }
.toggle-track.active-track .toggle-thumb { transform:translateX(20px); }
.loc-card { background:#fff;border:1.5px solid #e2e8f0;border-radius:14px;padding:1.25rem;transition:box-shadow .15s,border-color .15s; }
.loc-card:hover { box-shadow:0 4px 20px rgba(15,23,42,.08);border-color:#cbd5e1; }
</style>
<script>
const API = '/api/dashboard/locations';
let editId = null;
let debounceTimer;

function debouncedLoad() { clearTimeout(debounceTimer); debounceTimer = setTimeout(loadList, 280); }

function toggleLocStatus(wrap) {
  const track = wrap.querySelector('.toggle-track');
  const inp   = document.getElementById('inp-is_active');
  const label = document.getElementById('locStatusLabel');
  const isNowActive = !track.classList.contains('active-track');
  track.classList.toggle('active-track', isNowActive);
  inp.value = isNowActive ? '1' : '0';
  label.textContent = isNowActive ? 'Active' : 'Inactive';
  label.style.color = isNowActive ? '#10b981' : '#94a3b8';
}

async function loadList() {
  const s = document.getElementById('searchInput').value;
  const grid = document.getElementById('locGrid');
  grid.innerHTML = `<div style="grid-column:1/-1;text-align:center;padding:2rem;color:#94a3b8;">Loading…</div>`;
  try {
    const items = await apiFetch(`${API}?search=${encodeURIComponent(s)}`);
    if (!items.length) {
      grid.innerHTML = `<div style="grid-column:1/-1;text-align:center;padding:3rem 1rem;">
        <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 1rem;opacity:.3;display:block;color:#94a3b8;"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
        <div style="font-weight:600;color:#94a3b8;margin-bottom:.5rem;">No locations found</div>
        <div style="font-size:.8rem;color:#cbd5e1;">Add your first store location to assign cashiers</div>
        <button class="btn btn-primary" onclick="openAddModal()" style="margin-top:1rem;">Add First Location</button>
      </div>`;
      return;
    }
    grid.innerHTML = items.map(loc => {
      const staffCount = loc.staff_count ?? 0;
      const active = loc.is_active !== false;
      return `<div class="loc-card">
        <div style="display:flex;align-items:flex-start;gap:.85rem;margin-bottom:1rem;">
          <div style="width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,#6366f1,#8b5cf6);display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 2px 8px #6366f130;">
            <svg width="22" height="22" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
          </div>
          <div style="flex:1;min-width:0;">
            <div style="font-weight:800;font-size:.95rem;color:#1e293b;display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;">
              ${loc.name}
              ${loc.code ? `<span style="font-size:.68rem;background:#f1f5f9;border:1px solid #e2e8f0;color:#64748b;padding:.1rem .5rem;border-radius:4px;font-weight:700;">${loc.code}</span>` : ''}
            </div>
            ${loc.city ? `<div style="font-size:.75rem;color:#94a3b8;margin-top:.2rem;">📍 ${loc.city}</div>` : ''}
          </div>
          <span style="flex-shrink:0;font-size:.68rem;font-weight:700;padding:.2rem .55rem;border-radius:99px;${active?'background:#ecfdf5;color:#10b981;border:1px solid #6ee7b7':'background:#f8fafc;color:#94a3b8;border:1px solid #e2e8f0'}">
            ${active ? '● Active' : '○ Inactive'}
          </span>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.5rem;margin-bottom:1rem;">
          ${loc.phone ? `<div style="font-size:.75rem;color:#64748b;display:flex;align-items:center;gap:.35rem;"><svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.338c0 .414.336.75.75.75h1.5v5.25h-1.5a.75.75 0 00-.75.75v2.25a.75.75 0 00.75.75h2.25c.414 0 .75-.336.75-.75v-6l3.75-5.25H12a.75.75 0 00.75-.75V2.25A.75.75 0 0012 1.5H9a.75.75 0 00-.75.75V3H6a.75.75 0 00-.75.75v2.588z"/></svg>${loc.phone}</div>` : '<div></div>'}
          ${loc.email ? `<div style="font-size:.75rem;color:#64748b;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${loc.email}</div>` : '<div></div>'}
          ${loc.address ? `<div style="font-size:.75rem;color:#94a3b8;grid-column:1/-1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="${loc.address}">📍 ${loc.address}</div>` : ''}
        </div>
        <div style="display:flex;align-items:center;justify-content:space-between;padding-top:.75rem;border-top:1px solid #f1f5f9;">
          <div style="font-size:.78rem;color:#64748b;display:flex;align-items:center;gap:.4rem;">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
            <span style="font-weight:700;color:#6366f1;">${staffCount}</span> staff member${staffCount!==1?'s':''}
          </div>
          <div style="display:flex;gap:.4rem;">
            <button class="btn btn-sm btn-edit btn-icon" title="Edit Location" onclick="editItem(${loc.id})">
              <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </button>
            <button class="btn btn-sm btn-delete btn-icon" title="Delete Location" onclick="deleteItem(${loc.id},'${loc.name.replace(/'/g,"\\'")}')">
              <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
          </div>
        </div>
      </div>`;
    }).join('');
  } catch(e) {
    grid.innerHTML = `<div style="grid-column:1/-1;text-align:center;padding:2rem;color:#ef4444;">Error loading locations.</div>`;
  }
}

function openAddModal() {
  editId = null;
  document.getElementById('modal-title').textContent = 'Add Location';
  document.getElementById('saveBtn').innerHTML = `<svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Save Location`;
  document.getElementById('itemForm').reset();
  document.getElementById('inp-is_active').value = '1';
  document.querySelector('.toggle-track').classList.add('active-track');
  document.getElementById('locStatusLabel').textContent = 'Active';
  document.getElementById('locStatusLabel').style.color = '#10b981';
  document.querySelectorAll('.invalid-feedback').forEach(el => el.style.display='none');
  openModal('modal');
}

async function editItem(id) {
  try {
    const loc = await apiFetch(`${API}/${id}`);
    editId = id;
    document.getElementById('modal-title').textContent = 'Edit Location';
    document.getElementById('saveBtn').innerHTML = `<svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Update Location`;
    const f = document.getElementById('itemForm');
    f.reset();
    f.elements['name'].value    = loc.name;
    f.elements['code'].value    = loc.code||'';
    f.elements['city'].value    = loc.city||'';
    f.elements['phone'].value   = loc.phone||'';
    f.elements['address'].value = loc.address||'';
    f.elements['email'].value   = loc.email||'';
    f.elements['notes'].value   = loc.notes||'';
    const active = loc.is_active !== false;
    document.getElementById('inp-is_active').value = active ? '1' : '0';
    const track = document.querySelector('.toggle-track');
    track.classList.toggle('active-track', active);
    document.getElementById('locStatusLabel').textContent = active ? 'Active' : 'Inactive';
    document.getElementById('locStatusLabel').style.color = active ? '#10b981' : '#94a3b8';
    document.querySelectorAll('.invalid-feedback').forEach(el => el.style.display='none');
    openModal('modal');
  } catch(e) { showToast('Failed to load location.','error'); }
}

async function saveItem() {
  const btn = document.getElementById('saveBtn');
  const f   = document.getElementById('itemForm');
  document.querySelectorAll('.invalid-feedback').forEach(el => { el.style.display='none'; el.textContent=''; });
  const data = {
    name:      f.elements['name'].value.trim(),
    code:      f.elements['code'].value.trim() || null,
    city:      f.elements['city'].value.trim() || null,
    phone:     f.elements['phone'].value.trim() || null,
    address:   f.elements['address'].value.trim() || null,
    email:     f.elements['email'].value.trim() || null,
    notes:     f.elements['notes'].value.trim() || null,
    is_active: document.getElementById('inp-is_active').value === '1',
  };
  if (!data.name) {
    const fb = f.elements['name'].nextElementSibling;
    if (fb) { fb.textContent='Location name is required'; fb.style.display='block'; } return;
  }
  btn.disabled=true; btn.innerHTML='<svg class="spin" width="15" height="15" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-opacity=".25" stroke-width="3"/><path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg> Saving…';
  try {
    const res = editId
      ? await apiFetch(`${API}/${editId}`, { method:'PUT', body:JSON.stringify(data) })
      : await apiFetch(API, { method:'POST', body:JSON.stringify(data) });
    if (res.success) { showToast(editId?'Location updated.':'Location created.','success'); closeModal('modal'); loadList(); }
    else showToast(res.message||'Failed.','error');
  } catch(e) {
    if (e.errors) Object.entries(e.errors).forEach(([k,v]) => {
      const inp = f.elements[k]; if (!inp) return;
      const fb = inp.nextElementSibling; if (fb) { fb.textContent=v[0]; fb.style.display='block'; }
    });
    else showToast(e.message||'Failed.','error');
  } finally {
    btn.disabled=false;
    btn.innerHTML=`<svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> ${editId?'Update':'Save'} Location`;
  }
}

async function deleteItem(id, name) {
  const ok = await showConfirm(`Delete location "${name}"?`, 'Staff assigned to this location will be unassigned.');
  if (!ok) return;
  try {
    const res = await apiFetch(`${API}/${id}`, { method:'DELETE' });
    if (res.success) { showToast('Location deleted.','success'); loadList(); }
    else showToast(res.message||'Failed.','error');
  } catch(e) { showToast(e.message||'Failed.','error'); }
}

document.addEventListener('DOMContentLoaded', loadList);
</script>
@endsection
