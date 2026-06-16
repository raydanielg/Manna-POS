@extends('admin.layouts.app')
@section('page_title', 'File Manager')
@section('content')
<div class="page-card" style="margin-bottom:1.25rem;">
    <div class="card-header">
        <div class="card-title" id="breadcrumbDisplay">File Manager</div>
        <div class="actions-cell">
            <button class="btn btn-primary" onclick="openFolderModal()">+ New Folder</button>
            <button class="btn btn-success" onclick="document.getElementById('uploadInput').click()">Upload</button>
            <input type="file" id="uploadInput" style="display:none;" multiple onchange="uploadFiles(this)">
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead><tr><th>Name</th><th>Type</th><th>Size</th><th>Modified</th><th>Actions</th></tr></thead>
            <tbody id="tableBody"><tr><td colspan="5" class="tbl-empty">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="folderModal">
    <div class="modal" style="max-width:400px;">
        <div class="modal-header">
            <div class="modal-title">Create Folder</div>
            <button class="modal-close" onclick="closeModal('folderModal')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label>Folder Name</label>
                <input type="text" class="form-control" id="folderName" placeholder="new-folder" onkeydown="if(event.key==='Enter')createFolder()">
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('folderModal')">Cancel</button>
            <button class="btn btn-primary" onclick="createFolder()">Create</button>
        </div>
    </div>
</div>
@endsection
@section('scripts')
const API = '/api/admin/file-manager';
let currentPath = '';

async function loadList(path) {
    currentPath = path || '';
    try {
        const params = path ? '?path=' + encodeURIComponent(path) : '';
        const data = await apiFetch(API + params);
        renderBreadcrumb(path);
        const tbody = document.getElementById('tableBody');
        const items = data.items || data.files || data || [];
        if (!items.length) { tbody.innerHTML = '<tr><td colspan="5" class="tbl-empty">Empty directory</td></tr>'; return; }
        tbody.innerHTML = items.map(item => {
            const name = item.name || item.filename;
            const isDir = item.type === 'dir' || item.is_dir || item.type === 'folder';
            const icon = isDir ? '📁' : '📄';
            return `<tr>
                <td>${isDir ? `<a href="#" onclick="event.preventDefault();navigate('${path ? path + '/' + name : name}')" style="color:#0f172a;text-decoration:none;font-weight:500;">${icon} ${name}</a>` : icon + ' ' + name}</td>
                <td>${isDir ? 'Folder' : item.extension || 'File'}</td>
                <td>${item.size || '-'}</td>
                <td>${item.modified || item.last_modified ? new Date(item.modified || item.last_modified).toLocaleString() : '-'}</td>
                <td class="actions-cell">
                    ${!isDir ? `<a class="btn btn-primary btn-xs" href="${API}/${encodeURIComponent(name)}?path=${encodeURIComponent(path || '')}&download=1">Download</a>` : ''}
                    <button class="btn btn-danger btn-xs" onclick="deleteItem('${name}',${isDir})">Delete</button>
                </td>
            </tr>`;
        }).join('');
    } catch (e) {
        document.getElementById('tableBody').innerHTML = '<tr><td colspan="5" class="tbl-empty">Failed to load</td></tr>';
    }
}

function renderBreadcrumb(path) {
    const display = document.getElementById('breadcrumbDisplay');
    if (!path) { display.textContent = 'File Manager / root'; return; }
    const parts = path.split('/');
    let html = 'File Manager / <a href="#" onclick="loadList()" style="color:#e03057;text-decoration:none;">root</a>';
    let accum = '';
    parts.forEach((p, i) => {
        accum += (i > 0 ? '/' : '') + p;
        html += ' / <a href="#" onclick="loadList(\'' + accum + '\')" style="color:#e03057;text-decoration:none;">' + p + '</a>';
    });
    display.innerHTML = html;
}

function navigate(path) {
    loadList(path);
}

function openFolderModal() {
    document.getElementById('folderName').value = '';
    openModal('folderModal');
    setTimeout(() => document.getElementById('folderName').focus(), 100);
}

async function createFolder() {
    const name = document.getElementById('folderName').value.trim();
    if (!name) { Swal.fire({ icon: 'error', title: 'Enter a folder name' }); return; }
    try {
        await apiFetch(API + '/folder', { method: 'POST', body: { name, path: currentPath } });
        closeModal('folderModal');
        Swal.fire({ icon: 'success', title: 'Folder created!', timer: 1500, showConfirmButton: false });
        loadList(currentPath);
    } catch (e) {
        Swal.fire({ icon: 'error', title: 'Error', text: e.data?.message || 'Failed to create folder' });
    }
}

async function uploadFiles(input) {
    const files = input.files;
    if (!files.length) return;
    Swal.fire({ title: 'Uploading ' + files.length + ' file(s)...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
    try {
        for (let i = 0; i < files.length; i++) {
            const formData = new FormData();
            formData.append('file', files[i]);
            formData.append('path', currentPath || '');
            await apiFetch(API + '/upload', { method: 'POST', body: formData });
        }
        Swal.fire({ icon: 'success', title: 'Upload complete!', timer: 1500, showConfirmButton: false });
        input.value = '';
        loadList(currentPath);
    } catch (e) {
        Swal.fire({ icon: 'error', title: 'Upload failed', text: e.data?.message || 'Error uploading file' });
    }
}

function deleteItem(name, isDir) {
    Swal.fire({ title: 'Delete?', text: `Delete "${name}"?`, icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc2626', confirmButtonText: 'Delete' })
    .then(async (r) => {
        if (!r.isConfirmed) return;
        try {
            await apiFetch(API + '/delete', { method: 'POST', body: { name, path: currentPath, type: isDir ? 'folder' : 'file' } });
            Swal.fire({ icon: 'success', title: 'Deleted!', timer: 1500, showConfirmButton: false });
            loadList(currentPath);
        } catch (e) {
            Swal.fire({ icon: 'error', title: 'Error', text: e.data?.message || 'Delete failed' });
        }
    });
}

loadList();
@endsection