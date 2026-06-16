@extends('admin.layouts.app')
@section('page_title', 'Support Tickets')
@section('content')
<div class="page-card">
    <div class="card-header">
        <div class="card-title">Support Tickets</div>
        <div class="filters-row">
            <select class="form-control" style="width:auto;padding:0.45rem 0.75rem;font-size:0.78rem;" id="statusFilter" onchange="loadList()">
                <option value="">All</option>
                <option value="open">Open</option>
                <option value="in_progress">In Progress</option>
                <option value="resolved">Resolved</option>
                <option value="closed">Closed</option>
            </select>
            <select class="form-control" style="width:auto;padding:0.45rem 0.75rem;font-size:0.78rem;" id="priorityFilter" onchange="loadList()">
                <option value="">All Priorities</option>
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
                <option value="urgent">Urgent</option>
            </select>
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead><tr><th>Ticket #</th><th>Subject</th><th>Priority</th><th>Status</th><th>Assigned To</th><th>Created</th><th>Actions</th></tr></thead>
            <tbody id="tableBody"><tr><td colspan="7" class="tbl-empty">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="ticketModal">
    <div class="modal" style="max-width:700px;">
        <div class="modal-header">
            <div class="modal-title" id="modalTitle">Ticket Details</div>
            <button class="modal-close" onclick="closeModal('ticketModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="ticketForm">
                <div class="form-row">
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" id="status">
                            <option value="open">Open</option>
                            <option value="in_progress">In Progress</option>
                            <option value="resolved">Resolved</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Priority</label>
                        <select class="form-control" id="priority">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Assigned To (Staff ID)</label>
                    <input type="number" class="form-control" id="assigned_to" placeholder="Optional">
                </div>
                <div class="form-group">
                    <label>Admin Note</label>
                    <textarea class="form-control" id="note" rows="3" placeholder="Internal note"></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('ticketModal')">Cancel</button>
            <button class="btn btn-primary" onclick="updateTicket()">Update</button>
        </div>
    </div>
</div>
@endsection
@section('scripts')
const API = '/api/admin/support/tickets';

async function loadList() {
    const status = document.getElementById('statusFilter').value;
    const priority = document.getElementById('priorityFilter').value;
    const data = await apiFetch(`${API}?status=${status}&priority=${priority}`);
    const tbody = document.getElementById('tableBody');
    if (!data.length) { tbody.innerHTML = '<tr><td colspan="7" class="tbl-empty">No tickets</td></tr>'; return; }
    tbody.innerHTML = data.map(t => `<tr>
        <td><code>#${t.id}</code></td>
        <td><strong>${t.subject}</strong></td>
        <td><span class="badge ${t.priority === 'urgent' ? 'badge-danger' : t.priority === 'high' ? 'badge-pending' : 'badge-info'}">${t.priority}</span></td>
        <td><span class="badge ${t.status === 'open' ? 'badge-pending' : t.status === 'in_progress' ? 'badge-info' : t.status === 'resolved' ? 'badge-success' : 'badge-default'}">${t.status}</span></td>
        <td>${t.assigned_to || 'Unassigned'}</td>
        <td>${new Date(t.created_at).toLocaleDateString()}</td>
        <td class="actions-cell">
            <button class="btn btn-primary btn-xs" onclick="editTicket(${t.id})">Manage</button>
            <button class="btn btn-danger btn-xs" onclick="deleteTicket(${t.id})">Delete</button>
        </td>
    </tr>`).join('');
}

async function editTicket(id) {
    const data = await apiFetch(`${API}/${id}`);
    document.getElementById('status').value = data.status || 'open';
    document.getElementById('priority').value = data.priority || 'medium';
    document.getElementById('assigned_to').value = data.assigned_to || '';
    document.getElementById('note').value = data.note || '';
    document.getElementById('ticketForm').dataset.id = id;
    document.getElementById('modalTitle').textContent = `Ticket #${id} - ${data.subject}`;
    openModal('ticketModal');
}

async function updateTicket() {
    const id = document.getElementById('ticketForm').dataset.id;
    const body = { status: document.getElementById('status').value, priority: document.getElementById('priority').value, assigned_to: document.getElementById('assigned_to').value || null, note: document.getElementById('note').value };
    try {
        await apiFetch(`${API}/${id}`, { method: 'PUT', body });
        closeModal('ticketModal'); Swal.fire({ icon: 'success', title: 'Updated!', timer: 2000, showConfirmButton: false }); loadList();
    } catch (e) { Swal.fire({ icon: 'error', title: 'Error', text: e.data?.message || 'Something went wrong' }); }
}

function deleteTicket(id) {
    Swal.fire({ title: 'Delete?', text: 'Delete this ticket?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc2626', confirmButtonText: 'Delete' })
    .then(async (r) => { if (r.isConfirmed) { await apiFetch(`${API}/${id}`, { method: 'DELETE' }); Swal.fire({ icon: 'success', title: 'Deleted!', timer: 2000, showConfirmButton: false }); loadList(); }});
}

loadList();
@endsection
