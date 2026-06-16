@extends('admin.layouts.app')
@section('page_title', 'Security Settings')
@section('content')
<div class="page-card" style="margin-bottom:1.25rem;">
    <div class="card-header">
        <div class="card-title">Password Policy</div>
    </div>
    <div style="padding:1.25rem;">
        <div class="form-row">
            <div class="form-group">
                <label>Minimum Length</label>
                <input type="number" class="form-control" id="min_length" placeholder="8" min="4" max="128">
            </div>
            <div class="form-group">
                <label>Max Login Attempts</label>
                <input type="number" class="form-control" id="max_attempts" placeholder="5" min="1" max="50">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Lockout Duration (minutes)</label>
                <input type="number" class="form-control" id="lockout_duration" placeholder="15" min="1" max="1440">
            </div>
            <div class="form-group">
                <label>Session Lifetime (minutes)</label>
                <input type="number" class="form-control" id="session_lifetime" placeholder="120" min="1" max="43200">
            </div>
        </div>
        <div style="display:flex;gap:1.5rem;flex-wrap:wrap;">
            <label style="display:flex;align-items:center;gap:0.5rem;font-size:0.82rem;cursor:pointer;">
                <input type="checkbox" id="require_special_chars"> Require Special Characters
            </label>
            <label style="display:flex;align-items:center;gap:0.5rem;font-size:0.82rem;cursor:pointer;">
                <input type="checkbox" id="require_numbers"> Require Numbers
            </label>
        </div>
    </div>
</div>

<div class="page-card" style="margin-bottom:1.25rem;">
    <div class="card-header">
        <div class="card-title">Two-Factor Authentication</div>
    </div>
    <div style="padding:1.25rem;">
        <label style="display:flex;align-items:center;gap:0.5rem;font-size:0.82rem;cursor:pointer;">
            <input type="checkbox" id="twofa_enabled"> Enable Two-Factor Authentication
        </label>
    </div>
</div>

<div class="page-card">
    <div class="card-header">
        <div class="card-title">Actions</div>
        <button class="btn btn-primary" onclick="saveConfig()">Save Settings</button>
    </div>
</div>
@endsection
@section('scripts')
const API = '/api/admin/system/config';

async function loadConfig() {
    try {
        const data = await apiFetch(API + '?group=security');
        if (data.min_length) document.getElementById('min_length').value = data.min_length;
        if (data.max_attempts) document.getElementById('max_attempts').value = data.max_attempts;
        if (data.lockout_duration) document.getElementById('lockout_duration').value = data.lockout_duration;
        if (data.session_lifetime) document.getElementById('session_lifetime').value = data.session_lifetime;
        document.getElementById('require_special_chars').checked = !!data.require_special_chars;
        document.getElementById('require_numbers').checked = !!data.require_numbers;
        document.getElementById('twofa_enabled').checked = !!data.twofa_enabled;
    } catch (e) {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to load security settings' });
    }
}

async function saveConfig() {
    const body = {
        min_length: document.getElementById('min_length').value,
        max_attempts: document.getElementById('max_attempts').value,
        lockout_duration: document.getElementById('lockout_duration').value,
        session_lifetime: document.getElementById('session_lifetime').value,
        require_special_chars: document.getElementById('require_special_chars').checked ? '1' : '0',
        require_numbers: document.getElementById('require_numbers').checked ? '1' : '0',
        twofa_enabled: document.getElementById('twofa_enabled').checked ? '1' : '0',
    };
    try {
        await apiFetch(API, { method: 'POST', body: { setValue: body, group: 'security' } });
        Swal.fire({ icon: 'success', title: 'Saved!', timer: 2000, showConfirmButton: false });
    } catch (e) {
        Swal.fire({ icon: 'error', title: 'Error', text: e.data?.message || 'Save failed' });
    }
}

loadConfig();
@endsection