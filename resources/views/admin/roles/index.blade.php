@extends('admin.layouts.app')
@section('page_title', 'Roles & Permissions')
@section('content')
<div class="page-card">
    <div class="card-header">
        <div class="card-title">Roles & Permissions</div>
        <div class="filters-row">
            <div class="search-wrap">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                <input type="text" id="searchInput" placeholder="Search roles..." oninput="loadList()">
            </div>
            <button class="btn btn-success" onclick="openAddModal()">+ Add Role</button>
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead><tr><th>#</th><th>Role Name</th><th>Description</th><th>Permissions</th><th>Actions</th></tr></thead>
            <tbody id="tableBody"><tr><td colspan="5" class="tbl-empty">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="modal">
    <div class="modal" style="max-width:720px;">
        <div class="modal-header">
            <div class="modal-title" id="modal-title">Add Role</div>
            <button class="modal-close" onclick="closeModal('modal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="itemForm">
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Role Name *</label><input name="name" class="form-control" required placeholder="e.g. Manager"><div class="invalid-feedback"></div></div>
                    <div class="form-group"><label class="form-label">Description</label><input name="description" class="form-control" placeholder="Role description"><div class="invalid-feedback"></div></div>
                </div>
                <div class="form-group">
                    <label class="form-label">Permissions</label>
                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:0.5rem;" id="permGrid">
                        @foreach(['admin.access','sales.view','sales.create','sales.edit','sales.delete','purchases.view','purchases.create','purchases.edit','purchases.delete','inventory.view','inventory.create','inventory.edit','inventory.delete','customers.view','customers.manage','suppliers.view','suppliers.manage','reports.view','settings.manage','users.manage','expenses.view','expenses.manage'] as $perm)
                        <label style="display:flex;align-items:center;gap:0.4rem;font-size:0.8rem;cursor:pointer;">
                            <input type="checkbox" name="permissions[]" value="{{ $perm }}" style="width:14px;height:14px;">
                            {{ $perm }}
                        </label>
                        @endforeach
                    </div>
                    <div class="invalid-feedback"></div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('modal')">Cancel</button>
            <button class="btn btn-primary" id="saveBtn" onclick="saveItem()">Save Role</button>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
const API = '/api/admin/roles';
let editId = null;

async function loadList() {
    const s = document.getElementById('searchInput').value;
    const tbody = document.getElementById('tableBody');
    tbody.innerHTML = '<tr><td colspan="5" class="tbl-empty">Loading...</td></tr>';
    try {
        const items = await apiFetch(`${API}?search=${encodeURIComponent(s)}`);
        if (!items.length) { tbody.innerHTML = '<tr><td colspan="5" class="tbl-empty">No roles found.</td></tr>'; return; }
        tbody.innerHTML = items.map((r, i) => {
            const perms = Array.isArray(r.permissions) ? r.permissions : (r.permissions ? JSON.parse(r.permissions) : []);
            return `<tr>
                <td class="text-slate-400">${i + 1}</td>
                <td class="font-semibold">${r.name}</td>
                <td class="text-slate-500">${r.description || '-'}</td>
                <td><span class="badge badge-info">${perms.length} permission${perms.length !== 1 ? 's' : ''}</span></td>
                <td><div style="display:flex;gap:0.4rem;">
                    <button class="btn btn-primary btn-xs" onclick="editItem(${r.id})">Edit</button>
                    <button class="btn btn-danger btn-xs" onclick="deleteItem(${r.id},'${r.name}')">Delete</button>
                </div></td>
            </tr>`;
        }).join('');
    } catch (e) {
        tbody.innerHTML = '<tr><td colspan="5" class="tbl-empty">Error loading data.</td></tr>';
    }
}

function openAddModal() {
    editId = null;
    document.getElementById('modal-title').textContent = 'Add Role';
    document.getElementById('itemForm').reset();
    document.querySelectorAll('#permGrid input[type=checkbox]').forEach(c => c.checked = false);
    clearFormErrors('itemForm');
    openModal('modal');
}

async function editItem(id) {
    try {
        const r = await apiFetch(`${API}/${id}`);
        editId = id;
        document.getElementById('modal-title').textContent = 'Edit Role';
        const form = document.getElementById('itemForm');
        form.querySelector('[name="name"]').value = r.name || '';
        form.querySelector('[name="description"]').value = r.description || '';
        const perms = Array.isArray(r.permissions) ? r.permissions : (r.permissions ? JSON.parse(r.permissions) : []);
        document.querySelectorAll('#permGrid input[type=checkbox]').forEach(c => { c.checked = perms.includes(c.value); });
        clearFormErrors('itemForm');
        openModal('modal');
    } catch (e) { Swal.fire('Error', 'Failed to load role', 'error'); }
}

async function saveItem() {
    clearFormErrors('itemForm');
    const name = document.querySelector('#itemForm [name="name"]').value;
    const desc = document.querySelector('#itemForm [name="description"]').value;
    const perms = Array.from(document.querySelectorAll('#permGrid input[type=checkbox]:checked')).map(c => c.value);
    const data = { name, description: desc, permissions: perms };
    const btn = document.getElementById('saveBtn');
    btn.disabled = true;
    btn.textContent = 'Saving...';
    try {
        if (editId) await apiFetch(`${API}/${editId}`, { method: 'PUT', body: JSON.stringify(data) });
        else await apiFetch(API, { method: 'POST', body: JSON.stringify(data) });
        closeModal('modal');
        Swal.fire('Success', editId ? 'Role updated!' : 'Role added!', 'success');
        loadList();
    } catch (e) {
        if (e.errors) showFormErrors('itemForm', e.errors);
        else Swal.fire('Error', e.message || 'Save failed', 'error');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Save Role';
    }
}

function deleteItem(id, name) {
    Swal.fire({
        title: 'Delete Role',
        text: `Delete role "${name}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Delete',
    }).then(async (result) => {
        if (!result.isConfirmed) return;
        try {
            await apiFetch(`${API}/${id}`, { method: 'DELETE' });
            Swal.fire('Deleted', 'Role deleted!', 'success');
            loadList();
        } catch (e) { Swal.fire('Error', e.message || 'Delete failed', 'error'); }
    });
}

loadList();
</script>
@endsection
