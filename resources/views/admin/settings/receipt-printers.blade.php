@extends('admin.layouts.app')
@section('page_title', 'Receipt Printers')
@section('content')
<div class="page-card">
    <div class="card-header">
        <div class="card-title">Receipt Printers</div>
        <button class="btn btn-primary" onclick="openModal(null)">+ Add Printer</button>
    </div>
    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead><tr><th>Name</th><th>Type</th><th>Connection</th><th>IP / Port</th><th>Default</th><th>Actions</th></tr></thead>
            <tbody id="tableBody"><tr><td colspan="6" class="tbl-empty">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="printerModal">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title" id="modalTitle">Add Printer</div>
            <button class="modal-close" onclick="closeModal('printerModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="printerForm">
                <div class="form-group">
                    <label>Printer Name *</label>
                    <input type="text" class="form-control" id="printer_name" placeholder="Kitchen Printer">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Type</label>
                        <select class="form-control" id="printer_type">
                            <option value="thermal">Thermal</option>
                            <option value="inkjet">Inkjet</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Connection</label>
                        <select class="form-control" id="connection_type">
                            <option value="USB">USB</option>
                            <option value="network">Network</option>
                            <option value="bluetooth">Bluetooth</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>IP Address / Hostname</label>
                        <input type="text" class="form-control" id="ip_address" placeholder="192.168.1.100">
                    </div>
                    <div class="form-group">
                        <label>Port</label>
                        <input type="number" class="form-control" id="port" placeholder="9100">
                    </div>
                </div>
                <div class="form-group">
                    <label style="display:flex;align-items:center;gap:0.5rem;font-size:0.82rem;cursor:pointer;">
                        <input type="checkbox" id="is_default"> Set as Default Printer
                    </label>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('printerModal')">Cancel</button>
            <button class="btn btn-primary" onclick="savePrinter()">Save</button>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
const API = '/api/admin/settings/receipt-printers';
let editId = null;

async function loadList() {
    try {
        const data = await apiFetch(API);
        const tbody = document.getElementById('tableBody');
        if (!data.length) { tbody.innerHTML = '<tr><td colspan="6" class="tbl-empty">No printers configured</td></tr>'; return; }
        tbody.innerHTML = data.map(p => `<tr>
            <td><strong>${p.name || p.printer_name}</strong></td>
            <td><span class="badge badge-info">${p.type || p.printer_type}</span></td>
            <td>${p.connection || p.connection_type}</td>
            <td>${p.ip_address || '-'}${p.port ? ':' + p.port : ''}</td>
            <td>${p.is_default ? '<span class="badge badge-success">Default</span>' : '<span class="badge badge-default">No</span>'}</td>
            <td class="actions-cell">
                <button class="btn btn-success btn-xs" onclick="testPrint(${p.id})">Test</button>
                <button class="btn btn-primary btn-xs" onclick="openModal(${p.id})">Edit</button>
                <button class="btn btn-danger btn-xs" onclick="deletePrinter(${p.id},'${p.name || p.printer_name}')">Delete</button>
            </td>
        </tr>`).join('');
    } catch (e) {
        document.getElementById('tableBody').innerHTML = '<tr><td colspan="6" class="tbl-empty">Failed to load</td></tr>';
    }
}

function openModal(id) {
    editId = id;
    document.getElementById('printerForm').reset();
    document.getElementById('is_default').checked = false;
    if (id) {
        document.getElementById('modalTitle').textContent = 'Edit Printer';
        apiFetch(`${API}/${id}`).then(p => {
            document.getElementById('printer_name').value = p.name || p.printer_name || '';
            document.getElementById('printer_type').value = p.type || p.printer_type || 'thermal';
            document.getElementById('connection_type').value = p.connection || p.connection_type || 'USB';
            document.getElementById('ip_address').value = p.ip_address || '';
            document.getElementById('port').value = p.port || '';
            document.getElementById('is_default').checked = !!p.is_default;
        });
    } else {
        document.getElementById('modalTitle').textContent = 'Add Printer';
    }
    openModal('printerModal');
}

async function savePrinter() {
    const body = {
        name: document.getElementById('printer_name').value,
        type: document.getElementById('printer_type').value,
        connection: document.getElementById('connection_type').value,
        ip_address: document.getElementById('ip_address').value,
        port: document.getElementById('port').value,
        is_default: document.getElementById('is_default').checked ? 1 : 0,
    };
    if (!body.name) { Swal.fire({ icon: 'error', title: 'Required', text: 'Printer name is required' }); return; }
    try {
        if (editId) { await apiFetch(`${API}/${editId}`, { method: 'PUT', body }); }
        else { await apiFetch(API, { method: 'POST', body }); }
        closeModal('printerModal');
        Swal.fire({ icon: 'success', title: 'Saved!', timer: 2000, showConfirmButton: false });
        loadList();
    } catch (e) {
        Swal.fire({ icon: 'error', title: 'Error', text: e.data?.message || 'Save failed' });
    }
}

async function testPrint(id) {
    Swal.fire({ title: 'Testing printer...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
    try {
        await apiFetch(`${API}/${id}/test`, { method: 'POST' });
        Swal.fire({ icon: 'success', title: 'Test print sent!', timer: 2000, showConfirmButton: false });
    } catch (e) {
        Swal.fire({ icon: 'error', title: 'Test failed', text: e.data?.message || 'Could not connect to printer' });
    }
}

function deletePrinter(id, name) {
    Swal.fire({ title: 'Delete printer?', text: `Delete "${name}"?`, icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc2626', confirmButtonText: 'Delete' })
    .then(async (r) => { if (r.isConfirmed) { await apiFetch(`${API}/${id}`, { method: 'DELETE' }); Swal.fire({ icon: 'success', title: 'Deleted!', timer: 1500, showConfirmButton: false }); loadList(); }});
}

loadList();
</script>
@endsection