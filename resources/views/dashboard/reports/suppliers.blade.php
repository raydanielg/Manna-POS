@extends('layouts.dashboard')
@section('page_title','Suppliers Report')
@section('content')
<div class="dash-content">
<div class="page-card">
  <div class="card-header">
    <div class="card-title">Suppliers Report</div>
    <div class="filters-row">
      <input type="date" id="fromDate" class="form-control" style="width:140px;" onchange="loadReport()">
      <input type="date" id="toDate" class="form-control" style="width:140px;" onchange="loadReport()">
      <button class="btn btn-primary" onclick="loadReport()">Refresh</button>
    </div>
  </div>
  <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;padding:1.25rem;border-bottom:1px solid #e9edf5;">
    <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:1rem;"><div style="font-size:0.72rem;font-weight:600;color:#2563eb;text-transform:uppercase;">Total Suppliers</div><div style="font-size:1.6rem;font-weight:700;color:#1d4ed8;" id="totalSuppliers">-</div></div>
    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:1rem;"><div style="font-size:0.72rem;font-weight:600;color:#16a34a;text-transform:uppercase;">Active Suppliers</div><div style="font-size:1.6rem;font-weight:700;color:#15803d;" id="activeSuppliers">-</div></div>
    <div style="background:#fef3c7;border:1px solid #fde68a;border-radius:10px;padding:1rem;"><div style="font-size:0.72rem;font-weight:600;color:#d97706;text-transform:uppercase;">Purchase Value</div><div style="font-size:1.6rem;font-weight:700;color:#b45309;" id="totalPurchaseValue">-</div></div>
    <div style="background:#fff1f2;border:1px solid #fecdd3;border-radius:10px;padding:1rem;"><div style="font-size:0.72rem;font-weight:600;color:#e03057;text-transform:uppercase;">Total Orders</div><div style="font-size:1.6rem;font-weight:700;color:#be123c;" id="totalOrders">-</div></div>
  </div>
  <div style="overflow-x:auto;">
    <table class="tbl">
      <thead><tr><th>#</th><th>Supplier</th><th>Company</th><th>Total Purchases</th><th>Total Amount</th><th>Balance</th><th>Credit Limit</th><th>Status</th></tr></thead>
      <tbody id="tableBody"><tr><td colspan="8" class="tbl-empty">Loading...</td></tr></tbody>
    </table>
  </div>
</div>
</div>
@endsection
@section('scripts')
<script>
document.getElementById('fromDate').value=new Date(new Date().setMonth(new Date().getMonth()-3)).toISOString().split('T')[0];
document.getElementById('toDate').value=new Date().toISOString().split('T')[0];
async function loadReport(){
  const params=new URLSearchParams();
  params.append('from',document.getElementById('fromDate').value);
  params.append('to',document.getElementById('toDate').value);
  const res=await fetch('/api/dashboard/reports/suppliers?'+params,{headers:{'Accept':'application/json'}});
  const d=await res.json();
  document.getElementById('totalSuppliers').textContent=d.total_suppliers||0;
  document.getElementById('activeSuppliers').textContent=d.active_suppliers||0;
  document.getElementById('totalPurchaseValue').textContent=fmtMoney(d.total_purchase_value||0);
  document.getElementById('totalOrders').textContent=d.suppliers?.reduce((a,s)=>a+(s.total_purchases||0),0)||0;
  const tbody=document.getElementById('tableBody');
  if(!d.suppliers||!d.suppliers.length){tbody.innerHTML='<tr><td colspan="8" class="tbl-empty">No suppliers found.</td></tr>';return;}
  tbody.innerHTML=d.suppliers.map((s,i)=>`<tr>
    <td>${i+1}</td>
    <td><strong>${esc(s.name)}</strong></td>
    <td>${esc(s.company||'-')}</td>
    <td>${s.total_purchases}</td>
    <td>${fmtMoney(s.total_amount)}</td>
    <td>${fmtMoney(s.balance)}</td>
    <td>${fmtMoney(s.credit_limit)}</td>
    <td><span class="badge ${s.status==='active'?'badge-success':'badge-gray'}">${s.status}</span></td>
  </tr>`).join('');
}
function fmtMoney(n){return '{{ $userCurrency }} '+Number(n).toLocaleString('en-GB',{minimumFractionDigits:2,maximumFractionDigits:2});}
function esc(s){return(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');}
loadReport();
</script>
@endsection
