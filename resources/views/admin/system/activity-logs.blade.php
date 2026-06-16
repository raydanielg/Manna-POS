@extends('admin.layouts.app')
@section('page_title', 'Activity Logs')
@section('content')
<div class="page-card">
    <div class="card-header">
        <div class="card-title">Activity Logs</div>
        <div class="filters-row">
            <input type="text" class="form-control" style="width:180px;font-size:0.78rem;" id="search" placeholder="Search user or description...">
            <select class="form-control" style="width:auto;font-size:0.78rem;" id="logFilter" onchange="loadList()">
                <option value="">All Events</option>
                <option value="login">Login</option>
                <option value="logout">Logout</option>
                <option value="create">Create</option>
                <option value="update">Update</option>
                <option value="delete">Delete</option>
            </select>
            <button class="btn btn-danger btn-sm" onclick="clearLogs()">Clear All</button>
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead><tr><th>User</th><th>Event</th><th>Description</th><th>IP</th><th>Date</th></tr></thead>
            <tbody id="tableBody"><tr><td colspan="5" class="tbl-empty">Loading...</td></tr></tbody>
        </table>
    </div>
</div>
@endsection
@section('scripts')
const API = '/api/admin/system/activity-logs';
let debounceTimer;

async function loadList() {
    const event = document.getElementById('logFilter').value;
    const search = document.getElementById('search').value;
    const data = await apiFetch(`${API}?event=${event}&search=${search}`);
    const tbody = document.getElementById('tableBody');
    if (!data.length) { tbody.innerHTML = '<tr><td colspan="5" class="tbl-empty">No logs</td></tr>'; return; }
    tbody.innerHTML = data.map(l => `<tr>
        <td>${l.user_name || 'System'}</td>
        <td><span class="badge ${l.event === 'login' || l.event === 'logout' ? 'badge-info' : l.event === 'create' ? 'badge-success' : l.event === 'delete' ? 'badge-danger' : 'badge-pending'}">${l.event}</span></td>
        <td>${l.description}</td>
        <td><code>${l.ip_address || '-'}</code></td>
        <td>${new Date(l.created_at).toLocaleString()}</td>
    </tr>`).join('');
}

document.getElementById('search').addEventListener('input', function() {
    clearTimeout(debounceTimer); debounceTimer = setTimeout(loadList, 400);
});

function clearLogs() {
    Swal.fire({ title: 'Clear all logs?', text: 'This action cannot be undone.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc2626', confirmButtonText: 'Clear All' })
    .then(async (r) => { if (r.isConfirmed) { await apiFetch(API, { method: 'DELETE' }); Swal.fire({ icon: 'success', title: 'Cleared!', timer: 2000, showConfirmButton: false }); loadList(); }});
}

loadList();
@endsection
