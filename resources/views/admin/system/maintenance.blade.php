@extends('admin.layouts.app')
@section('page_title', 'Maintenance Mode')
@section('content')
<div class="page-card" style="margin-bottom:1.25rem;">
    <div class="card-header">
        <div class="card-title">Current Status</div>
    </div>
    <div style="padding:1.5rem;text-align:center;">
        <div style="font-size:2.5rem;font-weight:800;letter-spacing:-0.03em;" id="statusDisplay">
            <span class="badge" id="statusBadge" style="font-size:1rem;padding:0.4rem 1.2rem;">Loading...</span>
        </div>
        <div style="margin-top:1rem;">
            <button class="btn btn-warning" id="toggleBtn" onclick="toggleMaintenance()">Toggle Maintenance Mode</button>
        </div>
    </div>
</div>

<div class="page-card">
    <div class="card-header">
        <div class="card-title">Maintenance Settings</div>
        <button class="btn btn-primary" onclick="saveSettings()">Save</button>
    </div>
    <div style="padding:1.25rem;">
        <div class="form-group">
            <label>Maintenance Message</label>
            <textarea class="form-control" id="maintenance_message" rows="3" placeholder="We are currently under maintenance. Please check back soon."></textarea>
        </div>
        <div class="form-group">
            <label>Allowed IPs (one per line)</label>
            <textarea class="form-control" id="allowed_ips" rows="4" placeholder="127.0.0.1&#10;192.168.1.1"></textarea>
        </div>
    </div>
</div>
@endsection
@section('scripts')
const API = '/api/admin/system/maintenance';
const CONFIG_API = '/api/admin/system/config';

async function loadStatus() {
    try {
        const data = await apiFetch(API);
        const isOn = data.maintenance_mode || data.enabled || data.is_down;
        const badge = document.getElementById('statusBadge');
        const toggleBtn = document.getElementById('toggleBtn');
        badge.textContent = isOn ? 'MAINTENANCE ON' : 'ACTIVE';
        badge.className = 'badge ' + (isOn ? 'badge-danger' : 'badge-success');
        toggleBtn.textContent = isOn ? 'Disable Maintenance Mode' : 'Enable Maintenance Mode';
        if (data.message) document.getElementById('maintenance_message').value = data.message;
        if (data.allowed_ips) document.getElementById('allowed_ips').value = Array.isArray(data.allowed_ips) ? data.allowed_ips.join('\n') : data.allowed_ips;
    } catch (e) {
        document.getElementById('statusBadge').textContent = 'Error loading status';
        document.getElementById('statusBadge').className = 'badge badge-danger';
    }
}

async function toggleMaintenance() {
    const result = await Swal.fire({
        title: 'Are you sure?',
        text: 'This will toggle maintenance mode for the entire application.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, toggle',
        confirmButtonColor: '#f59e0b'
    });
    if (!result.isConfirmed) return;
    Swal.fire({ title: 'Updating...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
    try {
        await apiFetch(API, { method: 'POST' });
        Swal.fire({ icon: 'success', title: 'Updated!', timer: 1500, showConfirmButton: false });
        loadStatus();
    } catch (e) {
        Swal.fire({ icon: 'error', title: 'Error', text: e.data?.message || 'Toggle failed' });
    }
}

async function saveSettings() {
    try {
        await apiFetch(CONFIG_API, { method: 'POST', body: {
            setValue: {
                maintenance_message: document.getElementById('maintenance_message').value,
                allowed_ips: document.getElementById('allowed_ips').value
            }, group: 'maintenance'
        }});
        Swal.fire({ icon: 'success', title: 'Saved!', timer: 2000, showConfirmButton: false });
    } catch (e) {
        Swal.fire({ icon: 'error', title: 'Error', text: e.data?.message || 'Save failed' });
    }
}

loadStatus();
@endsection