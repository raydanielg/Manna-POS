@extends('admin.layouts.app')
@section('page_title', 'Email Configuration')
@section('content')
<div class="page-card">
    <div class="card-header">
        <div class="card-title">Email Configuration</div>
        <div class="actions-cell">
            <button class="btn btn-warning" onclick="testEmail()">Test Email</button>
            <button class="btn btn-primary" onclick="saveConfig()">Save Settings</button>
        </div>
    </div>
    <div style="padding:1.25rem;">
        <form id="emailForm">
            <div class="form-row">
                <div class="form-group">
                    <label>Mail Driver</label>
                    <select class="form-control" id="mail_driver">
                        <option value="smtp">SMTP</option>
                        <option value="sendmail">Sendmail</option>
                        <option value="mailgun">Mailgun</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Mail Host</label>
                    <input type="text" class="form-control" id="mail_host" placeholder="smtp.mailtrap.io">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Mail Port</label>
                    <input type="text" class="form-control" id="mail_port" placeholder="587">
                </div>
                <div class="form-group">
                    <label>Mail Username</label>
                    <input type="text" class="form-control" id="mail_username" placeholder="username">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Mail Password</label>
                    <input type="password" class="form-control" id="mail_password" placeholder="password">
                </div>
                <div class="form-group">
                    <label>Mail Encryption</label>
                    <select class="form-control" id="mail_encryption">
                        <option value="tls">TLS</option>
                        <option value="ssl">SSL</option>
                        <option value="null">None</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>From Address</label>
                    <input type="email" class="form-control" id="mail_from_address" placeholder="noreply@example.com">
                </div>
                <div class="form-group">
                    <label>From Name</label>
                    <input type="text" class="form-control" id="mail_from_name" placeholder="MannaPOS">
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
@section('scripts')
const API = '/api/admin/system/config';

async function loadConfig() {
    try {
        const data = await apiFetch(API + '?group=email');
        if (data.mail_driver) document.getElementById('mail_driver').value = data.mail_driver;
        if (data.mail_host) document.getElementById('mail_host').value = data.mail_host;
        if (data.mail_port) document.getElementById('mail_port').value = data.mail_port;
        if (data.mail_username) document.getElementById('mail_username').value = data.mail_username;
        if (data.mail_password) document.getElementById('mail_password').value = data.mail_password;
        if (data.mail_encryption) document.getElementById('mail_encryption').value = data.mail_encryption;
        if (data.mail_from_address) document.getElementById('mail_from_address').value = data.mail_from_address;
        if (data.mail_from_name) document.getElementById('mail_from_name').value = data.mail_from_name;
    } catch (e) {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to load email config' });
    }
}

async function saveConfig() {
    const fields = ['mail_driver','mail_host','mail_port','mail_username','mail_password','mail_encryption','mail_from_address','mail_from_name'];
    const body = {};
    fields.forEach(f => body[f] = document.getElementById(f).value);
    try {
        await apiFetch(API, { method: 'POST', body: { setValue: body, group: 'email' } });
        Swal.fire({ icon: 'success', title: 'Saved!', timer: 2000, showConfirmButton: false });
    } catch (e) {
        Swal.fire({ icon: 'error', title: 'Error', text: e.data?.message || 'Save failed' });
    }
}

async function testEmail() {
    const { value: email } = await Swal.fire({
        title: 'Test Email',
        input: 'email',
        inputLabel: 'Send test email to',
        inputValue: '{{ Auth::user()->email ?? "" }}',
        showCancelButton: true,
        confirmButtonText: 'Send',
        inputValidator: v => !v && 'Enter an email address'
    });
    if (!email) return;
    Swal.fire({ title: 'Sending...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
    try {
        await apiFetch('/api/admin/system/test-email', { method: 'POST', body: { email } });
        Swal.fire({ icon: 'success', title: 'Test email sent!', timer: 2000, showConfirmButton: false });
    } catch (e) {
        Swal.fire({ icon: 'error', title: 'Failed', text: e.data?.message || 'Could not send test email' });
    }
}

loadConfig();
@endsection