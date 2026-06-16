@extends('admin.layouts.app')
@section('page_title', 'System Updates')
@section('content')
<div class="page-card" style="margin-bottom:1.25rem;">
    <div class="card-header">
        <div class="card-title">Current Version</div>
        <div class="actions-cell">
            <button class="btn btn-secondary" onclick="checkUpdates()">Check for Updates</button>
            <button class="btn btn-primary" onclick="runUpdate()">Update Now</button>
        </div>
    </div>
    <div style="padding:1.25rem;">
        <div style="display:flex;align-items:center;gap:1rem;">
            <span style="font-size:2rem;font-weight:800;color:#0f172a;" id="currentVersion">Loading...</span>
            <span id="updateStatus" class="badge badge-default">Checking...</span>
        </div>
    </div>
</div>

<div class="page-card">
    <div class="card-header">
        <div class="card-title">Update History</div>
    </div>
    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead><tr><th>Version</th><th>Release Date</th><th>Description</th><th>Status</th></tr></thead>
            <tbody id="tableBody"><tr><td colspan="4" class="tbl-empty">Loading...</td></tr></tbody>
        </table>
    </div>
</div>
@endsection
@section('scripts')
const API = '/api/admin/system/updates';

async function loadInfo() {
    try {
        const data = await apiFetch(API);
        document.getElementById('currentVersion').textContent = data.current_version || 'v1.0.0';
        const badge = document.getElementById('updateStatus');
        if (data.update_available) {
            badge.textContent = 'Update Available: ' + (data.latest_version || '');
            badge.className = 'badge badge-warning';
        } else {
            badge.textContent = 'Up to date';
            badge.className = 'badge badge-success';
        }
        const tbody = document.getElementById('tableBody');
        const logs = data.history || data.logs || [];
        if (!logs.length) { tbody.innerHTML = '<tr><td colspan="4" class="tbl-empty">No update history</td></tr>'; return; }
        tbody.innerHTML = logs.map(u => `<tr>
            <td><strong>${u.version}</strong></td>
            <td>${u.release_date ? new Date(u.release_date).toLocaleDateString() : '-'}</td>
            <td>${u.description || '-'}</td>
            <td><span class="badge ${u.status === 'success' ? 'badge-success' : u.status === 'failed' ? 'badge-danger' : 'badge-info'}">${u.status || 'pending'}</span></td>
        </tr>`).join('');
    } catch (e) {
        document.getElementById('currentVersion').textContent = 'Error';
        document.getElementById('updateStatus').textContent = 'Failed to load';
        document.getElementById('updateStatus').className = 'badge badge-danger';
    }
}

async function checkUpdates() {
    Swal.fire({ title: 'Checking...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
    try {
        const data = await apiFetch(API + '/check', { method: 'POST' });
        Swal.fire({ icon: data.update_available ? 'warning' : 'success', title: data.update_available ? 'Update available: ' + data.latest_version : 'You are up to date!', timer: 2000, showConfirmButton: false });
        loadInfo();
    } catch (e) {
        Swal.fire({ icon: 'error', title: 'Check failed', text: e.data?.message || 'Could not check for updates' });
    }
}

function runUpdate() {
    Swal.fire({
        title: 'Run Update?',
        text: 'The system will be updated to the latest version. A backup is recommended first.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e03057',
        confirmButtonText: 'Yes, update!'
    }).then(async (r) => {
        if (!r.isConfirmed) return;
        Swal.fire({ title: 'Updating...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        try {
            await apiFetch(API + '/run', { method: 'POST' });
            Swal.fire({ icon: 'success', title: 'Update complete!', timer: 2000, showConfirmButton: false });
            loadInfo();
        } catch (e) {
            Swal.fire({ icon: 'error', title: 'Update failed', text: e.data?.message || 'An error occurred' });
        }
    });
}

loadInfo();
@endsection