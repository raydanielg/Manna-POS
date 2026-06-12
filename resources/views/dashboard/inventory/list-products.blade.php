@extends('layouts.dashboard')
@section('page_title','Products')
@section('content')
<div class="dash-content">
<div class="page-card">
  <div class="card-header">
    <div class="card-title">All Products</div>
    <div class="filters-row">
      <div class="search-wrap">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
        <input type="text" id="searchInput" placeholder="Search by name, SKU..." oninput="loadList()">
      </div>
      <select id="catFilter" class="form-control" style="width:160px;" onchange="loadList()">
        <option value="">All Categories</option>
        @foreach($categories as $c) <option value="{{ $c->id }}">{{ $c->name }}</option> @endforeach
      </select>
      <select id="brandFilter" class="form-control" style="width:140px;" onchange="loadList()">
        <option value="">All Brands</option>
        @foreach($brands as $b) <option value="{{ $b->id }}">{{ $b->name }}</option> @endforeach
      </select>
      <button class="btn btn-success" onclick="openAddModal()">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Add Product
      </button>
    </div>
  </div>
  <div style="overflow-x:auto;">
    <table class="tbl">
      <thead><tr><th>#</th><th>Name</th><th>SKU</th><th>Category</th><th>Brand</th><th>Buy Price</th><th>Sell Price</th><th>Stock</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody id="tableBody"><tr><td colspan="10" class="tbl-empty">Loading...</td></tr></tbody>
    </table>
  </div>
</div>
</div>

<div class="modal-overlay" id="modal">
  <div class="modal modal-lg">
    <div class="modal-header">
      <div class="modal-title" id="modal-title">Add Product</div>
      <button class="modal-close" onclick="closeModal('modal')"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <div class="modal-body">
      <form id="productForm">
        <div class="form-group">
          <label class="form-label">Product Name *</label>
          <input name="name" class="form-control" required placeholder="Product name">
          <div class="invalid-feedback"></div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">SKU</label>
            <input name="sku" class="form-control" placeholder="Auto-generated if blank">
            <div class="invalid-feedback"></div>
          </div>
          <div class="form-group">
            <label class="form-label">Barcode</label>
            <input name="barcode" class="form-control" placeholder="Barcode / GTIN">
            <div class="invalid-feedback"></div>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Category</label>
            <select name="category_id" class="form-control">
              <option value="">-- Select Category --</option>
              @foreach($categories as $c) <option value="{{ $c->id }}">{{ $c->name }}</option> @endforeach
            </select>
            <div class="invalid-feedback"></div>
          </div>
          <div class="form-group">
            <label class="form-label">Brand</label>
            <select name="brand_id" class="form-control">
              <option value="">-- Select Brand --</option>
              @foreach($brands as $b) <option value="{{ $b->id }}">{{ $b->name }}</option> @endforeach
            </select>
            <div class="invalid-feedback"></div>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Unit</label>
            <select name="unit_id" class="form-control">
              <option value="">-- Select Unit --</option>
              @foreach($units as $u) <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->short_name }})</option> @endforeach
            </select>
            <div class="invalid-feedback"></div>
          </div>
          <div class="form-group">
            <label class="form-label">Tax Rate</label>
            <select name="tax_rate_id" class="form-control">
              <option value="">-- No Tax --</option>
              @foreach($taxRates as $t) <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->rate }}%)</option> @endforeach
            </select>
            <div class="invalid-feedback"></div>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Purchase Price (TSh) *</label>
            <input name="purchase_price" type="number" step="0.01" min="0" class="form-control" required placeholder="0.00">
            <div class="invalid-feedback"></div>
          </div>
          <div class="form-group">
            <label class="form-label">Selling Price (TSh) *</label>
            <input name="selling_price" type="number" step="0.01" min="0" class="form-control" required placeholder="0.00">
            <div class="invalid-feedback"></div>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Opening Stock Qty</label>
            <input name="stock_quantity" type="number" step="0.0001" min="0" class="form-control" placeholder="0">
            <div class="invalid-feedback"></div>
          </div>
          <div class="form-group">
            <label class="form-label">Reorder Level</label>
            <input name="reorder_level" type="number" step="0.0001" min="0" class="form-control" placeholder="5">
            <div class="invalid-feedback"></div>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Description</label>
          <textarea name="description" class="form-control" placeholder="Product description..."></textarea>
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
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal')">Cancel</button>
      <button class="btn btn-primary" id="saveBtn" onclick="saveProduct()">Save Product</button>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
