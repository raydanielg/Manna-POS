@extends('layouts.dashboard')
@section('page_title','Roles & Permissions')
@section('content')
<div class="dash-content">
<div class="page-card">
  <div class="card-header">
    <div class="card-title">Roles & Permissions</div>
    <div class="filters-row">
      <div class="search-wrap">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
        <input type="text" id="searchInput" placeholder="Search roles..." oninput="loadList()">
      </div>
      <button class="btn btn-success" onclick="openAddModal()">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Add Role
      </button>
    </div>
  </div>
  <div style="overflow-x:auto;">
    <table class="tbl">
      <thead><tr><th>#</th><th>Role Name</th><th>Description</th><th>Permissions</th><th>Actions</th></tr></thead>
      <tbody id="tableBody"><tr><td colspan="5" class="tbl-empty">Loading...</td></tr></tbody>
    </table>
  </div>
</div>
</div>
<div class="modal-overlay" id="modal">
  <div class="modal modal-lg">
    <div class="modal-header">
      <div class="modal-title" id="modal-title">Add Role</div>
      <button class="modal-close" onclick="closeModal('modal')"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <div class="modal-body">
      <form id="itemForm">
        <div class="form-row">
          <div class="form-group"><label class="form-label">Role Name *</label><input name="name" class="form-control" required placeholder="e.g. Manager"><div class="invalid-feedback"></div></div>
          <div class="form-group"><label class="form-label">Description</label><input name="description" class="form-control" placeholder="Role description"><div class="invalid-feedback"></div></div>
        </div>
        <div class="form-group">
          <label class="form-label">Permissions</label>
          <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:0.5rem;" id="permGrid">
            @foreach(['sales.view','sales.create','sales.edit','sales.delete','purchases.view','purchases.create','purchases.edit','purchases.delete','inventory.view','inventory.create','inventory.edit','inventory.delete','customers.view','customers.manage','suppliers.view','suppliers.manage','reports.view','settings.manage','users.manage','expenses.view','expenses.manage'] as $perm)
            <label style="display:flex;align-items:center;gap:0.4rem;font-size:0.8rem;cursor:pointer;">
              <input type="checkbox" name="permissions[]" value="{{ $perm }}" style="width:14px;height:14px;">
              {{ $perm }}
            </label>
            @endforeach
          </div>
          <div class="invalid-feedback"></div>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal')">Cancel</button>
      <button class="btn btn-primary" id="saveBtn" onclick="saveItem()">Save Role</button>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
const API='/api/dashboard/roles'; let editId=null;
async function loadList(){
  const s=document.getElementById('searchInput').value; const tbody=document.getElementById('tableBody');
  tbody.innerHTML='<tr><td colspan="5" class="tbl-empty">Loading...</td></tr>';
  try{
    const items=await apiFetch(`${API}?search=${encodeURIComponent(s)}`);
    if(!items.length){tbody.innerHTML='<tr><td colspan="5" class="tbl-empty">No roles found.</td></tr>';return;}
    tbody.innerHTML=items.map((r,i)=>{
      const perms=r.permissions?JSON.parse(r.permissions||'[]'):[];
      const permCount=Array.isArray(perms)?perms.length:0;
      return `<tr>
        <td class="text-slate-400">${i+1}</td>
        <td class="font-semibold">${r.name}</td>
        <td class="text-slate-500">${r.description||'-'}</td>
        <td><span class="badge badge-info">${permCount} permission${permCount!==1?'s':''}</span></td>
        <td><div style="display:flex;gap:0.4rem;">
          <button class="btn btn-sm btn-edit btn-icon" onclick="editItem(${r.id})"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
          <button class="btn btn-sm btn-delete btn-icon" onclick="deleteItem(${r.id},'${r.name}')"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
        </div></td>
      </tr>`;
    }).join('');
  }catch(e){tbody.innerHTML='<tr><td colspan="5" class="tbl-empty">Error loading data.</td></tr>';}
}
function openAddModal(){editId=null;document.getElementById('modal-title').textContent='Add Role';document.getElementById('itemForm').reset();document.querySelectorAll('#permGrid input[type=checkbox]').forEach(c=>c.checked=false);clearFormErrors('itemForm');openModal('modal');}
async function editItem(id){
  try{const r=await apiFetch(`${API}/${id}`);editId=id;document.getElementById('modal-title').textContent='Edit Role';
  const form=document.getElementById('itemForm');
  form.querySelector('[name="name"]').value=r.name||'';
  form.querySelector('[name="description"]').value=r.description||'';
  const perms=JSON.parse(r.permissions||'[]');
  document.querySelectorAll('#permGrid input[type=checkbox]').forEach(c=>{c.checked=perms.includes(c.value);});
  clearFormErrors('itemForm');openModal('modal');}catch(e){showToast('Failed to load','error');}
}
async function saveItem(){
  clearFormErrors('itemForm');
  const name=document.querySelector('[name="name"]').value;
  const desc=document.querySelector('[name="description"]').value;
  const perms=Array.from(document.querySelectorAll('#permGrid input[type=checkbox]:checked')).map(c=>c.value);
  const data={name,description:desc,permissions:perms};
  const btn=document.getElementById('saveBtn');btn.disabled=true;btn.textContent='Saving...';
  try{if(editId)await apiFetch(`${API}/${editId}`,{method:'PUT',body:JSON.stringify(data)});
  else await apiFetch(API,{method:'POST',body:JSON.stringify(data)});
  closeModal('modal');showToast(editId?'Role updated!':'Role added!');loadList();}
  catch(e){if(e.errors)showFormErrors('itemForm',e.errors);else showToast(e.message||'Save failed','error');}
  finally{btn.disabled=false;btn.textContent='Save Role';}
}
function deleteItem(id,name){
  showConfirm('Delete Role',`Delete role "${name}"?`,async()=>{
    try{await apiFetch(`${API}/${id}`,{method:'DELETE'});showToast('Role deleted!');loadList();}
    catch(e){showToast(e.message||'Delete failed','error');}
  });
}
loadList();
</script>
@endsection
