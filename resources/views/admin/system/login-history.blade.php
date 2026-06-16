@extends('admin.layouts.app')
@section('page_title', 'Login History')
@section('content')
<div class="page-card">
    <div class="card-header">
        <div class="card-title">Login History</div>
        <div class="filters-row">
            <input type="text" class="form-control" style="width:180px;font-size:0.78rem;" id="search" placeholder="Search user or IP...">
            <button class="btn btn-danger btn-sm" onclick="clearHistory()">Clear All</button>
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead><tr><th>User</th><th>Email</th><th>IP Address</th><th>User Agent</th><th>Success</th><th>Date</th></tr></thead>
            <tbody id="tableBody"><tr><td colspan="6" class="tbl-empty">Loading...</td></tr></tbody>
        </table>
    </div>
</div>
@endsection
@section('scripts')
<script>
const API = '/api/admin/system/login-history';
let debounceTimer;

async function loadList() {
    const search = document.getElementById('search').value;
    const data = await apiFetch(`${API}?search=${search}`);
    const tbody = document.getElementById('tableBody');
    if (!data.length) { tbody.innerHTML = '<tr><td colspan="6" class="tbl-empty">No login history</td></tr>'; return; }
    tbody.innerHTML = data.map(l => `<tr>
        <td>${l.user_name || 'N/A'}</td>
        <td>${l.email || '-'}</td>
        <td><code>${l.ip_address}</code></td>
        <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="${l.user_agent}">${l.user_agent ? l.user_agent.substring(0,40)+'...' : '-'}</td>
        <td>${l.success ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>'}</td>
        <td>${new Date(l.created_at).toLocaleString()}</td>
    </tr>`).join('');
}

document.getElementById('search').addEventListener('input', function() {
    clearTimeout(debounceTimer); debounceTimer = setTimeout(loadList, 400);
});

function clearHistory() {
    Swal.fire({ title: 'Clear login history?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc2626', confirmButtonText: 'Clear' })
    .then(async (r) => { if (r.isConfirmed) { await apiFetch(API, { method: 'DELETE' }); Swal.fire({ icon: 'success', title: 'Cleared!', timer: 2000, showConfirmButton: false }); loadList(); }});
}

loadList();
</script>
@endsection