const API = '/api/dashboard/products';
let editId = null;
async function loadList() {
  const search = document.getElementById('searchInput').value;
  const cat = document.getElementById('catFilter').value;
  const brand = document.getElementById('brandFilter').value;
  const tbody = document.getElementById('tableBody');
  tbody.innerHTML = '<tr><td colspan="10" class="tbl-empty">Loading...</td></tr>';
  try {
    const params = new URLSearchParams({search,category_id:cat,brand_id:brand}).toString();
    const items = await apiFetch(`${API}?${params}`);
    if (!items.length) { tbody.innerHTML = '<tr><td colspan="10" class="tbl-empty">No products found.</td></tr>'; return; }
    tbody.innerHTML = items.map((p,i) => `<tr>
      <td class="text-slate-400">${i+1}</td>
      <td class="font-semibold">${p.name}</td>
      <td class="text-slate-400 text-xs">${p.sku||'—'}</td>
      <td>${p.category?.name||'—'}</td>
      <td>${p.brand?.name||'—'}</td>
      <td>TSh ${Number(p.purchase_price||0).toLocaleString()}</td>
      <td class="font-semibold text-green-700">TSh ${Number(p.selling_price||0).toLocaleString()}</td>
      <td><span class="badge ${Number(p.stock_quantity)<=Number(p.reorder_level)?'badge-danger':'badge-success'}">${Number(p.stock_quantity||0).toLocaleString()}</span></td>
      <td><span class="badge ${p.status==='active'?'badge-success':'badge-gray'}">${p.status}</span></td>
      <td><div style="display:flex;gap:0.4rem;">
        <button class="btn btn-sm btn-edit btn-icon" onclick="editProduct(${p.id})" title="Edit"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
        <button class="btn btn-sm btn-delete btn-icon" onclick="deleteProduct(${p.id},'${p.name.replace(/'/g,"\\'")}'" title="Delete"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
      </div></td>
    </tr>`).join('');
  } catch(e) { tbody.innerHTML = '<tr><td colspan="10" class="tbl-empty">Error loading data.</td></tr>'; }
}
function openAddModal() { editId=null; document.getElementById('modal-title').textContent='Add Product'; document.getElementById('productForm').reset(); clearFormErrors('productForm'); openModal('modal'); }
async function editProduct(id) {
  try {
    const p = await apiFetch(`${API}/${id}`);
    editId=id; document.getElementById('modal-title').textContent='Edit Product';
    const form = document.getElementById('productForm');
    Object.entries(p).forEach(([k,v]) => { const el=form.querySelector(`[name="${k}"]`); if(el) el.value=v??''; });
    clearFormErrors('productForm'); openModal('modal');
  } catch(e) { showToast('Failed to load product','error'); }
}
async function saveProduct() {
  clearFormErrors('productForm');
  const data = Object.fromEntries(new FormData(document.getElementById('productForm')));
  const btn = document.getElementById('saveBtn'); btn.disabled=true; btn.textContent='Saving...';
  try {
    if(editId) await apiFetch(`${API}/${editId}`,{method:'PUT',body:JSON.stringify(data)});
    else await apiFetch(API,{method:'POST',body:JSON.stringify(data)});
    closeModal('modal'); showToast(editId?'Product updated!':'Product added!'); loadList();
  } catch(e) { if(e.errors) showFormErrors('productForm',e.errors); else showToast(e.message||'Save failed','error'); }
  finally { btn.disabled=false; btn.textContent='Save Product'; }
}
function deleteProduct(id,name) {
  showConfirm('Delete Product',`Delete "${name}"? This cannot be undone.`, async()=>{
    try { await apiFetch(`${API}/${id}`,{method:'DELETE'}); showToast('Product deleted!'); loadList(); }
    catch(e){ showToast('Delete failed','error'); }
  });
}
loadList();
</script>
@endsection
