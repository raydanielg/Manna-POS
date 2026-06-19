@extends('layouts.dashboard')
@section('page_title','Units of Measure')
@section('content')
<div class="dash-content">
<div class="page-card">
  <div class="card-header">
    <div class="card-title">Units of Measure</div>
    <div class="filters-row">
      <div class="search-wrap">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
        <input type="text" id="searchInput" placeholder="Search units..." oninput="loadList()">
      </div>
      <select id="decimalFilter" class="form-control" style="width:140px;" onchange="loadList()">
        <option value="">All Types</option>
        <option value="1">Decimal</option>
        <option value="0">Whole Number</option>
      </select>
      <button class="btn btn-info" onclick="openImportModal()">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
        Import
      </button>
      <button class="btn btn-success" onclick="openAddModal()">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Add Unit
      </button>
    </div>
  </div>
  <div style="overflow-x:auto;">
    <table class="tbl">
      <thead><tr><th>#</th><th>Unit Name</th><th>Short Name</th><th>Allow Decimal</th><th>Products</th><th>Actions</th></tr></thead>
      <tbody id="tableBody"><tr><td colspan="6" class="tbl-empty">Loading...</td></tr></tbody>
    </table>
  </div>
</div>
</div>
<div class="modal-overlay" id="modal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title" id="modal-title">Add Unit</div>
      <button class="modal-close" onclick="closeModal('modal')"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <div class="modal-body">
      <form id="itemForm">
        <div class="form-row">
          <div class="form-group"><label class="form-label">Unit Name *</label><input name="name" class="form-control" required placeholder="e.g. Kilogram"><div class="invalid-feedback"></div></div>
          <div class="form-group"><label class="form-label">Short Name *</label><input name="short_name" class="form-control" required placeholder="e.g. kg"><div class="invalid-feedback"></div></div>
        </div>
        <div class="form-group">
          <label class="form-label" style="display:flex;align-items:center;gap:0.5rem;cursor:pointer;">
            <input name="allow_decimal" type="checkbox" value="1" style="width:16px;height:16px;cursor:pointer;">
            Allow Decimal Quantities
          </label>
          <div class="invalid-feedback"></div>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal')">Cancel</button>
      <button class="btn btn-primary" id="saveBtn" onclick="saveItem()">Save Unit</button>
    </div>
  </div>
</div>
<div class="modal-overlay" id="importModal">
  <div class="modal modal-lg">
    <div class="modal-header">
      <div class="modal-title">Import Units from Library</div>
      <button class="modal-close" onclick="closeModal('importModal')"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <div class="modal-body">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
        <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer;font-weight:500;">
          <input type="checkbox" id="selectAllImport" onchange="toggleSelectAll()" style="width:16px;height:16px;cursor:pointer;">
          Select All
        </label>
        <span id="importCount" class="text-sm text-slate-500">0 selected</span>
      </div>
      <div id="importList" style="max-height:400px;overflow-y:auto;border:1px solid #e5e7eb;border-radius:8px;padding:0.5rem;">
        <div class="tbl-empty">Loading...</div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('importModal')">Cancel</button>
      <button class="btn btn-success" id="importBtn" onclick="importSelected()">Import Selected</button>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
