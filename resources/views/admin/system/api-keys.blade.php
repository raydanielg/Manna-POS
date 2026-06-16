@extends('admin.layouts.app')
@section('page_title', 'API Keys & Integrations')
@section('content')
<div class="page-card">
    <div class="card-header">
        <div class="card-title">API Keys</div>
        <button class="btn btn-primary" onclick="openGenerateModal()">+ Generate Key</button>
    </div>
    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead><tr><th>Name</th><th>Key</th><th>Created</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody id="tableBody"><tr><td colspan="5" class="tbl-empty">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="generateModal">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title">Generate API Key</div>
            <button class="modal-close" onclick="closeModal('generateModal')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label>Key Name</label>
                <input type="text" class="form-control" id="key_name" placeholder="My App Key">
            </div>
            <div class="form-group">
                <label>Permissions</label>
                <div style="display:flex;flex-direction:column;gap:0.4rem;margin-top:0.3rem;">
                    <label style="display:flex;align-items:center;gap:0.5rem;font-size:0.82rem;cursor:pointer;">
                        <input type="checkbox" id="perm_read" checked> Read
                    </label>
                    <label style="display:flex;align-items:center;gap:0.5rem;font-size:0.82rem;cursor:pointer;">
                        <input type="checkbox" id="perm_write"> Write
                    </label>
                    <label style="display:flex;align-items:center;gap:0.5rem;font-size:0.82rem;cursor:pointer;">
                        <input type="checkbox" id="perm_admin"> Admin
                    </label>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('generateModal')">Cancel</button>
            <button class="btn btn-primary" onclick="generateKey()">Generate</button>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
const API = '/api/admin/system/api-keys';

async function loadList() {
    try {
        const data = await apiFetch(API);
        const tbody = document.getElementById('tableBody');
        if (!data.length) { tbody.innerHTML = '<tr><td colspan="5" class="tbl-empty">No API keys</td></tr>'; return; }
        tbody.innerHTML = data.map(k => `<tr>
            <td><strong>${k.name}</strong></td>
            <td><code>${k.key_preview || k.key?.substring(0,16) + '...' || '****'}</code></td>
            <td>${new Date(k.created_at).toLocaleDateString()}</td>
            <td><span class="badge ${k.is_active ? 'badge-success' : 'badge-danger'}">${k.is_active ? 'Active' : 'Inactive'}</span></td>
            <td class="actions-cell">
                <button class="btn btn-${k.is_active ? 'warning' : 'success'} btn-xs" onclick="toggleKey(${k.id}, ${k.is_active})">${k.is_active ? 'Deactivate' : 'Activate'}</button>
                <button class="btn btn-danger btn-xs" onclick="deleteKey(${k.id},'${k.name}')">Delete</button>
            </td>
        </tr>`).join('');
    } catch (e) {
        document.getElementById('tableBody').innerHTML = '<tr><td colspan="5" class="tbl-empty">Failed to load</td></tr>';
    }
}

function openGenerateModal() {
    document.getElementById('key_name').value = '';
    document.getElementById('perm_read').checked = true;
    document.getElementById('perm_write').checked = false;
    document.getElementById('perm_admin').checked = false;
    openModal('generateModal');
}

async function generateKey() {
    const name = document.getElementById('key_name').value.trim();
    if (!name) { Swal.fire({ icon: 'error', title: 'Required', text: 'Key name is required' }); return; }
    const permissions = [];
    if (document.getElementById('perm_read').checked) permissions.push('read');
    if (document.getElementById('perm_write').checked) permissions.push('write');
    if (document.getElementById('perm_admin').checked) permissions.push('admin');
    try {
        const data = await apiFetch(API, { method: 'POST', body: { name, permissions } });
        closeModal('generateModal');
        Swal.fire({ icon: 'success', title: 'Key Generated!', text: `Your API key: ${data.api_key || data.key}\n\nCopy this now — it won't be shown again.`, confirmButtonText: 'Copied!' });
        loadList();
    } catch (e) {
        Swal.fire({ icon: 'error', title: 'Error', text: e.data?.message || 'Generation failed' });
    }
}

async function toggleKey(id, current) {
    try {
        await apiFetch(`${API}/${id}/toggle`, { method: 'PUT' });
        Swal.fire({ icon: 'success', title: current ? 'Deactivated' : 'Activated', timer: 1500, showConfirmButton: false });
        loadList();
    } catch (e) {
        Swal.fire({ icon: 'error', title: 'Error', text: e.data?.message || 'Toggle failed' });
    }
}

function deleteKey(id, name) {
    Swal.fire({ title: 'Delete Key?', text: `Delete "${name}"? This cannot be undone.`, icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc2626', confirmButtonText: 'Delete' })
    .then(async (r) => { if (r.isConfirmed) { await apiFetch(`${API}/${id}`, { method: 'DELETE' }); Swal.fire({ icon: 'success', title: 'Deleted!', timer: 1500, showConfirmButton: false }); loadList(); }});
}

loadList();
</script>
@endsection