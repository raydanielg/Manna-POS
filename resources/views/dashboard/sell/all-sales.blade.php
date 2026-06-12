@extends('layouts.dashboard')
@section('page_title','Sales')
@section('content')
<div class="dash-content">
<div class="page-card">
  <div class="card-header">
    <div class="card-title">All Sales</div>
    <div class="filters-row">
      <div class="search-wrap">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
        <input type="text" id="searchInput" placeholder="Search sales..." oninput="loadList()">
      </div>
      <select id="statusFilter" class="form-control" style="width:140px;" onchange="loadList()">
        <option value="">All Status</option>
        <option value="completed">Completed</option>
        <option value="draft">Draft</option>
        <option value="quotation">Quotation</option>
        <option value="cancelled">Cancelled</option>
      </select>
      <button class="btn btn-success" onclick="openAddModal()">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Add Sale
      </button>
    </div>
  </div>
  <div style="overflow-x:auto;">
    <table class="tbl">
      <thead><tr><th>#</th><th>Invoice</th><th>Customer</th><th>Date</th><th>Total</th><th>Paid</th><th>Payment</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody id="tableBody"><tr><td colspan="9" class="tbl-empty">Loading...</td></tr></tbody>
    </table>
  </div>
</div>
</div>
<div class="modal-overlay" id="modal">
  <div class="modal modal-lg" style="max-width:900px;">
    <div class="modal-header">
      <div class="modal-title" id="modal-title">Add Sale</div>
      <button class="modal-close" onclick="closeModal('modal')"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <div class="modal-body">
      <form id="itemForm">
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Customer</label>
            <select name="customer_id" class="form-control"><option value="">Walk-in Customer</option></select>
            <div class="invalid-feedback"></div>
          </div>
          <div class="form-group"><label class="form-label">Sale Date *</label><input name="sale_date" type="date" class="form-control" required><div class="invalid-feedback"></div></div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
              <option value="completed">Completed</option>
              <option value="draft">Draft</option>
              <option value="quotation">Quotation</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Payment Method</label>
            <select name="payment_method" class="form-control">
              <option value="cash">Cash</option>
              <option value="card">Card</option>
              <option value="mobile_money">Mobile Money</option>
              <option value="credit">Credit</option>
            </select>
          </div>
        </div>
        <div style="margin-bottom:0.75rem;">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.5rem;">
            <label class="form-label" style="margin:0;">Items</label>
            <button type="button" class="btn btn-sm btn-secondary" onclick="addSaleItemRow()">+ Add Item</button>
          </div>
          <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;">
            <table style="width:100%;border-collapse:collapse;">
              <thead><tr style="background:#f1f5f9;"><th style="padding:0.5rem;font-size:0.72rem;font-weight:600;color:#64748b;text-align:left;">Product</th><th style="padding:0.5rem;font-size:0.72rem;font-weight:600;color:#64748b;text-align:right;">Price</th><th style="padding:0.5rem;font-size:0.72rem;font-weight:600;color:#64748b;text-align:right;">Qty</th><th style="padding:0.5rem;font-size:0.72rem;font-weight:600;color:#64748b;text-align:right;">Disc</th><th style="padding:0.5rem;font-size:0.72rem;font-weight:600;color:#64748b;text-align:right;">Total</th><th style="padding:0.5rem;"></th></tr></thead>
              <tbody id="saleItemsBody"></tbody>
            </table>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">Discount</label><input name="discount" type="number" min="0" step="0.01" class="form-control" value="0" oninput="calcSaleTotals()"></div>
          <div class="form-group"><label class="form-label">Amount Paid</label><input name="paid" type="number" min="0" step="0.01" class="form-control" value="0" oninput="calcSaleTotals()"></div>
        </div>
        <div style="background:#f0f9ff;border:1px solid #bae6fd;border-radius:8px;padding:0.75rem;display:flex;gap:1.5rem;justify-content:flex-end;">
          <span style="font-size:0.85rem;color:#64748b;">Subtotal: <strong id="saleSubtotalDisp">0.00</strong></span>
          <span style="font-size:0.85rem;color:#64748b;">Total: <strong id="saleTotalDisp" style="color:#2563eb;">0.00</strong></span>
          <span style="font-size:0.85rem;color:#64748b;">Balance: <strong id="saleBalanceDisp" style="color:#e03057;">0.00</strong></span>
        </div>
        <input type="hidden" name="subtotal" id="saleSubtotalInput">
        <input type="hidden" name="tax" value="0">
        <input type="hidden" name="total" id="saleTotalInput">
        <div class="form-group" style="margin-top:0.75rem;"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2" placeholder="Notes..."></textarea></div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal')">Cancel</button>
      <button class="btn btn-primary" id="saveBtn" onclick="saveSale()">Save Sale</button>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
