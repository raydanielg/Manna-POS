@extends('admin.layouts.app')
@section('page_title', 'Staff Attendance')
@section('content')
<div class="page-card">
    <div class="card-header">
        <div class="card-title">Staff Attendance</div>
        <div class="filters-row">
            <input type="date" class="form-control" style="width:auto;padding:0.45rem 0.75rem;font-size:0.78rem;" id="dateFilter" onchange="loadList()">
            <select class="form-control" style="width:auto;padding:0.45rem 0.75rem;font-size:0.78rem;" id="statusFilter" onchange="loadList()">
                <option value="">All Status</option>
                <option value="present">Present</option>
                <option value="absent">Absent</option>
                <option value="late">Late</option>
                <option value="half-day">Half Day</option>
                <option value="leave">On Leave</option>
            </select>
            <button class="btn btn-success" onclick="openAddModal()">+ Mark Attendance</button>
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead><tr><th>Staff</th><th>Date</th><th>Clock In</th><th>Clock Out</th><th>Status</th><th>Notes</th><th>Actions</th></tr></thead>
            <tbody id="tableBody"><tr><td colspan="7" class="tbl-empty">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="attModal">
    <div class="modal" style="max-width:500px;">
        <div class="modal-header">
            <div class="modal-title" id="modalTitle">Mark Attendance</div>
            <button class="modal-close" onclick="closeModal('attModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="attForm">
                <div class="form-group">
                    <label>Staff Member *</label>
                    <select class="form-control" id="staff_id" required></select>
                </div>
                <div class="form-group">
                    <label>Date *</label>
                    <input type="date" class="form-control" id="date" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Clock In</label>
                        <input type="time" class="form-control" id="clock_in">
                    </div>
                    <div class="form-group">
                        <label>Clock Out</label>
                        <input type="time" class="form-control" id="clock_out">
                    </div>
                </div>
                <div class="form-group">
                    <label>Status *</label>
                    <select class="form-control" id="status">
                        <option value="present">Present</option>
                        <option value="absent">Absent</option>
                        <option value="late">Late</option>
                        <option value="half-day">Half Day</option>
                        <option value="leave">On Leave</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Notes</label>
                    <textarea class="form-control" id="notes" rows="2"></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('attModal')">Cancel</button>
            <button class="btn btn-primary" onclick="saveAttendance()">Save</button>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
const API = '/api/admin/staff';
let editId = null, attId = null;

async function loadList() {
    const date = document.getElementById('dateFilter').value;
    const status = document.getElementById('statusFilter').value;
    const params = new URLSearchParams({date, status});
    const data = await apiFetch(`${API}/attendance?${params}`);
    const tbody = document.getElementById('tableBody');
    if (!data.length) { tbody.innerHTML = '<tr><td colspan="7" class="tbl-empty">No records found</td></tr>'; return; }
    tbody.innerHTML = data.map(a => `<tr>
        <td><strong>${a.staff?.first_name || ''} ${a.staff?.last_name || ''}</strong></td>
        <td>${a.date}</td>
        <td>${a.clock_in || '-'}</td>
        <td>${a.clock_out || '-'}</td>
        <td><span class="badge ${a.status === 'present' ? 'badge-success' : a.status === 'absent' ? 'badge-danger' : a.status === 'late' ? 'badge-pending' : 'badge-info'}">${a.status}</span></td>
        <td>${a.notes || '-'}</td>
        <td class="actions-cell">
            <button class="btn btn-danger btn-xs" onclick="deleteAtt(${a.id})">Delete</button>
        </td>
    </tr>`).join('');
}

async function openAddModal() {
    editId = null; document.getElementById('modalTitle').textContent = 'Mark Attendance';
    document.getElementById('attForm').reset();
    document.getElementById('date').value = new Date().toISOString().split('T')[0];
    const staff = await apiFetch(API + '?status=active');
    const sel = document.getElementById('staff_id');
    sel.innerHTML = '<option value="">Select Staff</option>' + staff.map(s => `<option value="${s.id}">${s.full_name}</option>`).join('');
    openModal('attModal');
}

async function saveAttendance() {
    const body = {
        staff_id: document.getElementById('staff_id').value,
        date: document.getElementById('date').value,
        clock_in: document.getElementById('clock_in').value,
        clock_out: document.getElementById('clock_out').value,
        status: document.getElementById('status').value,
        notes: document.getElementById('notes').value,
    };
    try {
        await apiFetch(`${API}/attendance`, { method: 'POST', body });
        closeModal('attModal');
        Swal.fire({ icon: 'success', title: 'Saved!', timer: 2000, showConfirmButton: false });
        loadList();
    } catch (e) {
        Swal.fire({ icon: 'error', title: 'Error', text: e.data?.message || 'Something went wrong' });
    }
}

function deleteAtt(id) {
    Swal.fire({ title: 'Delete?', text: 'Remove this attendance record?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc2626', confirmButtonText: 'Delete' })
    .then(async (r) => { if (r.isConfirmed) { await apiFetch(`${API}/attendance/${id}`, { method: 'DELETE' }); Swal.fire({ icon: 'success', title: 'Deleted!', timer: 2000, showConfirmButton: false }); loadList(); }});
}

document.getElementById('dateFilter').value = new Date().toISOString().split('T')[0];
loadList();
</script>
@endsection
