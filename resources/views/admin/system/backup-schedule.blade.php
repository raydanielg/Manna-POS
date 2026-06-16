@extends('admin.layouts.app')
@section('page_title', 'Backup Schedule')
@section('content')
<div class="page-card" style="margin-bottom:1.25rem;">
    <div class="card-header">
        <div class="card-title">Schedule Configuration</div>
        <button class="btn btn-primary" onclick="saveSchedule()">Save Schedule</button>
    </div>
    <div style="padding:1.25rem;">
        <div class="form-group">
            <label style="display:flex;align-items:center;gap:0.5rem;font-size:0.82rem;cursor:pointer;">
                <input type="checkbox" id="schedule_enabled"> Enable Scheduled Backups
            </label>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Frequency</label>
                <select class="form-control" id="frequency">
                    <option value="daily">Daily</option>
                    <option value="weekly">Weekly</option>
                    <option value="monthly">Monthly</option>
                </select>
            </div>
            <div class="form-group">
                <label>Time</label>
                <input type="time" class="form-control" id="backup_time" value="02:00">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Retention Count (number of backups to keep)</label>
                <input type="number" class="form-control" id="retention_count" value="7" min="1" max="365">
            </div>
            <div class="form-group">
                <label>Backup Type</label>
                <select class="form-control" id="backup_type">
                    <option value="full">Full</option>
                    <option value="database">Database Only</option>
                    <option value="files">Files Only</option>
                </select>
            </div>
        </div>
    </div>
</div>

<div class="page-card">
    <div class="card-header">
        <div class="card-title">Schedule Info</div>
    </div>
    <div style="padding:1.25rem;">
        <div class="form-row">
            <div class="form-group">
                <label>Last Backup Run</label>
                <div id="lastRun" style="font-size:0.9rem;font-weight:600;color:#0f172a;">N/A</div>
            </div>
            <div class="form-group">
                <label>Next Scheduled Run</label>
                <div id="nextRun" style="font-size:0.9rem;font-weight:600;color:#0f172a;">N/A</div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
const API = '/api/admin/system/backup-schedule';

async function loadSchedule() {
    try {
        const data = await apiFetch(API);
        document.getElementById('schedule_enabled').checked = !!data.enabled;
        if (data.frequency) document.getElementById('frequency').value = data.frequency;
        if (data.time) document.getElementById('backup_time').value = data.time;
        if (data.retention_count) document.getElementById('retention_count').value = data.retention_count;
        if (data.backup_type) document.getElementById('backup_type').value = data.backup_type;
        if (data.last_run) document.getElementById('lastRun').textContent = new Date(data.last_run).toLocaleString();
        if (data.next_run) document.getElementById('nextRun').textContent = new Date(data.next_run).toLocaleString();
    } catch (e) {
        // defaults
    }
}

async function saveSchedule() {
    const body = {
        enabled: document.getElementById('schedule_enabled').checked ? '1' : '0',
        frequency: document.getElementById('frequency').value,
        time: document.getElementById('backup_time').value,
        retention_count: document.getElementById('retention_count').value,
        backup_type: document.getElementById('backup_type').value,
    };
    try {
        await apiFetch(API, { method: 'POST', body });
        Swal.fire({ icon: 'success', title: 'Schedule saved!', timer: 2000, showConfirmButton: false });
        loadSchedule();
    } catch (e) {
        Swal.fire({ icon: 'error', title: 'Error', text: e.data?.message || 'Save failed' });
    }
}

loadSchedule();
@endsection