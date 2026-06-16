@extends('admin.layouts.app')
@section('page_title', 'Business Categories')
@section('content')
<div class="page-card">
    <div class="card-header">
        <div class="card-title">Business Categories</div>
        <button class="btn btn-success" onclick="openAddModal()">+ Add Category</button>
    </div>
    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead><tr><th>Name</th><th>Description</th><th>Icon</th><th>Active</th><th>Order</th><th>Actions</th></tr></thead>
            <tbody id="tableBody"><tr><td colspan="6" class="tbl-empty">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="catModal">
    <div class="modal" style="max-width:500px;">
        <div class="modal-header">
            <div class="modal-title" id="modalTitle">Add Category</div>
            <button class="modal-close" onclick="closeModal('catModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="catForm">
                <div class="form-group">
                    <label>Name *</label>
                    <input type="text" class="form-control" id="name" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea class="form-control" id="description" rows="2"></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Icon</label>
                        <input type="text" class="form-control" id="icon" placeholder="e.g. store, food">
                    </div>
                    <div class="form-group">
                        <label>Sort Order</label>
                        <input type="number" class="form-control" id="sort_order" value="0">
                    </div>
                </div>
                <div class="form-group">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" id="is_active" checked>
                        <span style="font-size:0.82rem;">Active</span>
                    </label>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('catModal')">Cancel</button>
            <button class="btn btn-primary" onclick="saveCategory()">Save</button>
        </div>
    </div>
</div>
@endsection
@section('scripts')
const API = '/api/admin/business/categories';
let editId = null;

async function loadList() {
    const data = await apiFetch(API);
    const tbody = document.getElementById('tableBody');
    if (!data.length) { tbody.innerHTML = '<tr><td colspan="6" class="tbl-empty">No categories</td></tr>'; return; }
    tbody.innerHTML = data.map(c => `<tr>
        <td><strong>${c.name}</strong></td>
        <td>${c.description || '-'}</td>
        <td>${c.icon || '-'}</td>
        <td>${c.is_active ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>'}</td>
        <td>${c.sort_order}</td>
        <td class="actions-cell">
            <button class="btn btn-primary btn-xs" onclick="editCat(${c.id})">Edit</button>
            <button class="btn btn-danger btn-xs" onclick="deleteCat(${c.id},'${c.name}')">Delete</button>
        </td>
    </tr>`).join('');
}

async function openAddModal() {
    editId = null; document.getElementById('modalTitle').textContent = 'Add Category';
    document.getElementById('catForm').reset(); openModal('catModal');
}

async function editCat(id) {
    editId = id; document.getElementById('modalTitle').textContent = 'Edit Category';
    const data = await apiFetch(`${API}/${id}`);
    document.getElementById('name').value = data.name || '';
    document.getElementById('description').value = data.description || '';
    document.getElementById('icon').value = data.icon || '';
    document.getElementById('sort_order').value = data.sort_order || 0;
    document.getElementById('is_active').checked = data.is_active;
    openModal('catModal');
}

async function saveCategory() {
    const body = { name: document.getElementById('name').value, description: document.getElementById('description').value, icon: document.getElementById('icon').value, sort_order: document.getElementById('sort_order').value, is_active: document.getElementById('is_active').checked };
    try {
        if (editId) { await apiFetch(`${API}/${editId}`, { method: 'PUT', body }); }
        else { await apiFetch(API, { method: 'POST', body }); }
        closeModal('catModal');
        Swal.fire({ icon: 'success', title: 'Saved!', timer: 2000, showConfirmButton: false });
        loadList();
    } catch (e) { Swal.fire({ icon: 'error', title: 'Error', text: e.data?.message || 'Something went wrong' }); }
}

function deleteCat(id, name) {
    Swal.fire({ title: 'Delete?', text: `Delete ${name}?`, icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc2626', confirmButtonText: 'Delete' })
    .then(async (r) => { if (r.isConfirmed) { await apiFetch(`${API}/${id}`, { method: 'DELETE' }); Swal.fire({ icon: 'success', title: 'Deleted!', timer: 2000, showConfirmButton: false }); loadList(); }});
}

loadList();
@endsection
