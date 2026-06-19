@extends('layouts.dashboard')
@section('page_title','Product Trends')
@section('content')
<div class="dash-content">
  <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1.5rem;">
    <div style="background:#fff;border:1px solid #e9edf5;border-radius:14px;padding:1.25rem;">
      <div style="font-size:0.72rem;font-weight:600;color:#2563eb;text-transform:uppercase;">Products Sold</div>
      <div style="font-size:1.8rem;font-weight:700;color:#1d4ed8;" id="totalProducts">-</div>
    </div>
    <div style="background:#fff;border:1px solid #e9edf5;border-radius:14px;padding:1.25rem;">
      <div style="font-size:0.72rem;font-weight:600;color:#16a34a;text-transform:uppercase;">Total Revenue</div>
      <div style="font-size:1.8rem;font-weight:700;color:#15803d;" id="totalRevenue">-</div>
    </div>
    <div style="background:#fff;border:1px solid #e9edf5;border-radius:14px;padding:1.25rem;">
      <div style="font-size:0.72rem;font-weight:600;color:#d97706;text-transform:uppercase;">Total Qty Sold</div>
      <div style="font-size:1.8rem;font-weight:700;color:#b45309;" id="totalQty">-</div>
    </div>
  </div>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">
    <div class="page-card">
      <div class="card-header"><div class="card-title">Fast Moving Products (Top 20)</div></div>
      <div style="overflow-x:auto;max-height:500px;overflow-y:auto;">
        <table class="tbl">
          <thead><tr><th>#</th><th>Product</th><th>Qty Sold</th><th>Revenue</th><th>Sale Count</th><th>Margin/Unit</th></tr></thead>
          <tbody id="fastBody"><tr><td colspan="6" class="tbl-empty">Loading...</td></tr></tbody>
        </table>
      </div>
    </div>
    <div class="page-card">
      <div class="card-header"><div class="card-title">Slow Moving Products (Bottom 20)</div></div>
      <div style="overflow-x:auto;max-height:500px;overflow-y:auto;">
        <table class="tbl">
          <thead><tr><th>#</th><th>Product</th><th>Qty Sold</th><th>Revenue</th><th>Sale Count</th><th>Margin/Unit</th></tr></thead>
          <tbody id="slowBody"><tr><td colspan="6" class="tbl-empty">Loading...</td></tr></tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
async function loadReport(){
  const from=new Date(new Date().setMonth(new Date().getMonth()-3)).toISOString().split('T')[0];
  const to=new Date().toISOString().split('T')[0];
  const res=await fetch('/api/dashboard/reports/product-trends?from='+from+'&to='+to+'&limit=20',{headers:{'Accept':'application/json'}});
  const d=await res.json();
  document.getElementById('totalProducts').textContent=d.total_products_sold||0;
  document.getElementById('totalRevenue').textContent=fmtMoney(d.total_revenue||0);
  document.getElementById('totalQty').textContent=Number(d.total_quantity_sold||0).toLocaleString('en-GB',{maximumFractionDigits:0});

  const fast=document.getElementById('fastBody');
  if(!d.fast_moving||!d.fast_moving.length){fast.innerHTML='<tr><td colspan="6" class="tbl-empty">No data available.</td></tr>';}
  else{fast.innerHTML=d.fast_moving.map((p,i)=>`<tr>
    <td>${i+1}</td>
    <td><strong>${esc(p.product_name)}</strong></td>
    <td>${Number(p.total_qty_sold).toLocaleString('en-GB',{maximumFractionDigits:2})}</td>
    <td>${fmtMoney(p.total_revenue)}</td>
    <td>${p.sale_count}</td>
    <td style="color:${(p.margin_per_unit||0)>=0?'#16a34a':'#dc2626'};font-weight:600;">${fmtMoney(p.margin_per_unit)}</td>
  </tr>`).join('');}

  const slow=document.getElementById('slowBody');
  if(!d.slow_moving||!d.slow_moving.length){slow.innerHTML='<tr><td colspan="6" class="tbl-empty">No data available.</td></tr>';}
  else{slow.innerHTML=d.slow_moving.map((p,i)=>`<tr>
    <td>${i+1}</td>
    <td><strong>${esc(p.product_name)}</strong></td>
    <td>${Number(p.total_qty_sold).toLocaleString('en-GB',{maximumFractionDigits:2})}</td>
    <td>${fmtMoney(p.total_revenue)}</td>
    <td>${p.sale_count}</td>
    <td style="color:${(p.margin_per_unit||0)>=0?'#16a34a':'#dc2626'};font-weight:600;">${fmtMoney(p.margin_per_unit)}</td>
  </tr>`).join('');}
}
function fmtMoney(n){return '{{ $userCurrency }} '+Number(n).toLocaleString('en-GB',{minimumFractionDigits:2,maximumFractionDigits:2});}
function esc(s){return(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');}
loadReport();
</script>
@endsection
