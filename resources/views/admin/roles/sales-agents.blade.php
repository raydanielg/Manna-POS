@extends('admin.layouts.app')
@section('page_title', 'Sales Commission Agents')
@section('content')
<div class="page-card">
    <div class="card-header">
        <div class="card-title">Sales Commission Agents</div>
        <div class="filters-row">
            <div class="search-wrap">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                <input type="text" id="searchInput" placeholder="Search agents..." oninput="loadList()">
            </div>
            <button class="btn btn-success" onclick="openAddModal()">+ Add Agent</button>
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead><tr><th>Name</th><th>Email</th><th>Commission Rate</th><th>Total Sales</th><th>Earnings</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody id="tableBody"><tr><td colspan="7" class="tbl-empty">Loading...</td></tr></tbody>
        </table>
    </div>
</div>
@endsection
@section('scripts')
<script>
const API = '/api/admin/sales-agents';
async function loadList() {
    try {
        const data = await apiFetch(API);
        const tbody = document.getElementById('tableBody');
        if (!data.length) { tbody.innerHTML = '<tr><td colspan="7" class="tbl-empty">No agents found</td></tr>'; return; }
        tbody.innerHTML = data.map(a => `<tr>
            <td><strong>${a.name}</strong></td>
            <td>${a.email}</td>
            <td>${a.commission_rate || 0}%</td>
            <td>$${(a.total_sales || 0).toLocaleString()}</td>
            <td>$${(a.earnings || 0).toLocaleString()}</td>
            <td><span class="badge ${a.status === 'active' ? 'badge-success' : 'badge-secondary'}">${a.status || 'active'}</span></td>
            <td class="actions-cell">
                <button class="btn btn-primary btn-xs" onclick="editAgent(${a.id})">Edit</button>
            </td>
        </tr>`).join('');
    } catch (e) { document.getElementById('tableBody').innerHTML = '<tr><td colspan="7" class="tbl-empty">Error loading data</td></tr>'; }
}
loadList();
</script>
@endsection
