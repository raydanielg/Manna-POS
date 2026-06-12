@extends('layouts.dashboard')
@section('page_title','Discounts')
@section('content')
<div class="dash-content">
<div class="page-card">
  <div class="card-header">
    <div class="card-title">Discounts</div>
    <div class="filters-row">
      <div class="search-wrap">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
        <input type="text" id="searchInput" placeholder="Search discounts..." oninput="loadList()">
      </div>
      <button class="btn btn-success" onclick="openAddModal()">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Add Discount
      </button>
    </div>
  </div>
  <div style="overflow-x:auto;">
    <table class="tbl">
      <thead><tr><th>#</th><th>Name</th><th>Amount</th><th>Type</th><th>Valid Period</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody id="tableBody"><tr><td colspan="7" class="tbl-empty">Loading...</td></tr></tbody>
    </table>
  </div>
</div>
</div>
<div class="modal-overlay" id="modal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title" id="modal-title">Add Discount</div>
      <button class="modal-close" onclick="closeModal('modal')"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <div class="modal-body">
      <form id="itemForm">
        <div class="form-group"><label class="form-label">Discount Name *</label><input name="name" class="form-control" required placeholder="e.g. Holiday Sale 10%"><div class="invalid-feedback"></div></div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">Amount *</label><input name="amount" type="number" step="0.01" min="0" class="form-control" required placeholder="10.00"><div class="invalid-feedback"></div></div>
          <div class="form-group">
            <label class="form-label">Type *</label>
            <select name="type" class="form-control">
              <option value="percentage">Percentage (%)</option>
              <option value="fixed">Fixed Amount</option>
            </select>
            <div class="invalid-feedback"></div>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">Start Date</label><input name="starts_at" type="date" class="form-control"><div class="invalid-feedback"></div></div>
          <div class="form-group"><label class="form-label">End Date</label><input name="ends_at" type="date" class="form-control"><div class="invalid-feedback"></div></div>
        </div>
        <div class="form-group">
          <label class="form-label">Status</label>
          <select name="status" class="form-control">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
          <div class="invalid-feedback"></div>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal')">Cancel</button>
      <button class="btn btn-primary" id="saveBtn" onclick="saveItem()">Save Discount</button>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
const API='/api/dashboard/discounts'; let editId=null;
async function loadList(){
  const s=document.getElementById('searchInput').value; const tbody=document.getElementById('tableBody');
  tbody.innerHTML='<tr><td colspan="7" class="tbl-empty">Loading...</td></tr>';
  try{
    const items=await apiFetch(`${API}?search=${encodeURIComponent(s)}`);
    if(!items.length){tbody.innerHTML='<tr><td colspan="7" class="tbl-empty">No discounts found.</td></tr>';return;}
    tbody.innerHTML=items.map((d,i)=>{
      const period=d.starts_at&&d.ends_at?`${d.starts_at} - ${d.ends_at}`:d.starts_at||d.ends_at||'No limit';
      return `<tr>
        <td class="text-slate-400">${i+1}</td>
        <td class="font-semibold">${d.name}</td>
        <td class="font-semibold text-green-600">${d.amount}${d.type==='percentage'?'%':''}</td>
        <td><span class="badge badge-info">${d.type}</span></td>
        <td class="text-slate-500 text-xs">${period}</td>
        <td><span class="badge ${d.status==='active'?'badge-success':'badge-gray'}">${d.status}</span></td>
        <td><div style="display:flex;gap:0.4rem;">
          <button class="btn btn-sm btn-edit btn-icon" onclick="editItem(${d.id})"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
          <button class="btn btn-sm btn-delete btn-icon" onclick="deleteItem(${d.id},'${d.name}')"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
        </div></td>
      </tr>`;
    }).join('');
  }catch(e){tbody.innerHTML='<tr><td colspan="7" class="tbl-empty">Error loading data.</td></tr>';}
}
function openAddModal(){editId=null;document.getElementById('modal-title').textContent='Add Discount';document.getElementById('itemForm').reset();clearFormErrors('itemForm');openModal('modal');}
async function editItem(id){
  try{const d=await apiFetch(`${API}/${id}`);editId=id;document.getElementById('modal-title').textContent='Edit Discount';
  const form=document.getElementById('itemForm');Object.entries(d).forEach(([k,v])=>{const el=form.querySelector(`[name="${k}"]`);if(el)el.value=v??'';});
  clearFormErrors('itemForm');openModal('modal');}catch(e){showToast('Failed to load','error');}
}
async function saveItem(){
  clearFormErrors('itemForm');const data=Object.fromEntries(new FormData(document.getElementById('itemForm')));
  const btn=document.getElementById('saveBtn');btn.disabled=true;btn.textContent='Saving...';
  try{if(editId)await apiFetch(`${API}/${editId}`,{method:'PUT',body:JSON.stringify(data)});
  else await apiFetch(API,{method:'POST',body:JSON.stringify(data)});
  closeModal('modal');showToast(editId?'Discount updated!':'Discount added!');loadList();}
  catch(e){if(e.errors)showFormErrors('itemForm',e.errors);else showToast(e.message||'Save failed','error');}
  finally{btn.disabled=false;btn.textContent='Save Discount';}
}
function deleteItem(id,name){
  showConfirm('Delete Discount',`Delete discount "${name}"?`,async()=>{
    try{await apiFetch(`${API}/${id}`,{method:'DELETE'});showToast('Discount deleted!');loadList();}
    catch(e){showToast(e.message||'Delete failed','error');}
  });
}
loadList();
</script>
@endsection
