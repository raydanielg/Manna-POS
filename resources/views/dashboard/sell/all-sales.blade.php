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
{{-- Receipt Modal --}}
<div class="modal-overlay" id="receipt-modal">
  <div class="modal" style="max-width:460px;padding:0;overflow:hidden;">
    <div class="modal-header" style="background:linear-gradient(135deg,#1e293b,#0f172a);color:#fff;border:none;">
      <div style="display:flex;align-items:center;gap:0.75rem;">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        <span class="modal-title" style="color:#fff;">Sale Receipt</span>
      </div>
      <div style="display:flex;gap:0.4rem;align-items:center;flex-wrap:wrap;justify-content:flex-end;">
        <button onclick="printReceipt()" style="background:#2563eb;border:none;color:#fff;padding:0.35rem 0.7rem;border-radius:7px;font-size:0.72rem;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:0.3rem;">
          <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
          Print
        </button>
        <a id="receiptInvoiceLink" href="/invoice" target="_blank" style="background:#7c3aed;border:none;color:#fff;padding:0.35rem 0.7rem;border-radius:7px;font-size:0.72rem;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:0.3rem;text-decoration:none;">
          <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
          Invoice
        </a>
        <a id="receiptWaLink" href="#" target="_blank" style="background:#22c55e;border:none;color:#fff;padding:0.35rem 0.7rem;border-radius:7px;font-size:0.72rem;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:0.3rem;text-decoration:none;">
          <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
          WhatsApp
        </a>
        <a id="receiptEmailLink" href="#" target="_blank" style="background:#2563eb;border:none;color:#fff;padding:0.35rem 0.7rem;border-radius:7px;font-size:0.72rem;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:0.3rem;text-decoration:none;">
          <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
          Email
        </a>
        <button class="modal-close" style="color:#94a3b8;background:none;border:none;cursor:pointer;" onclick="closeModal('receipt-modal')"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
      </div>
    </div>
    <div class="modal-body" style="padding:0;" id="receipt-content">
      <div style="padding:1.5rem;text-align:center;border-bottom:1px dashed #e2e8f0;">
        <div style="font-size:1.2rem;font-weight:800;color:#0f172a;">{{ config('app.name','MannaPOS') }}</div>
        <div style="font-size:0.78rem;color:#64748b;margin-top:2px;">Sales Receipt</div>
      </div>
      <div id="receipt-body" style="padding:1.25rem;">
        <div style="text-align:center;color:#64748b;padding:2rem;">Loading...</div>
      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<style id="print-styles">
