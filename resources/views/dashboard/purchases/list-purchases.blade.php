@extends('layouts.dashboard')
@section('page_title','Purchases')
@section('content')
<div class="dash-content">
<div class="page-card">
  <div class="card-header">
    <div class="card-title">Purchase Orders</div>
    <div class="filters-row">
      <div class="search-wrap">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
        <input type="text" id="searchInput" placeholder="Search purchases..." oninput="loadList()">
      </div>
      <select id="statusFilter" class="form-control" style="width:140px;" onchange="loadList()">
        <option value="">All Status</option>
        <option value="received">Received</option>
        <option value="pending">Pending</option>
        <option value="cancelled">Cancelled</option>
      </select>
      <button class="btn btn-success" onclick="openAddModal()">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Add Purchase
      </button>
    </div>
  </div>
  <div style="overflow-x:auto;">
    <table class="tbl">
      <thead><tr><th>#</th><th>Reference</th><th>Supplier</th><th>Date</th><th>Total</th><th>Payment</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody id="tableBody"><tr><td colspan="8" class="tbl-empty">Loading...</td></tr></tbody>
    </table>
  </div>
</div>
</div>
<div class="modal-overlay" id="modal">
  <div class="modal modal-lg" style="max-width:860px;">
    <div class="modal-header">
      <div class="modal-title" id="modal-title">Add Purchase</div>
      <button class="modal-close" onclick="closeModal('modal')"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <div class="modal-body">
      <form id="itemForm">
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Supplier *</label>
            <select name="supplier_id" class="form-control" required><option value="">Select supplier...</option></select>
            <div class="invalid-feedback"></div>
          </div>
          <div class="form-group"><label class="form-label">Purchase Date *</label><input name="purchase_date" type="date" class="form-control" required><div class="invalid-feedback"></div></div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
              <option value="received">Received</option>
              <option value="pending">Pending</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Payment Status</label>
            <select name="payment_status" class="form-control">
              <option value="paid">Paid</option>
              <option value="partial">Partial</option>
              <option value="unpaid">Unpaid</option>
            </select>
          </div>
        </div>
        <div style="margin-bottom:0.75rem;">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.5rem;">
            <label class="form-label" style="margin:0;">Items</label>
            <button type="button" class="btn btn-sm btn-secondary" onclick="addItemRow()">+ Add Item</button>
          </div>
          <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;">
            <table style="width:100%;border-collapse:collapse;">
              <thead><tr style="background:#f1f5f9;"><th style="padding:0.5rem;font-size:0.72rem;font-weight:600;color:#64748b;text-align:left;">Product</th><th style="padding:0.5rem;font-size:0.72rem;font-weight:600;color:#64748b;text-align:right;">Qty</th><th style="padding:0.5rem;font-size:0.72rem;font-weight:600;color:#64748b;text-align:right;">Unit Cost</th><th style="padding:0.5rem;font-size:0.72rem;font-weight:600;color:#64748b;text-align:right;">Total</th><th style="padding:0.5rem;"></th></tr></thead>
              <tbody id="itemsBody"></tbody>
            </table>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">Discount</label><input name="discount" type="number" min="0" step="0.01" class="form-control" value="0" oninput="calcTotals()"><div class="invalid-feedback"></div></div>
          <div class="form-group"><label class="form-label">Shipping</label><input name="shipping" type="number" min="0" step="0.01" class="form-control" value="0" oninput="calcTotals()"><div class="invalid-feedback"></div></div>
        </div>
        <div style="background:#f0f9ff;border:1px solid #bae6fd;border-radius:8px;padding:0.75rem;display:flex;gap:1.5rem;justify-content:flex-end;">
          <span style="font-size:0.85rem;color:#64748b;">Subtotal: <strong id="subtotalDisp">0.00</strong></span>
          <span style="font-size:0.85rem;color:#64748b;">Grand Total: <strong id="totalDisp" style="color:#2563eb;">0.00</strong></span>
        </div>
        <input type="hidden" name="subtotal" id="subtotalInput">
        <input type="hidden" name="tax" value="0">
        <input type="hidden" name="total" id="totalInput">
        <div class="form-group" style="margin-top:0.75rem;"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2" placeholder="Notes..."></textarea></div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal')">Cancel</button>
      <button class="btn btn-primary" id="saveBtn" onclick="saveItem()">Save Purchase</button>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
