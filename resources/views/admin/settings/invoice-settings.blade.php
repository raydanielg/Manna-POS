@extends('admin.layouts.app')
@section('page_title', 'Invoice Settings')
@section('content')
<div class="page-card" style="max-width:800px;margin:0 auto;">
    <div class="card-header"><div class="card-title">Invoice Settings</div></div>
    <div style="padding:1.5rem;">
        <form id="settingsForm">
            <div class="form-group"><label>Invoice Prefix</label><input name="invoice_prefix" class="form-control" id="invoice_prefix" placeholder="INV-"><div class="invalid-feedback"></div></div>
            <div class="form-group"><label>Invoice Footer</label><textarea name="invoice_footer" class="form-control" id="invoice_footer" rows="3"></textarea></div>
            <button type="submit" class="btn btn-primary">Save Settings</button>
        </form>
    </div>
</div>
@endsection
@section('scripts')
<script>
async function loadSettings() {
    try {
        const configs = await apiFetch('/api/admin/system/config');
        if (Array.isArray(configs)) configs.forEach(c => { const el = document.getElementById(c.key); if (el) el.value = c.value; });
    } catch (e) {}
}
document.getElementById('settingsForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    try {
        await apiFetch('/api/admin/system/config', { method: 'POST', body: JSON.stringify({ key: 'invoice_prefix', value: document.getElementById('invoice_prefix').value }) });
        await apiFetch('/api/admin/system/config', { method: 'POST', body: JSON.stringify({ key: 'invoice_footer', value: document.getElementById('invoice_footer').value }) });
        Swal.fire('Saved', 'Invoice settings updated', 'success');
    } catch (e) { Swal.fire('Error', 'Failed to save', 'error'); }
});
loadSettings();
</script>
@endsection
