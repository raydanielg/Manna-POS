@extends('layouts.dashboard')
@section('page_title','Stock Adjustments')
@section('content')
<div class="dash-content">
<div class="page-card">
  <div class="card-header">
    <div class="card-title">Stock Adjustments</div>
    <div class="filters-row">
      <div class="search-wrap">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
        <input type="text" id="searchInput" placeholder="Search adjustments..." oninput="loadList()">
      </div>
      <button class="btn btn-success" onclick="openAddModal()">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        New Adjustment
      </button>
    </div>
  </div>
  <div style="overflow-x:auto;">
    <table class="tbl">
      <thead><tr><th>#</th><th>Reference</th><th>Product</th><th>Type</th><th>Qty</th><th>Date</th><th>Reason</th><th>Actions</th></tr></thead>
      <tbody id="tableBody"><tr><td colspan="8" class="tbl-empty">Loading...</td></tr></tbody>
    </table>
  </div>
</div>
</div>
<div class="modal-overlay" id="modal">
  <div class="modal modal-lg">
    <div class="modal-header">
      <div class="modal-title" id="modal-title">New Stock Adjustment</div>
      <button class="modal-close" onclick="closeModal('modal')"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <div class="modal-body">
      <form id="itemForm">
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Product *</label>
            <select name="product_id" class="form-control" required>
              <option value="">Select product...</option>
            </select>
            <div class="invalid-feedback"></div>
          </div>
          <div class="form-group">
            <label class="form-label">Adjustment Type *</label>
            <select name="type" class="form-control" required>
              <option value="addition">Addition (Stock In)</option>
              <option value="subtraction">Subtraction (Stock Out)</option>
            </select>
            <div class="invalid-feedback"></div>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">Quantity *</label><input name="quantity" type="number" min="0.01" step="0.01" class="form-control" required placeholder="0"><div class="invalid-feedback"></div></div>
          <div class="form-group"><label class="form-label">Unit Cost</label><input name="unit_cost" type="number" min="0" step="0.01" class="form-control" placeholder="0.00"><div class="invalid-feedback"></div></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">Adjustment Date *</label><input name="adjustment_date" type="date" class="form-control" required><div class="invalid-feedback"></div></div>
          <div class="form-group"><label class="form-label">Reason</label><input name="reason" class="form-control" placeholder="e.g. Damaged goods, Inventory count"><div class="invalid-feedback"></div></div>
        </div>
        <div class="form-group"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2" placeholder="Additional notes..."></textarea><div class="invalid-feedback"></div></div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal')">Cancel</button>
      <button class="btn btn-primary" id="saveBtn" onclick="saveItem()">Save Adjustment</button>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
const API='/api/dashboard/stock-adjustments'; let editId=null;
async function loadProducts(){
  try{
    const products=await apiFetch('/api/dashboard/products');
    const sel=document.querySelector('[name="product_id"]');
    sel.innerHTML='<option value="">Select product...</option>'+products.map(p=>`<option value="${p.id}">${p.name} (Stock: ${p.stock_quantity})</option>`).join('');
  }catch(e){}
}
async function loadList(){
  const s=document.getElementById('searchInput').value; const tbody=document.getElementById('tableBody');
  tbody.innerHTML='<tr><td colspan="8" class="tbl-empty">Loading...</td></tr>';
  try{
    const items=await apiFetch(`${API}?search=${encodeURIComponent(s)}`);
    if(!items.length){tbody.innerHTML='<tr><td colspan="8" class="tbl-empty">No adjustments found.</td></tr>';return;}
    tbody.innerHTML=items.map((a,i)=>`<tr>
      <td class="text-slate-400">${i+1}</td>
      <td class="font-mono text-xs text-blue-600">${a.reference}</td>
      <td class="font-semibold">${a.product?a.product.name:'N/A'}</td>
      <td><span class="badge ${a.type==='addition'?'badge-success':'badge-danger'}">${a.type}</span></td>
      <td class="font-semibold">${a.quantity}</td>
      <td class="text-slate-500 text-xs">${a.adjustment_date}</td>
      <td class="text-slate-500">${a.reason||'-'}</td>
      <td><div style="display:flex;gap:0.4rem;">
        <button class="btn btn-sm btn-edit btn-icon" onclick="editItem(${a.id})"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
        <button class="btn btn-sm btn-delete btn-icon" onclick="deleteItem(${a.id},'${a.reference}')"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
      </div></td>
    </tr>`).join('');
  }catch(e){tbody.innerHTML='<tr><td colspan="8" class="tbl-empty">Error loading data.</td></tr>';}
}
function openAddModal(){editId=null;document.getElementById('modal-title').textContent='New Stock Adjustment';
  document.getElementById('itemForm').reset();document.querySelector('[name="adjustment_date"]').value=new Date().toISOString().split('T')[0];
  clearFormErrors('itemForm');loadProducts();openModal('modal');}
async function editItem(id){
  try{const a=await apiFetch(`${API}/${id}`);editId=id;document.getElementById('modal-title').textContent='Edit Adjustment';
  await loadProducts();
  const form=document.getElementById('itemForm');Object.entries(a).forEach(([k,v])=>{const el=form.querySelector(`[name="${k}"]`);if(el)el.value=v??'';});
  clearFormErrors('itemForm');openModal('modal');}catch(e){showToast('Failed to load','error');}
}
async function saveItem(){
  clearFormErrors('itemForm');const data=Object.fromEntries(new FormData(document.getElementById('itemForm')));
  const btn=document.getElementById('saveBtn');btn.disabled=true;btn.textContent='Saving...';
  try{if(editId)await apiFetch(`${API}/${editId}`,{method:'PUT',body:JSON.stringify(data)});
  else await apiFetch(API,{method:'POST',body:JSON.stringify(data)});
  closeModal('modal');showToast(editId?'Adjustment updated!':'Adjustment saved! Stock updated.');loadList();}
  catch(e){if(e.errors)showFormErrors('itemForm',e.errors);else showToast(e.message||'Save failed','error');}
  finally{btn.disabled=false;btn.textContent='Save Adjustment';}
}
function deleteItem(id,ref){
  showConfirm('Delete Adjustment',`Delete adjustment "${ref}"? Stock will NOT be reversed.`,async()=>{
    try{await apiFetch(`${API}/${id}`,{method:'DELETE'});showToast('Adjustment deleted!');loadList();}
    catch(e){showToast(e.message||'Delete failed','error');}
  });
}
loadList();
</script>
@endsection
