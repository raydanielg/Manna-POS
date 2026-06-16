@extends('admin.layouts.app')
@section('page_title', 'Pending Approvals')
@section('content')
<div class="page-card">
    <div class="card-header">
        <div class="card-title">Pending Business Approvals</div>
    </div>
    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead><tr><th>Business</th><th>Owner</th><th>Submitted</th><th>Documents</th><th>Actions</th></tr></thead>
            <tbody id="tableBody"><tr><td colspan="5" class="tbl-empty">Loading...</td></tr></tbody>
        </table>
    </div>
</div>
@endsection
@section('scripts')
<script>
const API = '/api/admin/business';

async function loadList() {
    const data = await apiFetch(`${API}?status=pending`);
    const tbody = document.getElementById('tableBody');
    if (!data.length) { tbody.innerHTML = '<tr><td colspan="5" class="tbl-empty">No pending approvals</td></tr>'; return; }
    tbody.innerHTML = data.map(b => `<tr>
        <td><strong>${b.business_name}</strong></td>
        <td>${b.owner || '-'}</td>
        <td>${b.created_at ? new Date(b.created_at).toLocaleDateString() : '-'}</td>
        <td>${b.documents_count ?? 0}</td>
        <td class="actions-cell">
            <button class="btn btn-success btn-xs" onclick="approveBiz(${b.id},'${b.business_name}')">Approve</button>
            <button class="btn btn-danger btn-xs" onclick="rejectBiz(${b.id},'${b.business_name}')">Reject</button>
        </td>
    </tr>`).join('');
}

function approveBiz(id, name) {
    Swal.fire({
        title: 'Approve Business?', text: `Approve "${name}"?`, icon: 'question',
        showCancelButton: true, confirmButtonColor: '#16a34a', confirmButtonText: 'Yes, approve!'
    }).then(async (r) => {
        if (r.isConfirmed) {
            await apiFetch(`${API}/${id}/verify`, { method: 'POST' });
            Swal.fire({ icon: 'success', title: 'Approved!', text: 'Business has been approved.', timer: 2000, showConfirmButton: false });
            loadList();
        }
    });
}

function rejectBiz(id, name) {
    Swal.fire({
        title: 'Reject Business?', text: `Reject "${name}"?`, icon: 'warning',
        input: 'textarea', inputPlaceholder: 'Reason for rejection (optional)',
        showCancelButton: true, confirmButtonColor: '#dc2626', confirmButtonText: 'Yes, reject!'
    }).then(async (r) => {
        if (r.isConfirmed) {
            await apiFetch(`${API}/${id}/reject`, { method: 'POST', body: { reason: r.value } });
            Swal.fire({ icon: 'success', title: 'Rejected!', text: 'Business has been rejected.', timer: 2000, showConfirmButton: false });
            loadList();
        }
    });
}

loadList();
</script>
@endsection