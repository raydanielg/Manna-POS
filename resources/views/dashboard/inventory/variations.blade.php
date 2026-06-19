@extends('layouts.dashboard')
@section('page_title','Product Variations')
@section('content')
<div class="dash-content">
<div class="page-card">
  <div class="card-header">
    <div class="card-title">Product Variations</div>
    <div class="filters-row">
      <div class="search-wrap">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
        <input type="text" id="searchInput" placeholder="Search variations..." oninput="loadList()">
      </div>
      <select id="productFilter" class="form-control" style="width:180px;" onchange="loadList()">
        <option value="">All Products</option>
      </select>
      <select id="statusFilter" class="form-control" style="width:130px;" onchange="loadList()">
        <option value="">All Status</option>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
      </select>
      <button class="btn btn-info" onclick="openImportModal()">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
        Import
      </button>
      <button class="btn btn-primary" onclick="openAddModal()">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Add Variation
      </button>
    </div>
  </div>
  <div style="overflow-x:auto;">
    <table class="tbl">
      <thead><tr><th>#</th><th>Product</th><th>Attribute</th><th>Value</th><th>SKU</th><th>Extra Price</th><th>Stock</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody id="tableBody"><tr><td colspan="9" class="tbl-empty">Loading...</td></tr></tbody>
    </table>
  </div>
</div>
</div>
<div class="modal-overlay" id="modal">
  <div class="modal modal-lg">
    <div class="modal-header">
      <div class="modal-title" id="modal-title">Add Variation</div>
      <button class="modal-close" onclick="closeModal('modal')"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <div class="modal-body">
      <form id="itemForm">
        <div class="form-group">
          <label class="form-label">Product *</label>
          <select name="product_id" class="form-control" required id="productSel"><option value="">Select product...</option></select>
          <div class="invalid-feedback"></div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Attribute Name *</label>
            <input name="attribute_name" class="form-control" required placeholder="e.g. Color, Size, Weight">
            <div class="invalid-feedback"></div>
          </div>
          <div class="form-group">
            <label class="form-label">Attribute Value *</label>
            <input name="attribute_value" class="form-control" required placeholder="e.g. Red, Large, 500g">
            <div class="invalid-feedback"></div>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">SKU</label>
            <input name="sku" class="form-control" placeholder="Auto-generated if empty">
          </div>
          <div class="form-group">
            <label class="form-label">Additional Price</label>
            <input name="additional_price" type="number" step="0.01" min="0" class="form-control" placeholder="0.00">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Stock Quantity</label>
            <input name="stock_quantity" type="number" min="0" class="form-control" placeholder="0">
          </div>
          <div class="form-group">
            <label class="form-label">Status</label>
            <select name="status" class="form-control"><option value="active">Active</option><option value="inactive">Inactive</option></select>
          </div>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal')">Cancel</button>
      <button class="btn btn-primary" id="saveBtn" onclick="saveItem()">Save Variation</button>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
const API='/api/dashboard/product-variations'; let editId=null;
async function init(){
  try{
    const products=await apiFetch('/api/dashboard/products');
    const sel1=document.getElementById('productFilter');
    const sel2=document.getElementById('productSel');
    const opts=products.map(p=>`<option value="${p.id}">${p.name}</option>`).join('');
    sel1.innerHTML='<option value="">All Products</option>'+opts;
    sel2.innerHTML='<option value="">Select product...</option>'+opts;
  }catch(e){}
  loadList();
}
async function loadList(){
  const s=document.getElementById('searchInput').value;
  const pid=document.getElementById('productFilter').value;
  const tbody=document.getElementById('tableBody');
  tbody.innerHTML='<tr><td colspan="9" class="tbl-empty">Loading...</td></tr>';
  try{
    const items=await apiFetch(`${API}?search=${encodeURIComponent(s)}&product_id=${pid}`);
    if(!items.length){tbody.innerHTML='<tr><td colspan="9" class="tbl-empty">No variations found.</td></tr>';return;}
    tbody.innerHTML=items.map((v,i)=>`<tr>
      <td class="text-slate-400">${i+1}</td>
      <td class="font-semibold">${v.product?v.product.name:'N/A'}</td>
      <td><span class="badge badge-info">${v.attribute_name}</span></td>
      <td>${v.attribute_value}</td>
      <td class="font-mono text-xs text-slate-400">${v.sku||'-'}</td>
      <td class="text-blue-600">+${parseFloat(v.additional_price||0).toFixed(2)}</td>
      <td><span class="badge ${v.stock_quantity<=0?'badge-danger':'badge-success'}">${v.stock_quantity}</span></td>
      <td><span class="badge ${v.status==='active'?'badge-success':'badge-gray'}">${v.status}</span></td>
      <td><div style="display:flex;gap:0.4rem;">
        <button class="btn btn-sm btn-edit btn-icon" onclick="editItem(${v.id})"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
        <button class="btn btn-sm btn-delete btn-icon" onclick="deleteItem(${v.id},'${v.attribute_value}')"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
      </div></td>
    </tr>`).join('');
  }catch(e){tbody.innerHTML='<tr><td colspan="9" class="tbl-empty">Error loading data.</td></tr>';}
}
function openAddModal(){editId=null;document.getElementById('modal-title').textContent='Add Variation';document.getElementById('itemForm').reset();clearFormErrors('itemForm');openModal('modal');}
async function editItem(id){
  try{const v=await apiFetch(`${API}/${id}`);editId=id;document.getElementById('modal-title').textContent='Edit Variation';
  const form=document.getElementById('itemForm');Object.entries(v).forEach(([k,val])=>{const el=form.querySelector(`[name="${k}"]`);if(el)el.value=val??'';});
  clearFormErrors('itemForm');openModal('modal');}catch(e){showToast('Failed to load','error');}
}
async function saveItem(){
  clearFormErrors('itemForm');const data=Object.fromEntries(new FormData(document.getElementById('itemForm')));
  const btn=document.getElementById('saveBtn');btn.disabled=true;btn.textContent='Saving...';
  try{if(editId)await apiFetch(`${API}/${editId}`,{method:'PUT',body:JSON.stringify(data)});
  else await apiFetch(API,{method:'POST',body:JSON.stringify(data)});
  closeModal('modal');showToast(editId?'Variation updated!':'Variation added!');loadList();}
  catch(e){if(e.errors)showFormErrors('itemForm',e.errors);else showToast(e.message||'Save failed','error');}
  finally{btn.disabled=false;btn.textContent='Save Variation';}
}
function deleteItem(id,val){
  showConfirm('Delete Variation',`Delete variation "${val}"?`,async()=>{
    try{await apiFetch(`${API}/${id}`,{method:'DELETE'});showToast('Variation deleted!');loadList();}
    catch(e){showToast(e.message||'Delete failed','error');}
  });
}
init();
</script>
@endsection
