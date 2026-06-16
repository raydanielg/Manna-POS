@extends('admin.layouts.app')
@section('page_title', 'Commission Reports')
@section('content')
<div class="page-card">
    <div class="card-header">
        <div class="card-title">Commission Reports</div>
        <div class="filters-row">
            <input type="text" class="form-control" style="width:auto;padding:0.45rem 0.75rem;font-size:0.78rem;" id="agentFilter" placeholder="Agent name...">
            <input type="date" class="form-control" style="width:auto;padding:0.45rem 0.75rem;font-size:0.78rem;" id="dateFrom">
            <input type="date" class="form-control" style="width:auto;padding:0.45rem 0.75rem;font-size:0.78rem;" id="dateTo">
            <button class="btn btn-primary btn-xs" onclick="loadList()">Filter</button>
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead><tr><th>Sales Agent</th><th>Total Sales</th><th>Commission Rate</th><th>Commission Earned</th><th>Period</th><th>Actions</th></tr></thead>
            <tbody id="tableBody"><tr><td colspan="6" class="tbl-empty">Loading...</td></tr></tbody>
        </table>
    </div>
</div>
@endsection
@section('scripts')
<script>
const API = '/api/admin/finance/commissions';

async function loadList() {
    const agent = document.getElementById('agentFilter').value;
    const from = document.getElementById('dateFrom').value;
    const to = document.getElementById('dateTo').value;
    const params = new URLSearchParams({ agent, from, to });
    const data = await apiFetch(`${API}?${params}`);
    const tbody = document.getElementById('tableBody');
    if (!data.length) { tbody.innerHTML = '<tr><td colspan="6" class="tbl-empty">No commission records found</td></tr>'; return; }
    tbody.innerHTML = data.map(c => `<tr>
        <td><strong>${c.agent || c.sales_agent || c.name || '-'}</strong></td>
        <td>${(c.total_sales || c.sales || 0).toLocaleString()}</td>
        <td>${c.commission_rate || c.rate || 0}%</td>
        <td>${(c.commission_earned || c.earned || 0).toLocaleString()}</td>
        <td>${c.period || '-'}</td>
        <td class="actions-cell">
            <button class="btn btn-primary btn-xs" onclick="viewCommission(${c.id})">View</button>
        </td>
    </tr>`).join('');
}

function viewCommission(id) {
    Swal.fire({ icon: 'info', title: 'Commission Detail', text: 'Commission record #' + id, confirmButtonText: 'OK' });
}

loadList();
</script>
@endsection
