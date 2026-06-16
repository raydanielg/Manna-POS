@extends('layouts.dashboard')
@section('page_title','POS Transactions')
@section('content')
<div class="dash-content">
<div class="page-card">
  <div class="card-header">
    <div class="card-title">POS Transactions</div>
    <div class="filters-row">
      <div class="search-wrap">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
        <input type="text" id="searchInput" placeholder="Search reference..." oninput="loadList()">
      </div>
      <input type="date" id="fromDate" class="form-control" style="width:160px;" onchange="loadList()">
      <input type="date" id="toDate" class="form-control" style="width:160px;" onchange="loadList()">
      <a href="/dashboard/sell/pos" class="btn btn-success">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
        Open POS
      </a>
    </div>
  </div>
  <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;padding:1.25rem;border-bottom:1px solid #e9edf5;">
    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:1rem;">
      <div style="font-size:0.72rem;font-weight:600;color:#16a34a;text-transform:uppercase;">Total Transactions</div>
      <div style="font-size:1.6rem;font-weight:700;color:#15803d;" id="totalTx">-</div>
    </div>
    <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:1rem;">
      <div style="font-size:0.72rem;font-weight:600;color:#2563eb;text-transform:uppercase;">Total Revenue</div>
      <div style="font-size:1.6rem;font-weight:700;color:#1d4ed8;" id="totalRev">-</div>
    </div>
    <div style="background:#fef3c7;border:1px solid #fde68a;border-radius:10px;padding:1rem;">
      <div style="font-size:0.72rem;font-weight:600;color:#d97706;text-transform:uppercase;">Total Paid</div>
      <div style="font-size:1.6rem;font-weight:700;color:#b45309;" id="totalPd">-</div>
    </div>
  </div>
  <div style="overflow-x:auto;">
    <table class="tbl">
      <thead><tr><th>#</th><th>Reference</th><th>Customer</th><th>Date</th><th>Total</th><th>Paid</th><th>Method</th><th>Pay Status</th></tr></thead>
      <tbody id="tableBody"><tr><td colspan="8" class="tbl-empty">Loading...</td></tr></tbody>
    </table>
  </div>
</div>
</div>
@endsection
@section('scripts')
<script>
const API='/api/dashboard/sales';
const today=new Date().toISOString().split('T')[0];
const firstDay=new Date(new Date().getFullYear(),new Date().getMonth(),1).toISOString().split('T')[0];
document.getElementById('fromDate').value=firstDay;
document.getElementById('toDate').value=today;
const payColors={paid:'badge-success',partial:'badge-warning',unpaid:'badge-danger'};
async function loadList(){
  const s=document.getElementById('searchInput').value;
  const from=document.getElementById('fromDate').value;
  const to=document.getElementById('toDate').value;
  const tbody=document.getElementById('tableBody');
  tbody.innerHTML='<tr><td colspan="8" class="tbl-empty">Loading...</td></tr>';
  try{
    const items=await apiFetch(`${API}?status=completed&search=${encodeURIComponent(s)}&from=${from}&to=${to}`);
    document.getElementById('totalTx').textContent=items.length;
    document.getElementById('totalRev').textContent=items.reduce((a,x)=>a+parseFloat(x.total||0),0).toFixed(2);
    document.getElementById('totalPd').textContent=items.reduce((a,x)=>a+parseFloat(x.paid||0),0).toFixed(2);
    if(!items.length){tbody.innerHTML='<tr><td colspan="8" class="tbl-empty">No transactions found.</td></tr>';return;}
    tbody.innerHTML=items.map((s,i)=>`<tr>
      <td class="text-slate-400">${i+1}</td>
      <td class="font-mono text-xs text-blue-600">${s.reference}</td>
      <td>${s.customer?s.customer.name:'Walk-in'}</td>
      <td class="text-slate-500 text-xs">${s.sale_date}</td>
      <td class="font-semibold">${parseFloat(s.total).toFixed(2)}</td>
      <td class="text-green-600">${parseFloat(s.paid||0).toFixed(2)}</td>
      <td><span class="badge badge-info">${s.payment_method||'-'}</span></td>
      <td><span class="badge ${payColors[s.payment_status]||'badge-gray'}">${s.payment_status}</span></td>
    </tr>`).join('');
  }catch(e){tbody.innerHTML='<tr><td colspan="8" class="tbl-empty">Error loading data.</td></tr>';}
}
loadList();
</script>
@endsection
