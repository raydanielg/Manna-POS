<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Setup Your Business — MannaPOS</title>
<link rel="icon" type="image/png" href="{{ asset('icons8-dynamics-365-100.png') }}">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Inter',sans-serif;background:linear-gradient(135deg,#f0f4ff 0%,#e8f5e9 100%);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:1.5rem;}
.setup-wrap{background:#fff;border-radius:24px;box-shadow:0 20px 60px rgba(0,0,0,.1);width:100%;max-width:680px;overflow:hidden;}
.setup-header{background:linear-gradient(135deg,#1e3a5f,#0f2748);padding:2rem 2.5rem;color:#fff;}
.setup-brand{display:flex;align-items:center;gap:.75rem;margin-bottom:1.5rem;}
.setup-brand-icon{width:38px;height:38px;background:linear-gradient(135deg,#10b981,#059669);border-radius:10px;display:flex;align-items:center;justify-content:center;}
.setup-brand-icon svg{width:22px;height:22px;color:#fff;}
.setup-brand-name{font-size:1.2rem;font-weight:800;color:#fff;}
.welcome-text h1{font-size:1.5rem;font-weight:800;margin-bottom:.35rem;}
.welcome-text p{color:#94b3d4;font-size:.9rem;}
/* Steps bar */
.steps-bar{display:flex;justify-content:center;gap:0;padding:1.5rem 2.5rem 0;}
.wiz-step{display:flex;flex-direction:column;align-items:center;flex:1;}
.wiz-step-line{height:2px;flex:1;background:#e2e8f0;margin-top:15px;}
.wiz-step-line.done{background:#10b981;}
.wiz-circle{width:32px;height:32px;border-radius:50%;border:2px solid #e2e8f0;display:flex;align-items:center;justify-content:center;font-size:.8rem;font-weight:700;color:#94a3b8;background:#fff;transition:all .3s;margin-bottom:.35rem;}
.wiz-circle.active{border-color:#2563eb;background:#2563eb;color:#fff;box-shadow:0 0 0 4px rgba(37,99,235,.15);}
.wiz-circle.done{border-color:#10b981;background:#10b981;color:#fff;}
.wiz-step-label{font-size:.72rem;font-weight:600;color:#94a3b8;text-align:center;}
.wiz-step-label.active{color:#2563eb;}
.wiz-step-label.done{color:#10b981;}
.step-row{display:flex;align-items:center;width:100%;}
/* Content */
.setup-body{padding:2rem 2.5rem;}
.panel{display:none;}
.panel.active{display:block;}
.panel-title{font-size:1.2rem;font-weight:800;color:#0f172a;margin-bottom:.35rem;}
.panel-desc{font-size:.875rem;color:#64748b;margin-bottom:1.5rem;}
.form-group{margin-bottom:1rem;}
.form-label{display:block;font-size:.8rem;font-weight:600;color:#374151;margin-bottom:.4rem;}
.input-wrap{position:relative;}
.input-wrap svg.ico{position:absolute;left:.85rem;top:50%;transform:translateY(-50%);width:17px;height:17px;color:#94a3b8;pointer-events:none;}
.form-control{width:100%;padding:.65rem .85rem .65rem 2.5rem;border:1.5px solid #e2e8f0;border-radius:10px;font-size:.875rem;color:#0f172a;background:#f8fafc;transition:all .2s;outline:none;font-family:inherit;}
.form-control:focus{border-color:#2563eb;background:#fff;box-shadow:0 0 0 3px rgba(37,99,235,.1);}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:.75rem;}
.feature-cards{display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-bottom:1.5rem;}
.feat-card{border:1.5px solid #e9edf5;border-radius:12px;padding:1rem;display:flex;align-items:flex-start;gap:.75rem;cursor:pointer;transition:all .2s;}
.feat-card:hover,.feat-card.selected{border-color:#2563eb;background:#eff6ff;}
.feat-card-icon{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.feat-card h4{font-size:.85rem;font-weight:700;color:#1e293b;margin-bottom:.2rem;}
.feat-card p{font-size:.75rem;color:#64748b;}
.btn-row{display:flex;gap:.75rem;margin-top:1.5rem;}
.btn{padding:.7rem 1.5rem;border-radius:10px;font-size:.875rem;font-weight:600;cursor:pointer;border:none;transition:all .2s;font-family:inherit;}
.btn-primary{background:linear-gradient(135deg,#2563eb,#1d4ed8);color:#fff;flex:1;box-shadow:0 4px 12px rgba(37,99,235,.3);}
.btn-primary:hover{background:linear-gradient(135deg,#1d4ed8,#1e40af);transform:translateY(-1px);}
.btn-secondary{background:#f1f5f9;color:#475569;min-width:90px;}
.btn-secondary:hover{background:#e2e8f0;}
/* Done screen */
.done-screen{text-align:center;padding:2rem 0 1rem;}
.done-icon{width:80px;height:80px;background:linear-gradient(135deg,#10b981,#059669);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;box-shadow:0 8px 24px rgba(16,185,129,.3);}
.done-icon svg{width:40px;height:40px;color:#fff;}
.done-screen h2{font-size:1.5rem;font-weight:800;color:#0f172a;margin-bottom:.5rem;}
.done-screen p{color:#64748b;font-size:.9rem;line-height:1.6;max-width:420px;margin:0 auto 2rem;}
.done-btn{display:inline-flex;align-items:center;gap:.5rem;background:linear-gradient(135deg,#2563eb,#1d4ed8);color:#fff;padding:.875rem 2rem;border-radius:12px;font-size:.95rem;font-weight:700;border:none;cursor:pointer;box-shadow:0 8px 20px rgba(37,99,235,.3);text-decoration:none;transition:all .2s;}
.done-btn:hover{transform:translateY(-2px);box-shadow:0 12px 24px rgba(37,99,235,.4);}
</style>
</head>
<body>
<div class="setup-wrap">
  <div class="setup-header">
    <div class="setup-brand">
      <div class="setup-brand-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
      </div>
      <span class="setup-brand-name">MannaPOS</span>
    </div>
    <div class="welcome-text">
      <h1>Welcome, {{ explode(' ', $user->name)[0] }}! 👋</h1>
      <p>Let's set up your business in 2 quick steps</p>
    </div>
  </div>

  <!-- Steps indicator -->
  <div style="padding:1.5rem 2.5rem 0;">
    <div style="display:flex;align-items:center;">
      <div style="display:flex;flex-direction:column;align-items:center;">
        <div class="wiz-circle active" id="wc1">1</div>
        <span class="wiz-step-label active" id="wl1">Business Info</span>
      </div>
      <div style="flex:1;height:2px;background:#e2e8f0;margin:-14px .75rem 0;" id="wline1"></div>
      <div style="display:flex;flex-direction:column;align-items:center;">
        <div class="wiz-circle" id="wc2">2</div>
        <span class="wiz-step-label" id="wl2">Preferences</span>
      </div>
      <div style="flex:1;height:2px;background:#e2e8f0;margin:-14px .75rem 0;" id="wline2"></div>
      <div style="display:flex;flex-direction:column;align-items:center;">
        <div class="wiz-circle" id="wc3">3</div>
        <span class="wiz-step-label" id="wl3">Done!</span>
      </div>
    </div>
  </div>

  <div class="setup-body">
    <form id="setupForm" method="POST" action="/setup">
      @csrf

    <!-- PANEL 1: Business Info -->
    <div class="panel active" id="wp1">
      <h3 class="panel-title">Confirm your business details</h3>
      <p class="panel-desc">We've pre-filled this from your registration. Update anything that's not right.</p>

      <div class="form-group">
        <label class="form-label">Business Name *</label>
        <div class="input-wrap">
          <svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
          <input type="text" name="business_name" class="form-control" value="{{ $user->business_name }}" required>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">City / Town</label>
          <div class="input-wrap">
            <svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <input type="text" name="business_city" class="form-control" value="{{ $user->business_city }}" placeholder="e.g. Dar es Salaam">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Currency *</label>
          <div class="input-wrap">
            <svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <select name="currency" class="form-control" style="padding-left:2.5rem;" required>
              <option value="TZS" {{ $user->currency=='TZS'?'selected':'' }}>TZS — Tanzanian Shilling</option>
              <option value="KES" {{ $user->currency=='KES'?'selected':'' }}>KES — Kenyan Shilling</option>
              <option value="UGX" {{ $user->currency=='UGX'?'selected':'' }}>UGX — Ugandan Shilling</option>
              <option value="USD" {{ $user->currency=='USD'?'selected':'' }}>USD — US Dollar</option>
              <option value="EUR" {{ $user->currency=='EUR'?'selected':'' }}>EUR — Euro</option>
              <option value="GBP" {{ $user->currency=='GBP'?'selected':'' }}>GBP — British Pound</option>
              <option value="NGN" {{ $user->currency=='NGN'?'selected':'' }}>NGN — Nigerian Naira</option>
            </select>
          </div>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Business Address</label>
        <div class="input-wrap">
          <svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
          <input type="text" name="business_address" class="form-control" value="{{ $user->business_address }}" placeholder="Street address">
        </div>
      </div>
      <div class="btn-row">
        <button type="button" class="btn btn-primary" onclick="goWiz(2)">
          Next: Preferences →
        </button>
      </div>
    </div>

    <!-- PANEL 2: Preferences -->
    <div class="panel" id="wp2">
      <h3 class="panel-title">Set your preferences</h3>
      <p class="panel-desc">Help us customize your experience for your business needs.</p>

      <div class="form-group">
        <label class="form-label">Fiscal Year Start</label>
        <div class="input-wrap">
          <svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
          <select name="fiscal_year_start" class="form-control" style="padding-left:2.5rem;">
            <option value="January">January</option><option value="February">February</option>
            <option value="March">March</option><option value="April">April</option>
            <option value="May">May</option><option value="June">June</option>
            <option value="July">July</option><option value="August">August</option>
            <option value="September">September</option><option value="October">October</option>
            <option value="November">November</option><option value="December">December</option>
          </select>
        </div>
      </div>

      <p style="font-size:.8rem;font-weight:600;color:#374151;margin-bottom:.75rem;">What will you mainly use MannaPOS for?</p>
      <div class="feature-cards" id="useCase">
        <div class="feat-card selected" onclick="selectUse(this)">
          <div class="feat-card-icon" style="background:#dbeafe;"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg></div>
          <div><h4>Retail Sales</h4><p>POS, inventory & customers</p></div>
        </div>
        <div class="feat-card" onclick="selectUse(this)">
          <div class="feat-card-icon" style="background:#dcfce7;"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg></div>
          <div><h4>Wholesale</h4><p>Bulk orders & purchases</p></div>
        </div>
        <div class="feat-card" onclick="selectUse(this)">
          <div class="feat-card-icon" style="background:#fef9c3;"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg></div>
          <div><h4>Services</h4><p>Invoices & billing</p></div>
        </div>
        <div class="feat-card" onclick="selectUse(this)">
          <div class="feat-card-icon" style="background:#fce7f3;"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#db2777" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg></div>
          <div><h4>Restaurant</h4><p>Menu, orders & tables</p></div>
        </div>
      </div>

      <div class="btn-row">
        <button type="button" class="btn btn-secondary" onclick="goWiz(1)">← Back</button>
        <button type="button" class="btn btn-primary" onclick="goWiz(3)">
          All Done! →
        </button>
      </div>
    </div>

    <!-- PANEL 3: Done -->
    <div class="panel" id="wp3">
      <div class="done-screen">
        <div class="done-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <h2>You're all set, {{ explode(' ', $user->name)[0] }}!</h2>
        <p>Your business is configured. You have a <strong>14-day free trial</strong> active. Choose a plan to continue after the trial ends.</p>
        <button type="submit" class="done-btn" id="completeBtn">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
          View Plans & Go to Dashboard
        </button>
      </div>
    </div>

    </form>
  </div>
</div>

<script>
let currentStep=1;
function goWiz(step){
  if(step===2){
    const bname=document.querySelector('[name="business_name"]').value.trim();
    if(!bname){alert('Business name is required.');return;}
  }
  document.getElementById('wp'+currentStep).classList.remove('active');
  document.getElementById('wp'+step).classList.add('active');
  // Update circles
  for(let i=1;i<=3;i++){
    const c=document.getElementById('wc'+i);
    const l=document.getElementById('wl'+i);
    if(!c)continue;
    if(i<step){c.className='wiz-circle done';c.innerHTML='✓';l.className='wiz-step-label done';}
    else if(i===step){c.className='wiz-circle active';c.innerHTML=i;l.className='wiz-step-label active';}
    else{c.className='wiz-circle';c.innerHTML=i;l.className='wiz-step-label';}
    if(i<3){const ln=document.getElementById('wline'+i);if(ln)ln.style.background=i<step?'#10b981':'#e2e8f0';}
  }
  currentStep=step;
}
function selectUse(el){
  document.querySelectorAll('.feat-card').forEach(c=>c.classList.remove('selected'));
  el.classList.add('selected');
}
document.getElementById('setupForm').addEventListener('submit',function(){
  const btn=document.getElementById('completeBtn');btn.disabled=true;btn.innerHTML='<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation:spin 1s linear infinite"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg> Setting up...';
});
</script>
<style>@keyframes spin{from{transform:rotate(0deg)}to{transform:rotate(360deg)}}</style>
</body>
</html>