const API='/api/dashboard/purchases'; let editId=null; let productList=[];
const payColors={paid:'badge-success',partial:'badge-warning',unpaid:'badge-danger'};
const statusColors={received:'badge-success',pending:'badge-warning',cancelled:'badge-danger'};
async function loadDropdowns(){
  try{
    const [suppliers,products]=await Promise.all([apiFetch('/api/dashboard/suppliers'),apiFetch('/api/dashboard/products')]);
    productList=products;
    const sel=document.querySelector('[name="supplier_id"]');
    sel.innerHTML='<option value="">Select supplier...</option>'+suppliers.map(s=>`<option value="${s.id}">${s.name}</option>`).join('');
  }catch(e){}
}
function addItemRow(product_id='',quantity=1,unit_cost=0){
  const tr=document.createElement('tr');
  tr.style.borderTop='1px solid #e2e8f0';
  tr.innerHTML=`<td style="padding:0.4rem;">
    <select class="form-control item-product" style="font-size:0.8rem;" onchange="updateRowCost(this)">
      <option value="">Select product...</option>
      ${productList.map(p=>`<option value="${p.id}" data-cost="${p.purchase_price||0}" ${p.id==product_id?'selected':''}>${p.name}</option>`).join('')}
    </select></td>
    <td style="padding:0.4rem;"><input type="number" class="form-control item-qty" style="font-size:0.8rem;text-align:right;" value="${quantity}" min="0.01" step="0.01" oninput="calcTotals()"></td>
    <td style="padding:0.4rem;"><input type="number" class="form-control item-cost" style="font-size:0.8rem;text-align:right;" value="${unit_cost}" min="0" step="0.01" oninput="calcTotals()"></td>
    <td style="padding:0.4rem;text-align:right;font-weight:600;font-size:0.8rem;" class="row-total">0.00</td>
    <td style="padding:0.4rem;text-align:center;"><button type="button" onclick="this.closest('tr').remove();calcTotals();" style="background:none;border:none;color:#e03057;cursor:pointer;font-size:1rem;">×</button></td>`;
  document.getElementById('itemsBody').appendChild(tr);
  if(product_id)updateRowCost(tr.querySelector('.item-product'));
  calcTotals();
}
function updateRowCost(sel){
  const opt=sel.selectedOptions[0];
  const cost=opt?parseFloat(opt.dataset.cost||0):0;
  sel.closest('tr').querySelector('.item-cost').value=cost.toFixed(2);
  calcTotals();
}
function calcTotals(){
  let sub=0;
  document.querySelectorAll('#itemsBody tr').forEach(tr=>{
    const qty=parseFloat(tr.querySelector('.item-qty')?.value||0);
    const cost=parseFloat(tr.querySelector('.item-cost')?.value||0);
    const rowTotal=qty*cost; sub+=rowTotal;
    const td=tr.querySelector('.row-total'); if(td)td.textContent=rowTotal.toFixed(2);
  });
  const disc=parseFloat(document.querySelector('[name="discount"]')?.value||0);
  const ship=parseFloat(document.querySelector('[name="shipping"]')?.value||0);
  const total=sub-disc+ship;
  document.getElementById('subtotalDisp').textContent=sub.toFixed(2);
  document.getElementById('totalDisp').textContent=total.toFixed(2);
  document.getElementById('subtotalInput').value=sub.toFixed(2);
  document.getElementById('totalInput').value=total.toFixed(2);
}
async function loadList(){
  const s=document.getElementById('searchInput').value;
  const st=document.getElementById('statusFilter').value;
  const tbody=document.getElementById('tableBody');
  tbody.innerHTML='<tr><td colspan="8" class="tbl-empty">Loading...</td></tr>';
  try{
    const items=await apiFetch(`${API}?search=${encodeURIComponent(s)}&status=${st}`);
    if(!items.length){tbody.innerHTML='<tr><td colspan="8" class="tbl-empty">No purchases found.</td></tr>';return;}
    tbody.innerHTML=items.map((p,i)=>`<tr>
      <td class="text-slate-400">${i+1}</td>
      <td class="font-mono text-xs text-blue-600">${p.reference}</td>
      <td class="font-semibold">${p.supplier?p.supplier.name:'N/A'}</td>
      <td class="text-slate-500 text-xs">${p.purchase_date}</td>
      <td class="font-semibold">${parseFloat(p.total).toFixed(2)}</td>
      <td><span class="badge ${payColors[p.payment_status]||'badge-gray'}">${p.payment_status}</span></td>
      <td><span class="badge ${statusColors[p.status]||'badge-gray'}">${p.status}</span></td>
      <td><div style="display:flex;gap:0.4rem;">
        <button class="btn btn-sm btn-edit btn-icon" onclick="editItem(${p.id})"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
        <button class="btn btn-sm btn-delete btn-icon" onclick="deleteItem(${p.id},'${p.reference}')"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
      </div></td>
    </tr>`).join('');
  }catch(e){tbody.innerHTML='<tr><td colspan="8" class="tbl-empty">Error loading data.</td></tr>';}
}
async function openAddModal(){editId=null;document.getElementById('modal-title').textContent='Add Purchase';
  document.getElementById('itemForm').reset();document.getElementById('itemsBody').innerHTML='';
  document.querySelector('[name="purchase_date"]').value=new Date().toISOString().split('T')[0];
  calcTotals();clearFormErrors('itemForm');await loadDropdowns();addItemRow();openModal('modal');}
