@extends('admin.layouts.app')
@section('page_title', 'Currency Settings')
@section('content')
<div class="page-card">
    <div class="card-header">
        <div class="card-title">Currencies</div>
        <button class="btn btn-primary" onclick="openModal(null)">+ Add Currency</button>
    </div>
    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead><tr><th>Code</th><th>Name</th><th>Symbol</th><th>Exchange Rate</th><th>Default</th><th>Actions</th></tr></thead>
            <tbody id="tableBody"><tr><td colspan="6" class="tbl-empty">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="currencyModal">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title" id="modalTitle">Add Currency</div>
            <button class="modal-close" onclick="closeModal('currencyModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="currencyForm">
                <div class="form-row">
                    <div class="form-group">
                        <label>Currency Code *</label>
                        <input type="text" class="form-control" id="code" placeholder="USD" maxlength="3">
                    </div>
                    <div class="form-group">
                        <label>Symbol</label>
                        <input type="text" class="form-control" id="symbol" placeholder="$">
                    </div>
                </div>
                <div class="form-group">
                    <label>Name *</label>
                    <input type="text" class="form-control" id="name" placeholder="US Dollar">
                </div>
                <div class="form-group">
                    <label>Exchange Rate (relative to base currency)</label>
                    <input type="number" step="0.000001" class="form-control" id="exchange_rate" placeholder="1.000000">
                </div>
                <div class="form-group">
                    <label style="display:flex;align-items:center;gap:0.5rem;font-size:0.82rem;cursor:pointer;">
                        <input type="checkbox" id="is_default"> Set as Default Currency
                    </label>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('currencyModal')">Cancel</button>
            <button class="btn btn-primary" onclick="saveCurrency()">Save</button>
        </div>
    </div>
</div>
@endsection
@section('scripts')
const API = '/api/admin/settings/currency';
let editId = null;

async function loadList() {
    try {
        const data = await apiFetch(API);
        const tbody = document.getElementById('tableBody');
        if (!data.length) { tbody.innerHTML = '<tr><td colspan="6" class="tbl-empty">No currencies</td></tr>'; return; }
        tbody.innerHTML = data.map(c => `<tr>
            <td><strong>${c.code}</strong></td>
            <td>${c.name}</td>
            <td>${c.symbol}</td>
            <td>${c.exchange_rate || '1.000000'}</td>
            <td>${c.is_default ? '<span class="badge badge-success">Default</span>' : '<span class="badge badge-default">No</span>'}</td>
            <td class="actions-cell">
                <button class="btn btn-primary btn-xs" onclick="openModal(${c.id})">Edit</button>
                <button class="btn btn-danger btn-xs" onclick="deleteCurrency(${c.id},'${c.code}')">Delete</button>
            </td>
        </tr>`).join('');
    } catch (e) {
        document.getElementById('tableBody').innerHTML = '<tr><td colspan="6" class="tbl-empty">Failed to load</td></tr>';
    }
}

function openModal(id) {
    editId = id;
    document.getElementById('currencyForm').reset();
    document.getElementById('is_default').checked = false;
    if (id) {
        document.getElementById('modalTitle').textContent = 'Edit Currency';
        apiFetch(`${API}/${id}`).then(c => {
            document.getElementById('code').value = c.code || '';
            document.getElementById('name').value = c.name || '';
            document.getElementById('symbol').value = c.symbol || '';
            document.getElementById('exchange_rate').value = c.exchange_rate || '';
            document.getElementById('is_default').checked = !!c.is_default;
        });
    } else {
        document.getElementById('modalTitle').textContent = 'Add Currency';
    }
    openModal('currencyModal');
}

async function saveCurrency() {
    const body = {
        code: document.getElementById('code').value.toUpperCase(),
        name: document.getElementById('name').value,
        symbol: document.getElementById('symbol').value,
        exchange_rate: document.getElementById('exchange_rate').value,
        is_default: document.getElementById('is_default').checked ? 1 : 0,
    };
    if (!body.code || !body.name) { Swal.fire({ icon: 'error', title: 'Required', text: 'Code and Name are required' }); return; }
    try {
        if (editId) { await apiFetch(`${API}/${editId}`, { method: 'PUT', body }); }
        else { await apiFetch(API, { method: 'POST', body }); }
        closeModal('currencyModal');
        Swal.fire({ icon: 'success', title: 'Saved!', timer: 2000, showConfirmButton: false });
        loadList();
    } catch (e) {
        Swal.fire({ icon: 'error', title: 'Error', text: e.data?.message || 'Save failed' });
    }
}

function deleteCurrency(id, code) {
    Swal.fire({ title: 'Delete?', text: `Delete "${code}" currency?`, icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc2626', confirmButtonText: 'Delete' })
    .then(async (r) => { if (r.isConfirmed) { await apiFetch(`${API}/${id}`, { method: 'DELETE' }); Swal.fire({ icon: 'success', title: 'Deleted!', timer: 1500, showConfirmButton: false }); loadList(); }});
}

loadList();
@endsection