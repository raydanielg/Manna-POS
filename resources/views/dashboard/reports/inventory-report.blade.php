@extends('layouts.dashboard')
@section('page_title','Inventory Report')
@section('content')
<div class="dash-content">
<div class="page-card">
  <div class="card-header">
    <div class="card-title">Inventory Report</div>
    <div class="filters-row">
      <div class="search-wrap">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
        <input type="text" id="searchInput" placeholder="Search products..." oninput="loadReport()">
      </div>
      <select id="stockFilter" class="form-control" style="width:160px;" onchange="loadReport()">
        <option value="">All Stock</option>
        <option value="low">Low Stock</option>
        <option value="out">Out of Stock</option>
      </select>
      <button class="btn btn-primary" onclick="loadReport()">Refresh</button>
    </div>
  </div>
  <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;padding:1.25rem;border-bottom:1px solid #e9edf5;">
    <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:1rem;">
      <div style="font-size:0.72rem;font-weight:600;color:#2563eb;text-transform:uppercase;">Total Products</div>
      <div style="font-size:1.6rem;font-weight:700;color:#1d4ed8;" id="totalProducts">-</div>
    </div>
    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:1rem;">
      <div style="font-size:0.72rem;font-weight:600;color:#16a34a;text-transform:uppercase;">Stock Value</div>
      <div style="font-size:1.6rem;font-weight:700;color:#15803d;" id="stockValue">-</div>
    </div>
    <div style="background:#fef3c7;border:1px solid #fde68a;border-radius:10px;padding:1rem;">
      <div style="font-size:0.72rem;font-weight:600;color:#d97706;text-transform:uppercase;">Low Stock</div>
      <div style="font-size:1.6rem;font-weight:700;color:#b45309;" id="lowStockCount">-</div>
    </div>
    <div style="background:#fff1f2;border:1px solid #fecdd3;border-radius:10px;padding:1rem;">
      <div style="font-size:0.72rem;font-weight:600;color:#e03057;text-transform:uppercase;">Out of Stock</div>
      <div style="font-size:1.6rem;font-weight:700;color:#be123c;" id="outOfStockCount">-</div>
    </div>
  </div>
  <div style="overflow-x:auto;">
    <table class="tbl">
      <thead><tr><th>#</th><th>Product</th><th>SKU</th><th>Category</th><th>Stock Qty</th><th>Reorder Level</th><th>Purchase Price</th><th>Selling Price</th><th>Stock Value</th></tr></thead>
      <tbody id="tableBody"><tr><td colspan="9" class="tbl-empty">Loading...</td></tr></tbody>
    </table>
  </div>
</div>
</div>
@endsection
@section('scripts')
<script>
async function loadReport(){
  const s=document.getElementById('searchInput').value;const sf=document.getElementById('stockFilter').value;
  const tbody=document.getElementById('tableBody');
  tbody.innerHTML='<tr><td colspan="9" class="tbl-empty">Loading...</td></tr>';
  try{
    let items=await apiFetch(`/api/dashboard/products?search=${encodeURIComponent(s)}&per_page=500`);
    if(sf==='low')items=items.filter(p=>p.stock_quantity<=p.reorder_level&&p.stock_quantity>0);
    else if(sf==='out')items=items.filter(p=>p.stock_quantity<=0);
    document.getElementById('totalProducts').textContent=items.length;
    const sv=items.reduce((a,p)=>a+(parseFloat(p.purchase_price||0)*parseFloat(p.stock_quantity||0)),0);
    document.getElementById('stockValue').textContent=sv.toFixed(2);
    document.getElementById('lowStockCount').textContent=items.filter(p=>p.stock_quantity<=p.reorder_level&&p.stock_quantity>0).length;
    document.getElementById('outOfStockCount').textContent=items.filter(p=>p.stock_quantity<=0).length;
    if(!items.length){tbody.innerHTML='<tr><td colspan="9" class="tbl-empty">No products found.</td></tr>';return;}
    tbody.innerHTML=items.map((p,i)=>{
      const isLow=p.stock_quantity<=p.reorder_level&&p.stock_quantity>0;
      const isOut=p.stock_quantity<=0;
      const qty=parseFloat(p.stock_quantity||0);
      const sv=(parseFloat(p.purchase_price||0)*qty).toFixed(2);
      return `<tr>
        <td class="text-slate-400">${i+1}</td>
        <td class="font-semibold">${p.name}</td>
        <td class="font-mono text-xs text-slate-400">${p.sku||'-'}</td>
        <td>${p.category?p.category.name:'N/A'}</td>
        <td><span class="badge ${isOut?'badge-danger':isLow?'badge-warning':'badge-success'}">${qty}</span></td>
        <td class="text-slate-500">${p.reorder_level||0}</td>
        <td>${parseFloat(p.purchase_price||0).toFixed(2)}</td>
        <td>${parseFloat(p.selling_price||0).toFixed(2)}</td>
        <td class="font-semibold">${sv}</td>
      </tr>`;
    }).join('');
  }catch(e){tbody.innerHTML='<tr><td colspan="9" class="tbl-empty">Error loading report.</td></tr>';}
}
loadReport();
</script>
@endsection
