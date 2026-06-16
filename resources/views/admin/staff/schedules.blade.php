@extends('admin.layouts.app')
@section('page_title', 'Staff Schedules')
@section('content')
<div class="page-card">
    <div class="card-header">
        <div class="card-title">Staff Work Schedules</div>
        <div class="filters-row">
            <select class="form-control" style="width:auto;padding:0.45rem 0.75rem;font-size:0.78rem;" id="staffFilter" onchange="loadList()">
                <option value="">All Staff</option>
            </select>
            <button class="btn btn-success" onclick="openAddModal()">+ Add Schedule</button>
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead><tr><th>Staff</th><th>Day</th><th>Start</th><th>End</th><th>Working Day</th><th>Actions</th></tr></thead>
            <tbody id="tableBody"><tr><td colspan="6" class="tbl-empty">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="schedModal">
    <div class="modal" style="max-width:500px;">
        <div class="modal-header">
            <div class="modal-title">Add Schedule</div>
            <button class="modal-close" onclick="closeModal('schedModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="schedForm">
                <div class="form-group">
                    <label>Staff *</label>
                    <select class="form-control" id="sched_staff_id" required></select>
                </div>
                <div class="form-group">
                    <label>Day of Week *</label>
                    <select class="form-control" id="day_of_week">
                        <option value="Monday">Monday</option>
                        <option value="Tuesday">Tuesday</option>
                        <option value="Wednesday">Wednesday</option>
                        <option value="Thursday">Thursday</option>
                        <option value="Friday">Friday</option>
                        <option value="Saturday">Saturday</option>
                        <option value="Sunday">Sunday</option>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Start Time *</label>
                        <input type="time" class="form-control" id="start_time" required>
                    </div>
                    <div class="form-group">
                        <label>End Time *</label>
                        <input type="time" class="form-control" id="end_time" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" id="is_working_day" checked>
                        <span style="font-size:0.82rem;">Working Day</span>
                    </label>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('schedModal')">Cancel</button>
            <button class="btn btn-primary" onclick="saveSchedule()">Save</button>
        </div>
    </div>
</div>
@endsection
@section('scripts')
const API = '/api/admin/staff';
let editSchedId = null;

async function init() {
    const staff = await apiFetch(API + '?status=active');
    const sel = document.getElementById('staffFilter');
    sel.innerHTML = '<option value="">All Staff</option>' + staff.map(s => `<option value="${s.id}">${s.full_name}</option>`).join('');
    document.getElementById('sched_staff_id').innerHTML = '<option value="">Select</option>' + staff.map(s => `<option value="${s.id}">${s.full_name}</option>`).join('');
    loadList();
}

async function loadList() {
    const staff_id = document.getElementById('staffFilter').value;
    const data = await apiFetch(`${API}/schedules?staff_id=${staff_id}`);
    const tbody = document.getElementById('tableBody');
    if (!data.length) { tbody.innerHTML = '<tr><td colspan="6" class="tbl-empty">No schedules found</td></tr>'; return; }
    tbody.innerHTML = data.map(s => `<tr>
        <td><strong>${s.staff?.first_name || ''} ${s.staff?.last_name || ''}</strong></td>
        <td>${s.day_of_week}</td>
        <td>${s.start_time}</td>
        <td>${s.end_time}</td>
        <td>${s.is_working_day ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>'}</td>
        <td class="actions-cell">
            <button class="btn btn-danger btn-xs" onclick="deleteSched(${s.id})">Delete</button>
        </td>
    </tr>`).join('');
}

async function openAddModal() {
    document.getElementById('schedForm').reset();
    openModal('schedModal');
}

async function saveSchedule() {
    const body = {
        staff_id: document.getElementById('sched_staff_id').value,
        day_of_week: document.getElementById('day_of_week').value,
        start_time: document.getElementById('start_time').value,
        end_time: document.getElementById('end_time').value,
        is_working_day: document.getElementById('is_working_day').checked,
    };
    try {
        await apiFetch(`${API}/schedules`, { method: 'POST', body });
        closeModal('schedModal');
        Swal.fire({ icon: 'success', title: 'Saved!', timer: 2000, showConfirmButton: false });
        loadList();
    } catch (e) { Swal.fire({ icon: 'error', title: 'Error', text: e.data?.message || 'Something went wrong' }); }
}

function deleteSched(id) {
    Swal.fire({ title: 'Delete?', text: 'Remove this schedule?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc2626', confirmButtonText: 'Delete' })
    .then(async (r) => { if (r.isConfirmed) { await apiFetch(`${API}/schedules/${id}`, { method: 'DELETE' }); Swal.fire({ icon: 'success', title: 'Deleted!', timer: 2000, showConfirmButton: false }); loadList(); }});
}

init();
@endsection
