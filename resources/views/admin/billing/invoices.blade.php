@extends('admin.layouts.app')
@section('page_title', 'Invoices')
@section('content')
<div class="page-card">
    <div class="card-header">
        <div class="card-title">All Invoices</div>
        <div class="filters-row">
            <div class="search-wrap">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" id="searchInput" placeholder="Search invoice..." oninput="loadList()">
            </div>
            <select class="form-control" style="width:auto;padding:0.45rem 0.75rem;font-size:0.78rem;" id="statusFilter" onchange="loadList()">
                <option value="">All</option>
                <option value="pending">Pending</option>
                <option value="paid">Paid</option>
                <option value="overdue">Overdue</option>
                <option value="cancelled">Cancelled</option>
            </select>
            <button class="btn btn-success" onclick="openAddModal()">+ New Invoice</button>
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead><tr><th>Invoice #</th><th>Customer</th><th>Total</th><th>Status</th><th>Due Date</th><th>Created</th><th>Actions</th></tr></thead>
            <tbody id="tableBody"><tr><td colspan="7" class="tbl-empty">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="invModal">
    <div class="modal" style="max-width:560px;">
        <div class="modal-header">
            <div class="modal-title" id="modalTitle">New Invoice</div>
            <button class="modal-close" onclick="closeModal('invModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="invForm">
                <div class="form-group">
                    <label>User *</label>
                    <select class="form-control" id="user_id" required></select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Subtotal *</label>
                        <input type="number" step="0.01" class="form-control" id="subtotal" oninput="calcTotal()" required>
                    </div>
                    <div class="form-group">
                        <label>Tax</label>
                        <input type="number" step="0.01" class="form-control" id="tax" value="0" oninput="calcTotal()">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Discount</label>
                        <input type="number" step="0.01" class="form-control" id="discount" value="0" oninput="calcTotal()">
                    </div>
                    <div class="form-group">
                        <label>Total</label>
                        <input type="number" step="0.01" class="form-control" id="total" readonly style="font-weight:700;">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Due Date</label>
                        <input type="date" class="form-control" id="due_date">
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" id="status">
                            <option value="pending">Pending</option>
                            <option value="paid">Paid</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Notes</label>
                    <textarea class="form-control" id="notes" rows="2"></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('invModal')">Cancel</button>
            <button class="btn btn-primary" onclick="saveInvoice()">Create Invoice</button>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
const API = '/api/admin/billing/invoices';
let editId = null;

async function init() {
    const users = await apiFetch('/api/admin/billing/users');
    document.getElementById('user_id').innerHTML = '<option value="">Select User</option>' + users.map(u => `<option value="${u.id}">${u.name} (${u.email})</option>`).join('');
    loadList();
}

function calcTotal() {
    const sub = parseFloat(document.getElementById('subtotal').value) || 0;
    const tax = parseFloat(document.getElementById('tax').value) || 0;
    const disc = parseFloat(document.getElementById('discount').value) || 0;
    document.getElementById('total').value = (sub + tax - disc).toFixed(2);
}

async function loadList() {
    const search = document.getElementById('searchInput').value;
    const status = document.getElementById('statusFilter').value;
    const params = new URLSearchParams({search, status});
    const data = await apiFetch(`${API}?${params}`);
    const tbody = document.getElementById('tableBody');
    if (!data.length) { tbody.innerHTML = '<tr><td colspan="7" class="tbl-empty">No invoices</td></tr>'; return; }
    tbody.innerHTML = data.map(i => `<tr>
        <td><strong>${i.invoice_number}</strong></td>
        <td>${i.user}</td>
        <td>${i.currency} ${i.total}</td>
        <td><span class="badge ${i.status === 'paid' ? 'badge-success' : i.status === 'overdue' ? 'badge-danger' : i.status === 'cancelled' ? 'badge-default' : 'badge-pending'}">${i.status}</span></td>
        <td>${i.due_date || '-'}</td>
        <td>${i.created_at}</td>
        <td class="actions-cell">
            <button class="btn btn-primary btn-xs" onclick="editInv(${i.id})">Edit</button>
            <button class="btn btn-danger btn-xs" onclick="deleteInv(${i.id})">Delete</button>
        </td>
    </tr>`).join('');
}

async function openAddModal() {
    editId = null; document.getElementById('invForm').reset();
    document.getElementById('modalTitle').textContent = 'New Invoice';
    document.getElementById('subtotal').value = 0; document.getElementById('total').value = 0;
    openModal('invModal');
}

async function editInv(id) {
    editId = id; document.getElementById('modalTitle').textContent = 'Edit Invoice';
    const data = await apiFetch(`${API}/${id}`);
    document.getElementById('user_id').value = data.user_id || '';
    document.getElementById('subtotal').value = data.subtotal || 0;
    document.getElementById('tax').value = data.tax || 0;
    document.getElementById('discount').value = data.discount || 0;
    document.getElementById('total').value = data.total || 0;
    document.getElementById('due_date').value = data.due_date ? data.due_date.split('T')[0] : '';
    document.getElementById('status').value = data.status || 'pending';
    document.getElementById('notes').value = data.notes || '';
    openModal('invModal');
}

async function saveInvoice() {
    const body = {
        user_id: document.getElementById('user_id').value,
        subtotal: document.getElementById('subtotal').value,
        tax: document.getElementById('tax').value,
        discount: document.getElementById('discount').value,
        total: document.getElementById('total').value,
        due_date: document.getElementById('due_date').value,
        status: document.getElementById('status').value,
        notes: document.getElementById('notes').value,
    };
    try {
        if (editId) { await apiFetch(`${API}/${editId}`, { method: 'PUT', body }); }
        else { await apiFetch(API, { method: 'POST', body }); }
        closeModal('invModal');
        Swal.fire({ icon: 'success', title: 'Saved!', timer: 2000, showConfirmButton: false });
        loadList();
    } catch (e) { Swal.fire({ icon: 'error', title: 'Error', text: e.data?.message || 'Something went wrong' }); }
}

function deleteInv(id) {
    Swal.fire({ title: 'Delete?', text: 'Delete this invoice?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc2626', confirmButtonText: 'Delete' })
    .then(async (r) => { if (r.isConfirmed) { await apiFetch(`${API}/${id}`, { method: 'DELETE' }); Swal.fire({ icon: 'success', title: 'Deleted!', timer: 2000, showConfirmButton: false }); loadList(); }});
}

init();
</script>
@endsection
