@extends('layouts.dashboard')
@section('page_title','Customers')
@section('content')
<div class="dash-content">
<div class="page-card">
  <div class="card-header">
    <div class="card-title">All Customers</div>
    <div class="filters-row">
      <div class="search-wrap">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
        <input type="text" id="searchInput" placeholder="Search customers..." oninput="loadList()">
      </div>
      <button class="btn btn-success" onclick="openAddModal()">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Add Customer
      </button>
    </div>
  </div>
  <div style="overflow-x:auto;">
    <table class="tbl">
      <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Phone</th><th>Group</th><th>Balance</th><th>Points</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody id="tableBody"><tr><td colspan="9" class="tbl-empty">Loading...</td></tr></tbody>
    </table>
  </div>
</div>
</div>

<div class="modal-overlay" id="modal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title" id="modal-title">Add Customer</div>
      <button class="modal-close" onclick="closeModal('modal')">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <div class="modal-body">
      <form id="customerForm">
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Name *</label>
            <input name="name" class="form-control" required placeholder="Full name">
            <div class="invalid-feedback"></div>
          </div>
          <div class="form-group">
            <label class="form-label">Email</label>
            <input name="email" type="email" class="form-control" placeholder="email@example.com">
            <div class="invalid-feedback"></div>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Phone</label>
            <input name="phone" class="form-control" placeholder="+255 7xx xxx xxx">
            <div class="invalid-feedback"></div>
          </div>
          <div class="form-group">
            <label class="form-label">Customer Group</label>
            <select name="customer_group_id" class="form-control">
              <option value="">-- No Group --</option>
              @foreach($groups as $g)
              <option value="{{ $g->id }}">{{ $g->name }}</option>
              @endforeach
            </select>
            <div class="invalid-feedback"></div>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">City</label>
            <input name="city" class="form-control" placeholder="City">
            <div class="invalid-feedback"></div>
          </div>
          <div class="form-group">
            <label class="form-label">Country</label>
            <input name="country" class="form-control" value="Tanzania">
            <div class="invalid-feedback"></div>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Credit Limit (TSh)</label>
            <input name="credit_limit" type="number" step="0.01" min="0" class="form-control" placeholder="0.00">
            <div class="invalid-feedback"></div>
          </div>
          <div class="form-group">
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
            <div class="invalid-feedback"></div>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Address</label>
          <textarea name="address" class="form-control" placeholder="Street address"></textarea>
          <div class="invalid-feedback"></div>
        </div>
        <div class="form-group">
          <label class="form-label">Notes</label>
          <textarea name="notes" class="form-control" placeholder="Additional notes..."></textarea>
          <div class="invalid-feedback"></div>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal')">Cancel</button>
      <button class="btn btn-primary" id="saveBtn" onclick="saveCustomer()">Save Customer</button>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
const API = '/api/dashboard/customers';
let editId = null;
async function loadList() {
  const search = document.getElementById('searchInput').value;
  const tbody = document.getElementById('tableBody');
  tbody.innerHTML = '<tr><td colspan="9" class="tbl-empty">Loading...</td></tr>';
  try {
    const items = await apiFetch(`${API}?search=${encodeURIComponent(search)}`);
    if (!items.length) { tbody.innerHTML = '<tr><td colspan="9" class="tbl-empty">No customers found.</td></tr>'; return; }
    tbody.innerHTML = items.map((c,i) => `
      <tr>
        <td class="text-slate-400">${i+1}</td>
        <td class="font-semibold">${c.name}</td>
        <td>${c.email||'—'}</td>
        <td>${c.phone||'—'}</td>
        <td>${c.group?.name||'—'}</td>
        <td>TSh ${Number(c.balance||0).toLocaleString()}</td>
        <td>${Number(c.loyalty_points||0).toLocaleString()}</td>
        <td><span class="badge ${c.status==='active'?'badge-success':'badge-danger'}">${c.status}</span></td>
        <td><div style="display:flex;gap:0.4rem;">
          <a href="{{ url('/dashboard/contacts/customers') }}/${c.id}" class="btn btn-sm btn-secondary btn-icon" title="View"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></a>
          <button class="btn btn-sm btn-edit btn-icon" onclick="editCustomer(${c.id})" title="Edit"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
          <button class="btn btn-sm btn-delete btn-icon" onclick="deleteCustomer(${c.id},'${c.name}')" title="Delete"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
        </div></td>
      </tr>`).join('');
  } catch(e) { tbody.innerHTML = '<tr><td colspan="9" class="tbl-empty">Error loading data.</td></tr>'; }
}
function openAddModal() { editId=null; document.getElementById('modal-title').textContent='Add Customer'; document.getElementById('customerForm').reset(); clearFormErrors('customerForm'); openModal('modal'); }
async function editCustomer(id) {
  try {
    const c = await apiFetch(`${API}/${id}`);
    editId = id; document.getElementById('modal-title').textContent = 'Edit Customer';
    const form = document.getElementById('customerForm');
    Object.entries(c).forEach(([k,v]) => { const el = form.querySelector(`[name="${k}"]`); if(el) el.value = v??''; });
    clearFormErrors('customerForm'); openModal('modal');
  } catch(e) { showToast('Failed to load customer','error'); }
}
async function saveCustomer() {
  clearFormErrors('customerForm');
  const data = Object.fromEntries(new FormData(document.getElementById('customerForm')));
  const btn = document.getElementById('saveBtn'); btn.disabled=true; btn.textContent='Saving...';
  try {
    if (editId) await apiFetch(`${API}/${editId}`, {method:'PUT', body:JSON.stringify(data)});
    else await apiFetch(API, {method:'POST', body:JSON.stringify(data)});
    closeModal('modal'); showToast(editId?'Customer updated!':'Customer added!'); loadList();
  } catch(e) { if(e.errors) showFormErrors('customerForm',e.errors); else showToast(e.message||'Save failed','error'); }
  finally { btn.disabled=false; btn.textContent='Save Customer'; }
}
function deleteCustomer(id,name) {
  showConfirm('Delete Customer',`Delete "${name}"? This cannot be undone.`, async()=>{
    try { await apiFetch(`${API}/${id}`,{method:'DELETE'}); showToast('Customer deleted!'); loadList(); }
    catch(e){ showToast('Delete failed','error'); }
  });
}
loadList();
</script>
@endsection
