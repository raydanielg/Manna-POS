@extends('layouts.dashboard')
@section('page_title','Expense Report')
@section('content')
<div class="dash-content">
<div class="page-card">
  <div class="card-header">
    <div class="card-title">Expense Report</div>
    <div class="filters-row">
      <input type="date" id="fromDate" class="form-control" style="width:160px;" onchange="loadReport()">
      <input type="date" id="toDate" class="form-control" style="width:160px;" onchange="loadReport()">
      <button class="btn btn-primary" onclick="loadReport()">Generate</button>
    </div>
  </div>
  <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:1rem;padding:1.25rem;border-bottom:1px solid #e9edf5;">
    <div style="background:#fff1f2;border:1px solid #fecdd3;border-radius:10px;padding:1rem;">
      <div style="font-size:0.72rem;font-weight:600;color:#e03057;text-transform:uppercase;">Total Expenses</div>
      <div style="font-size:1.6rem;font-weight:700;color:#be123c;" id="totalExpenses">-</div>
    </div>
    <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:1rem;">
      <div style="font-size:0.72rem;font-weight:600;color:#2563eb;text-transform:uppercase;">Total Amount</div>
      <div style="font-size:1.6rem;font-weight:700;color:#1d4ed8;" id="totalAmount">-</div>
    </div>
  </div>
  <div style="overflow-x:auto;">
    <table class="tbl">
      <thead><tr><th>#</th><th>Reference</th><th>Category</th><th>Date</th><th>Amount</th><th>Payment</th></tr></thead>
      <tbody id="tableBody"><tr><td colspan="6" class="tbl-empty">Select date range to generate report.</td></tr></tbody>
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
async function loadReport(){
  const from=document.getElementById('fromDate').value;const to=document.getElementById('toDate').value;
  const tbody=document.getElementById('tableBody');
  tbody.innerHTML='<tr><td colspan="6" class="tbl-empty">Loading...</td></tr>';
  try{
    const items=await apiFetch(`/api/dashboard/expenses?from=${from}&to=${to}&per_page=500`);
    document.getElementById('totalExpenses').textContent=items.length;
    document.getElementById('totalAmount').textContent=items.reduce((a,e)=>a+parseFloat(e.amount||0),0).toFixed(2);
    if(!items.length){tbody.innerHTML='<tr><td colspan="6" class="tbl-empty">No expenses in this period.</td></tr>';return;}
    tbody.innerHTML=items.map((e,i)=>`<tr>
      <td class="text-slate-400">${i+1}</td>
      <td class="font-mono text-xs text-blue-600">${e.reference}</td>
      <td>${e.category?e.category.name:'N/A'}</td>
      <td class="text-slate-500 text-xs">${e.expense_date}</td>
      <td class="font-semibold text-red-600">${parseFloat(e.amount).toFixed(2)}</td>
      <td><span class="badge badge-info">${e.payment_method||'-'}</span></td>
    </tr>`).join('');
  }catch(e){tbody.innerHTML='<tr><td colspan="6" class="tbl-empty">Error loading report.</td></tr>';}
}
loadReport();
</script>
@endsection
