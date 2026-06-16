@extends('admin.layouts.app')
@section('page_title', 'Database Manager')
@section('content')
<div class="page-card" style="margin-bottom:1.25rem;">
    <div class="card-header">
        <div class="card-title">Run SQL Query</div>
        <button class="btn btn-danger" onclick="runQuery()">Execute</button>
    </div>
    <div style="padding:1.25rem;">
        <div class="form-group">
            <textarea class="form-control" id="sqlQuery" rows="4" placeholder="SELECT * FROM users LIMIT 10" style="font-family:monospace;font-size:0.8rem;"></textarea>
        </div>
        <div id="queryResult" style="display:none;margin-top:0.75rem;background:#f8fafc;border-radius:8px;padding:0.75rem;overflow-x:auto;max-height:300px;overflow-y:auto;">
            <pre id="queryResultContent" style="font-size:0.75rem;white-space:pre-wrap;"></pre>
        </div>
    </div>
</div>

<div class="page-card">
    <div class="card-header">
        <div class="card-title">Database Tables</div>
    </div>
    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead><tr><th>Table Name</th><th>Engine</th><th>Rows</th><th>Size</th><th>Collation</th><th>Actions</th></tr></thead>
            <tbody id="tableBody"><tr><td colspan="6" class="tbl-empty">Loading...</td></tr></tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="structureModal">
    <div class="modal" style="max-width:800px;">
        <div class="modal-header">
            <div class="modal-title" id="structureModalTitle">Table Structure</div>
            <button class="modal-close" onclick="closeModal('structureModal')">&times;</button>
        </div>
        <div class="modal-body">
            <div style="overflow-x:auto;">
                <table class="tbl">
                    <thead><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr></thead>
                    <tbody id="structureBody"></tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('structureModal')">Close</button>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
const API = '/api/admin/database';

async function loadTables() {
    try {
        const data = await apiFetch(API + '/tables');
        const tbody = document.getElementById('tableBody');
        if (!data.length) { tbody.innerHTML = '<tr><td colspan="6" class="tbl-empty">No tables found</td></tr>'; return; }
        tbody.innerHTML = data.map(t => `<tr>
            <td><strong>${t.name || t.table_name}</strong></td>
            <td>${t.engine || '-'}</td>
            <td>${t.rows?.toLocaleString() || '-'}</td>
            <td>${t.size || '-'}</td>
            <td>${t.collation || '-'}</td>
            <td class="actions-cell">
                <button class="btn btn-primary btn-xs" onclick="viewStructure('${t.name || t.table_name}')">Structure</button>
                <button class="btn btn-success btn-xs" onclick="optimizeTable('${t.name || t.table_name}')">Optimize</button>
            </td>
        </tr>`).join('');
    } catch (e) {
        document.getElementById('tableBody').innerHTML = '<tr><td colspan="6" class="tbl-empty">Failed to load</td></tr>';
    }
}

async function viewStructure(table) {
    Swal.fire({ title: 'Loading...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
    try {
        const data = await apiFetch(`${API}/tables/${table}`);
        document.getElementById('structureModalTitle').textContent = 'Structure: ' + table;
        const body = document.getElementById('structureBody');
        if (!data.length) { body.innerHTML = '<tr><td colspan="6" class="tbl-empty">No columns</td></tr>'; }
        else {
            body.innerHTML = data.map(c => `<tr>
                <td><code>${c.Field || c.field}</code></td>
                <td><code>${c.Type || c.type}</code></td>
                <td>${c.Null || c.null || 'NO'}</td>
                <td>${c.Key || c.key || ''}</td>
                <td><code>${c.Default || c.default || '-'}</code></td>
                <td>${c.Extra || c.extra || ''}</td>
            </tr>`).join('');
        }
        Swal.close();
        openModal('structureModal');
    } catch (e) {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to load structure' });
    }
}

function optimizeTable(table) {
    Swal.fire({ title: 'Optimize table?', text: `Optimize "${table}"?`, icon: 'question', showCancelButton: true, confirmButtonText: 'Optimize' })
    .then(async (r) => {
        if (!r.isConfirmed) return;
        Swal.fire({ title: 'Optimizing...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        try {
            await apiFetch(`${API}/tables/${table}/optimize`, { method: 'POST' });
            Swal.fire({ icon: 'success', title: 'Table optimized!', timer: 1500, showConfirmButton: false });
        } catch (e) {
            Swal.fire({ icon: 'error', title: 'Failed', text: e.data?.message || 'Optimization failed' });
        }
    });
}

async function runQuery() {
    const sql = document.getElementById('sqlQuery').value.trim();
    if (!sql) { Swal.fire({ icon: 'warning', title: 'Enter a SQL query' }); return; }
    Swal.fire({ title: 'Executing...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
    try {
        const data = await apiFetch(API + '/query', { method: 'POST', body: { query: sql } });
        const resultDiv = document.getElementById('queryResult');
        const content = document.getElementById('queryResultContent');
        resultDiv.style.display = 'block';
        content.textContent = JSON.stringify(data, null, 2);
        Swal.close();
    } catch (e) {
        Swal.fire({ icon: 'error', title: 'Query failed', text: e.data?.message || 'Error executing query' });
    }
}

loadTables();
</script>
@endsection