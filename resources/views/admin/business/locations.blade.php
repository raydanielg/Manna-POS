@extends('admin.layouts.app')
@section('page_title', 'Business Locations')
@section('content')
<div class="page-card">
    <div class="card-header">
        <div class="card-title">Business Locations</div>
        <div class="filters-row">
            <select class="form-control" style="width:auto;padding:0.45rem 0.75rem;font-size:0.78rem;" id="statusFilter" onchange="loadList()">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="pending">Pending</option>
            </select>
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead><tr><th>Business</th><th>Location Name</th><th>Address</th><th>City</th><th>Country</th><th>Status</th></tr></thead>
            <tbody id="tableBody"><tr><td colspan="6" class="tbl-empty">Loading...</td></tr></tbody>
        </table>
    </div>
</div>
@endsection
@section('scripts')
const API = '/api/admin/business/locations';

async function loadList() {
    const status = document.getElementById('statusFilter').value;
    const data = await apiFetch(`${API}?status=${status}`);
    const tbody = document.getElementById('tableBody');
    if (!data.length) { tbody.innerHTML = '<tr><td colspan="6" class="tbl-empty">No locations found</td></tr>'; return; }
    tbody.innerHTML = data.map(l => `<tr>
        <td><strong>${l.business_name}</strong></td>
        <td>${l.location_name}</td>
        <td>${l.address || '-'}</td>
        <td>${l.city || '-'}</td>
        <td>${l.country || '-'}</td>
        <td><span class="badge ${l.status === 'active' ? 'badge-success' : l.status === 'pending' ? 'badge-pending' : 'badge-danger'}">${l.status}</span></td>
    </tr>`).join('');
}

loadList();
@endsection