@extends('layouts.dashboard')
@section('page_title','Sales Report')
@section('content')
<div class="dash-content">
<div class="page-card">
  <div class="card-header">
    <div class="card-title">Sales Report</div>
    <div class="filters-row">
      <input type="date" id="fromDate" class="form-control" style="width:160px;" onchange="loadReport()">
      <input type="date" id="toDate" class="form-control" style="width:160px;" onchange="loadReport()">
      <button class="btn btn-primary" onclick="loadReport()">Generate</button>
    </div>
  </div>
  <div id="summaryCards" style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;padding:1.25rem;border-bottom:1px solid #e9edf5;">
    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:1rem;">
      <div style="font-size:0.72rem;font-weight:600;color:#16a34a;text-transform:uppercase;">Total Sales</div>
      <div style="font-size:1.6rem;font-weight:700;color:#15803d;" id="totalSales">-</div>
    </div>
    <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:1rem;">
      <div style="font-size:0.72rem;font-weight:600;color:#2563eb;text-transform:uppercase;">Total Revenue</div>
      <div style="font-size:1.6rem;font-weight:700;color:#1d4ed8;" id="totalRevenue">-</div>
    </div>
    <div style="background:#fef3c7;border:1px solid #fde68a;border-radius:10px;padding:1rem;">
      <div style="font-size:0.72rem;font-weight:600;color:#d97706;text-transform:uppercase;">Total Paid</div>
      <div style="font-size:1.6rem;font-weight:700;color:#b45309;" id="totalPaid">-</div>
    </div>
    <div style="background:#fff1f2;border:1px solid #fecdd3;border-radius:10px;padding:1rem;">
      <div style="font-size:0.72rem;font-weight:600;color:#e03057;text-transform:uppercase;">Outstanding</div>
      <div style="font-size:1.6rem;font-weight:700;color:#be123c;" id="totalOutstanding">-</div>
    </div>
  </div>
  <div style="overflow-x:auto;">
    <table class="tbl">
      <thead><tr><th>#</th><th>Invoice</th><th>Customer</th><th>Date</th><th>Total</th><th>Paid</th><th>Method</th><th>Status</th></tr></thead>
      <tbody id="tableBody"><tr><td colspan="8" class="tbl-empty">Select date range to generate report.</td></tr></tbody>
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
const statusColors={completed:'badge-success',draft:'badge-warning',quotation:'badge-info',cancelled:'badge-danger'};
async function loadReport(){
  const from=document.getElementById('fromDate').value;const to=document.getElementById('toDate').value;
  const tbody=document.getElementById('tableBody');
  tbody.innerHTML='<tr><td colspan="8" class="tbl-empty">Loading...</td></tr>';
  try{
    const items=await apiFetch(`/api/dashboard/sales?from=${from}&to=${to}&per_page=500`);
    const total=items.length;
    const revenue=items.reduce((a,s)=>a+parseFloat(s.total||0),0);
    const paid=items.reduce((a,s)=>a+parseFloat(s.paid||0),0);
    document.getElementById('totalSales').textContent=total;
    document.getElementById('totalRevenue').textContent=revenue.toFixed(2);
    document.getElementById('totalPaid').textContent=paid.toFixed(2);
    document.getElementById('totalOutstanding').textContent=(revenue-paid).toFixed(2);
    if(!items.length){tbody.innerHTML='<tr><td colspan="8" class="tbl-empty">No sales in this period.</td></tr>';return;}
    tbody.innerHTML=items.map((s,i)=>`<tr>
      <td class="text-slate-400">${i+1}</td>
      <td class="font-mono text-xs text-blue-600">${s.reference}</td>
      <td>${s.customer?s.customer.name:'Walk-in'}</td>
      <td class="text-slate-500 text-xs">${s.sale_date}</td>
      <td class="font-semibold">${parseFloat(s.total).toFixed(2)}</td>
      <td class="text-green-600">${parseFloat(s.paid||0).toFixed(2)}</td>
      <td><span class="badge badge-info">${s.payment_method}</span></td>
      <td><span class="badge ${statusColors[s.status]||'badge-gray'}">${s.status}</span></td>
    </tr>`).join('');
  }catch(e){tbody.innerHTML='<tr><td colspan="8" class="tbl-empty">Error loading report.</td></tr>';}
}
loadReport();
</script>
@endsection
