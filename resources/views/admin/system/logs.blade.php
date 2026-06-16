@extends('admin.layouts.app')
@section('page_title', 'System Logs')
@section('content')
<div class="page-card">
    <div class="card-header">
        <div class="card-title">System Log Files</div>
    </div>
    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead><tr><th>Filename</th><th>Size</th><th>Last Modified</th><th>Actions</th></tr></thead>
            <tbody id="tableBody"><tr><td colspan="4" class="tbl-empty">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="logModal">
    <div class="modal" style="max-width:800px;">
        <div class="modal-header">
            <div class="modal-title" id="logModalTitle">Log Content</div>
            <button class="modal-close" onclick="closeModal('logModal')">&times;</button>
        </div>
        <div class="modal-body">
            <pre id="logContent" style="background:#1e293b;color:#e2e8f0;padding:1rem;border-radius:8px;font-size:0.72rem;overflow-x:auto;max-height:500px;white-space:pre-wrap;font-family:monospace;"></pre>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('logModal')">Close</button>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
const API = '/api/admin/system/logs';

async function loadList() {
    try {
        const data = await apiFetch(API);
        const tbody = document.getElementById('tableBody');
        if (!data.length) { tbody.innerHTML = '<tr><td colspan="4" class="tbl-empty">No log files found</td></tr>'; return; }
        tbody.innerHTML = data.map(l => `<tr>
            <td><code>${l.filename || l.name}</code></td>
            <td>${l.size}</td>
            <td>${l.last_modified ? new Date(l.last_modified).toLocaleString() : '-'}</td>
            <td class="actions-cell">
                <button class="btn btn-primary btn-xs" onclick="viewLog('${l.filename || l.name}')">View</button>
                <a class="btn btn-success btn-xs" href="${API}/${encodeURIComponent(l.filename || l.name)}/download">Download</a>
                <button class="btn btn-danger btn-xs" onclick="clearLog('${l.filename || l.name}')">Clear</button>
            </td>
        </tr>`).join('');
    } catch (e) {
        document.getElementById('tableBody').innerHTML = '<tr><td colspan="4" class="tbl-empty">Failed to load</td></tr>';
    }
}

async function viewLog(filename) {
    Swal.fire({ title: 'Loading...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
    try {
        const data = await apiFetch(`${API}/${encodeURIComponent(filename)}`);
        document.getElementById('logModalTitle').textContent = filename;
        document.getElementById('logContent').textContent = data.content || data;
        closeModal('logModal'); // ensure it's closed first
        openModal('logModal');
        Swal.close();
    } catch (e) {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to load log content' });
    }
}

function clearLog(filename) {
    Swal.fire({ title: 'Clear log?', text: `Clear "${filename}"?`, icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc2626', confirmButtonText: 'Clear' })
    .then(async (r) => { if (r.isConfirmed) { await apiFetch(`${API}/${encodeURIComponent(filename)}`, { method: 'DELETE' }); Swal.fire({ icon: 'success', title: 'Cleared!', timer: 1500, showConfirmButton: false }); loadList(); }});
}

loadList();
</script>
@endsection