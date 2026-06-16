@extends('layouts.dashboard')
@section('page_title','Update Prices')
@section('content')
<div class="dash-content">
<div class="page-card">
  <div class="card-header">
    <div class="card-title">Update Product Prices</div>
    <div class="filters-row">
      <div class="search-wrap">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
        <input type="text" id="searchInput" placeholder="Search products..." oninput="loadProducts()">
      </div>
      <select id="categoryFilter" class="form-control" style="width:180px;" onchange="loadProducts()">
        <option value="">All Categories</option>
      </select>
      <button class="btn btn-success" onclick="saveAllChanges()" id="saveAllBtn">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        Save All Changes
      </button>
    </div>
  </div>
  <div style="padding:1rem 1.25rem;background:#eff6ff;border-bottom:1px solid #bfdbfe;font-size:0.82rem;color:#1d4ed8;">
    <strong>Tip:</strong> Edit prices directly in the table cells below, then click "Save All Changes" to apply.
  </div>
  <div style="overflow-x:auto;">
    <table class="tbl">
      <thead><tr><th>Product</th><th>SKU</th><th>Category</th><th>Current Cost</th><th>New Cost Price</th><th>Current Sell</th><th>New Selling Price</th><th>Stock</th></tr></thead>
      <tbody id="tableBody"><tr><td colspan="8" class="tbl-empty">Loading...</td></tr></tbody>
    </table>
  </div>
</div>
</div>
@endsection
@section('scripts')
<script>
let allProducts=[]; const changes={};
async function loadProducts(){
  const s=document.getElementById('searchInput').value;
  const cat=document.getElementById('categoryFilter').value;
  const tbody=document.getElementById('tableBody');
  tbody.innerHTML='<tr><td colspan="8" class="tbl-empty">Loading...</td></tr>';
  try{
    const params=new URLSearchParams({search:s,category_id:cat}).toString();
    allProducts=await apiFetch(`/api/dashboard/products?${params}`);
    if(!allProducts.length){tbody.innerHTML='<tr><td colspan="8" class="tbl-empty">No products found.</td></tr>';return;}
    tbody.innerHTML=allProducts.map(p=>`<tr data-id="${p.id}">
      <td class="font-semibold">${p.name}</td>
      <td class="font-mono text-xs text-slate-400">${p.sku||'-'}</td>
      <td>${p.category?p.category.name:'N/A'}</td>
      <td class="text-slate-500">${parseFloat(p.purchase_price||0).toFixed(2)}</td>
      <td><input type="number" step="0.01" min="0" value="${parseFloat(p.purchase_price||0).toFixed(2)}" class="form-control" style="width:110px;padding:0.3rem 0.5rem;" oninput="markChange(${p.id},'purchase_price',this.value)"></td>
      <td class="text-slate-500">${parseFloat(p.selling_price||0).toFixed(2)}</td>
      <td><input type="number" step="0.01" min="0" value="${parseFloat(p.selling_price||0).toFixed(2)}" class="form-control" style="width:110px;padding:0.3rem 0.5rem;" oninput="markChange(${p.id},'selling_price',this.value)"></td>
      <td><span class="badge ${p.stock_quantity<=0?'badge-danger':p.stock_quantity<=p.reorder_level?'badge-warning':'badge-success'}">${p.stock_quantity}</span></td>
    </tr>`).join('');
  }catch(e){tbody.innerHTML='<tr><td colspan="8" class="tbl-empty">Error loading products.</td></tr>';}
}
function markChange(id,field,val){
  if(!changes[id])changes[id]={};
  changes[id][field]=parseFloat(val)||0;
}
async function saveAllChanges(){
  const ids=Object.keys(changes);
  if(!ids.length){showToast('No changes to save','error');return;}
  const btn=document.getElementById('saveAllBtn');btn.disabled=true;btn.textContent='Saving...';
  try{
    await Promise.all(ids.map(id=>{
      const p=allProducts.find(x=>x.id==id);
      if(!p)return;
      const upd={name:p.name,purchase_price:changes[id].purchase_price??p.purchase_price,selling_price:changes[id].selling_price??p.selling_price};
      return apiFetch(`/api/dashboard/products/${id}`,{method:'PUT',body:JSON.stringify(upd)});
    }));
    Object.keys(changes).forEach(k=>delete changes[k]);
    showToast(`${ids.length} product(s) updated!`);
    loadProducts();
  }catch(e){showToast(e.message||'Save failed','error');}
  finally{btn.disabled=false;btn.textContent='Save All Changes';}
}
async function init(){
  try{
    const cats=await apiFetch('/api/dashboard/categories');
    document.getElementById('categoryFilter').innerHTML='<option value="">All Categories</option>'+cats.map(c=>`<option value="${c.id}">${c.name}</option>`).join('');
  }catch(e){}
  loadProducts();
}
init();
</script>
@endsection
