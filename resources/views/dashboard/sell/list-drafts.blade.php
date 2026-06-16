@extends('layouts.dashboard')
@section('page_title','Draft Sales')
@section('content')
<div class="dash-content">
<div class="page-card">
  <div class="card-header">
    <div class="card-title">Draft Sales</div>
    <div class="filters-row">
      <div class="search-wrap">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
        <input type="text" id="searchInput" placeholder="Search reference..." oninput="loadList()">
      </div>
      <a href="/dashboard/sell/all-sales" class="btn btn-primary">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        New Sale
      </a>
    </div>
  </div>
  <div style="overflow-x:auto;">
    <table class="tbl">
      <thead><tr><th>#</th><th>Reference</th><th>Customer</th><th>Date</th><th>Total</th><th>Paid</th><th>Payment</th><th>Actions</th></tr></thead>
      <tbody id="tableBody"><tr><td colspan="8" class="tbl-empty">Loading...</td></tr></tbody>
    </table>
  </div>
</div>
</div>
<div class="modal-overlay" id="viewModal">
  <div class="modal modal-lg">
    <div class="modal-header">
      <div class="modal-title">Draft Details</div>
      <button class="modal-close" onclick="closeModal('viewModal')"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <div class="modal-body" id="viewBody"></div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('viewModal')">Close</button>
      <button class="btn btn-success" id="convertBtn" onclick="convertToSale()">Convert to Sale</button>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
const API='/api/dashboard/sales'; let currentId=null;
const payColors={paid:'badge-success',partial:'badge-warning',unpaid:'badge-danger'};
async function loadList(){
  const s=document.getElementById('searchInput').value;
  const tbody=document.getElementById('tableBody');
  tbody.innerHTML='<tr><td colspan="8" class="tbl-empty">Loading...</td></tr>';
  try{
    const items=await apiFetch(`${API}?status=draft&search=${encodeURIComponent(s)}`);
    if(!items.length){tbody.innerHTML='<tr><td colspan="8" class="tbl-empty">No drafts found.</td></tr>';return;}
    tbody.innerHTML=items.map((s,i)=>`<tr>
      <td class="text-slate-400">${i+1}</td>
      <td class="font-mono text-xs text-blue-600">${s.reference}</td>
      <td>${s.customer?s.customer.name:'Walk-in'}</td>
      <td class="text-slate-500 text-xs">${s.sale_date}</td>
      <td class="font-semibold">${parseFloat(s.total).toFixed(2)}</td>
      <td class="text-green-600">${parseFloat(s.paid||0).toFixed(2)}</td>
      <td><span class="badge ${payColors[s.payment_status]||'badge-gray'}">${s.payment_status}</span></td>
      <td><div style="display:flex;gap:0.4rem;">
        <button class="btn btn-sm btn-edit btn-icon" onclick="viewDraft(${s.id})" title="View"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>
        <button class="btn btn-sm btn-delete btn-icon" onclick="deleteDraft(${s.id},'${s.reference}')"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
      </div></td>
    </tr>`).join('');
  }catch(e){tbody.innerHTML='<tr><td colspan="8" class="tbl-empty">Error loading data.</td></tr>';}
}
async function viewDraft(id){
  currentId=id;
  try{
    const s=await apiFetch(`${API}/${id}`);
    document.getElementById('viewBody').innerHTML=`
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem;">
        <div><strong>Reference:</strong> <span class="font-mono text-blue-600">${s.reference}</span></div>
        <div><strong>Date:</strong> ${s.sale_date}</div>
        <div><strong>Customer:</strong> ${s.customer?s.customer.name:'Walk-in'}</div>
        <div><strong>Payment:</strong> <span class="badge ${payColors[s.payment_status]||'badge-gray'}">${s.payment_status}</span></div>
      </div>
      <table class="tbl" style="margin-bottom:1rem;">
        <thead><tr><th>Product</th><th>Qty</th><th>Unit Price</th><th>Discount</th><th>Total</th></tr></thead>
        <tbody>${(s.items||[]).map(it=>`<tr>
          <td>${it.product_name||it.product?.name||'N/A'}</td>
          <td>${it.quantity}</td>
          <td>${parseFloat(it.unit_price).toFixed(2)}</td>
          <td>${parseFloat(it.discount||0).toFixed(2)}</td>
          <td>${parseFloat(it.total).toFixed(2)}</td>
        </tr>`).join('')}</tbody>
      </table>
      <div style="text-align:right;font-size:0.9rem;">
        <div>Subtotal: <strong>${parseFloat(s.subtotal||0).toFixed(2)}</strong></div>
        <div>Discount: <strong>${parseFloat(s.discount||0).toFixed(2)}</strong></div>
        <div>Tax: <strong>${parseFloat(s.tax||0).toFixed(2)}</strong></div>
        <div style="font-size:1.1rem;margin-top:0.5rem;">Total: <strong>${parseFloat(s.total).toFixed(2)}</strong></div>
      </div>`;
    openModal('viewModal');
  }catch(e){showToast('Failed to load draft','error');}
}
async function convertToSale(){
  if(!currentId)return;
  const btn=document.getElementById('convertBtn');btn.disabled=true;btn.textContent='Converting...';
  try{
    await apiFetch(`${API}/${currentId}`,{method:'PUT',body:JSON.stringify({status:'completed'})});
    closeModal('viewModal');showToast('Draft converted to sale!');loadList();
  }catch(e){showToast(e.message||'Failed to convert','error');}
  finally{btn.disabled=false;btn.textContent='Convert to Sale';}
}
function deleteDraft(id,ref){
  showConfirm('Delete Draft',`Delete draft "${ref}"?`,async()=>{
    try{await apiFetch(`${API}/${id}`,{method:'DELETE'});showToast('Draft deleted!');loadList();}
    catch(e){showToast(e.message||'Delete failed','error');}
  });
}
loadList();
</script>
@endsection