const API='/api/dashboard/units'; let editId=null;
async function loadList(){
  const s=document.getElementById('searchInput').value;
  const dec=document.getElementById('decimalFilter').value;
  const tbody=document.getElementById('tableBody');
  tbody.innerHTML='<tr><td colspan="6" class="tbl-empty">Loading...</td></tr>';
  try{
    const items=await apiFetch(`${API}?search=${encodeURIComponent(s)}${dec?'&allow_decimal='+dec:''}`);
    if(!items.length){tbody.innerHTML='<tr><td colspan="6" class="tbl-empty">No units found.</td></tr>';return;}
    tbody.innerHTML=items.map((u,i)=>`<tr>
      <td class="text-slate-400">${i+1}</td><td class="font-semibold">${u.name}</td>
      <td><span class="badge badge-info">${u.short_name}</span></td>
      <td>${u.allow_decimal?'<span class="badge badge-success">Yes</span>':'<span class="badge badge-gray">No</span>'}</td>
      <td><span class="badge badge-info">${u.products_count||0}</span></td>
      <td><div style="display:flex;gap:0.4rem;">
        <button class="btn btn-sm btn-edit btn-icon" onclick="editItem(${u.id})"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
        <button class="btn btn-sm btn-delete btn-icon" onclick="deleteItem(${u.id},'${u.name}')"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
      </div></td>
    </tr>`).join('');
  }catch(e){tbody.innerHTML='<tr><td colspan="6" class="tbl-empty">Error loading data.</td></tr>';}
}
function openAddModal(){editId=null;document.getElementById('modal-title').textContent='Add Unit';document.getElementById('itemForm').reset();clearFormErrors('itemForm');openModal('modal');}
async function editItem(id){
  try{const u=await apiFetch(`${API}/${id}`);editId=id;document.getElementById('modal-title').textContent='Edit Unit';
  const form=document.getElementById('itemForm');
  Object.entries(u).forEach(([k,v])=>{const el=form.querySelector(`[name="${k}"]`);if(el){if(el.type==='checkbox')el.checked=!!v;else el.value=v??'';}});
  clearFormErrors('itemForm');openModal('modal');}catch(e){showToast('Failed to load','error');}
}
async function saveItem(){
  clearFormErrors('itemForm');
  const form=document.getElementById('itemForm');
  const data=Object.fromEntries(new FormData(form));
  data.allow_decimal = form.querySelector('[name="allow_decimal"]').checked ? 1 : 0;
  const btn=document.getElementById('saveBtn');btn.disabled=true;btn.textContent='Saving...';
  try{if(editId)await apiFetch(`${API}/${editId}`,{method:'PUT',body:JSON.stringify(data)});
  else await apiFetch(API,{method:'POST',body:JSON.stringify(data)});
  closeModal('modal');showToast(editId?'Unit updated!':'Unit added!');loadList();}
  catch(e){if(e.errors)showFormErrors('itemForm',e.errors);else showToast(e.message||'Save failed','error');}
  finally{btn.disabled=false;btn.textContent='Save Unit';}
}
function deleteItem(id,name){
  showConfirm('Delete Unit',`Delete "${name}"?`,async()=>{
    try{await apiFetch(`${API}/${id}`,{method:'DELETE'});showToast('Unit deleted!');loadList();}
    catch(e){showToast('Delete failed','error');}
  });
}
async function openImportModal(){
  document.getElementById('selectAllImport').checked=false;
  document.getElementById('importCount').textContent='0 selected';
  const list=document.getElementById('importList');
  list.innerHTML='<div class="tbl-empty">Loading...</div>';
  openModal('importModal');
  try{
    const items=await apiFetch(`${API}/library`);
    if(!items.length){list.innerHTML='<div class="tbl-empty">No library items available.</div>';return;}
    list.innerHTML=items.map((it,idx)=>`<label style="display:flex;align-items:center;gap:0.75rem;padding:0.5rem;border-radius:6px;cursor:pointer;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
      <input type="checkbox" class="import-checkbox" data-item='${JSON.stringify(it).replace(/'/g,"&#39;")}' onchange="updateImportCount()" style="width:16px;height:16px;cursor:pointer;">
      <div style="flex:1;"><div class="font-semibold">${it.name}</div><div class="text-xs text-slate-500">${it.short_name} - ${it.allow_decimal?'Decimal':'Whole Number'}</div></div>
    </label>`).join('');
  }catch(e){list.innerHTML='<div class="tbl-empty">Failed to load library.</div>';}
}
function toggleSelectAll(){
  const checked=document.getElementById('selectAllImport').checked;
  document.querySelectorAll('.import-checkbox').forEach(cb=>cb.checked=checked);
  updateImportCount();
}
function updateImportCount(){
  const count=document.querySelectorAll('.import-checkbox:checked').length;
  document.getElementById('importCount').textContent=count+' selected';
}
async function importSelected(){
  const checked=document.querySelectorAll('.import-checkbox:checked');
  if(!checked.length){showToast('Please select at least one item','warning');return;}
  const items=Array.from(checked).map(cb=>JSON.parse(cb.dataset.item));
  const btn=document.getElementById('importBtn');btn.disabled=true;btn.textContent='Importing...';
  try{
    const res=await apiFetch(`${API}/import`,{method:'POST',body:JSON.stringify({items})});
    closeModal('importModal');showToast(res.message||`Imported ${res.imported} units`);
    loadList();
  }catch(e){showToast(e.message||'Import failed','error');}
  finally{btn.disabled=false;btn.textContent='Import Selected';}
}
loadList();
</script>
@endsection
