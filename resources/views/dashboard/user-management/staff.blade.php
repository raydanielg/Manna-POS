@extends('layouts.dashboard')
@section('page_title','Staff Management')
@section('content')
<div class="dash-content">
<div class="page-card">
  <div class="card-header">
    <div class="card-title">Staff Management</div>
    <div class="filters-row">
      <div class="search-wrap">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
        <input type="text" id="searchInput" placeholder="Search staff..." oninput="loadList()">
      </div>
      <button class="btn btn-success" onclick="openAddModal()">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Register Staff
      </button>
    </div>
  </div>
  <div style="overflow-x:auto;">
    <table class="tbl">
      <thead><tr><th>#</th><th>Name</th><th>Email</th><th>System Role</th><th>Permission Role</th><th>Status</th><th>Created</th><th>Actions</th></tr></thead>
      <tbody id="tableBody"><tr><td colspan="7" class="tbl-empty">Loading...</td></tr></tbody>
    </table>
  </div>
</div>
</div>
<div class="modal-overlay" id="modal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title" id="modal-title">Register Staff</div>
      <button class="modal-close" onclick="closeModal('modal')"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <div class="modal-body">
      <form id="itemForm">
        <div class="form-row">
          <div class="form-group"><label class="form-label">Full Name *</label><input name="name" class="form-control" required placeholder="Staff name"><div class="invalid-feedback"></div></div>
          <div class="form-group"><label class="form-label">Email *</label><input name="email" type="email" class="form-control" required placeholder="email@example.com"><div class="invalid-feedback"></div></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">Password <span id="pwHint" class="text-slate-400 font-normal">(required)</span></label><input name="password" type="password" class="form-control" placeholder="Min 8 characters"><div class="invalid-feedback"></div></div>
          <div class="form-group">
            <label class="form-label">System Role</label>
            <select name="role" class="form-control">
              <option value="cashier">Cashier</option>
              <option value="user">User</option>
              <option value="manager">Manager</option>
              <option value="admin">Admin</option>
            </select>
            <div class="invalid-feedback"></div>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Permission Role</label>
          <select name="role_id" class="form-control">
            <option value="">None</option>
            @foreach($roles as $role)
            <option value="{{ $role->id }}">{{ $role->name }} @if($role->description)- {{ $role->description }}@endif</option>
            @endforeach
          </select>
          <div class="invalid-feedback"></div>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal')">Cancel</button>
      <button class="btn btn-primary" id="saveBtn" onclick="saveItem()">Register Staff</button>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
const API='/api/dashboard/users'; const STAFF_LIST='/api/dashboard/staff'; let editId=null;
async function loadList(){
  const s=document.getElementById('searchInput').value; const tbody=document.getElementById('tableBody');
  tbody.innerHTML='<tr><td colspan="8" class="tbl-empty">Loading...</td></tr>';
  try{
    const items=await apiFetch(`${STAFF_LIST}?search=${encodeURIComponent(s)}`);
    if(!items.length){tbody.innerHTML='<tr><td colspan="8" class="tbl-empty">No staff registered yet.</td></tr>';return;}
    tbody.innerHTML=items.map((u,i)=>`<tr>
      <td class="text-slate-400">${i+1}</td>
      <td class="font-semibold">${u.name}</td>
      <td>${u.email}</td>
      <td><span class="badge ${u.role==='admin'?'badge-danger':u.role==='manager'?'badge-warning':u.role==='cashier'?'badge-info':'badge-gray'}">${u.role||'user'}</span></td>
      <td>${u.role_name?`<span class="badge badge-primary">${u.role_name}</span>`:'<span class="text-slate-400 text-xs">-</span>'}</td>
      <td><span class="badge ${u.status==='active'?'badge-success':'badge-danger'}">${u.status||'active'}</span></td>
      <td class="text-slate-400 text-xs">${u.created_at?new Date(u.created_at).toLocaleDateString():''}</td>
      <td><div style="display:flex;gap:0.4rem;">
        <button class="btn btn-sm btn-edit btn-icon" onclick="editItem(${u.id})"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
        <button class="btn btn-sm btn-delete btn-icon" onclick="deleteItem(${u.id},'${u.name}')"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
      </div></td>
    </tr>`).join('');
  }catch(e){tbody.innerHTML='<tr><td colspan="8" class="tbl-empty">Error loading data.</td></tr>';}
}
function openAddModal(){editId=null;document.getElementById('modal-title').textContent='Register Staff';document.getElementById('itemForm').reset();document.getElementById('pwHint').textContent='(required)';clearFormErrors('itemForm');openModal('modal');}
async function editItem(id){
  try{const u=await apiFetch(`${API}/${id}`);editId=id;document.getElementById('modal-title').textContent='Edit Staff';document.getElementById('pwHint').textContent='(leave blank to keep current)';
  const form=document.getElementById('itemForm');
  Object.entries(u).forEach(([k,v])=>{const el=form.querySelector(`[name="${k}"]`);if(el)el.value=v??'';});
  form.querySelector('[name="password"]').value='';
  clearFormErrors('itemForm');openModal('modal');}catch(e){showToast('Failed to load','error');}
}
async function saveItem(){
  clearFormErrors('itemForm');const data=Object.fromEntries(new FormData(document.getElementById('itemForm')));
  if(!editId && !data.password){showToast('Password is required for new staff','error');return;}
  if(!data.role_id) delete data.role_id;
  const btn=document.getElementById('saveBtn');btn.disabled=true;btn.textContent='Saving...';
  try{if(editId)await apiFetch(`${API}/${editId}`,{method:'PUT',body:JSON.stringify(data)});
  else await apiFetch(API,{method:'POST',body:JSON.stringify(data)});
  closeModal('modal');showToast(editId?'Staff updated!':'Staff registered!');loadList();}
  catch(e){if(e.errors)showFormErrors('itemForm',e.errors);else showToast(e.message||'Save failed','error');}
  finally{btn.disabled=false;btn.textContent='Register Staff';}
}
function deleteItem(id,name){
  showConfirm('Remove Staff',`Remove staff "${name}"? This cannot be undone.`,async()=>{
    try{await apiFetch(`${API}/${id}`,{method:'DELETE'});showToast('Staff removed!');loadList();}
    catch(e){showToast(e.message||'Delete failed','error');}
  });
}
loadList();
</script>
@endsection
