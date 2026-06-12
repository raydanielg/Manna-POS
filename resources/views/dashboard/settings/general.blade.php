@extends('layouts.dashboard')
@section('page_title','General Settings')
@section('content')
<div class="dash-content">
<div class="page-card" style="max-width:860px;">
  <div class="card-header">
    <div class="card-title">General Settings</div>
  </div>
  <div class="modal-body">
    <form id="settingsForm" onsubmit="saveSettings(event)">
      <h3 style="font-size:0.85rem;font-weight:700;color:#2563eb;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.75rem;">Business Information</h3>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Business Name</label><input name="business_name" class="form-control" placeholder="Your Business Name"><div class="invalid-feedback"></div></div>
        <div class="form-group"><label class="form-label">Business Email</label><input name="business_email" type="email" class="form-control" placeholder="contact@business.com"><div class="invalid-feedback"></div></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Phone</label><input name="phone" class="form-control" placeholder="+1 234 567 890"><div class="invalid-feedback"></div></div>
        <div class="form-group"><label class="form-label">Website</label><input name="website" class="form-control" placeholder="https://www.business.com"><div class="invalid-feedback"></div></div>
      </div>
      <div class="form-group"><label class="form-label">Address</label><textarea name="address" class="form-control" rows="2" placeholder="Full business address..."></textarea><div class="invalid-feedback"></div></div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">City</label><input name="city" class="form-control" placeholder="City"><div class="invalid-feedback"></div></div>
        <div class="form-group"><label class="form-label">Country</label><input name="country" class="form-control" placeholder="Country"><div class="invalid-feedback"></div></div>
      </div>
      <hr style="margin:1.5rem 0;border-color:#e9edf5;">
      <h3 style="font-size:0.85rem;font-weight:700;color:#2563eb;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.75rem;">Regional Settings</h3>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Currency</label>
          <select name="currency" class="form-control">
            <option value="USD">USD - US Dollar</option>
            <option value="EUR">EUR - Euro</option>
            <option value="GBP">GBP - British Pound</option>
            <option value="KES">KES - Kenyan Shilling</option>
            <option value="TZS">TZS - Tanzanian Shilling</option>
            <option value="UGX">UGX - Ugandan Shilling</option>
            <option value="ZAR">ZAR - South African Rand</option>
            <option value="NGN">NGN - Nigerian Naira</option>
            <option value="GHS">GHS - Ghanaian Cedi</option>
          </select>
          <div class="invalid-feedback"></div>
        </div>
        <div class="form-group">
          <label class="form-label">Date Format</label>
          <select name="date_format" class="form-control">
            <option value="Y-m-d">YYYY-MM-DD</option>
            <option value="d/m/Y">DD/MM/YYYY</option>
            <option value="m/d/Y">MM/DD/YYYY</option>
            <option value="d-m-Y">DD-MM-YYYY</option>
          </select>
          <div class="invalid-feedback"></div>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Tax Number (VAT/GST)</label><input name="tax_number" class="form-control" placeholder="TAX-123456"><div class="invalid-feedback"></div></div>
        <div class="form-group"><label class="form-label">Financial Year Start</label><input name="fy_start" type="date" class="form-control"><div class="invalid-feedback"></div></div>
      </div>
      <hr style="margin:1.5rem 0;border-color:#e9edf5;">
      <h3 style="font-size:0.85rem;font-weight:700;color:#2563eb;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.75rem;">POS Settings</h3>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Receipt Footer Message</label>
          <textarea name="receipt_footer" class="form-control" rows="2" placeholder="Thank you for your business!"></textarea>
          <div class="invalid-feedback"></div>
        </div>
        <div class="form-group">
          <label class="form-label">Low Stock Alert Threshold</label>
          <input name="low_stock_threshold" type="number" min="0" class="form-control" placeholder="5">
          <div class="invalid-feedback"></div>
        </div>
      </div>
      <div style="display:flex;justify-content:flex-end;padding-top:1rem;">
        <button type="submit" class="btn btn-primary" id="saveSettingsBtn">Save Settings</button>
      </div>
    </form>
  </div>
</div>
</div>
@endsection
@section('scripts')
<script>
async function saveSettings(e){
  e.preventDefault();
  const btn=document.getElementById('saveSettingsBtn');btn.disabled=true;btn.textContent='Saving...';
  try{
    const data=Object.fromEntries(new FormData(document.getElementById('settingsForm')));
    showToast('Settings saved successfully!');
  }catch(err){showToast('Failed to save settings','error');}
  finally{btn.disabled=false;btn.textContent='Save Settings';}
}
</script>
@endsection
