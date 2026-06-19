@extends('layouts.dashboard')
@section('page_title','Business Settings')
@section('page_styles')
<style>
.settings-wrap{max-width:900px;margin:0 auto;}

/* Progress card */
.progress-card{background:linear-gradient(135deg,#f8fafc,#fff);border-radius:16px;border:1.5px solid #e9edf5;padding:1.25rem 1.5rem;margin-bottom:1.5rem;display:flex;align-items:center;gap:1.25rem;flex-wrap:wrap;}
.progress-card .info{flex:1;min-width:200px;}
.progress-card .info h3{font-size:0.95rem;font-weight:700;color:#0f172a;margin-bottom:0.2rem;}
.progress-card .info p{font-size:0.78rem;color:#64748b;}
.progress-track{flex:1;min-width:200px;height:8px;background:#e2e8f0;border-radius:999px;overflow:hidden;}
.progress-fill{height:100%;background:linear-gradient(90deg,#2563eb,#10b981);border-radius:999px;transition:width .6s cubic-bezier(.4,0,.2,1);}
.progress-text{font-size:0.72rem;font-weight:700;color:#2563eb;white-space:nowrap;}

/* Section cards */
.section-card{background:#fff;border-radius:16px;border:1.5px solid #eef2f6;margin-bottom:1.25rem;overflow:hidden;}
.section-header{padding:1.1rem 1.5rem;background:#fafbff;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:0.75rem;}
.section-header .icon{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.section-header .icon svg{width:18px;height:18px;}
.section-header h3{font-size:0.9rem;font-weight:700;color:#0f172a;letter-spacing:-0.01em;}
.section-header p{font-size:0.72rem;color:#64748b;margin-top:0.1rem;}
.section-body{padding:1.25rem 1.5rem 1.5rem;}

/* Missing fields alert */
.missing-alert{background:linear-gradient(135deg,#fffbeb,#fef3c7);border:1.5px solid #fde68a;border-radius:14px;padding:1rem 1.25rem;margin-bottom:1.5rem;display:flex;align-items:flex-start;gap:0.75rem;}
.missing-alert svg{width:20px;height:20px;color:#f59e0b;flex-shrink:0;margin-top:0.15rem;}
.missing-alert .body{flex:1;}
.missing-alert .body h4{font-size:0.85rem;font-weight:700;color:#92400e;margin-bottom:0.25rem;}
.missing-alert .body ul{list-style:none;padding:0;margin:0;display:flex;flex-wrap:wrap;gap:0.35rem;}
.missing-alert .body li{font-size:0.75rem;background:#fff;color:#b45309;padding:0.2rem 0.55rem;border-radius:6px;border:1px solid #fcd34d;font-weight:500;}
.missing-alert .btn-now{background:#f59e0b;color:#fff;font-size:0.75rem;font-weight:700;padding:0.4rem 0.85rem;border-radius:8px;border:none;cursor:pointer;transition:all .2s;white-space:nowrap;}
.missing-alert .btn-now:hover{background:#d97706;}

/* Form polish */
.form-control::placeholder{color:#cbd5e1;}
.form-label{display:flex;align-items:center;gap:0.35rem;}
.required-mark{color:#ef4444;font-size:0.7rem;}
.field-hint{font-size:0.7rem;color:#94a3b8;margin-top:0.25rem;}

/* Responsive */
@media(max-width:640px){
  .progress-card{padding:1rem;gap:0.85rem;}
  .section-body{padding:1rem;}
  .missing-alert{flex-direction:column;gap:0.5rem;}
}
</style>
@endsection
@section('content')
<div class="dash-content">
<div class="settings-wrap">

  {{-- Profile Completeness Progress --}}
  <div class="progress-card" id="progressCard" style="display:none;">
    <div class="info">
      <h3>Business Profile</h3>
      <p id="progressDesc">Complete your business details to unlock all features</p>
    </div>
    <div style="flex:1;min-width:200px;">
      <div class="progress-track"><div class="progress-fill" id="progressFill" style="width:0%"></div></div>
    </div>
    <div class="progress-text" id="progressText">0%</div>
  </div>

  {{-- Missing Fields Alert --}}
  <div class="missing-alert" id="missingAlert" style="display:none;">
    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    <div class="body">
      <h4>Complete your business profile</h4>
      <p style="font-size:0.78rem;color:#a16207;margin-bottom:0.5rem;">The following fields are missing:</p>
      <ul id="missingList"></ul>
    </div>
    <button class="btn-now" onclick="document.getElementById('businessSection').scrollIntoView({behavior:'smooth'})">Fill Now</button>
  </div>

  <form id="settingsForm" onsubmit="saveSettings(event)">

    {{-- Business Information --}}
    <div class="section-card" id="businessSection">
      <div class="section-header">
        <div class="icon" style="background:#eff6ff;color:#2563eb;">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
        </div>
        <div>
          <h3>Business Information</h3>
          <p>Core details about your company</p>
        </div>
      </div>
      <div class="section-body">
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Business Name <span class="required-mark">*</span></label>
            <input name="business_name" class="form-control" placeholder="e.g. Manna Supermarket" data-required>
            <div class="field-hint">This name appears on invoices and receipts</div>
          </div>
          <div class="form-group">
            <label class="form-label">Business Email <span class="required-mark">*</span></label>
            <input name="business_email" type="email" class="form-control" placeholder="contact@yourbusiness.com" data-required>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Phone Number <span class="required-mark">*</span></label>
            <input name="phone" class="form-control" placeholder="+255 7xx xxx xxx" data-required>
          </div>
          <div class="form-group">
            <label class="form-label">Website</label>
            <input name="website" class="form-control" placeholder="https://www.yourbusiness.com">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Business Address <span class="required-mark">*</span></label>
          <textarea name="address" class="form-control" rows="2" placeholder="Street, building, area..." data-required></textarea>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">City <span class="required-mark">*</span></label>
            <input name="city" class="form-control" placeholder="e.g. Dar es Salaam" data-required>
          </div>
          <div class="form-group">
            <label class="form-label">Country <span class="required-mark">*</span></label>
            <input name="country" class="form-control" placeholder="e.g. Tanzania" data-required>
          </div>
        </div>
      </div>
    </div>

    {{-- Regional Settings --}}
    <div class="section-card">
      <div class="section-header">
        <div class="icon" style="background:#f0fdf4;color:#16a34a;">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
          <h3>Regional & Tax Settings</h3>
          <p>Currency, date format, and tax configuration</p>
        </div>
      </div>
      <div class="section-body">
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Currency <span class="required-mark">*</span></label>
            <select name="currency" class="form-control" data-required>
              <option value="">Select currency…</option>
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
          </div>
          <div class="form-group">
            <label class="form-label">Date Format</label>
            <select name="date_format" class="form-control">
              <option value="Y-m-d">YYYY-MM-DD</option>
              <option value="d/m/Y">DD/MM/YYYY</option>
              <option value="m/d/Y">MM/DD/YYYY</option>
              <option value="d-m-Y">DD-MM-YYYY</option>
            </select>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Tax / VAT Number</label>
            <input name="tax_number" class="form-control" placeholder="e.g. TAX-123456">
            <div class="field-hint">Shown on invoices if provided</div>
          </div>
          <div class="form-group">
            <label class="form-label">Financial Year Start</label>
            <input name="fy_start" type="date" class="form-control">
          </div>
        </div>
      </div>
    </div>

    {{-- POS Settings --}}
    <div class="section-card">
      <div class="section-header">
        <div class="icon" style="background:#fdf4ff;color:#7c3aed;">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4m0 1a1 1 0 011-1h14a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1V5z"/><path d="M4 12h16v7a2 2 0 01-2 2H6a2 2 0 01-2-2v-7z"/><path d="M8 7h8"/><path d="M8 15h.01"/><path d="M12 15h.01"/><path d="M16 15h.01"/></svg>
        </div>
        <div>
          <h3>POS & Receipt Settings</h3>
          <p>Configure your point-of-sale experience</p>
        </div>
      </div>
      <div class="section-body">
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Receipt Footer Message</label>
            <textarea name="receipt_footer" class="form-control" rows="2" placeholder="Thank you for shopping with us!"></textarea>
            <div class="field-hint">Printed at the bottom of every receipt</div>
          </div>
          <div class="form-group">
            <label class="form-label">Low Stock Alert Threshold</label>
            <input name="low_stock_threshold" type="number" min="0" class="form-control" placeholder="5">
            <div class="field-hint">You will be alerted when stock falls below this number</div>
          </div>
        </div>
      </div>
    </div>

    {{-- Store Settings --}}
    <div class="section-card" id="storeSection">
      <div class="section-header">
        <div class="icon" style="background:#fff7ed;color:#ea580c;">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        </div>
        <div>
          <h3>Online Store</h3>
          <p>Generate a public storefront for your customers</p>
        </div>
      </div>
      <div class="section-body">
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Store Title</label>
            <input name="store_title" class="form-control" placeholder="e.g. Manna Supermarket Online">
            <div class="field-hint">Shown at the top of your public store</div>
          </div>
          <div class="form-group">
            <label class="form-label">Store Description</label>
            <input name="store_description" class="form-control" placeholder="Short tagline for your store">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Store Link</label>
            <div style="display:flex;gap:0.5rem;">
              <input id="storeLink" class="form-control" readonly style="background:#f8fafc;color:#475569;font-size:0.82rem;" value="Not generated yet">
              <button type="button" class="btn btn-edit btn-sm" id="copyLinkBtn" style="white-space:nowrap;" onclick="copyStoreLink()">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                Copy
              </button>
            </div>
          </div>
          <div class="form-group" style="display:flex;align-items:flex-end;gap:0.75rem;">
            <button type="button" class="btn btn-primary" id="generateLinkBtn" onclick="generateStoreLink()" style="gap:0.4rem;">
              <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
              Generate Link
            </button>
            <a id="previewLink" href="#" target="_blank" class="btn btn-view btn-sm" style="display:none;white-space:nowrap;">
              <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
              Preview Store
            </a>
          </div>
        </div>
        <div class="form-group" style="margin-top:0.5rem;">
          <label class="toggle-wrap" style="cursor:pointer;">
            <label class="toggle">
              <input type="checkbox" id="showImagesToggle" name="show_images" checked onchange="toggleStoreImages()">
              <span class="toggle-slider"></span>
            </label>
            <span style="font-size:0.85rem;font-weight:600;color:#475569;">Show product images on store</span>
          </label>
          <div class="field-hint">Turn off if your products don't have images yet</div>
        </div>
      </div>
    </div>

    {{-- Save button --}}
    <div style="display:flex;justify-content:flex-end;gap:0.75rem;margin-bottom:2rem;">
      <button type="button" class="btn btn-secondary" onclick="loadSettings()">Reset</button>
      <button type="submit" class="btn btn-primary" id="saveSettingsBtn" style="gap:0.5rem;">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        Save Settings
      </button>
    </div>

  </form>
</div>
</div>
@endsection

@section('scripts')
<script>
/* ── SweetAlert2 Toast ────────────────────────────────── */
const Toast = Swal.mixin({
  toast: true, position: 'top-end', showConfirmButton: false,
  timer: 3500, timerProgressBar: true,
  didOpen: (t) => { t.addEventListener('mouseenter', Swal.stopTimer); t.addEventListener('mouseleave', Swal.resumeTimer); }
});

/* ── Required fields tracking ─────────────────────────── */
const requiredFields = ['business_name','business_email','phone','address','city','country','currency'];
const fieldLabels = {
  business_name:'Business Name', business_email:'Business Email', phone:'Phone Number',
  address:'Business Address', city:'City', country:'Country', currency:'Currency'
};

function checkCompleteness() {
  const form = document.getElementById('settingsForm');
  let filled = 0;
  const missing = [];
  requiredFields.forEach(name => {
    const el = form.querySelector(`[name="${name}"]`);
    const val = el ? el.value.trim() : '';
    if (val) filled++; else missing.push(fieldLabels[name] || name);
  });
  const pct = Math.round((filled / requiredFields.length) * 100);

  // Progress bar
  const pc = document.getElementById('progressCard');
  const pf = document.getElementById('progressFill');
  const pt = document.getElementById('progressText');
  const pd = document.getElementById('progressDesc');
  pc.style.display = 'flex';
  pf.style.width = pct + '%';
  pt.textContent = pct + '%';
  if (pct === 100) { pd.textContent = 'Your business profile is complete!'; pt.style.color = '#16a34a'; }
  else { pd.textContent = 'Complete your business details to unlock all features'; pt.style.color = '#2563eb'; }

  // Missing alert
  const ma = document.getElementById('missingAlert');
  const ml = document.getElementById('missingList');
  if (missing.length > 0) {
    ma.style.display = 'flex';
    ml.innerHTML = missing.map(m => `<li>${m}</li>`).join('');
  } else {
    ma.style.display = 'none';
  }
}

/* ── Load Settings ────────────────────────────────────── */
async function loadSettings(){
  try{
    const d = await apiFetch('/api/dashboard/settings');
    const form = document.getElementById('settingsForm');
    Object.entries(d).forEach(([k,v]) => {
      const el = form.querySelector(`[name="${k}"]`);
      if (el && v != null) el.value = v;
    });
    checkCompleteness();
  } catch(e) { console.error(e); }
}

/* ── Save Settings ────────────────────────────────────── */
async function saveSettings(e){
  e.preventDefault();
  const btn = document.getElementById('saveSettingsBtn');
  btn.disabled = true;
  const originalHTML = btn.innerHTML;
  btn.innerHTML = `<svg class="animate-spin" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707"/></svg> Saving…`;

  try{
    const data = Object.fromEntries(new FormData(document.getElementById('settingsForm')));
    await apiFetch('/api/dashboard/settings', {method:'PUT', body:JSON.stringify(data)});
    checkCompleteness();
    Toast.fire({ icon: 'success', title: 'Settings saved successfully!' });
  } catch(err) {
    Toast.fire({ icon: 'error', title: err.message || 'Failed to save settings' });
  } finally {
    btn.disabled = false;
    btn.innerHTML = originalHTML;
  }
}

// Re-check on input
['input','change'].forEach(evt => {
  document.getElementById('settingsForm').addEventListener(evt, () => {
    clearTimeout(window._compTimer);
    window._compTimer = setTimeout(checkCompleteness, 300);
  });
});

loadSettings();
</script>
@endsection
