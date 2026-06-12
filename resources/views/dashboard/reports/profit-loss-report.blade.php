@extends('layouts.dashboard')
@section('page_title','Profit & Loss Report')
@section('content')
<div class="dash-content">
<div class="page-card">
  <div class="card-header">
    <div class="card-title">Profit & Loss Report</div>
    <div class="filters-row">
      <input type="date" id="fromDate" class="form-control" style="width:160px;" onchange="loadReport()">
      <input type="date" id="toDate" class="form-control" style="width:160px;" onchange="loadReport()">
      <button class="btn btn-primary" onclick="loadReport()">Generate</button>
    </div>
  </div>
  <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;padding:1.25rem;border-bottom:1px solid #e9edf5;">
    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:1rem;">
      <div style="font-size:0.72rem;font-weight:600;color:#16a34a;text-transform:uppercase;">Revenue</div>
      <div style="font-size:1.6rem;font-weight:700;color:#15803d;" id="totalRevenue">-</div>
    </div>
    <div style="background:#fff1f2;border:1px solid #fecdd3;border-radius:10px;padding:1rem;">
      <div style="font-size:0.72rem;font-weight:600;color:#e03057;text-transform:uppercase;">Purchase Cost</div>
      <div style="font-size:1.6rem;font-weight:700;color:#be123c;" id="totalCost">-</div>
    </div>
    <div style="background:#fef3c7;border:1px solid #fde68a;border-radius:10px;padding:1rem;">
      <div style="font-size:0.72rem;font-weight:600;color:#d97706;text-transform:uppercase;">Expenses</div>
      <div style="font-size:1.6rem;font-weight:700;color:#b45309;" id="totalExpenses">-</div>
    </div>
    <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:1rem;">
      <div style="font-size:0.72rem;font-weight:600;color:#2563eb;text-transform:uppercase;">Net Profit</div>
      <div style="font-size:1.6rem;font-weight:700;" id="netProfit" style="color:#1d4ed8;">-</div>
    </div>
  </div>
  <div style="padding:1.5rem;">
    <h3 style="font-size:0.9rem;font-weight:700;color:#1e293b;margin-bottom:1rem;">Summary Breakdown</h3>
    <div id="breakdown" style="color:#64748b;font-size:0.9rem;">Select date range to generate report.</div>
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
  document.getElementById('breakdown').textContent='Loading...';
  try{
    const [sales,purchases,expenses]=await Promise.all([
      apiFetch(`/api/dashboard/sales?from=${from}&to=${to}&per_page=500`),
      apiFetch(`/api/dashboard/purchases?from=${from}&to=${to}&per_page=500`),
      apiFetch(`/api/dashboard/expenses?from=${from}&to=${to}&per_page=500`)
    ]);
    const revenue=sales.filter(s=>s.status==='completed').reduce((a,s)=>a+parseFloat(s.total||0),0);
    const cost=purchases.filter(p=>p.status==='received').reduce((a,p)=>a+parseFloat(p.total||0),0);
    const exp=expenses.reduce((a,e)=>a+parseFloat(e.amount||0),0);
    const profit=revenue-cost-exp;
    document.getElementById('totalRevenue').textContent=revenue.toFixed(2);
    document.getElementById('totalCost').textContent=cost.toFixed(2);
    document.getElementById('totalExpenses').textContent=exp.toFixed(2);
    const netEl=document.getElementById('netProfit');
    netEl.textContent=profit.toFixed(2);
    netEl.style.color=profit>=0?'#15803d':'#be123c';
    document.getElementById('breakdown').innerHTML=`
      <table class="tbl">
        <tbody>
          <tr><td class="font-semibold" style="color:#15803d;">Sales Revenue (Completed)</td><td style="text-align:right;font-weight:700;color:#15803d;">${revenue.toFixed(2)}</td></tr>
          <tr><td style="color:#be123c;">Less: Purchase Cost (Received)</td><td style="text-align:right;color:#be123c;">- ${cost.toFixed(2)}</td></tr>
          <tr><td style="color:#be123c;">Less: Operating Expenses</td><td style="text-align:right;color:#be123c;">- ${exp.toFixed(2)}</td></tr>
          <tr style="border-top:2px solid #e2e8f0;"><td class="font-semibold" style="font-size:1rem;">Net Profit / Loss</td><td style="text-align:right;font-weight:700;font-size:1rem;color:${profit>=0?'#15803d':'#be123c'};">${profit.toFixed(2)}</td></tr>
        </tbody>
      </table>`;
  }catch(e){document.getElementById('breakdown').textContent='Error loading report.';}
}
loadReport();
</script>
@endsection
