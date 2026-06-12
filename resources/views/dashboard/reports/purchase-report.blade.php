@extends('layouts.dashboard')
@section('page_title','Purchase Report')
@section('content')
<div class="dash-content">
<div class="page-card">
  <div class="card-header">
    <div class="card-title">Purchase Report</div>
    <div class="filters-row">
      <input type="date" id="fromDate" class="form-control" style="width:160px;" onchange="loadReport()">
      <input type="date" id="toDate" class="form-control" style="width:160px;" onchange="loadReport()">
      <button class="btn btn-primary" onclick="loadReport()">Generate</button>
    </div>
  </div>
  <div id="summaryCards" style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;padding:1.25rem;border-bottom:1px solid #e9edf5;">
    <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:1rem;">
      <div style="font-size:0.72rem;font-weight:600;color:#2563eb;text-transform:uppercase;">Total Orders</div>
      <div style="font-size:1.6rem;font-weight:700;color:#1d4ed8;" id="totalOrders">-</div>
    </div>
    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:1rem;">
      <div style="font-size:0.72rem;font-weight:600;color:#16a34a;text-transform:uppercase;">Total Cost</div>
      <div style="font-size:1.6rem;font-weight:700;color:#15803d;" id="totalCost">-</div>
    </div>
    <div style="background:#fef3c7;border:1px solid #fde68a;border-radius:10px;padding:1rem;">
      <div style="font-size:0.72rem;font-weight:600;color:#d97706;text-transform:uppercase;">Unpaid</div>
      <div style="font-size:1.6rem;font-weight:700;color:#b45309;" id="totalUnpaid">-</div>
    </div>
  </div>
  <div style="overflow-x:auto;">
    <table class="tbl">
      <thead><tr><th>#</th><th>Reference</th><th>Supplier</th><th>Date</th><th>Total</th><th>Payment</th><th>Status</th></tr></thead>
      <tbody id="tableBody"><tr><td colspan="7" class="tbl-empty">Select date range to generate report.</td></tr></tbody>
    </table>
  </div>
</div>
</div>
@endsection
@section('scripts')
<script>
const today=new Date().toISOString().split('T')[0];
const firstDay=new Date(new Date().getFullYear(),new Date().getMonth(),1).toISOString().split('T')[0];
document.getElementById('fromDate').value=firstDay;
document.getElementById('toDate').value=today;
const payColors={paid:'badge-success',partial:'badge-warning',unpaid:'badge-danger'};
const statusColors={received:'badge-success',pending:'badge-warning',cancelled:'badge-danger'};
async function loadReport(){
  const from=document.getElementById('fromDate').value;const to=document.getElementById('toDate').value;
  const tbody=document.getElementById('tableBody');
  tbody.innerHTML='<tr><td colspan="7" class="tbl-empty">Loading...</td></tr>';
  try{
    const items=await apiFetch(`/api/dashboard/purchases?from=${from}&to=${to}&per_page=500`);
    document.getElementById('totalOrders').textContent=items.length;
    document.getElementById('totalCost').textContent=items.reduce((a,p)=>a+parseFloat(p.total||0),0).toFixed(2);
    const unpaid=items.filter(p=>p.payment_status==='unpaid').reduce((a,p)=>a+parseFloat(p.total||0),0);
    document.getElementById('totalUnpaid').textContent=unpaid.toFixed(2);
    if(!items.length){tbody.innerHTML='<tr><td colspan="7" class="tbl-empty">No purchases in this period.</td></tr>';return;}
    tbody.innerHTML=items.map((p,i)=>`<tr>
      <td class="text-slate-400">${i+1}</td>
      <td class="font-mono text-xs text-blue-600">${p.reference}</td>
      <td>${p.supplier?p.supplier.name:'N/A'}</td>
      <td class="text-slate-500 text-xs">${p.purchase_date}</td>
      <td class="font-semibold">${parseFloat(p.total).toFixed(2)}</td>
      <td><span class="badge ${payColors[p.payment_status]||'badge-gray'}">${p.payment_status}</span></td>
      <td><span class="badge ${statusColors[p.status]||'badge-gray'}">${p.status}</span></td>
    </tr>`).join('');
  }catch(e){tbody.innerHTML='<tr><td colspan="7" class="tbl-empty">Error loading report.</td></tr>';}
}
loadReport();
</script>
@endsection