async function editItem(id){
  try{const p=await apiFetch(`${API}/${id}`);editId=id;document.getElementById('modal-title').textContent='Edit Purchase';
  await loadDropdowns();
  const form=document.getElementById('itemForm');
  ['supplier_id','purchase_date','status','payment_status','discount','shipping','notes'].forEach(k=>{const el=form.querySelector(`[name="${k}"]`);if(el)el.value=p[k]??'';});
  document.getElementById('itemsBody').innerHTML='';
  (p.items||[]).forEach(item=>addItemRow(item.product_id,item.quantity,item.unit_cost));
  calcTotals();clearFormErrors('itemForm');openModal('modal');}catch(e){showToast('Failed to load','error');}
}
async function saveItem(){
  clearFormErrors('itemForm');
  const form=document.getElementById('itemForm');
  const data=Object.fromEntries(new FormData(form));
  data.items=Array.from(document.querySelectorAll('#itemsBody tr')).map(tr=>({
    product_id:tr.querySelector('.item-product')?.value,
    quantity:tr.querySelector('.item-qty')?.value,
    unit_cost:tr.querySelector('.item-cost')?.value
  })).filter(i=>i.product_id);
  const btn=document.getElementById('saveBtn');btn.disabled=true;btn.textContent='Saving...';
  try{if(editId)await apiFetch(`${API}/${editId}`,{method:'PUT',body:JSON.stringify(data)});
  else await apiFetch(API,{method:'POST',body:JSON.stringify(data)});
  closeModal('modal');showToast(editId?'Purchase updated!':'Purchase created! Stock updated.');loadList();}
  catch(e){if(e.errors)showFormErrors('itemForm',e.errors);else showToast(e.message||'Save failed','error');}
  finally{btn.disabled=false;btn.textContent='Save Purchase';}
}
function deleteItem(id,ref){
  showConfirm('Delete Purchase',`Delete purchase "${ref}"?`,async()=>{
    try{await apiFetch(`${API}/${id}`,{method:'DELETE'});showToast('Purchase deleted!');loadList();}
    catch(e){showToast(e.message||'Delete failed','error');}
  });
}
loadList();
</script>
@endsection
