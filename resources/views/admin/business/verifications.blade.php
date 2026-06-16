@extends('admin.layouts.app')
@section('page_title', 'Business Verifications')
@section('content')
<div class="page-card">
    <div class="card-header">
        <div class="card-title">Document Verifications</div>
        <select class="form-control" style="width:auto;padding:0.45rem 0.75rem;font-size:0.78rem;" id="statusFilter" onchange="loadList()">
            <option value="">All</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
        </select>
    </div>
    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead><tr><th>Business</th><th>Document Type</th><th>Status</th><th>Submitted</th><th>Actions</th></tr></thead>
            <tbody id="tableBody"><tr><td colspan="5" class="tbl-empty">Loading...</td></tr></tbody>
        </table>
    </div>
</div>
@endsection
@section('scripts')
const API = '/api/admin/business/verifications';

async function loadList() {
    const status = document.getElementById('statusFilter').value;
    const data = await apiFetch(`${API}?status=${status}`);
    const tbody = document.getElementById('tableBody');
    if (!data.length) { tbody.innerHTML = '<tr><td colspan="5" class="tbl-empty">No verifications</td></tr>'; return; }
    tbody.innerHTML = data.map(v => `<tr>
        <td><strong>${v.business?.business_name || 'N/A'}</strong></td>
        <td>${v.document_type}</td>
        <td><span class="badge ${v.status === 'approved' ? 'badge-success' : v.status === 'rejected' ? 'badge-danger' : 'badge-pending'}">${v.status}</span></td>
        <td>${v.created_at ? new Date(v.created_at).toLocaleDateString() : '-'}</td>
        <td class="actions-cell">
            ${v.status === 'pending' ? `
                <button class="btn btn-success btn-xs" onclick="approve(${v.id})">Approve</button>
                <button class="btn btn-danger btn-xs" onclick="reject(${v.id})">Reject</button>
            ` : `<span class="text-xs text-slate-400">${v.reviewed_by ? 'Reviewed' : 'N/A'}</span>`}
        </td>
    </tr>`).join('');
}

async function approve(id) {
    await apiFetch(`${API}/${id}/approve`, { method: 'POST' });
    Swal.fire({ icon: 'success', title: 'Approved!', timer: 2000, showConfirmButton: false });
    loadList();
}

function reject(id) {
    Swal.fire({ title: 'Reject?', text: 'Reject this verification?', icon: 'warning', input: 'textarea', inputPlaceholder: 'Reason (optional)', showCancelButton: true, confirmButtonColor: '#dc2626', confirmButtonText: 'Reject' })
    .then(async (r) => { if (r.isConfirmed) { await apiFetch(`${API}/${id}/reject`, { method: 'POST', body: { notes: r.value } }); Swal.fire({ icon: 'success', title: 'Rejected!' }); loadList(); }});
}

loadList();
@endsection
