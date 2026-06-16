@extends('layouts.dashboard')
@section('page_title','Purchase Returns')
@section('content')
<div class="dash-content">
<div class="page-card">
  <div class="card-header">
    <div class="card-title">Purchase Returns</div>
    <div class="filters-row">
      <div class="search-wrap">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
        <input type="text" id="searchInput" placeholder="Search reference..." oninput="loadList()">
      </div>
      <button class="btn btn-primary" onclick="openAddModal()">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        New Return
      </button>
    </div>
  </div>
  <div style="overflow-x:auto;">
    <table class="tbl">
      <thead><tr><th>#</th><th>Return Ref</th><th>Supplier</th><th>Date</th><th>Amount</th><th>Notes</th><th>Actions</th></tr></thead>
      <tbody id="tableBody"><tr><td colspan="7" class="tbl-empty">Loading...</td></tr></tbody>
    </table>
  </div>
</div>
</div>
<div class="modal-overlay" id="modal">
  <div class="modal modal-lg">
    <div class="modal-header">
      <div class="modal-title">New Purchase Return</div>
      <button class="modal-close" onclick="closeModal('modal')"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <div class="modal-body">
      <form id="itemForm">
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Supplier</label>
            <select name="supplier_id" class="form-control" id="supplierSel">
              <option value="">Select Supplier...</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Return Date *</label>
            <input name="purchase_date" type="date" class="form-control" required>
            <div class="invalid-feedback"></div>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Amount *</label>
            <input name="total" type="number" step="0.01" min="0" class="form-control" required placeholder="0.00" oninput="syncFields(this)">
            <div class="invalid-feedback"></div>
          </div>
          <div class="form-group">
            <label class="form-label">Payment Status</label>
            <select name="payment_status" class="form-control">
              <option value="paid">Paid</option><option value="unpaid">Unpaid</option>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Reason / Notes</label>
          <textarea name="notes" class="form-control" rows="3" placeholder="Reason for return..."></textarea>
        </div>
        <input type="hidden" name="status" value="return">
        <input type="hidden" name="subtotal" id="subtotalHidden" value="0">
        <input type="hidden" name="discount" value="0">
        <input type="hidden" name="tax" value="0">
        <input type="hidden" name="shipping" value="0">
        <input type="hidden" name="items" value="[]">
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal')">Cancel</button>
      <button class="btn btn-primary" id="saveBtn" onclick="saveItem()">Save Return</button>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
const API='/api/dashboard/purchases'; let editId=null;
function syncFields(el){document.getElementById('subtotalHidden').value=el.value;}
async function loadList(){
  const s=document.getElementById('searchInput').value;
  const tbody=document.getElementById('tableBody');
  tbody.innerHTML='<tr><td colspan="7" class="tbl-empty">Loading...</td></tr>';
  try{
    const items=await apiFetch(`${API}?status=return&search=${encodeURIComponent(s)}`);
    if(!items.length){tbody.innerHTML='<tr><td colspan="7" class="tbl-empty">No purchase returns found.</td></tr>';return;}
    tbody.innerHTML=items.map((r,i)=>`<tr>
      <td class="text-slate-400">${i+1}</td>
      <td class="font-mono text-xs text-orange-600">${r.reference}</td>
      <td>${r.supplier?r.supplier.name:'N/A'}</td>
      <td class="text-slate-500 text-xs">${r.purchase_date}</td>
      <td class="font-semibold text-red-600">${parseFloat(r.total).toFixed(2)}</td>
      <td class="text-slate-500 text-xs" style="max-width:200px;overflow:hidden;text-overflow:ellipsis;">${r.notes||'-'}</td>
      <td><button class="btn btn-sm btn-delete btn-icon" onclick="deleteReturn(${r.id},'${r.reference}')"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></td>
    </tr>`).join('');
  }catch(e){tbody.innerHTML='<tr><td colspan="7" class="tbl-empty">Error loading data.</td></tr>';}
}
async function openAddModal(){
  document.getElementById('itemForm').reset();
  const today=new Date().toISOString().split('T')[0];
  document.querySelector('[name="purchase_date"]').value=today;
  try{
    const suppliers=await apiFetch('/api/dashboard/suppliers');
    document.getElementById('supplierSel').innerHTML='<option value="">Select Supplier...</option>'+suppliers.map(s=>`<option value="${s.id}">${s.name}${s.company?' ('+s.company+')':''}</option>`).join('');
  }catch(e){}
  clearFormErrors('itemForm');openModal('modal');
}
async function saveItem(){
  const form=document.getElementById('itemForm');
  const data=Object.fromEntries(new FormData(form));
  data.items=[{product_name:'Return',quantity:1,unit_cost:parseFloat(data.total||0),total:parseFloat(data.total||0)}];
  data.subtotal=parseFloat(data.total||0);
  const btn=document.getElementById('saveBtn');btn.disabled=true;btn.textContent='Saving...';
  try{
    await apiFetch(API,{method:'POST',body:JSON.stringify(data)});
    closeModal('modal');showToast('Purchase return recorded!');loadList();
  }catch(e){if(e.errors)showFormErrors('itemForm',e.errors);else showToast(e.message||'Save failed','error');}
  finally{btn.disabled=false;btn.textContent='Save Return';}
}
function deleteReturn(id,ref){
  showConfirm('Delete Return',`Delete return "${ref}"?`,async()=>{
    try{await apiFetch(`${API}/${id}`,{method:'DELETE'});showToast('Return deleted!');loadList();}
    catch(e){showToast(e.message||'Delete failed','error');}
  });
}
loadList();
</script>
@endsection
