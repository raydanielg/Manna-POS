@extends('admin.layouts.app')
@section('page_title', 'Blocked Users')
@section('content')
<div class="page-card">
    <div class="card-header">
        <div class="card-title">Blocked Users</div>
        <div class="filters-row">
            <div class="search-wrap">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" id="searchInput" placeholder="Search..." oninput="loadList()">
            </div>
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead><tr><th>Name</th><th>Email</th><th>Reason</th><th>Blocked At</th><th>Actions</th></tr></thead>
            <tbody id="tableBody"><tr><td colspan="5" class="tbl-empty">Loading...</td></tr></tbody>
        </table>
    </div>
</div>
@endsection
@section('scripts')
const API = '/api/admin/users';

async function loadList() {
    const search = document.getElementById('searchInput').value;
    const res = await apiFetch(`${API}?status=blocked&search=${search}`);
    const data = res.data || res;
    const tbody = document.getElementById('tableBody');
    if (!data.length) { tbody.innerHTML = '<tr><td colspan="5" class="tbl-empty">No blocked users</td></tr>'; return; }
    tbody.innerHTML = data.map(u => `<tr>
        <td><strong>${u.name}</strong></td>
        <td>${u.email}</td>
        <td>${u.block_reason || '-'}</td>
        <td>${u.blocked_at ? new Date(u.blocked_at).toLocaleDateString() : '-'}</td>
        <td class="actions-cell">
            <button class="btn btn-success btn-xs" onclick="unblockUser(${u.id},'${u.name}')">Unblock</button>
        </td>
    </tr>`).join('');
}

function unblockUser(id, name) {
    Swal.fire({
        title: 'Unblock User?', text: `Unblock ${name}?`, icon: 'warning',
        showCancelButton: true, confirmButtonColor: '#16a34a', confirmButtonText: 'Yes, unblock!'
    }).then(async (r) => {
        if (r.isConfirmed) {
            await apiFetch(`${API}/${id}/unblock`, { method: 'POST' });
            Swal.fire({ icon: 'success', title: 'Unblocked!', text: 'User has been unblocked.', timer: 2000, showConfirmButton: false });
            loadList();
        }
    });
}

loadList();
@endsection