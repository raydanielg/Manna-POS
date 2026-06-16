@extends('admin.layouts.app')
@section('page_title', 'Push Notifications')
@section('content')
<div class="page-card">
    <div class="card-header">
        <div class="card-title">Push Notifications</div>
        <div class="filters-row">
            <button class="btn btn-success" onclick="openSendModal()">+ Send Push</button>
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead><tr><th>Title</th><th>Message</th><th>Target</th><th>Sent Date</th><th>Status</th></tr></thead>
            <tbody id="tableBody"><tr><td colspan="5" class="tbl-empty">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="pushModal">
    <div class="modal" style="max-width:500px;">
        <div class="modal-header">
            <div class="modal-title">Send Push Notification</div>
            <button class="modal-close" onclick="closeModal('pushModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="pushForm">
                <div class="form-group">
                    <label>Title *</label>
                    <input type="text" class="form-control" id="pushTitle" required>
                </div>
                <div class="form-group">
                    <label>Message *</label>
                    <textarea class="form-control" id="pushMessage" rows="4" required></textarea>
                </div>
                <div class="form-group">
                    <label>Target Audience</label>
                    <select class="form-control" id="pushTarget">
                        <option value="all">All Users</option>
                        <option value="users">Users Only</option>
                        <option value="admins">Admins Only</option>
                    </select>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('pushModal')">Cancel</button>
            <button class="btn btn-primary" onclick="sendPush()">Send</button>
        </div>
    </div>
</div>
@endsection
@section('scripts')
const API = '/api/admin/communication/push';

async function loadList() {
    const data = await apiFetch(API);
    const tbody = document.getElementById('tableBody');
    if (!data.length) { tbody.innerHTML = '<tr><td colspan="5" class="tbl-empty">No push notifications</td></tr>'; return; }
    tbody.innerHTML = data.map(p => `<tr>
        <td><strong>${p.title || '-'}</strong></td>
        <td>${p.message || '-'}</td>
        <td><span class="badge ${p.target === 'all' ? 'badge-info' : p.target === 'admins' ? 'badge-danger' : 'badge-success'}">${p.target || '-'}</span></td>
        <td>${p.sent_date || p.created_at ? new Date(p.sent_date || p.created_at).toLocaleDateString() : '-'}</td>
        <td><span class="badge ${p.status === 'sent' ? 'badge-success' : p.status === 'failed' ? 'badge-danger' : 'badge-pending'}">${p.status || 'pending'}</span></td>
    </tr>`).join('');
}

function openSendModal() {
    document.getElementById('pushForm').reset();
    openModal('pushModal');
}

async function sendPush() {
    const title = document.getElementById('pushTitle').value.trim();
    const message = document.getElementById('pushMessage').value.trim();
    const target = document.getElementById('pushTarget').value;
    if (!title || !message) { Swal.fire({ icon: 'error', title: 'Required', text: 'Title and message are required' }); return; }
    Swal.fire({
        title: 'Send Push?',
        text: `Send "${title}" to ${target}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#e03057',
        confirmButtonText: 'Send'
    }).then(async (r) => {
        if (!r.isConfirmed) return;
        try {
            await apiFetch(API, { method: 'POST', body: { title, message, target } });
            closeModal('pushModal');
            Swal.fire({ icon: 'success', title: 'Push sent!', timer: 2000, showConfirmButton: false });
            loadList();
        } catch (e) { Swal.fire({ icon: 'error', title: 'Error', text: e.data?.message || 'Something went wrong' }); }
    });
}

loadList();
@endsection