const API='/api/dashboard/sales'; let editId=null; let saleProductList=[];
const payColors={paid:'badge-success',partial:'badge-warning',unpaid:'badge-danger'};
const saleStatusColors={completed:'badge-success',draft:'badge-warning',quotation:'badge-info',cancelled:'badge-danger'};
async function loadSaleDropdowns(){
  try{
    const [customers,products]=await Promise.all([apiFetch('/api/dashboard/customers'),apiFetch('/api/dashboard/products')]);
    saleProductList=products;
    const sel=document.querySelector('[name="customer_id"]');
    sel.innerHTML='<option value="">Walk-in Customer</option>'+customers.map(c=>`<option value="${c.id}">${c.name}</option>`).join('');
  }catch(e){}
}
function addSaleItemRow(product_id='',unit_price=0,quantity=1,discount=0){
  const tr=document.createElement('tr');tr.style.borderTop='1px solid #e2e8f0';
  tr.innerHTML=`<td style="padding:0.4rem;">
    <select class="form-control sale-product" style="font-size:0.8rem;" onchange="updateSaleRowPrice(this)">
      <option value="">Select...</option>
      ${saleProductList.map(p=>`<option value="${p.id}" data-price="${p.selling_price||0}" ${p.id==product_id?'selected':''}>${p.name}</option>`).join('')}
    </select></td>
    <td style="padding:0.4rem;"><input type="number" class="form-control sale-price" style="font-size:0.8rem;text-align:right;" value="${unit_price}" min="0" step="0.01" oninput="calcSaleTotals()"></td>
    <td style="padding:0.4rem;"><input type="number" class="form-control sale-qty" style="font-size:0.8rem;text-align:right;" value="${quantity}" min="0.01" step="0.01" oninput="calcSaleTotals()"></td>
    <td style="padding:0.4rem;"><input type="number" class="form-control sale-disc" style="font-size:0.8rem;text-align:right;" value="${discount}" min="0" step="0.01" oninput="calcSaleTotals()"></td>
    <td style="padding:0.4rem;text-align:right;font-weight:600;font-size:0.8rem;" class="sale-row-total">0.00</td>
    <td style="padding:0.4rem;text-align:center;"><button type="button" onclick="this.closest('tr').remove();calcSaleTotals();" style="background:none;border:none;color:#e03057;cursor:pointer;font-size:1rem;">×</button></td>`;
  document.getElementById('saleItemsBody').appendChild(tr);
  if(product_id)updateSaleRowPrice(tr.querySelector('.sale-product'));
  calcSaleTotals();
}
function updateSaleRowPrice(sel){
  const opt=sel.selectedOptions[0];
  const price=opt?parseFloat(opt.dataset.price||0):0;
  sel.closest('tr').querySelector('.sale-price').value=price.toFixed(2);
  calcSaleTotals();
}
function calcSaleTotals(){
  let sub=0;
  document.querySelectorAll('#saleItemsBody tr').forEach(tr=>{
    const qty=parseFloat(tr.querySelector('.sale-qty')?.value||0);
    const price=parseFloat(tr.querySelector('.sale-price')?.value||0);
    const disc=parseFloat(tr.querySelector('.sale-disc')?.value||0);
    const rowTotal=(qty*price)-disc; sub+=rowTotal;
    const td=tr.querySelector('.sale-row-total');if(td)td.textContent=rowTotal.toFixed(2);
  });
  const disc=parseFloat(document.querySelector('[name="discount"]')?.value||0);
  const paid=parseFloat(document.querySelector('[name="paid"]')?.value||0);
  const total=sub-disc;
  document.getElementById('saleSubtotalDisp').textContent=sub.toFixed(2);
  document.getElementById('saleTotalDisp').textContent=total.toFixed(2);
  document.getElementById('saleBalanceDisp').textContent=Math.max(0,total-paid).toFixed(2);
  document.getElementById('saleSubtotalInput').value=sub.toFixed(2);
  document.getElementById('saleTotalInput').value=total.toFixed(2);
}
async function loadList(){
  const s=document.getElementById('searchInput').value;const st=document.getElementById('statusFilter').value;
  const tbody=document.getElementById('tableBody');
  tbody.innerHTML='<tr><td colspan="9" class="tbl-empty">Loading...</td></tr>';
  try{
    const items=await apiFetch(`${API}?search=${encodeURIComponent(s)}&status=${st}`);
    if(!items.length){tbody.innerHTML='<tr><td colspan="9" class="tbl-empty">No sales found.</td></tr>';return;}
    tbody.innerHTML=items.map((s,i)=>`<tr>
      <td class="text-slate-400">${i+1}</td>
      <td class="font-mono text-xs text-blue-600">${s.reference}</td>
      <td class="font-semibold">${s.customer?s.customer.name:'Walk-in'}</td>
      <td class="text-slate-500 text-xs">${s.sale_date}</td>
      <td class="font-semibold">${parseFloat(s.total).toFixed(2)}</td>
      <td class="text-green-600">${parseFloat(s.paid||0).toFixed(2)}</td>
      <td><span class="badge badge-info">${s.payment_method}</span></td>
      <td><span class="badge ${saleStatusColors[s.status]||'badge-gray'}">${s.status}</span></td>
      <td><div style="display:flex;gap:0.4rem;">
        <button class="btn btn-sm btn-edit btn-icon" onclick="editSale(${s.id})"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
        <button class="btn btn-sm btn-delete btn-icon" onclick="deleteSale(${s.id},'${s.reference}')"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
      </div></td>
    </tr>`).join('');
  }catch(e){tbody.innerHTML='<tr><td colspan="9" class="tbl-empty">Error loading data.</td></tr>';}
}
async function openAddModal(){editId=null;document.getElementById('modal-title').textContent='Add Sale';
  document.getElementById('itemForm').reset();document.getElementById('saleItemsBody').innerHTML='';
  document.querySelector('[name="sale_date"]').value=new Date().toISOString().split('T')[0];
  calcSaleTotals();clearFormErrors('itemForm');await loadSaleDropdowns();addSaleItemRow();openModal('modal');}
