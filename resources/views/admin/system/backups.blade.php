@extends('admin.layouts.app')
@section('page_title', 'Backups')
@section('content')
<div class="page-card">
    <div class="card-header">
        <div class="card-title">Database Backups</div>
        <button class="btn btn-success" onclick="createBackup()">+ Create Backup</button>
    </div>
    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead><tr><th>Filename</th><th>Size</th><th>Created</th><th>Actions</th></tr></thead>
            <tbody id="tableBody"><tr><td colspan="4" class="tbl-empty">Loading...</td></tr></tbody>
        </table>
    </div>
</div>
@endsection
@section('scripts')
<script>
const API = '/api/admin/system/backups';

async function loadList() {
    const data = await apiFetch(API);
    const tbody = document.getElementById('tableBody');
    if (!data.length) { tbody.innerHTML = '<tr><td colspan="4" class="tbl-empty">No backups</td></tr>'; return; }
    tbody.innerHTML = data.map(b => `<tr>
        <td><code>${b.filename}</code></td>
        <td>${b.size}</td>
        <td>${new Date(b.created_at).toLocaleString()}</td>
        <td class="actions-cell">
            <a class="btn btn-primary btn-xs" href="${API}/${b.id}/download">Download</a>
            <button class="btn btn-danger btn-xs" onclick="deleteBackup(${b.id},'${b.filename}')">Delete</button>
        </td>
    </tr>`).join('');
}

async function createBackup() {
    Swal.fire({ title: 'Creating backup...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
    try {
        await apiFetch(API, { method: 'POST' });
        Swal.fire({ icon: 'success', title: 'Backup created!', timer: 2000, showConfirmButton: false }); loadList();
    } catch (e) { Swal.fire({ icon: 'error', title: 'Error', text: e.data?.message || 'Failed to create backup' }); }
}

function deleteBackup(id, filename) {
    Swal.fire({ title: 'Delete?', text: `Delete "${filename}"?`, icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc2626', confirmButtonText: 'Delete' })
    .then(async (r) => { if (r.isConfirmed) { await apiFetch(`${API}/${id}`, { method: 'DELETE' }); Swal.fire({ icon: 'success', title: 'Deleted!', timer: 2000, showConfirmButton: false }); loadList(); }});
}

loadList();
</script>
@endsection
