@extends('admin.layouts.app')
@section('page_title', 'Sales Report')
@section('content')
<div class="page-card">
    <div class="card-header">
        <div class="card-title">Sales Report</div>
        <div class="filters-row">
            <input type="date" id="fromDate" class="form-control" style="width:160px;padding:0.4rem 0.6rem;font-size:0.78rem;" onchange="loadReport()">
            <input type="date" id="toDate" class="form-control" style="width:160px;padding:0.4rem 0.6rem;font-size:0.78rem;" onchange="loadReport()">
            <button class="btn btn-primary" onclick="loadReport()">Generate</button>
        </div>
    </div>
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;padding:1.25rem;border-bottom:1px solid #e9edf5;" id="summaryCards">
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:1rem;">
            <div style="font-size:0.72rem;font-weight:600;color:#16a34a;text-transform:uppercase;">Total Sales</div>
            <div style="font-size:1.5rem;font-weight:700;color:#15803d;" id="totalSales">-</div>
        </div>
        <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:1rem;">
            <div style="font-size:0.72rem;font-weight:600;color:#2563eb;text-transform:uppercase;">Total Revenue</div>
            <div style="font-size:1.5rem;font-weight:700;color:#1d4ed8;" id="totalRevenue">-</div>
        </div>
        <div style="background:#fef3c7;border:1px solid #fde68a;border-radius:10px;padding:1rem;">
            <div style="font-size:0.72rem;font-weight:600;color:#d97706;text-transform:uppercase;">Total Paid</div>
            <div style="font-size:1.5rem;font-weight:700;color:#b45309;" id="totalPaid">-</div>
        </div>
        <div style="background:#fff1f2;border:1px solid #fecdd3;border-radius:10px;padding:1rem;">
            <div style="font-size:0.72rem;font-weight:600;color:#e03057;text-transform:uppercase;">Outstanding</div>
            <div style="font-size:1.5rem;font-weight:700;color:#be123c;" id="totalOutstanding">-</div>
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead><tr><th>Invoice</th><th>Customer</th><th>Date</th><th>Total</th><th>Paid</th><th>Method</th><th>Status</th></tr></thead>
            <tbody id="tableBody"><tr><td colspan="7" class="tbl-empty">Select date range to generate report.</td></tr></tbody>
        </table>
    </div>
</div>
@endsection
@section('scripts')
const today = new Date().toISOString().split('T')[0];
const firstDay = new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0];
document.getElementById('fromDate').value = firstDay;
document.getElementById('toDate').value = today;

async function loadReport() {
    const from = document.getElementById('fromDate').value;
    const to = document.getElementById('toDate').value;
    const data = await apiFetch(`/api/admin/reports/sales?from=${from}&to=${to}&per_page=500`);
    const items = Array.isArray(data) ? data : (data.data || []);
    let totalSales = 0, totalRevenue = 0, totalPaid = 0, totalOutstanding = 0;
    items.forEach(s => {
        totalSales++;
        totalRevenue += Number(s.total || 0);
        totalPaid += Number(s.paid || 0);
        totalOutstanding += Number(s.total || 0) - Number(s.paid || 0);
    });
    document.getElementById('totalSales').textContent = totalSales;
    document.getElementById('totalRevenue').textContent = totalRevenue.toLocaleString('en-US', {style:'currency',currency:'TZS'});
    document.getElementById('totalPaid').textContent = totalPaid.toLocaleString('en-US', {style:'currency',currency:'TZS'});
    document.getElementById('totalOutstanding').textContent = totalOutstanding.toLocaleString('en-US', {style:'currency',currency:'TZS'});
    const tbody = document.getElementById('tableBody');
    if (!items.length) { tbody.innerHTML = '<tr><td colspan="7" class="tbl-empty">No sales found for this period.</td></tr>'; return; }
    tbody.innerHTML = items.map(s => `<tr>
        <td><strong>${s.reference || '#'+s.id}</strong></td>
        <td>${s.customer?.name || '-'}</td>
        <td>${s.sale_date ? new Date(s.sale_date).toLocaleDateString() : (s.created_at ? new Date(s.created_at).toLocaleDateString() : '-')}</td>
        <td>${Number(s.total || 0).toLocaleString()}</td>
        <td>${Number(s.paid || 0).toLocaleString()}</td>
        <td>${s.payment_method || '-'}</td>
        <td><span class="badge ${s.status === 'completed' ? 'badge-success' : s.status === 'draft' ? 'badge-warning' : 'badge-default'}">${s.status || 'N/A'}</span></td>
    </tr>`).join('');
}
loadReport();
@endsection
