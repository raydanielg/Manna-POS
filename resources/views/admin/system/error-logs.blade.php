@extends('admin.layouts.app')
@section('page_title', 'Error Logs')
@section('content')
<div class="page-card">
    <div class="card-header">
        <div class="card-title">Error Logs</div>
        <div class="filters-row">
            <div class="search-wrap">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/></svg>
                <input type="text" id="searchInput" placeholder="Search logs..." oninput="loadList()">
            </div>
            <select class="form-control" id="levelFilter" onchange="loadList()" style="width:auto;min-width:120px;">
                <option value="">All Levels</option>
                <option value="ERROR">ERROR</option>
                <option value="WARNING">WARNING</option>
                <option value="INFO">INFO</option>
            </select>
            <button class="btn btn-danger" onclick="clearLogs()">Clear Logs</button>
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead><tr><th>Level</th><th>Message</th><th>File</th><th>Line</th><th>Timestamp</th></tr></thead>
            <tbody id="tableBody"><tr><td colspan="5" class="tbl-empty">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="detailModal">
    <div class="modal" style="max-width:700px;">
        <div class="modal-header">
            <div class="modal-title">Error Details</div>
            <button class="modal-close" onclick="closeModal('detailModal')">&times;</button>
        </div>
        <div class="modal-body">
            <pre id="errorDetail" style="background:#f8fafc;padding:1rem;border-radius:8px;font-size:0.78rem;overflow-x:auto;max-height:400px;white-space:pre-wrap;"></pre>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('detailModal')">Close</button>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
const API = '/api/admin/system/error-logs';

async function loadList() {
    try {
        const level = document.getElementById('levelFilter').value;
        const search = document.getElementById('searchInput').value;
        const params = new URLSearchParams();
        if (level) params.set('level', level);
        if (search) params.set('search', search);
        const data = await apiFetch(API + '?' + params.toString());
        const tbody = document.getElementById('tableBody');
        if (!data.length) { tbody.innerHTML = '<tr><td colspan="5" class="tbl-empty">No logs found</td></tr>'; return; }
        tbody.innerHTML = data.map(l => `<tr style="cursor:pointer;" onclick="showDetail(${JSON.stringify(l).replace(/"/g,'&quot;')})">
            <td><span class="badge ${l.level === 'ERROR' ? 'badge-danger' : l.level === 'WARNING' ? 'badge-pending' : 'badge-info'}">${l.level}</span></td>
            <td>${l.message || l.message?.substring(0,80)}</td>
            <td><code>${l.file || '-'}</code></td>
            <td>${l.line || '-'}</td>
            <td>${l.timestamp ? new Date(l.timestamp).toLocaleString() : '-'}</td>
        </tr>`).join('');
    } catch (e) {
        document.getElementById('tableBody').innerHTML = '<tr><td colspan="5" class="tbl-empty">Failed to load</td></tr>';
    }
}

function showDetail(log) {
    document.getElementById('errorDetail').textContent = JSON.stringify(log, null, 2);
    openModal('detailModal');
}

function clearLogs() {
    Swal.fire({ title: 'Clear all error logs?', text: 'This action cannot be undone.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc2626', confirmButtonText: 'Clear' })
    .then(async (r) => { if (r.isConfirmed) { await apiFetch(API, { method: 'DELETE' }); Swal.fire({ icon: 'success', title: 'Cleared!', timer: 1500, showConfirmButton: false }); loadList(); }});
}

loadList();
</script>
@endsection