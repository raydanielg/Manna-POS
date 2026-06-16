@extends('admin.layouts.app')
@section('page_title', 'Restore Backup')
@section('content')
<div class="page-card" style="margin-bottom:1.25rem;">
    <div class="card-header">
        <div class="card-title">Available Backups</div>
        <button class="btn btn-danger" id="restoreBtn" onclick="restoreBackup()" disabled>Restore Selected</button>
    </div>
    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead><tr><th style="width:50px;">Select</th><th>Filename</th><th>Type</th><th>Size</th><th>Created</th></tr></thead>
            <tbody id="tableBody"><tr><td colspan="5" class="tbl-empty">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<div class="page-card">
    <div class="card-header">
        <div class="card-title">Upload Backup File</div>
    </div>
    <div style="padding:1.25rem;">
        <div class="form-group">
            <label>Choose backup file (.sql or .zip)</label>
            <input type="file" class="form-control" id="backupFile" accept=".sql,.zip,.gz">
        </div>
        <button class="btn btn-primary" onclick="uploadBackup()">Upload & Restore</button>
    </div>
</div>
@endsection
@section('scripts')
<script>
const API = '/api/admin/system/backups';
let selectedId = null;

async function loadList() {
    try {
        const data = await apiFetch(API);
        const tbody = document.getElementById('tableBody');
        if (!data.length) { tbody.innerHTML = '<tr><td colspan="5" class="tbl-empty">No backups available</td></tr>'; return; }
        tbody.innerHTML = data.map(b => `<tr>
            <td><input type="radio" name="backup_select" value="${b.id}" onchange="selectBackup(${b.id})"></td>
            <td><code>${b.filename}</code></td>
            <td><span class="badge badge-info">${b.type || 'full'}</span></td>
            <td>${b.size}</td>
            <td>${new Date(b.created_at).toLocaleString()}</td>
        </tr>`).join('');
    } catch (e) {
        document.getElementById('tableBody').innerHTML = '<tr><td colspan="5" class="tbl-empty">Failed to load</td></tr>';
    }
}

function selectBackup(id) {
    selectedId = id;
    document.getElementById('restoreBtn').disabled = false;
}

async function restoreBackup() {
    if (!selectedId) { Swal.fire({ icon: 'warning', title: 'Select a backup' }); return; }
    const result = await Swal.fire({
        title: 'Restore Backup?',
        text: 'This will overwrite current data! This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        confirmButtonText: 'Yes, restore!'
    });
    if (!result.isConfirmed) return;
    Swal.fire({ title: 'Restoring...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
    try {
        await apiFetch(`${API}/${selectedId}/restore`, { method: 'POST' });
        Swal.fire({ icon: 'success', title: 'Restore complete!', timer: 2000, showConfirmButton: false });
    } catch (e) {
        Swal.fire({ icon: 'error', title: 'Restore failed', text: e.data?.message || 'An error occurred' });
    }
}

async function uploadBackup() {
    const fileInput = document.getElementById('backupFile');
    if (!fileInput.files.length) { Swal.fire({ icon: 'warning', title: 'Select a file' }); return; }
    Swal.fire({ title: 'Uploading...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
    try {
        const formData = new FormData();
        formData.append('file', fileInput.files[0]);
        await apiFetch(`${API}/upload`, { method: 'POST', body: formData });
        Swal.fire({ icon: 'success', title: 'Uploaded! Restoring...', timer: 2000, showConfirmButton: false });
        fileInput.value = '';
        loadList();
    } catch (e) {
        Swal.fire({ icon: 'error', title: 'Upload failed', text: e.data?.message || 'An error occurred' });
    }
}

loadList();
</script>
@endsection