@media print {
  body * { visibility: hidden; }
  #receipt-content, #receipt-content * { visibility: visible; }
  #receipt-content { position: fixed; top: 0; left: 0; width: 100%; }
  .modal-header button { display: none !important; }
}
</style>
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
        <button class="btn btn-sm btn-icon" style="background:#eff6ff;border:1px solid #bfdbfe;color:#2563eb;" onclick="viewReceipt(${s.id})" title="View Receipt"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>
        <button class="btn btn-sm btn-edit btn-icon" onclick="editSale(${s.id})" title="Edit"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
        <button class="btn btn-sm btn-delete btn-icon" onclick="deleteSale(${s.id},'${s.reference}')" title="Delete"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
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
  data.items=Array.from(document.querySelectorAll('#saleItemsBody tr')).map(tr=>{
    const qty=parseFloat(tr.querySelector('.sale-qty')?.value||0);
    const price=parseFloat(tr.querySelector('.sale-price')?.value||0);
    const disc=parseFloat(tr.querySelector('.sale-disc')?.value||0);
    const sel=tr.querySelector('.sale-product');
    const productId=sel?.value;
    const productName=sel?.selectedOptions[0]?.text||'';
    return{
      product_id:productId||null,
      product_name:productName||'Manual Item',
      unit_price:price,
      quantity:qty,
      discount:disc,
      total:(qty*price)-disc
    };
  }).filter(i=>i.quantity>0);
  if(!data.items.length){showToast('Please add at least one item','error');return;}
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
async function viewReceipt(id) {
  document.getElementById('receipt-body').innerHTML = '<div style="text-align:center;color:#64748b;padding:2rem;">Loading...</div>';
  openModal('receipt-modal');
  try {
    const s = await apiFetch(`${API}/${id}`);
    const items = s.items || [];
    const fmt = n => parseFloat(n||0).toLocaleString('en',{minimumFractionDigits:2,maximumFractionDigits:2});
    const payBadge = {'cash':'💵 Cash','card':'💳 Card','mobile_money':'📱 Mobile Money','credit':'🕐 Credit'};
    const statusBadge = {'completed':'✅ Completed','draft':'📝 Draft','quotation':'📄 Quotation','cancelled':'❌ Cancelled'};

    // Update header action links
    const invUrl = '/invoice/' + s.reference;
    document.getElementById('receiptInvoiceLink').href = invUrl;
    document.getElementById('receiptWaLink').href = 'https://wa.me/?text=' + encodeURIComponent('Invoice: ' + s.reference + ' - View: ' + window.location.origin + invUrl);
    document.getElementById('receiptEmailLink').href = 'mailto:?subject=Invoice ' + s.reference + '&body=Please find your invoice here: ' + encodeURIComponent(window.location.origin + invUrl);

    document.getElementById('receipt-body').innerHTML = `
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.5rem;margin-bottom:1rem;font-size:0.8rem;">
        <div><span style="color:#64748b;">Invoice #</span><br><strong style="font-family:monospace;color:#2563eb;">${s.reference}</strong></div>
        <div style="text-align:right;"><span style="color:#64748b;">Date</span><br><strong>${s.sale_date}</strong></div>
        <div><span style="color:#64748b;">Customer</span><br><strong>${s.customer?.name||'Walk-in Customer'}</strong></div>
        <div style="text-align:right;"><span style="color:#64748b;">Payment</span><br><strong>${payBadge[s.payment_method]||s.payment_method}</strong></div>
      </div>
      <div style="border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;margin-bottom:1rem;">
        <table style="width:100%;border-collapse:collapse;font-size:0.8rem;">
          <thead><tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
            <th style="padding:0.5rem 0.75rem;text-align:left;color:#64748b;font-size:0.72rem;font-weight:700;">ITEM</th>
            <th style="padding:0.5rem 0.75rem;text-align:right;color:#64748b;font-size:0.72rem;font-weight:700;">QTY</th>
            <th style="padding:0.5rem 0.75rem;text-align:right;color:#64748b;font-size:0.72rem;font-weight:700;">PRICE</th>
            <th style="padding:0.5rem 0.75rem;text-align:right;color:#64748b;font-size:0.72rem;font-weight:700;">TOTAL</th>
          </tr></thead>
          <tbody>
            ${items.map(i=>`<tr style="border-top:1px solid #f1f5f9;">
              <td style="padding:0.5rem 0.75rem;font-weight:500;">${i.product?.name||i.product_name||'Unknown'}</td>
              <td style="padding:0.5rem 0.75rem;text-align:right;color:#64748b;">${i.quantity}</td>
              <td style="padding:0.5rem 0.75rem;text-align:right;color:#64748b;">${fmt(i.unit_price)}</td>
              <td style="padding:0.5rem 0.75rem;text-align:right;font-weight:600;">${fmt(parseFloat(i.unit_price)*parseFloat(i.quantity)-(parseFloat(i.discount)||0))}</td>
            </tr>`).join('')}
          </tbody>
        </table>
      </div>
      <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:0.75rem;font-size:0.82rem;">
        <div style="display:flex;justify-content:space-between;margin-bottom:0.3rem;"><span style="color:#64748b;">Subtotal</span><span>${fmt(s.subtotal||s.total)}</span></div>
        ${parseFloat(s.discount||0)>0?`<div style="display:flex;justify-content:space-between;margin-bottom:0.3rem;"><span style="color:#64748b;">Discount</span><span style="color:#e03057;">-${fmt(s.discount)}</span></div>`:''}
        ${parseFloat(s.tax||0)>0?`<div style="display:flex;justify-content:space-between;margin-bottom:0.3rem;"><span style="color:#64748b;">Tax (VAT)</span><span>${fmt(s.tax)}</span></div>`:''}
        <div style="display:flex;justify-content:space-between;border-top:1px solid #e2e8f0;padding-top:0.5rem;margin-top:0.3rem;font-weight:800;font-size:0.95rem;"><span>Total</span><span style="color:#2563eb;">TSh ${fmt(s.total)}</span></div>
        <div style="display:flex;justify-content:space-between;margin-top:0.3rem;"><span style="color:#64748b;">Paid</span><span style="color:#10b981;font-weight:600;">TSh ${fmt(s.paid||s.total)}</span></div>
        ${parseFloat(s.total||0)>parseFloat(s.paid||0)?`<div style="display:flex;justify-content:space-between;margin-top:0.3rem;"><span style="color:#64748b;">Balance Due</span><span style="color:#e03057;font-weight:600;">TSh ${fmt(parseFloat(s.total)-parseFloat(s.paid||0))}</span></div>`:'<div style="margin-top:0.3rem;text-align:center;color:#10b981;font-weight:700;font-size:0.8rem;">✅ PAID IN FULL</div>'}
      </div>
      ${s.notes?`<div style="margin-top:0.75rem;padding:0.6rem;background:#fffbeb;border:1px solid #fcd34d;border-radius:6px;font-size:0.78rem;color:#92400e;"><strong>Note:</strong> ${s.notes}</div>`:''}
      <div style="text-align:center;margin-top:1rem;font-size:0.72rem;color:#94a3b8;">
        <div>${statusBadge[s.status]||s.status}</div>
        <div style="margin-top:4px;">Thank you for your business!</div>
        <div style="font-family:monospace;margin-top:2px;">MannaPOS · ${new Date().toLocaleDateString()}</div>
      </div>`;
  } catch(e) {
    document.getElementById('receipt-body').innerHTML = '<div style="text-align:center;color:#ef4444;padding:2rem;">Failed to load receipt</div>';
  }
}
function printReceipt() {
  const printContent = document.getElementById('receipt-content').innerHTML;
  const w = window.open('', '_blank', 'width=420,height=700');
  w.document.write(`<!DOCTYPE html><html><head><title>Receipt</title><style>body{font-family:'Inter',sans-serif;margin:0;padding:16px;background:#fff;}@page{margin:12px;}</style></head><body>${printContent}</body></html>`);
  w.document.close(); w.focus(); setTimeout(()=>{w.print();w.close();},300);
}
loadList();
</script>
@endsection
