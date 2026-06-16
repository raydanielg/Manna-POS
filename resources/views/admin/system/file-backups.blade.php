@extends('admin.layouts.app')
@section('page_title', 'File Backups')
@section('content')
<div class="page-card">
    <div class="card-header">
        <div class="card-title">File Backups</div>
        <button class="btn btn-success" onclick="createBackup()">+ Create Backup</button>
    </div>
    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead><tr><th>Filename</th><th>Type</th><th>Size</th><th>Created</th><th>Actions</th></tr></thead>
            <tbody id="tableBody"><tr><td colspan="5" class="tbl-empty">Loading...</td></tr></tbody>
        </table>
    </div>
</div>
@endsection
@section('scripts')
const API = '/api/admin/system/file-backups';

async function loadList() {
    try {
        const data = await apiFetch(API);
        const tbody = document.getElementById('tableBody');
        if (!data.length) { tbody.innerHTML = '<tr><td colspan="5" class="tbl-empty">No backups found</td></tr>'; return; }
        tbody.innerHTML = data.map(b => `<tr>
            <td><code>${b.filename}</code></td>
            <td><span class="badge badge-info">${b.type || 'full'}</span></td>
            <td>${b.size}</td>
            <td>${new Date(b.created_at).toLocaleString()}</td>
            <td class="actions-cell">
                <a class="btn btn-primary btn-xs" href="${API}/${b.id}/download">Download</a>
                <button class="btn btn-danger btn-xs" onclick="deleteBackup(${b.id},'${b.filename}')">Delete</button>
            </td>
        </tr>`).join('');
    } catch (e) {
        document.getElementById('tableBody').innerHTML = '<tr><td colspan="5" class="tbl-empty">Failed to load</td></tr>';
    }
}

async function createBackup() {
    const { value: type } = await Swal.fire({
        title: 'Backup Type',
        input: 'select',
        inputOptions: { full: 'Full Backup', files: 'Files Only', database: 'Database Only' },
        inputValue: 'full',
        showCancelButton: true,
        confirmButtonText: 'Create'
    });
    if (!type) return;
    Swal.fire({ title: 'Creating backup...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
    try {
        await apiFetch(API, { method: 'POST', body: { type } });
        Swal.fire({ icon: 'success', title: 'Backup created!', timer: 2000, showConfirmButton: false });
        loadList();
    } catch (e) {
        Swal.fire({ icon: 'error', title: 'Error', text: e.data?.message || 'Backup failed' });
    }
}

function deleteBackup(id, filename) {
    Swal.fire({ title: 'Delete backup?', text: `Delete "${filename}"?`, icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc2626', confirmButtonText: 'Delete' })
    .then(async (r) => { if (r.isConfirmed) { await apiFetch(`${API}/${id}`, { method: 'DELETE' }); Swal.fire({ icon: 'success', title: 'Deleted!', timer: 1500, showConfirmButton: false }); loadList(); }});
}

loadList();
@endsection