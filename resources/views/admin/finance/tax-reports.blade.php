@extends('admin.layouts.app')
@section('page_title', 'Tax Reports')
@section('content')
<div class="page-card">
    <div class="card-header">
        <div class="card-title">Tax Reports</div>
        <div class="filters-row">
            <input type="date" class="form-control" style="width:auto;padding:0.45rem 0.75rem;font-size:0.78rem;" id="dateFrom">
            <input type="date" class="form-control" style="width:auto;padding:0.45rem 0.75rem;font-size:0.78rem;" id="dateTo">
            <button class="btn btn-primary btn-xs" onclick="loadList()">Filter</button>
            <button class="btn btn-secondary btn-xs" onclick="exportReport()">Export</button>
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead><tr><th>Tax Name</th><th>Rate</th><th>Collected Amount</th><th>Period</th><th>Actions</th></tr></thead>
            <tbody id="tableBody"><tr><td colspan="5" class="tbl-empty">Loading...</td></tr></tbody>
        </table>
    </div>
</div>
@endsection
@section('scripts')
const API = '/api/admin/finance/tax-reports';

async function loadList() {
    const from = document.getElementById('dateFrom').value;
    const to = document.getElementById('dateTo').value;
    const params = new URLSearchParams({ from, to });
    const data = await apiFetch(`${API}?${params}`);
    const tbody = document.getElementById('tableBody');
    if (!data.length) { tbody.innerHTML = '<tr><td colspan="5" class="tbl-empty">No tax reports found</td></tr>'; return; }
    tbody.innerHTML = data.map(t => `<tr>
        <td><strong>${t.name || t.tax_name || '-'}</strong></td>
        <td>${t.rate || 0}%</td>
        <td>${(t.collected_amount || t.collected || 0).toLocaleString()}</td>
        <td>${t.period || '-'}</td>
        <td class="actions-cell">
            <button class="btn btn-primary btn-xs" onclick="viewTax(${t.id})">View</button>
        </td>
    </tr>`).join('');
}

function viewTax(id) {
    Swal.fire({ icon: 'info', title: 'Tax Detail', text: 'Tax report #' + id, confirmButtonText: 'OK' });
}

function exportReport() {
    Swal.fire({ icon: 'info', title: 'Coming Soon', text: 'Export feature is coming soon!', confirmButtonText: 'OK' });
}

loadList();
@endsection
