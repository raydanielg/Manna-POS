@extends('layouts.dashboard')
@section('page_title','Shipments')
@section('content')
<div class="dash-content">
<div class="page-card">
  <div class="card-header">
    <div class="card-title">Shipments</div>
    <div class="filters-row">
      <div class="search-wrap">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
        <input type="text" id="searchInput" placeholder="Search reference, recipient..." oninput="loadList()">
      </div>
      <select id="statusFilter" class="form-control" style="width:160px;" onchange="loadList()">
        <option value="">All Status</option>
        <option value="pending">Pending</option>
        <option value="shipped">Shipped</option>
        <option value="delivered">Delivered</option>
        <option value="cancelled">Cancelled</option>
      </select>
      <button class="btn btn-primary" onclick="openAddModal()">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Add Shipment
      </button>
    </div>
  </div>
  <div style="overflow-x:auto;">
    <table class="tbl">
      <thead><tr><th>#</th><th>Reference</th><th>Recipient</th><th>Carrier</th><th>Tracking</th><th>Ship Date</th><th>Expected</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody id="tableBody"><tr><td colspan="9" class="tbl-empty">Loading...</td></tr></tbody>
    </table>
  </div>
</div>
</div>
<div class="modal-overlay" id="modal">
  <div class="modal modal-lg">
    <div class="modal-header">
      <div class="modal-title" id="modal-title">Add Shipment</div>
      <button class="modal-close" onclick="closeModal('modal')"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <div class="modal-body">
      <form id="itemForm">
        <div class="form-row">
          <div class="form-group"><label class="form-label">Recipient Name</label><input name="recipient_name" class="form-control" placeholder="Customer or recipient name"><div class="invalid-feedback"></div></div>
          <div class="form-group"><label class="form-label">Status</label>
            <select name="status" class="form-control">
              <option value="pending">Pending</option><option value="shipped">Shipped</option><option value="delivered">Delivered</option><option value="cancelled">Cancelled</option>
            </select>
          </div>
        </div>
        <div class="form-group"><label class="form-label">Shipping Address</label><textarea name="shipping_address" class="form-control" rows="2" placeholder="Full delivery address..."></textarea><div class="invalid-feedback"></div></div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">Carrier</label><input name="carrier" class="form-control" placeholder="e.g. DHL, FedEx, Local Courier"><div class="invalid-feedback"></div></div>
          <div class="form-group"><label class="form-label">Tracking Number</label><input name="tracking_number" class="form-control" placeholder="Tracking number"><div class="invalid-feedback"></div></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">Ship Date</label><input name="ship_date" type="date" class="form-control"><div class="invalid-feedback"></div></div>
          <div class="form-group"><label class="form-label">Expected Delivery</label><input name="expected_delivery" type="date" class="form-control"><div class="invalid-feedback"></div></div>
        </div>
        <div class="form-group"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2" placeholder="Additional notes..."></textarea></div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal')">Cancel</button>
      <button class="btn btn-primary" id="saveBtn" onclick="saveItem()">Save Shipment</button>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
const API='/api/dashboard/shipments'; let editId=null;
const statusColors={pending:'badge-warning',shipped:'badge-info',delivered:'badge-success',cancelled:'badge-danger'};
async function loadList(){
  const s=document.getElementById('searchInput').value;
  const st=document.getElementById('statusFilter').value;
  const tbody=document.getElementById('tableBody');
  tbody.innerHTML='<tr><td colspan="9" class="tbl-empty">Loading...</td></tr>';
  try{
    const items=await apiFetch(`${API}?search=${encodeURIComponent(s)}&status=${st}`);
    if(!items.length){tbody.innerHTML='<tr><td colspan="9" class="tbl-empty">No shipments found.</td></tr>';return;}
    tbody.innerHTML=items.map((s,i)=>`<tr>
      <td class="text-slate-400">${i+1}</td>
      <td class="font-mono text-xs text-blue-600">${s.reference}</td>
      <td class="font-semibold">${s.recipient_name||'N/A'}</td>
      <td>${s.carrier||'-'}</td>
      <td class="font-mono text-xs">${s.tracking_number||'-'}</td>
      <td class="text-slate-500 text-xs">${s.ship_date||'-'}</td>
      <td class="text-slate-500 text-xs">${s.expected_delivery||'-'}</td>
      <td><span class="badge ${statusColors[s.status]||'badge-gray'}">${s.status}</span></td>
      <td><div style="display:flex;gap:0.4rem;">
        <button class="btn btn-sm btn-edit btn-icon" onclick="editItem(${s.id})"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
        <button class="btn btn-sm btn-delete btn-icon" onclick="deleteItem(${s.id},'${s.reference}')"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
      </div></td>
    </tr>`).join('');
  }catch(e){tbody.innerHTML='<tr><td colspan="9" class="tbl-empty">Error loading data.</td></tr>';}
}
function openAddModal(){editId=null;document.getElementById('modal-title').textContent='Add Shipment';document.getElementById('itemForm').reset();clearFormErrors('itemForm');openModal('modal');}
async function editItem(id){
  try{const s=await apiFetch(`${API}/${id}`);editId=id;document.getElementById('modal-title').textContent='Edit Shipment';
  const form=document.getElementById('itemForm');Object.entries(s).forEach(([k,v])=>{const el=form.querySelector(`[name="${k}"]`);if(el)el.value=v??'';});
  clearFormErrors('itemForm');openModal('modal');}catch(e){showToast('Failed to load','error');}
}
async function saveItem(){
  clearFormErrors('itemForm');const data=Object.fromEntries(new FormData(document.getElementById('itemForm')));
  const btn=document.getElementById('saveBtn');btn.disabled=true;btn.textContent='Saving...';
  try{if(editId)await apiFetch(`${API}/${editId}`,{method:'PUT',body:JSON.stringify(data)});
  else await apiFetch(API,{method:'POST',body:JSON.stringify(data)});
  closeModal('modal');showToast(editId?'Shipment updated!':'Shipment added!');loadList();}
  catch(e){if(e.errors)showFormErrors('itemForm',e.errors);else showToast(e.message||'Save failed','error');}
  finally{btn.disabled=false;btn.textContent='Save Shipment';}
}
function deleteItem(id,ref){
  showConfirm('Delete Shipment',`Delete shipment "${ref}"?`,async()=>{
    try{await apiFetch(`${API}/${id}`,{method:'DELETE'});showToast('Shipment deleted!');loadList();}
    catch(e){showToast(e.message||'Delete failed','error');}
  });
}
loadList();
</script>
@endsection
