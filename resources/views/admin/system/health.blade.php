@extends('admin.layouts.app')
@section('page_title', 'System Health')
@section('content')
<div class="page-card">
    <div class="card-header">
        <div class="card-title">System Health</div>
        <button class="btn btn-secondary btn-sm" onclick="loadHealth()">Refresh</button>
    </div>
    <div id="healthContent" class="p-4">
        <div class="tbl-empty">Loading...</div>
    </div>
</div>
@endsection
@section('scripts')
const API = '/api/admin/system/health';

async function loadHealth() {
    const data = await apiFetch(API);
    const div = document.getElementById('healthContent');
    div.innerHTML = `<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:1rem;padding:1rem;">
        <div class="stat-card"><div class="stat-value ${data.php_version ? 'text-green-600' : 'text-red-600'}">${data.php_version || 'N/A'}</div><div class="stat-label">PHP Version</div></div>
        <div class="stat-card"><div class="stat-value">${data.laravel_version || 'N/A'}</div><div class="stat-label">Laravel</div></div>
        <div class="stat-card"><div class="stat-value">${data.db_connection || 'N/A'}</div><div class="stat-label">Database</div></div>
        <div class="stat-card"><div class="stat-value ${data.db_size ? '' : 'text-gray-400'}">${data.db_size || 'Unknown'}</div><div class="stat-label">DB Size</div></div>
        <div class="stat-card"><div class="stat-value ${data.server_uptime ? '' : 'text-gray-400'}">${data.server_uptime || 'N/A'}</div><div class="stat-label">Server Uptime</div></div>
        <div class="stat-card"><div class="stat-value">${data.memory_usage || 'N/A'}</div><div class="stat-label">Memory Usage</div></div>
        <div class="stat-card"><div class="stat-value ${data.storage_writable ? 'text-green-600' : 'text-red-600'}">${data.storage_writable ? 'Writable' : 'Not Writable'}</div><div class="stat-label">Storage</div></div>
        <div class="stat-card"><div class="stat-value">${data.environment || 'N/A'}</div><div class="stat-label">Environment</div></div>
        <div class="stat-card"><div class="stat-value">${data.debug_mode ? 'ON' : 'OFF'}</div><div class="stat-label">Debug Mode</div></div>
        <div class="stat-card"><div class="stat-value ${data.schedule_running ? 'text-green-600' : 'text-red-600'}">${data.schedule_running ? 'Running' : 'Not Running'}</div><div class="stat-label">Schedule</div></div>
        <div class="stat-card"><div class="stat-value">${data.queue_worker || 'N/A'}</div><div class="stat-label">Queue Worker</div></div>
        <div class="stat-card"><div class="stat-value ${data.cache_accessible ? 'text-green-600' : 'text-red-600'}">${data.cache_accessible ? 'Accessible' : 'Not Accessible'}</div><div class="stat-label">Cache</div></div>
    </div>`;
}

loadHealth();
</script>
<style>
.stat-card { background:var(--card-bg); border:1px solid var(--border); border-radius:8px; padding:1.25rem; text-align:center; }
.stat-value { font-size:1.15rem; font-weight:700; color:var(--heading); }
.stat-label { font-size:0.75rem; color:var(--muted); margin-top:0.35rem; }
.text-green-600 { color:#16a34a; } .text-red-600 { color:#dc2626; } .text-gray-400 { color:#9ca3af; }
</style>
@endsection
