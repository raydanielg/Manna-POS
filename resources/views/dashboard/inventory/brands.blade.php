@extends('layouts.dashboard')
@section('page_title','Brands')
@section('content')
<div class="dash-content">
<div class="page-card">
  <div class="card-header">
    <div class="card-title">Product Brands</div>
    <div class="filters-row">
      <div class="search-wrap">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
        <input type="text" id="searchInput" placeholder="Search brands..." oninput="loadList()">
      </div>
      <button class="btn btn-success" onclick="openAddModal()">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Add Brand
      </button>
    </div>
  </div>
  <div style="overflow-x:auto;">
    <table class="tbl">
      <thead><tr><th>#</th><th>Brand Name</th><th>Products</th><th>Description</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody id="tableBody"><tr><td colspan="6" class="tbl-empty">Loading...</td></tr></tbody>
    </table>
  </div>
</div>
</div>
<div class="modal-overlay" id="modal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title" id="modal-title">Add Brand</div>
      <button class="modal-close" onclick="closeModal('modal')"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <div class="modal-body">
      <form id="itemForm">
        <div class="form-group"><label class="form-label">Brand Name *</label><input name="name" class="form-control" required placeholder="Brand name"><div class="invalid-feedback"></div></div>
        <div class="form-group"><label class="form-label">Description</label><textarea name="description" class="form-control" placeholder="Description..."></textarea><div class="invalid-feedback"></div></div>
        <div class="form-group"><label class="form-label">Status</label><select name="status" class="form-control"><option value="active">Active</option><option value="inactive">Inactive</option></select><div class="invalid-feedback"></div></div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal')">Cancel</button>
      <button class="btn btn-primary" id="saveBtn" onclick="saveItem()">Save Brand</button>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
const API='/api/dashboard/brands'; let editId=null;
async function loadList(){
  const s=document.getElementById('searchInput').value; const tbody=document.getElementById('tableBody');
  tbody.innerHTML='<tr><td colspan="6" class="tbl-empty">Loading...</td></tr>';
  try {
    const items=await apiFetch(`${API}?search=${encodeURIComponent(s)}`);
    if(!items.length){tbody.innerHTML='<tr><td colspan="6" class="tbl-empty">No brands found.</td></tr>';return;}
    tbody.innerHTML=items.map((b,i)=>`<tr>
      <td class="text-slate-400">${i+1}</td><td class="font-semibold">${b.name}</td>
      <td><span class="badge badge-info">${b.products_count||0}</span></td>
      <td class="text-slate-400">${b.description||'—'}</td>
      <td><span class="badge ${b.status==='active'?'badge-success':'badge-gray'}">${b.status}</span></td>
      <td><div style="display:flex;gap:0.4rem;">
        <button class="btn btn-sm btn-edit btn-icon" onclick="editItem(${b.id})"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
        <button class="btn btn-sm btn-delete btn-icon" onclick="deleteItem(${b.id},'${b.name}')"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
      </div></td>
    </tr>`).join('');
  }catch(e){tbody.innerHTML='<tr><td colspan="6" class="tbl-empty">Error loading data.</td></tr>';}
}
function openAddModal(){editId=null;document.getElementById('modal-title').textContent='Add Brand';document.getElementById('itemForm').reset();clearFormErrors('itemForm');openModal('modal');}
async function editItem(id){
  try{const b=await apiFetch(`${API}/${id}`);editId=id;document.getElementById('modal-title').textContent='Edit Brand';
  const form=document.getElementById('itemForm');Object.entries(b).forEach(([k,v])=>{const el=form.querySelector(`[name="${k}"]`);if(el)el.value=v??'';});
  clearFormErrors('itemForm');openModal('modal');}catch(e){showToast('Failed to load','error');}
}
async function saveItem(){
  clearFormErrors('itemForm');const data=Object.fromEntries(new FormData(document.getElementById('itemForm')));
  const btn=document.getElementById('saveBtn');btn.disabled=true;btn.textContent='Saving...';
  try{if(editId)await apiFetch(`${API}/${editId}`,{method:'PUT',body:JSON.stringify(data)});
  else await apiFetch(API,{method:'POST',body:JSON.stringify(data)});
  closeModal('modal');showToast(editId?'Brand updated!':'Brand added!');loadList();}
  catch(e){if(e.errors)showFormErrors('itemForm',e.errors);else showToast(e.message||'Save failed','error');}
  finally{btn.disabled=false;btn.textContent='Save Brand';}
}
function deleteItem(id,name){
  showConfirm('Delete Brand',`Delete "${name}"?`,async()=>{
    try{await apiFetch(`${API}/${id}`,{method:'DELETE'});showToast('Brand deleted!');loadList();}
    catch(e){showToast('Delete failed','error');}
  });
}
loadList();
</script>
@endsection
