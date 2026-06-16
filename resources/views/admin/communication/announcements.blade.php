@extends('admin.layouts.app')
@section('page_title', 'Announcements')
@section('content')
<div class="page-card">
    <div class="card-header">
        <div class="card-title">Announcements</div>
        <div class="filters-row">
            <select class="form-control" style="width:auto;padding:0.45rem 0.75rem;font-size:0.78rem;" id="statusFilter" onchange="loadList()">
                <option value="">All</option>
                <option value="draft">Draft</option>
                <option value="published">Published</option>
                <option value="archived">Archived</option>
            </select>
            <button class="btn btn-success" onclick="openAddModal()">+ New Announcement</button>
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead><tr><th>Title</th><th>Type</th><th>Status</th><th>Scheduled</th><th>Expires</th><th>Actions</th></tr></thead>
            <tbody id="tableBody"><tr><td colspan="6" class="tbl-empty">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="annModal">
    <div class="modal" style="max-width:600px;">
        <div class="modal-header">
            <div class="modal-title" id="modalTitle">New Announcement</div>
            <button class="modal-close" onclick="closeModal('annModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="annForm">
                <div class="form-group">
                    <label>Title *</label>
                    <input type="text" class="form-control" id="title" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Type</label>
                        <select class="form-control" id="type">
                            <option value="info">Info</option>
                            <option value="success">Success</option>
                            <option value="warning">Warning</option>
                            <option value="danger">Critical</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" id="status">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                            <option value="archived">Archived</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Scheduled At</label>
                        <input type="datetime-local" class="form-control" id="scheduled_at">
                    </div>
                    <div class="form-group">
                        <label>Expires At</label>
                        <input type="datetime-local" class="form-control" id="expires_at">
                    </div>
                </div>
                <div class="form-group">
                    <label>Content *</label>
                    <textarea class="form-control" id="content" rows="5" required></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('annModal')">Cancel</button>
            <button class="btn btn-primary" onclick="saveAnnouncement()">Save</button>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
const API = '/api/admin/communication/announcements';
let editId = null;

async function loadList() {
    const status = document.getElementById('statusFilter').value;
    const data = await apiFetch(`${API}?status=${status}`);
    const tbody = document.getElementById('tableBody');
    if (!data.length) { tbody.innerHTML = '<tr><td colspan="6" class="tbl-empty">No announcements</td></tr>'; return; }
    tbody.innerHTML = data.map(a => `<tr>
        <td><strong>${a.title}</strong></td>
        <td><span class="badge ${a.type === 'success' ? 'badge-success' : a.type === 'warning' ? 'badge-pending' : a.type === 'danger' ? 'badge-danger' : 'badge-info'}">${a.type}</span></td>
        <td><span class="badge ${a.status === 'published' ? 'badge-success' : a.status === 'archived' ? 'badge-default' : 'badge-pending'}">${a.status}</span></td>
        <td>${a.scheduled_at ? new Date(a.scheduled_at).toLocaleDateString() : '-'}</td>
        <td>${a.expires_at ? new Date(a.expires_at).toLocaleDateString() : '-'}</td>
        <td class="actions-cell">
            <button class="btn btn-primary btn-xs" onclick="editAnn(${a.id})">Edit</button>
            <button class="btn btn-danger btn-xs" onclick="deleteAnn(${a.id},'${a.title}')">Delete</button>
        </td>
    </tr>`).join('');
}

async function openAddModal() { editId = null; document.getElementById('annForm').reset(); document.getElementById('modalTitle').textContent = 'New Announcement'; openModal('annModal'); }

async function editAnn(id) {
    editId = id; document.getElementById('modalTitle').textContent = 'Edit Announcement';
    const data = await apiFetch(`${API}/${id}`);
    document.getElementById('title').value = data.title || '';
    document.getElementById('type').value = data.type || 'info';
    document.getElementById('status').value = data.status || 'draft';
    document.getElementById('content').value = data.content || '';
    document.getElementById('scheduled_at').value = data.scheduled_at ? data.scheduled_at.replace(' ','T').substring(0,16) : '';
    document.getElementById('expires_at').value = data.expires_at ? data.expires_at.replace(' ','T').substring(0,16) : '';
    openModal('annModal');
}

async function saveAnnouncement() {
    const body = { title: document.getElementById('title').value, type: document.getElementById('type').value, status: document.getElementById('status').value, content: document.getElementById('content').value, scheduled_at: document.getElementById('scheduled_at').value, expires_at: document.getElementById('expires_at').value };
    try {
        if (editId) { await apiFetch(`${API}/${editId}`, { method: 'PUT', body }); }
        else { await apiFetch(API, { method: 'POST', body }); }
        closeModal('annModal'); Swal.fire({ icon: 'success', title: 'Saved!', timer: 2000, showConfirmButton: false }); loadList();
    } catch (e) { Swal.fire({ icon: 'error', title: 'Error', text: e.data?.message || 'Something went wrong' }); }
}

function deleteAnn(id, title) {
    Swal.fire({ title: 'Delete?', text: `Delete "${title}"?`, icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc2626', confirmButtonText: 'Delete' })
    .then(async (r) => { if (r.isConfirmed) { await apiFetch(`${API}/${id}`, { method: 'DELETE' }); Swal.fire({ icon: 'success', title: 'Deleted!', timer: 2000, showConfirmButton: false }); loadList(); }});
}

loadList();
</script>
@endsection
