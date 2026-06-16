@extends('admin.layouts.app')
@section('page_title', 'Staff Management')
@section('content')
<div class="page-card">
    <div class="card-header">
        <div class="card-title">All Staff Members</div>
        <div class="filters-row">
            <div class="search-wrap">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" id="searchInput" placeholder="Search staff..." oninput="loadList()">
            </div>
            <select class="form-control" style="width:auto;padding:0.45rem 0.75rem;font-size:0.78rem;" id="statusFilter" onchange="loadList()">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="terminated">Terminated</option>
            </select>
            <button class="btn btn-success" onclick="openAddModal()">+ Add Staff</button>
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead><tr>
                <th>Name</th><th>Email</th><th>Phone</th><th>Department</th><th>Position</th><th>Salary</th><th>Status</th><th>Actions</th>
            </tr></thead>
            <tbody id="tableBody"><tr><td colspan="8" class="tbl-empty">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="staffModal">
    <div class="modal" style="max-width:640px;">
        <div class="modal-header">
            <div class="modal-title" id="modalTitle">Add Staff</div>
            <button class="modal-close" onclick="closeModal('staffModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="staffForm">
                <div class="form-row">
                    <div class="form-group">
                        <label>First Name *</label>
                        <input type="text" class="form-control" id="first_name" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label>Last Name *</label>
                        <input type="text" class="form-control" id="last_name" required>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" class="form-control" id="email" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" class="form-control" id="phone">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Department</label>
                        <input type="text" class="form-control" id="department">
                    </div>
                    <div class="form-group">
                        <label>Position</label>
                        <input type="text" class="form-control" id="position">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Salary</label>
                        <input type="number" step="0.01" class="form-control" id="salary">
                    </div>
                    <div class="form-group">
                        <label>Pay Type</label>
                        <select class="form-control" id="pay_type">
                            <option value="monthly">Monthly</option>
                            <option value="weekly">Weekly</option>
                            <option value="daily">Daily</option>
                            <option value="hourly">Hourly</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Hire Date</label>
                        <input type="date" class="form-control" id="hire_date">
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" id="status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="terminated">Terminated</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <textarea class="form-control" id="address" rows="2"></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Emergency Contact</label>
                        <input type="text" class="form-control" id="emergency_contact">
                    </div>
                    <div class="form-group">
                        <label>Emergency Phone</label>
                        <input type="text" class="form-control" id="emergency_phone">
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('staffModal')">Cancel</button>
            <button class="btn btn-primary" id="saveBtn" onclick="saveStaff()">Save Staff</button>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
const API = '/api/admin/staff';
let editId = null;

async function loadList() {
    const search = document.getElementById('searchInput').value;
    const status = document.getElementById('statusFilter').value;
    const params = new URLSearchParams({search, status});
    const data = await apiFetch(`${API}?${params}`);
    const tbody = document.getElementById('tableBody');
    if (!data.length) { tbody.innerHTML = '<tr><td colspan="8" class="tbl-empty">No staff found</td></tr>'; return; }
    tbody.innerHTML = data.map(s => `<tr>
        <td><strong>${s.full_name}</strong></td>
        <td>${s.email}</td>
        <td>${s.phone || '-'}</td>
        <td>${s.department || '-'}</td>
        <td>${s.position || '-'}</td>
        <td>TSh ${s.salary}</td>
        <td><span class="badge ${s.status === 'active' ? 'badge-success' : s.status === 'inactive' ? 'badge-pending' : 'badge-danger'}">${s.status}</span></td>
        <td class="actions-cell">
            <button class="btn btn-primary btn-xs" onclick="editStaff(${s.id})">Edit</button>
            <button class="btn btn-danger btn-xs" onclick="deleteStaff(${s.id},'${s.full_name}')">Delete</button>
        </td>
    </tr>`).join('');
}

async function openAddModal() {
    editId = null; document.getElementById('modalTitle').textContent = 'Add Staff';
    document.getElementById('staffForm').reset();
    document.getElementById('saveBtn').textContent = 'Save Staff';
    openModal('staffModal');
}

async function editStaff(id) {
    editId = id; document.getElementById('modalTitle').textContent = 'Edit Staff';
    document.getElementById('saveBtn').textContent = 'Update Staff';
    const data = await apiFetch(`${API}/${id}`);
    document.getElementById('first_name').value = data.first_name || '';
    document.getElementById('last_name').value = data.last_name || '';
    document.getElementById('email').value = data.email || '';
    document.getElementById('phone').value = data.phone || '';
    document.getElementById('department').value = data.department || '';
    document.getElementById('position').value = data.position || '';
    document.getElementById('salary').value = data.salary || '';
    document.getElementById('pay_type').value = data.pay_type || 'monthly';
    document.getElementById('hire_date').value = data.hire_date || '';
    document.getElementById('status').value = data.status || 'active';
    document.getElementById('address').value = data.address || '';
    document.getElementById('emergency_contact').value = data.emergency_contact || '';
    document.getElementById('emergency_phone').value = data.emergency_phone || '';
    openModal('staffModal');
}

async function saveStaff() {
    const body = {
        first_name: document.getElementById('first_name').value,
        last_name: document.getElementById('last_name').value,
        email: document.getElementById('email').value,
        phone: document.getElementById('phone').value,
        department: document.getElementById('department').value,
        position: document.getElementById('position').value,
        salary: document.getElementById('salary').value,
        pay_type: document.getElementById('pay_type').value,
        hire_date: document.getElementById('hire_date').value,
        status: document.getElementById('status').value,
        address: document.getElementById('address').value,
        emergency_contact: document.getElementById('emergency_contact').value,
        emergency_phone: document.getElementById('emergency_phone').value,
    };
    try {
        if (editId) { await apiFetch(`${API}/${editId}`, { method: 'PUT', body }); }
        else { await apiFetch(API, { method: 'POST', body }); }
        closeModal('staffModal');
        Swal.fire({ icon: 'success', title: 'Success!', text: editId ? 'Staff updated successfully' : 'Staff added successfully', timer: 2000, showConfirmButton: false });
        loadList();
    } catch (e) {
        if (e.data && e.data.errors) {
            for (const [field, msgs] of Object.entries(e.data.errors)) {
                const el = document.getElementById(field);
                if (el) { el.classList.add('is-invalid'); el.nextElementSibling.textContent = msgs[0]; }
            }
        }
        Swal.fire({ icon: 'error', title: 'Error', text: e.data?.message || 'Something went wrong' });
    }
}

function deleteStaff(id, name) {
    Swal.fire({
        title: 'Delete Staff?', text: `Are you sure you want to delete ${name}?`, icon: 'warning',
        showCancelButton: true, confirmButtonColor: '#dc2626', confirmButtonText: 'Yes, delete!'
    }).then(async (result) => {
        if (result.isConfirmed) {
            await apiFetch(`${API}/${id}`, { method: 'DELETE' });
            Swal.fire({ icon: 'success', title: 'Deleted!', text: 'Staff has been deleted.', timer: 2000, showConfirmButton: false });
            loadList();
        }
    });
}

loadList();
</script>
@endsection
