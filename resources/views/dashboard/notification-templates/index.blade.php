@extends('layouts.dashboard')
@section('page_title','Notification Templates')
@section('content')
<div class="dash-content">
<div class="page-card">
  <div class="card-header">
    <div class="card-title">Notification Templates</div>
    <div class="filters-row">
      <div class="search-wrap">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
        <input type="text" id="searchInput" placeholder="Search templates..." oninput="loadList()">
      </div>
      <button class="btn btn-success" onclick="openAddModal()">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Add Template
      </button>
    </div>
  </div>
  <div style="overflow-x:auto;">
    <table class="tbl">
      <thead><tr><th>#</th><th>Type</th><th>Subject</th><th>Active</th><th>Actions</th></tr></thead>
      <tbody id="tableBody"><tr><td colspan="5" class="tbl-empty">Loading...</td></tr></tbody>
    </table>
  </div>
</div>
</div>
<div class="modal-overlay" id="modal">
  <div class="modal modal-lg">
    <div class="modal-header">
      <div class="modal-title" id="modal-title">Add Template</div>
      <button class="modal-close" onclick="closeModal('modal')"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <div class="modal-body">
      <form id="itemForm">
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Type *</label>
            <select name="type" class="form-control" required>
              <option value="">Select type...</option>
              <option value="invoice">Invoice</option>
              <option value="payment_reminder">Payment Reminder</option>
              <option value="low_stock">Low Stock Alert</option>
              <option value="sale_confirmation">Sale Confirmation</option>
              <option value="purchase_order">Purchase Order</option>
              <option value="stock_adjustment">Stock Adjustment</option>
              <option value="customer_welcome">Customer Welcome</option>
            </select>
            <div class="invalid-feedback"></div>
          </div>
          <div class="form-group">
            <label class="form-label">Active</label>
            <select name="is_active" class="form-control">
              <option value="1">Yes</option>
              <option value="0">No</option>
            </select>
            <div class="invalid-feedback"></div>
          </div>
        </div>
        <div class="form-group"><label class="form-label">Subject *</label><input name="subject" class="form-control" required placeholder="Email/SMS subject line"><div class="invalid-feedback"></div></div>
        <div class="form-group">
          <label class="form-label">Body *</label>
          <textarea name="body" class="form-control" rows="8" required placeholder="Template body. Use {customer_name}, {invoice_no}, {amount}, {date} as placeholders."></textarea>
          <div class="invalid-feedback"></div>
          <small class="text-slate-400 mt-1 block">Available: {customer_name}, {invoice_no}, {amount}, {date}, {product_name}, {stock_qty}</small>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal')">Cancel</button>
      <button class="btn btn-primary" id="saveBtn" onclick="saveItem()">Save Template</button>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
const API='/api/dashboard/notification-templates'; let editId=null;
async function loadList(){
  const s=document.getElementById('searchInput').value; const tbody=document.getElementById('tableBody');
  tbody.innerHTML='<tr><td colspan="5" class="tbl-empty">Loading...</td></tr>';
  try{
    const items=await apiFetch(`${API}?search=${encodeURIComponent(s)}`);
    if(!items.length){tbody.innerHTML='<tr><td colspan="5" class="tbl-empty">No templates found.</td></tr>';return;}
    tbody.innerHTML=items.map((t,i)=>`<tr>
      <td class="text-slate-400">${i+1}</td>
      <td><span class="badge badge-info">${t.type.replace(/_/g,' ')}</span></td>
      <td class="font-semibold">${t.subject}</td>
      <td><span class="badge ${t.is_active?'badge-success':'badge-gray'}">${t.is_active?'Active':'Inactive'}</span></td>
      <td><div style="display:flex;gap:0.4rem;">
        <button class="btn btn-sm btn-edit btn-icon" onclick="editItem(${t.id})"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
        <button class="btn btn-sm btn-delete btn-icon" onclick="deleteItem(${t.id},'${t.subject}')"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
      </div></td>
    </tr>`).join('');
  }catch(e){tbody.innerHTML='<tr><td colspan="5" class="tbl-empty">Error loading data.</td></tr>';}
}
function openAddModal(){editId=null;document.getElementById('modal-title').textContent='Add Template';document.getElementById('itemForm').reset();clearFormErrors('itemForm');openModal('modal');}
async function editItem(id){
  try{const t=await apiFetch(`${API}/${id}`);editId=id;document.getElementById('modal-title').textContent='Edit Template';
  const form=document.getElementById('itemForm');Object.entries(t).forEach(([k,v])=>{const el=form.querySelector(`[name="${k}"]`);if(el)el.value=v??'';});
  clearFormErrors('itemForm');openModal('modal');}catch(e){showToast('Failed to load','error');}
}
async function saveItem(){
  clearFormErrors('itemForm');const data=Object.fromEntries(new FormData(document.getElementById('itemForm')));
  const btn=document.getElementById('saveBtn');btn.disabled=true;btn.textContent='Saving...';
  try{if(editId)await apiFetch(`${API}/${editId}`,{method:'PUT',body:JSON.stringify(data)});
  else await apiFetch(API,{method:'POST',body:JSON.stringify(data)});
  closeModal('modal');showToast(editId?'Template updated!':'Template added!');loadList();}
  catch(e){if(e.errors)showFormErrors('itemForm',e.errors);else showToast(e.message||'Save failed','error');}
  finally{btn.disabled=false;btn.textContent='Save Template';}
}
function deleteItem(id,name){
  showConfirm('Delete Template',`Delete template "${name}"?`,async()=>{
    try{await apiFetch(`${API}/${id}`,{method:'DELETE'});showToast('Template deleted!');loadList();}
    catch(e){showToast(e.message||'Delete failed','error');}
  });
}
loadList();
</script>
@endsection
