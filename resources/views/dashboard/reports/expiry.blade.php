@extends('layouts.dashboard')
@section('page_title','Expiry Date Report')
@section('content')
<div class="dash-content">
<div class="page-card">
  <div class="card-header">
    <div class="card-title">Expiry Date Report</div>
    <div class="filters-row">
      <select id="statusFilter" class="form-control" style="width:150px;" onchange="loadReport()">
        <option value="">All</option>
        <option value="expiring">Expiring Soon</option>
        <option value="expired">Already Expired</option>
      </select>
      <input type="number" id="daysInput" class="form-control" style="width:100px;" value="30" placeholder="Days" onchange="loadReport()">
      <button class="btn btn-primary" onclick="loadReport()">Refresh</button>
    </div>
  </div>
  <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;padding:1.25rem;border-bottom:1px solid #e9edf5;">
    <div style="background:#fff1f2;border:1px solid #fecdd3;border-radius:10px;padding:1rem;"><div style="font-size:0.72rem;font-weight:600;color:#dc2626;text-transform:uppercase;">Expired</div><div style="font-size:1.6rem;font-weight:700;color:#be123c;" id="expiredCount">-</div></div>
    <div style="background:#fef3c7;border:1px solid #fde68a;border-radius:10px;padding:1rem;"><div style="font-size:0.72rem;font-weight:600;color:#d97706;text-transform:uppercase;">Expiring 7 Days</div><div style="font-size:1.6rem;font-weight:700;color:#b45309;" id="expiring7">-</div></div>
    <div style="background:#ffedd5;border:1px solid #fed7aa;border-radius:10px;padding:1rem;"><div style="font-size:0.72rem;font-weight:600;color:#ea580c;text-transform:uppercase;">Expiring 30 Days</div><div style="font-size:1.6rem;font-weight:700;color:#c2410c;" id="expiring30">-</div></div>
    <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:1rem;"><div style="font-size:0.72rem;font-weight:600;color:#2563eb;text-transform:uppercase;">Expiring 90 Days</div><div style="font-size:1.6rem;font-weight:700;color:#1d4ed8;" id="expiring90">-</div></div>
  </div>
  <div style="overflow-x:auto;">
    <table class="tbl">
      <thead><tr><th>#</th><th>Product</th><th>SKU</th><th>Batch #</th><th>Supplier</th><th>Qty</th><th>Unit Cost</th><th>Expiry Date</th><th>Days Left</th><th>Status</th></tr></thead>
      <tbody id="tableBody"><tr><td colspan="10" class="tbl-empty">Loading...</td></tr></tbody>
    </table>
  </div>
</div>
</div>
@endsection
@section('scripts')
<script>
async function loadReport(){
  const params=new URLSearchParams();
  params.append('days',document.getElementById('daysInput').value);
  const st=document.getElementById('statusFilter').value; if(st) params.append('status',st);
  const res=await fetch('/api/dashboard/reports/expiry?'+params,{headers:{'Accept':'application/json'}});
  const d=await res.json();
  document.getElementById('expiredCount').textContent=d.summary?.expired_count||0;
  document.getElementById('expiring7').textContent=d.summary?.expiring_7_days||0;
  document.getElementById('expiring30').textContent=d.summary?.expiring_30_days||0;
  document.getElementById('expiring90').textContent=d.summary?.expiring_90_days||0;
  const tbody=document.getElementById('tableBody');
  if(!d.batches||!d.batches.length){tbody.innerHTML='<tr><td colspan="10" class="tbl-empty">No expiry data found.</td></tr>';return;}
  tbody.innerHTML=d.batches.map((b,i)=>{
    const days=b.expiry_date?Math.ceil((new Date(b.expiry_date)-new Date())/(1000*60*60*24)):null;
    let badge='badge-success'; let label='Active';
    if(days===null){badge='badge-gray';label='No Date';}
    else if(days<0){badge='badge-danger';label='Expired';}
    else if(days<=7){badge='badge-danger';label=days+' days';}
    else if(days<=30){badge='badge-warning';label=days+' days';}
    else{badge='badge-info';label=days+' days';}
    return `<tr>
      <td>${i+1}</td>
      <td><strong>${esc(b.product?.name||'N/A')}</strong></td>
      <td>${esc(b.product?.sku||'-')}</td>
      <td>${esc(b.batch_number||'-')}</td>
      <td>${esc(b.supplier?.name||'-')}</td>
      <td>${b.quantity}</td>
      <td>${fmtMoney(b.unit_cost)}</td>
      <td>${b.expiry_date?new Date(b.expiry_date).toLocaleDateString('en-GB'):'-'}</td>
      <td>${days!==null?days:''}</td>
      <td><span class="badge ${badge}">${label}</span></td>
    </tr>`;
  }).join('');
}
function fmtMoney(n){return '{{ $userCurrency }} '+Number(n).toLocaleString('en-GB',{minimumFractionDigits:2,maximumFractionDigits:2});}
function esc(s){return(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');}
loadReport();
</script>
@endsection
