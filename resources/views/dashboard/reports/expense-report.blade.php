@extends('layouts.dashboard')
@section('page_title','Expense Report')
@section('content')
<div class="dash-content animate__animated animate__fadeInUp report-page">

    <div class="report-header-bar" data-aos="fade-down">
        <div>
            <h1>Expense Report</h1>
            <p>Monitor spending by category and payment method over time</p>
        </div>
        <div class="report-actions no-print">
            <button type="button" class="btn btn-primary" onclick="openPdfPreview('Expense Report')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                Preview PDF
            </button>
            <button type="button" class="btn btn-success" onclick="exportTableToCSV('#expenseTable', 'expense-report.csv')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export Excel
            </button>
        </div>
    </div>

    <div class="report-filters no-print" data-aos="fade-up" data-aos-delay="50">
        <div><label>From</label><input type="date" id="fromDate" onchange="loadReport()"></div>
        <div><label>To</label><input type="date" id="toDate" onchange="loadReport()"></div>
        <button class="btn btn-primary" style="height:40px;" onclick="loadReport()">Generate</button>
    </div>

    <div class="report-summary" data-aos="fade-up" data-aos-delay="100">
        <div class="report-summary-card"><div class="rsc-bar red"></div><div class="rsc-label">Total Expenses</div><div class="rsc-value" id="totalExpenses">-</div></div>
        <div class="report-summary-card"><div class="rsc-bar blue"></div><div class="rsc-label">Total Amount</div><div class="rsc-value" id="totalAmount">-</div></div>
    </div>

    <div class="report-table-wrap" data-aos="fade-up" data-aos-delay="150">
        <div class="rtw-head"><div class="rtw-title">Expense Details</div></div>
        <div class="rtw-body tbl-responsive">
            <table class="report-table" id="expenseTable">
                <thead><tr><th>#</th><th>Reference</th><th>Category</th><th>Date</th><th class="text-right">Amount</th><th>Payment</th></tr></thead>
                <tbody id="tableBody"><tr><td colspan="6"><div class="empty-state"><div class="empty-title">Select date range</div></div></td></tr></tbody>
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
  tbody.innerHTML='<tr><td colspan="6"><div class="empty-state"><div class="empty-title">Loading...</div></div></td></tr>';
  try{
    const items=await apiFetch(`/api/dashboard/expenses?from=${from}&to=${to}&per_page=500`);
    document.getElementById('totalExpenses').textContent=items.length.toLocaleString();
    document.getElementById('totalAmount').textContent='TZS ' + items.reduce((a,e)=>a+parseFloat(e.amount||0),0).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2});
    if(!items.length){tbody.innerHTML='<tr><td colspan="6"><div class="empty-state"><svg class="empty-icon" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg><div class="empty-title">No expenses found</div><div class="empty-desc">Adjust the date range to see results.</div></div></td></tr>';return;}
    tbody.innerHTML=items.map((e,i)=>`<tr>
      <td class="text-slate-400">${i+1}</td>
      <td class="font-mono text-xs" style="color:#2563eb;font-weight:600;">${e.reference}</td>
      <td>${e.category?e.category.name:'N/A'}</td>
      <td style="white-space:nowrap;color:#64748b;font-size:0.82rem;">${e.expense_date}</td>
      <td class="text-right" style="font-weight:700;color:#e11d48;">${parseFloat(e.amount).toFixed(2)}</td>
      <td><span class="badge badge-info">${e.payment_method||'-'}</span></td>
    </tr>`).join('');
  }catch(e){tbody.innerHTML='<tr><td colspan="6"><div class="empty-state"><div class="empty-title">Error loading report</div></div></td></tr>';}
}
loadReport();
</script>
@endsection
