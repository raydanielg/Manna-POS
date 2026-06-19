@extends('layouts.dashboard')
@section('page_title','Supplier Price Comparison')
@section('content')
<div class="dash-content">
<div class="page-card">
  <div class="card-header">
    <div class="card-title">Supplier Price Comparison</div>
    <div class="filters-row">
      <select id="productFilter" class="form-control" style="width:220px;" onchange="loadReport()"><option value="">All Products</option></select>
      <input type="date" id="fromDate" class="form-control" style="width:140px;" onchange="loadReport()">
      <input type="date" id="toDate" class="form-control" style="width:140px;" onchange="loadReport()">
      <button class="btn btn-primary" onclick="loadReport()">Refresh</button>
    </div>
  </div>
  <div style="overflow-x:auto;">
    <table class="tbl">
      <thead><tr><th>Product</th><th>SKU</th><th>Supplier</th><th>Avg Cost</th><th>Lowest</th><th>Highest</th><th>Qty Purchased</th><th>Last Purchase</th></tr></thead>
      <tbody id="tableBody"><tr><td colspan="8" class="tbl-empty">Loading...</td></tr></tbody>
    </table>
  </div>
</div>
</div>
@endsection
@section('scripts')
<script>
document.getElementById('fromDate').value=new Date(new Date().setMonth(new Date().getMonth()-6)).toISOString().split('T')[0];
document.getElementById('toDate').value=new Date().toISOString().split('T')[0];
async function loadProducts(){
  const res=await fetch('/api/dashboard/products',{headers:{'Accept':'application/json'}});
  const data=await res.json();
  const sel=document.getElementById('productFilter');
  data.forEach(p=>{const o=document.createElement('option');o.value=p.id;o.textContent=esc(p.name);sel.appendChild(o);});
}
async function loadReport(){
  const params=new URLSearchParams();
  params.append('from',document.getElementById('fromDate').value);
  params.append('to',document.getElementById('toDate').value);
  const pid=document.getElementById('productFilter').value; if(pid) params.append('product_id',pid);
  const res=await fetch('/api/dashboard/reports/supplier-price-comparison?'+params,{headers:{'Accept':'application/json'}});
  const d=await res.json();
  const tbody=document.getElementById('tableBody');
  if(!d.comparison||!d.comparison.length){tbody.innerHTML='<tr><td colspan="8" class="tbl-empty">No comparison data found.</td></tr>';return;}
  let rows='';
  d.comparison.forEach(p=>{
    p.suppliers.forEach((s,i)=>{
      rows+=`<tr>
        ${i===0?`<td rowspan="${p.suppliers.length}"><strong>${esc(p.product_name)}</strong></td><td rowspan="${p.suppliers.length}">${esc(p.sku||'-')}</td>`:''}
        <td>${esc(s.supplier_name)}</td>
        <td>${fmtMoney(s.avg_unit_cost)}</td>
        <td style="color:#16a34a;font-weight:600;">${fmtMoney(s.lowest_cost)}</td>
        <td style="color:#dc2626;font-weight:600;">${fmtMoney(s.highest_cost)}</td>
        <td>${s.total_qty_purchased}</td>
        <td>${fmtDate(s.last_purchase_date)}</td>
      </tr>`;
    });
  });
  tbody.innerHTML=rows;
}
function fmtMoney(n){return '{{ $userCurrency }} '+Number(n).toLocaleString('en-GB',{minimumFractionDigits:2,maximumFractionDigits:2});}
function fmtDate(d){return d?new Date(d).toLocaleDateString('en-GB',{day:'2-digit',month:'short',year:'numeric'}):'-';}
function esc(s){return(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');}
loadProducts().then(loadReport);
</script>
@endsection