async function editSale(id){
  try{const s=await apiFetch(`${API}/${id}`);editId=id;document.getElementById('modal-title').textContent='Edit Sale';
  await loadSaleDropdowns();
  const form=document.getElementById('itemForm');
  ['customer_id','sale_date','status','payment_method','discount','paid','notes'].forEach(k=>{const el=form.querySelector(`[name="${k}"]`);if(el)el.value=s[k]??'';});
  document.getElementById('saleItemsBody').innerHTML='';
  (s.items||[]).forEach(item=>addSaleItemRow(item.product_id,item.unit_price,item.quantity,item.discount||0));
  calcSaleTotals();clearFormErrors('itemForm');openModal('modal');}catch(e){showToast('Failed to load','error');}
}
async function saveSale(){
  clearFormErrors('itemForm');
  const form=document.getElementById('itemForm');
  const data=Object.fromEntries(new FormData(form));
  data.items=Array.from(document.querySelectorAll('#saleItemsBody tr')).map(tr=>({
    product_id:tr.querySelector('.sale-product')?.value,
    unit_price:tr.querySelector('.sale-price')?.value,
    quantity:tr.querySelector('.sale-qty')?.value,
    discount:tr.querySelector('.sale-disc')?.value
  })).filter(i=>i.product_id);
  const btn=document.getElementById('saveBtn');btn.disabled=true;btn.textContent='Saving...';
  try{if(editId)await apiFetch(`${API}/${editId}`,{method:'PUT',body:JSON.stringify(data)});
  else await apiFetch(API,{method:'POST',body:JSON.stringify(data)});
  closeModal('modal');showToast(editId?'Sale updated!':'Sale recorded!');loadList();}
  catch(e){if(e.errors)showFormErrors('itemForm',e.errors);else showToast(e.message||'Save failed','error');}
  finally{btn.disabled=false;btn.textContent='Save Sale';}
}
function deleteSale(id,ref){
  showConfirm('Delete Sale',`Delete sale "${ref}"?`,async()=>{
    try{await apiFetch(`${API}/${id}`,{method:'DELETE'});showToast('Sale deleted!');loadList();}
    catch(e){showToast(e.message||'Delete failed','error');}
  });
}
loadList();
</script>
@endsection
