@extends('admin.layouts.app')
@section('page_title', 'Business Settings')
@section('content')
<div class="page-card" style="max-width:800px;margin:0 auto;">
    <div class="card-header"><div class="card-title">Business Settings</div></div>
    <div style="padding:1.5rem;">
        <form id="settingsForm">
            <div class="form-group"><label>Business Name</label><input name="business_name" class="form-control" id="business_name"><div class="invalid-feedback"></div></div>
            <div class="form-group"><label>Business Email</label><input name="business_email" class="form-control" id="business_email"><div class="invalid-feedback"></div></div>
            <div class="form-group"><label>Business Phone</label><input name="business_phone" class="form-control" id="business_phone"><div class="invalid-feedback"></div></div>
            <div class="form-group"><label>Business Address</label><textarea name="business_address" class="form-control" id="business_address" rows="2"></textarea><div class="invalid-feedback"></div></div>
            <div class="form-group"><label>Default Currency</label><select name="default_currency" class="form-control" id="default_currency"><option value="TZS">TZS</option><option value="USD">USD</option><option value="EUR">EUR</option></select></div>
            <div class="form-group"><label>Time Zone</label><select name="time_zone" class="form-control" id="time_zone"><option value="Africa/Dar_es_Salaam">Africa/Dar es Salaam</option><option value="UTC">UTC</option></select></div>
            <button type="submit" class="btn btn-primary">Save Settings</button>
        </form>
    </div>
</div>
@endsection
@section('scripts')
async function loadSettings() {
    try {
        const configs = await apiFetch('/api/admin/system/config');
        const map = {};
        if (Array.isArray(configs)) configs.forEach(c => map[c.key] = c.value);
        if (map.business_name) document.getElementById('business_name').value = map.business_name;
        if (map.business_email) document.getElementById('business_email').value = map.business_email;
        if (map.business_phone) document.getElementById('business_phone').value = map.business_phone;
        if (map.business_address) document.getElementById('business_address').value = map.business_address;
        if (map.default_currency) document.getElementById('default_currency').value = map.default_currency;
        if (map.time_zone) document.getElementById('time_zone').value = map.time_zone;
    } catch (e) { /* silent */ }
}
document.getElementById('settingsForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const data = {
        business_name: document.getElementById('business_name').value,
        business_email: document.getElementById('business_email').value,
        business_phone: document.getElementById('business_phone').value,
        business_address: document.getElementById('business_address').value,
        default_currency: document.getElementById('default_currency').value,
        time_zone: document.getElementById('time_zone').value,
    };
    try {
        for (const [key, value] of Object.entries(data)) {
            await apiFetch('/api/admin/system/config', { method: 'POST', body: JSON.stringify({ key, value }) });
        }
        Swal.fire('Saved', 'Settings updated successfully', 'success');
    } catch (e) { Swal.fire('Error', 'Failed to save settings', 'error'); }
});
loadSettings();
@endsection
