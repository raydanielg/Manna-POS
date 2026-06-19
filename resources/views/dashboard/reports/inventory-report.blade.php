@extends('layouts.dashboard')
@section('page_title','Inventory Report')
@section('content')
<div class="dash-content animate__animated animate__fadeInUp report-page">

    <div class="report-header-bar" data-aos="fade-down">
        <div>
            <h1>Inventory Report</h1>
            <p>Real-time stock levels, valuations, and reorder alerts</p>
        </div>
        <div class="report-actions no-print">
            <button type="button" class="btn btn-primary" onclick="openPdfPreview('Inventory Report')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                Preview PDF
            </button>
            <button type="button" class="btn btn-success" onclick="exportTableToCSV('#inventoryTable', 'inventory-report.csv')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export Excel
            </button>
        </div>
    </div>

    <div class="report-filters no-print" data-aos="fade-up" data-aos-delay="50">
        <div class="search-wrap" style="position:relative;">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="position:absolute;left:0.7rem;top:50%;transform:translateY(-50%);width:16px;height:16px;color:#94a3b8;"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
            <input type="text" id="searchInput" placeholder="Search products..." oninput="loadReport()" style="padding-left:2.4rem;">
        </div>
        <div>
            <label>Stock Filter</label>
            <select id="stockFilter" onchange="loadReport()">
                <option value="">All Stock</option>
                <option value="low">Low Stock</option>
                <option value="out">Out of Stock</option>
            </select>
        </div>
        <button class="btn btn-primary" style="height:40px;" onclick="loadReport()">Refresh</button>
    </div>

    <div class="report-summary" data-aos="fade-up" data-aos-delay="100">
        <div class="report-summary-card"><div class="rsc-bar blue"></div><div class="rsc-label">Total Products</div><div class="rsc-value" id="totalProducts">-</div></div>
        <div class="report-summary-card"><div class="rsc-bar green"></div><div class="rsc-label">Stock Value</div><div class="rsc-value" id="stockValue">-</div></div>
        <div class="report-summary-card"><div class="rsc-bar amber"></div><div class="rsc-label">Low Stock</div><div class="rsc-value" id="lowStockCount">-</div></div>
        <div class="report-summary-card"><div class="rsc-bar red"></div><div class="rsc-label">Out of Stock</div><div class="rsc-value" id="outOfStockCount">-</div></div>
    </div>

    <div class="report-table-wrap" data-aos="fade-up" data-aos-delay="150">
        <div class="rtw-head"><div class="rtw-title">Product Inventory</div></div>
        <div class="rtw-body tbl-responsive">
            <table class="report-table" id="inventoryTable">
                <thead><tr><th>#</th><th>Product</th><th>SKU</th><th>Category</th><th>Stock Qty</th><th>Reorder Level</th><th class="text-right">Purchase Price</th><th class="text-right">Selling Price</th><th class="text-right">Stock Value</th></tr></thead>
                <tbody id="tableBody"><tr><td colspan="9"><div class="empty-state"><div class="empty-title">Loading...</div></div></td></tr></tbody>
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
  tbody.innerHTML='<tr><td colspan="9"><div class="empty-state"><div class="empty-title">Loading...</div></div></td></tr>';
  try{
    let items=await apiFetch(`/api/dashboard/products?search=${encodeURIComponent(s)}&per_page=500`);
    if(sf==='low')items=items.filter(p=>p.stock_quantity<=p.reorder_level&&p.stock_quantity>0);
    else if(sf==='out')items=items.filter(p=>p.stock_quantity<=0);
    document.getElementById('totalProducts').textContent=items.length.toLocaleString();
    const sv=items.reduce((a,p)=>a+(parseFloat(p.purchase_price||0)*parseFloat(p.stock_quantity||0)),0);
    document.getElementById('stockValue').textContent='{{ $userCurrency }} ' + sv.toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2});
    document.getElementById('lowStockCount').textContent=items.filter(p=>p.stock_quantity<=p.reorder_level&&p.stock_quantity>0).length.toLocaleString();
    document.getElementById('outOfStockCount').textContent=items.filter(p=>p.stock_quantity<=0).length.toLocaleString();
    if(!items.length){tbody.innerHTML='<tr><td colspan="9"><div class="empty-state"><svg class="empty-icon" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg><div class="empty-title">No products found</div></div></td></tr>';return;}
    tbody.innerHTML=items.map((p,i)=>{
      const isLow=p.stock_quantity<=p.reorder_level&&p.stock_quantity>0;
      const isOut=p.stock_quantity<=0;
      const qty=parseFloat(p.stock_quantity||0);
      const stockVal=(parseFloat(p.purchase_price||0)*qty);
      return `<tr>
        <td class="text-slate-400">${i+1}</td>
        <td style="font-weight:600;">${p.name}</td>
        <td class="font-mono text-xs" style="color:#94a3b8;">${p.sku||'-'}</td>
        <td>${p.category?p.category.name:'N/A'}</td>
        <td><span class="badge ${isOut?'badge-danger':isLow?'badge-warning':'badge-success'}">${qty}</span></td>
        <td style="color:#64748b;">${p.reorder_level||0}</td>
        <td class="text-right">${parseFloat(p.purchase_price||0).toFixed(2)}</td>
        <td class="text-right">${parseFloat(p.selling_price||0).toFixed(2)}</td>
        <td class="text-right" style="font-weight:700;">${stockVal.toFixed(2)}</td>
      </tr>`;
    }).join('');
  }catch(e){tbody.innerHTML='<tr><td colspan="9"><div class="empty-state"><div class="empty-title">Error loading report</div></div></td></tr>';}
}
loadReport();
</script>
@endsection
