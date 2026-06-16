@extends('admin.layouts.app')
@section('page_title', 'Transaction Logs')
@section('content')
<div class="page-card">
    <div class="card-header">
        <div class="card-title">Transaction Logs</div>
        <div class="filters-row">
            <div class="search-wrap">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" id="searchInput" placeholder="Search ID or user..." oninput="loadList()">
            </div>
            <select class="form-control" style="width:auto;padding:0.45rem 0.75rem;font-size:0.78rem;" id="statusFilter" onchange="loadList()">
                <option value="">All</option>
                <option value="completed">Completed</option>
                <option value="pending">Pending</option>
                <option value="failed">Failed</option>
                <option value="refunded">Refunded</option>
            </select>
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead><tr><th>Transaction ID</th><th>Invoice</th><th>User</th><th>Gateway</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead>
            <tbody id="tableBody"><tr><td colspan="7" class="tbl-empty">Loading...</td></tr></tbody>
        </table>
    </div>
</div>
@endsection
@section('scripts')
const API = '/api/admin/billing/transactions';

async function loadList() {
    const search = document.getElementById('searchInput').value;
    const status = document.getElementById('statusFilter').value;
    const params = new URLSearchParams({ search, status });
    const data = await apiFetch(`${API}?${params}`);
    const tbody = document.getElementById('tableBody');
    if (!data.length) { tbody.innerHTML = '<tr><td colspan="7" class="tbl-empty">No transactions found</td></tr>'; return; }
    tbody.innerHTML = data.map(t => `<tr>
        <td><strong>${t.transaction_id || '-'}</strong></td>
        <td>${t.invoice?.invoice_number || t.invoice_number || '-'}</td>
        <td>${t.user?.name || t.user_name || 'N/A'}</td>
        <td>${t.gateway || '-'}</td>
        <td>${t.currency || 'TZS'} ${(t.amount || 0).toLocaleString()}</td>
        <td><span class="badge ${t.status === 'completed' ? 'badge-success' : t.status === 'pending' ? 'badge-pending' : t.status === 'refunded' ? 'badge-info' : 'badge-danger'}">${t.status}</span></td>
        <td>${t.created_at ? new Date(t.created_at).toLocaleDateString() : '-'}</td>
    </tr>`).join('');
}

loadList();
@endsection
