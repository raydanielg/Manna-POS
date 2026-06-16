@extends('admin.layouts.app')
@section('page_title', 'SMS Configuration')
@section('content')
<div class="page-card">
    <div class="card-header">
        <div class="card-title">SMS Configuration</div>
        <div class="actions-cell">
            <button class="btn btn-warning" onclick="testSms()">Test SMS</button>
            <button class="btn btn-primary" onclick="saveConfig()">Save Settings</button>
        </div>
    </div>
    <div style="padding:1.25rem;">
        <form id="smsForm">
            <div class="form-group">
                <label>SMS Driver</label>
                <select class="form-control" id="sms_driver">
                    <option value="twilio">Twilio</option>
                    <option value="africastalking">Africa's Talking</option>
                </select>
            </div>
            <div class="form-group">
                <label>Account SID / Username</label>
                <input type="text" class="form-control" id="account_sid" placeholder="ACxxxxxxxxxxxx">
            </div>
            <div class="form-group">
                <label>Auth Token / API Key</label>
                <input type="password" class="form-control" id="auth_token" placeholder="auth token">
            </div>
            <div class="form-group">
                <label>From Number / Sender ID</label>
                <input type="text" class="form-control" id="from_number" placeholder="+1234567890">
            </div>
        </form>
    </div>
</div>
@endsection
@section('scripts')
<script>
const API = '/api/admin/system/config';

async function loadConfig() {
    try {
        const data = await apiFetch(API + '?group=sms');
        if (data.sms_driver) document.getElementById('sms_driver').value = data.sms_driver;
        if (data.account_sid) document.getElementById('account_sid').value = data.account_sid;
        if (data.auth_token) document.getElementById('auth_token').value = data.auth_token;
        if (data.from_number) document.getElementById('from_number').value = data.from_number;
    } catch (e) {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to load SMS config' });
    }
}

async function saveConfig() {
    const fields = ['sms_driver','account_sid','auth_token','from_number'];
    const body = {};
    fields.forEach(f => body[f] = document.getElementById(f).value);
    try {
        await apiFetch(API, { method: 'POST', body: { setValue: body, group: 'sms' } });
        Swal.fire({ icon: 'success', title: 'Saved!', timer: 2000, showConfirmButton: false });
    } catch (e) {
        Swal.fire({ icon: 'error', title: 'Error', text: e.data?.message || 'Save failed' });
    }
}

async function testSms() {
    const { value: phone } = await Swal.fire({
        title: 'Test SMS',
        input: 'tel',
        inputLabel: 'Send test SMS to',
        showCancelButton: true,
        confirmButtonText: 'Send',
        inputValidator: v => !v && 'Enter a phone number'
    });
    if (!phone) return;
    Swal.fire({ title: 'Sending...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
    try {
        await apiFetch('/api/admin/system/test-sms', { method: 'POST', body: { phone } });
        Swal.fire({ icon: 'success', title: 'Test SMS sent!', timer: 2000, showConfirmButton: false });
    } catch (e) {
        Swal.fire({ icon: 'error', title: 'Failed', text: e.data?.message || 'Could not send test SMS' });
    }
}

loadConfig();
</script>
@endsection