@extends('layouts.dashboard')
@section('page_title','Business Locations')
@section('content')
<div class="dash-content">
<div style="background:#fef3c7;border:1px solid #fde68a;border-radius:10px;padding:0.9rem 1.25rem;margin-bottom:1.25rem;font-size:0.85rem;color:#b45309;">
  <strong>Multi-location support:</strong> Manage stock and operations across multiple locations. Each location can have its own stock tracking.
</div>
<div class="page-card">
  <div class="card-header">
    <div class="card-title">Business Locations</div>
    <div class="filters-row">
      <div class="search-wrap">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
        <input type="text" id="searchInput" placeholder="Search locations..." oninput="loadList()">
      </div>
      <button class="btn btn-primary" onclick="openAddModal()">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Add Location
      </button>
    </div>
  </div>
  <div id="locationGrid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:1rem;padding:1.25rem;">
    <div style="color:#94a3b8;text-align:center;grid-column:1/-1;padding:2rem;">Loading...</div>
  </div>
</div>
</div>
<div class="modal-overlay" id="modal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title" id="modal-title">Add Location</div>
      <button class="modal-close" onclick="closeModal('modal')"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <div class="modal-body">
      <form id="itemForm">
        <div class="form-group"><label class="form-label">Location Name *</label><input name="name" class="form-control" required placeholder="e.g. Main Branch, Warehouse"><div class="invalid-feedback"></div></div>
        <div class="form-group"><label class="form-label">Address</label><textarea name="address" class="form-control" rows="2" placeholder="Full address..."></textarea><div class="invalid-feedback"></div></div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">City</label><input name="city" class="form-control" placeholder="City"><div class="invalid-feedback"></div></div>
          <div class="form-group"><label class="form-label">Country</label><input name="country" class="form-control" placeholder="Country"><div class="invalid-feedback"></div></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">Phone</label><input name="phone" class="form-control" placeholder="+1 234 567 890"><div class="invalid-feedback"></div></div>
          <div class="form-group">
            <label class="form-label">Status</label>
            <select name="status" class="form-control"><option value="active">Active</option><option value="inactive">Inactive</option></select>
          </div>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal')">Cancel</button>
      <button class="btn btn-primary" id="saveBtn" onclick="saveItem()">Save Location</button>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
const API='/api/dashboard/business-locations'; let editId=null;
async function loadList(){
  const s=document.getElementById('searchInput').value;
  const grid=document.getElementById('locationGrid');
  grid.innerHTML='<div style="color:#94a3b8;text-align:center;grid-column:1/-1;padding:2rem;">Loading...</div>';
  try{
    const items=await apiFetch(`${API}?search=${encodeURIComponent(s)}`);
    if(!items.length){grid.innerHTML='<div style="color:#94a3b8;text-align:center;grid-column:1/-1;padding:2rem;">No locations found. Add your first location.</div>';return;}
    grid.innerHTML=items.map(loc=>`<div style="border:1px solid #e9edf5;border-radius:12px;padding:1.25rem;background:#fff;">
      <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:0.75rem;">
        <div>
          <div style="font-size:1rem;font-weight:700;color:#1e293b;">${loc.name}</div>
          <span class="badge ${loc.status==='active'?'badge-success':'badge-gray'}" style="margin-top:0.25rem;">${loc.status||'active'}</span>
        </div>
        <div style="display:flex;gap:0.4rem;">
          <button class="btn btn-sm btn-edit btn-icon" onclick="editItem(${loc.id})"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
          <button class="btn btn-sm btn-delete btn-icon" onclick="deleteItem(${loc.id},'${loc.name}')"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
        </div>
      </div>
      ${loc.address?`<div style="font-size:0.82rem;color:#64748b;">${loc.address}${loc.city?', '+loc.city:''}${loc.country?', '+loc.country:''}</div>`:''}
      ${loc.phone?`<div style="font-size:0.82rem;color:#2563eb;margin-top:0.25rem;">${loc.phone}</div>`:''}
    </div>`).join('');
  }catch(e){grid.innerHTML='<div style="color:#e03057;text-align:center;grid-column:1/-1;padding:2rem;">Error loading locations.</div>';}
}
function openAddModal(){editId=null;document.getElementById('modal-title').textContent='Add Location';document.getElementById('itemForm').reset();clearFormErrors('itemForm');openModal('modal');}
async function editItem(id){
  try{const loc=await apiFetch(`${API}/${id}`);editId=id;document.getElementById('modal-title').textContent='Edit Location';
  const form=document.getElementById('itemForm');Object.entries(loc).forEach(([k,v])=>{const el=form.querySelector(`[name="${k}"]`);if(el)el.value=v??'';});
  clearFormErrors('itemForm');openModal('modal');}catch(e){showToast('Failed to load','error');}
}
async function saveItem(){
  clearFormErrors('itemForm');const data=Object.fromEntries(new FormData(document.getElementById('itemForm')));
  const btn=document.getElementById('saveBtn');btn.disabled=true;btn.textContent='Saving...';
  try{if(editId)await apiFetch(`${API}/${editId}`,{method:'PUT',body:JSON.stringify(data)});
  else await apiFetch(API,{method:'POST',body:JSON.stringify(data)});
  closeModal('modal');showToast(editId?'Location updated!':'Location added!');loadList();}
  catch(e){if(e.errors)showFormErrors('itemForm',e.errors);else showToast(e.message||'Save failed','error');}
  finally{btn.disabled=false;btn.textContent='Save Location';}
}
function deleteItem(id,name){
  showConfirm('Delete Location',`Delete "${name}"?`,async()=>{
    try{await apiFetch(`${API}/${id}`,{method:'DELETE'});showToast('Location deleted!');loadList();}
    catch(e){showToast(e.message||'Delete failed','error');}
  });
}
loadList();
</script>
@endsection
