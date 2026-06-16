@extends('admin.layouts.app')
@section('page_title', 'Staff Performance')
@section('content')
<div class="page-card">
    <div class="card-header">
        <div class="card-title">Staff Performance</div>
        <div class="filters-row">
            <select class="form-control" style="width:auto;padding:0.45rem 0.75rem;font-size:0.78rem;" id="departmentFilter" onchange="loadList()">
                <option value="">All Departments</option>
            </select>
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead><tr><th>Staff Name</th><th>Department</th><th>Attendance Rate</th><th>Tasks Completed</th><th>Rating</th><th>Actions</th></tr></thead>
            <tbody id="tableBody"><tr><td colspan="6" class="tbl-empty">Loading...</td></tr></tbody>
        </table>
    </div>
</div>
@endsection
@section('scripts')
const API = '/api/admin/staff/performance';

async function loadList() {
    const department = document.getElementById('departmentFilter').value;
    const data = await apiFetch(`${API}?department=${department}`);
    const tbody = document.getElementById('tableBody');
    const filter = document.getElementById('departmentFilter');
    if (!data.length && !department) { tbody.innerHTML = '<tr><td colspan="6" class="tbl-empty">No performance data</td></tr>'; return; }
    const departments = [...new Set(data.map(s => s.department).filter(Boolean))];
    const currentVal = filter.value;
    filter.innerHTML = '<option value="">All Departments</option>' + departments.map(d => `<option value="${d}">${d}</option>`).join('');
    filter.value = currentVal || '';
    tbody.innerHTML = data.map(s => `<tr>
        <td><strong>${s.staff_name}</strong></td>
        <td>${s.department || '-'}</td>
        <td><span class="badge ${parseFloat(s.attendance_rate) >= 80 ? 'badge-success' : parseFloat(s.attendance_rate) >= 50 ? 'badge-pending' : 'badge-danger'}">${s.attendance_rate || 0}%</span></td>
        <td>${s.tasks_completed ?? 0}</td>
        <td>
            <span class="badge ${parseFloat(s.rating) >= 4 ? 'badge-success' : parseFloat(s.rating) >= 2.5 ? 'badge-pending' : 'badge-danger'}">${s.rating ?? 'N/A'}</span>
        </td>
        <td class="actions-cell">
            <button class="btn btn-primary btn-xs" onclick="viewDetails(${s.id})">View</button>
        </td>
    </tr>`).join('');
}

function viewDetails(id) {
    Swal.fire({ title: 'Performance Details', text: 'Loading...', didOpen: async (popup) => {
        try {
            const data = await apiFetch(`${API}/${id}`);
            popup.setTitle(data.staff_name);
            popup.setHtml(`
                <div style="text-align:left;">
                    <p><strong>Department:</strong> ${data.department || '-'}</p>
                    <p><strong>Attendance Rate:</strong> ${data.attendance_rate || 0}%</p>
                    <p><strong>Tasks Completed:</strong> ${data.tasks_completed ?? 0}</p>
                    <p><strong>Rating:</strong> ${data.rating || 'N/A'}</p>
                    ${data.notes ? `<p><strong>Notes:</strong> ${data.notes}</p>` : ''}
                </div>
            `);
            popup.setConfirmButtonText('Close');
        } catch { popup.setHtml('Failed to load details'); }
    }, showConfirmButton: true });
}

loadList();
@endsection