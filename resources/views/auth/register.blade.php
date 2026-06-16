<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title>Create Account — MannaPOS</title>
<link rel="icon" href="{{ asset('logo.svg') }}" type="image/svg+xml">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
html,body{height:100%;-webkit-tap-highlight-color:transparent;}

body{
  font-family:'Inter',sans-serif;
  min-height:100vh;
  display:flex;
  align-items:center;
  justify-content:center;
  padding:0;
  background:#063d2a;
  overflow-x:hidden;
  -webkit-font-smoothing:antialiased;
}

/* ── Full-page background ── */
.bg{
  position:fixed;inset:0;z-index:0;
  background:linear-gradient(160deg,#022c1e 0%,#064e3b 30%,#065f46 60%,#047857 85%,#022c1e 100%);
  overflow:hidden;
}
.bg-circle{position:absolute;border-radius:50%;pointer-events:none;}
.bg-c1{width:500px;height:500px;background:radial-gradient(circle,rgba(16,185,129,.18) 0%,transparent 70%);top:-180px;right:-150px;}
.bg-c2{width:400px;height:400px;background:radial-gradient(circle,rgba(5,150,105,.12) 0%,transparent 70%);bottom:-120px;left:-100px;}
.bg-c3{width:240px;height:240px;background:radial-gradient(circle,rgba(167,243,208,.07) 0%,transparent 70%);top:45%;left:50%;transform:translate(-50%,-50%);}
.bg-dots{
  position:absolute;inset:0;
  background-image:radial-gradient(circle,rgba(255,255,255,.055) 1px,transparent 0);
  background-size:24px 24px;
}

/* ── App card ── */
.app-card{
  position:relative;z-index:1;
  width:100%;max-width:400px;
  margin:0 auto;
  background:#fff;
  border-radius:24px;
  box-shadow:0 32px 80px rgba(0,0,0,.35),0 0 0 1px rgba(255,255,255,.06);
  overflow:hidden;
}

/* ── Card top / brand ── */
.card-top{
  background:linear-gradient(160deg,#064e3b 0%,#065f46 50%,#047857 100%);
  padding:2.25rem 2rem 1.75rem;
  text-align:center;
  position:relative;
  overflow:hidden;
}
.card-top::before{
  content:'';position:absolute;inset:0;
  background-image:radial-gradient(circle,rgba(255,255,255,.06) 1px,transparent 0);
  background-size:20px 20px;
}
.card-top::after{
  content:'';position:absolute;
  width:260px;height:260px;border-radius:50%;
  background:rgba(16,185,129,.12);
  top:-120px;right:-80px;pointer-events:none;
}
.logo-wrap{
  position:relative;z-index:1;
  width:76px;height:76px;margin:0 auto .875rem;
  border-radius:20px;
  background:rgba(255,255,255,.12);
  padding:6px;
  box-shadow:0 8px 24px rgba(0,0,0,.2),0 0 0 1px rgba(255,255,255,.1);
}
.logo-wrap img{width:100%;height:100%;object-fit:contain;border-radius:14px;}
.brand-name{
  position:relative;z-index:1;
  font-size:1.45rem;font-weight:900;color:#fff;
  letter-spacing:-.5px;line-height:1;
  margin-bottom:.25rem;
}
.brand-tagline{
  position:relative;z-index:1;
  font-size:.72rem;color:rgba(255,255,255,.6);
  font-weight:500;letter-spacing:.5px;text-transform:uppercase;
}

/* ── Step progress ── */
.steps-bar{
  display:flex;align-items:center;
  padding:1rem 2rem .25rem;
  gap:0;
}
.step-dot{
  width:30px;height:30px;border-radius:50%;
  display:flex;align-items:center;justify-content:center;
  font-size:.72rem;font-weight:700;
  border:2px solid #e5e7eb;color:#9ca3af;background:#fff;
  transition:all .3s ease;flex-shrink:0;
}
.step-dot.active{background:#16a34a;border-color:#16a34a;color:#fff;box-shadow:0 0 0 4px rgba(22,163,74,.15);}
.step-dot.done{background:#16a34a;border-color:#16a34a;color:#fff;}
.step-line{flex:1;height:2px;background:#e5e7eb;transition:background .3s ease;}
.step-line.done{background:#16a34a;}
.step-label{font-size:.68rem;font-weight:600;color:#9ca3af;transition:color .3s;}
.step-label.active,.step-label.done{color:#16a34a;}
.step-meta{display:flex;justify-content:space-between;padding:.3rem 2rem .75rem;border-bottom:1px solid #f3f4f6;}

/* ── Form body ── */
.card-body{padding:1.375rem 2rem 1.75rem;}
.section-title{font-size:.95rem;font-weight:800;color:#111827;margin-bottom:.125rem;}
.section-sub{font-size:.75rem;color:#6b7280;margin-bottom:1.125rem;}

/* ── Form elements ── */
.row2{display:grid;grid-template-columns:1fr 1fr;gap:.65rem;}
.field{margin-bottom:.75rem;}
.field label{display:block;font-size:.73rem;font-weight:700;color:#374151;margin-bottom:.3rem;letter-spacing:.01em;}
.field label .req{color:#ef4444;margin-left:.1rem;}
.input-box{position:relative;}
.input-box .icon{
  position:absolute;left:.7rem;top:50%;transform:translateY(-50%);
  width:15px;height:15px;color:#9ca3af;pointer-events:none;
}
input.fc,select.fc{
  width:100%;padding:.6rem .7rem .6rem 2.2rem;
  border:1.5px solid #e5e7eb;border-radius:10px;
  font-size:.82rem;color:#111827;background:#f9fafb;
  font-family:inherit;outline:none;
  transition:border-color .2s,box-shadow .2s,background .2s;
  appearance:none;-webkit-appearance:none;
}
input.fc:focus,select.fc:focus{
  border-color:#16a34a;background:#fff;
  box-shadow:0 0 0 3px rgba(22,163,74,.1);
}
input.fc.err,select.fc.err{border-color:#ef4444;background:#fff;}
select.fc{cursor:pointer;}
.eye-btn{
  position:absolute;right:.65rem;top:50%;transform:translateY(-50%);
  background:none;border:none;cursor:pointer;color:#9ca3af;
  display:flex;align-items:center;padding:0;line-height:0;
}
.eye-btn:hover{color:#374151;}
.eye-btn svg{width:14px;height:14px;}
.hint{font-size:.67rem;color:#9ca3af;margin-top:.2rem;}

/* strength bar */
.sbar{height:2px;background:#e5e7eb;border-radius:2px;margin-top:.3rem;overflow:hidden;}
.sfill{height:100%;width:0;border-radius:2px;transition:width .3s,background .3s;}
.slbl{font-size:.66rem;color:#9ca3af;margin-top:.15rem;}

/* ── Buttons ── */
.btn-row{display:flex;gap:.5rem;margin-top:1.125rem;}
.btn{
  padding:.7rem 1rem;border-radius:10px;
  font-size:.84rem;font-weight:700;
  cursor:pointer;border:none;font-family:inherit;
  display:flex;align-items:center;justify-content:center;gap:.4rem;
  transition:transform .15s,box-shadow .15s,background .15s,opacity .15s;
  -webkit-tap-highlight-color:transparent;
}
.btn-main{
  flex:1;
  background:linear-gradient(135deg,#16a34a,#15803d);
  color:#fff;
  box-shadow:0 4px 16px rgba(22,163,74,.35);
}
.btn-main:hover{background:linear-gradient(135deg,#15803d,#166534);transform:translateY(-1px);box-shadow:0 6px 20px rgba(22,163,74,.4);}
.btn-main:active{transform:translateY(0);opacity:.9;}
.btn-main:disabled{opacity:.6;cursor:not-allowed;transform:none;box-shadow:none;}
.btn-back{
  background:#f3f4f6;color:#374151;min-width:72px;font-weight:600;
}
.btn-back:hover{background:#e5e7eb;}

/* ── Bottom link ── */
.signin-row{
  text-align:center;padding:.875rem 2rem 1.25rem;
  border-top:1px solid #f3f4f6;
  font-size:.78rem;color:#6b7280;
}
.signin-row a{color:#16a34a;font-weight:700;text-decoration:none;}

/* ── Step panels ── */
.panel{display:none;}
.panel.on{display:block;}

/* ── Spinner ── */
@keyframes spin{to{transform:rotate(360deg)}}
.spin-ico{display:inline-block;width:14px;height:14px;border:2px solid rgba(255,255,255,.35);border-top-color:#fff;border-radius:50%;animation:spin .7s linear infinite;}

/* ── Mobile (full screen) ── */
@media(max-width:440px){
  body{align-items:flex-start;padding:0;}
  .app-card{border-radius:0;min-height:100vh;max-width:100%;box-shadow:none;}
  .card-top{border-radius:0;}
}
@media(min-width:441px){
  body{padding:1.5rem;}
}
</style>
</head>
<body>

<!-- Background -->
<div class="bg">
  <div class="bg-dots"></div>
  <div class="bg-circle bg-c1"></div>
  <div class="bg-circle bg-c2"></div>
  <div class="bg-circle bg-c3"></div>
</div>

<div class="app-card">

  <!-- ── Brand top ── -->
  <div class="card-top">
    <div class="logo-wrap">
      <img src="{{ asset('logo.svg') }}" alt="MannaPOS">
    </div>
    <div class="brand-name">MannaPOS</div>
    <div class="brand-tagline">Business Management System</div>
  </div>

  <!-- ── Step bar ── -->
  <div class="steps-bar">
    <div class="step-dot active" id="sd1">1</div>
    <div class="step-line" id="sl"></div>
    <div class="step-dot" id="sd2">2</div>
  </div>
  <div class="step-meta">
    <span class="step-label active" id="sn1">Account Details</span>
    <span class="step-label" id="sn2">Your Business</span>
  </div>

  <!-- ── Form ── -->
  <div class="card-body">
    <form method="POST" action="{{ route('register') }}" id="regForm" novalidate>
      @csrf

      <!-- STEP 1 -->
      <div class="panel on" id="p1">
        <div class="section-title">Create your account</div>
        <div class="section-sub">Enter your personal details to get started</div>

        <div class="row2">
          <div class="field">
            <label>First Name <span class="req">*</span></label>
            <div class="input-box">
              <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
              <input type="text" name="first_name" id="fn" class="fc @error('first_name') err @enderror" value="{{ old('first_name') }}" placeholder="John" autocomplete="given-name">
            </div>
          </div>
          <div class="field">
            <label>Last Name <span class="req">*</span></label>
            <div class="input-box">
              <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
              <input type="text" name="last_name" id="ln" class="fc @error('last_name') err @enderror" value="{{ old('last_name') }}" placeholder="Doe" autocomplete="family-name">
            </div>
          </div>
        </div>

        <div class="field">
          <label>Phone Number <span class="req">*</span></label>
          <div class="input-box">
            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
            <input type="tel" name="phone" id="ph" class="fc @error('phone') err @enderror" value="{{ old('phone') }}" placeholder="+255 712 345 678" autocomplete="tel">
          </div>
          <div class="hint">Include country code — e.g. +255 for Tanzania</div>
        </div>

        <div class="field">
          <label>Email Address <span class="req">*</span></label>
          <div class="input-box">
            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            <input type="email" name="email" id="em" class="fc @error('email') err @enderror" value="{{ old('email') }}" placeholder="name@business.com" autocomplete="email">
          </div>
        </div>

        <div class="row2">
          <div class="field">
            <label>Password <span class="req">*</span></label>
            <div class="input-box">
              <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
              <input type="password" name="password" id="pw" class="fc @error('password') err @enderror" placeholder="Min 8 chars" autocomplete="new-password" oninput="chkStr(this)" style="padding-right:2.1rem;">
              <button type="button" class="eye-btn" onclick="togPwd('pw')" tabindex="-1">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              </button>
            </div>
            <div class="sbar"><div class="sfill" id="sf"></div></div>
            <div class="slbl" id="sl2"></div>
          </div>
          <div class="field">
            <label>Confirm <span class="req">*</span></label>
            <div class="input-box">
              <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              <input type="password" name="password_confirmation" id="pc" class="fc" placeholder="Repeat" autocomplete="new-password" style="padding-right:2.1rem;">
              <button type="button" class="eye-btn" onclick="togPwd('pc')" tabindex="-1">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              </button>
            </div>
          </div>
        </div>

        <div class="btn-row">
          <button type="button" class="btn btn-main" onclick="goNext()">
            Continue
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
          </button>
        </div>
      </div>

      <!-- STEP 2 -->
      <div class="panel" id="p2">
        <div class="section-title">Set up your business</div>
        <div class="section-sub">Tell us about your business</div>

        <div class="field">
          <label>Business Name <span class="req">*</span></label>
          <div class="input-box">
            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            <input type="text" name="business_name" id="bn" class="fc @error('business_name') err @enderror" value="{{ old('business_name') }}" placeholder="e.g. Manna Supermarket">
          </div>
        </div>

        <div class="field">
          <label>Business Type</label>
          <div class="input-box">
            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
            <select name="business_type" class="fc" style="padding-left:2.2rem;">
              <option value="">Select type</option>
              <option value="retail" {{ old('business_type')=='retail'?'selected':'' }}>Retail Shop</option>
              <option value="wholesale" {{ old('business_type')=='wholesale'?'selected':'' }}>Wholesale</option>
              <option value="restaurant" {{ old('business_type')=='restaurant'?'selected':'' }}>Restaurant / Cafe</option>
              <option value="supermarket" {{ old('business_type')=='supermarket'?'selected':'' }}>Supermarket</option>
              <option value="pharmacy" {{ old('business_type')=='pharmacy'?'selected':'' }}>Pharmacy</option>
              <option value="electronics" {{ old('business_type')=='electronics'?'selected':'' }}>Electronics</option>
              <option value="services" {{ old('business_type')=='services'?'selected':'' }}>Services</option>
              <option value="other" {{ old('business_type')=='other'?'selected':'' }}>Other</option>
            </select>
          </div>
        </div>

        <div class="row2">
          <div class="field">
            <label>Country <span class="req">*</span></label>
            <div class="input-box">
              <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/></svg>
              <select name="business_country" class="fc @error('business_country') err @enderror" style="padding-left:2.2rem;">
                <option value="">Country</option>
                <option value="Tanzania" {{ old('business_country')=='Tanzania'?'selected':'' }}>Tanzania</option>
                <option value="Kenya" {{ old('business_country')=='Kenya'?'selected':'' }}>Kenya</option>
                <option value="Uganda" {{ old('business_country')=='Uganda'?'selected':'' }}>Uganda</option>
                <option value="Rwanda" {{ old('business_country')=='Rwanda'?'selected':'' }}>Rwanda</option>
                <option value="Ethiopia" {{ old('business_country')=='Ethiopia'?'selected':'' }}>Ethiopia</option>
                <option value="South Africa" {{ old('business_country')=='South Africa'?'selected':'' }}>South Africa</option>
                <option value="Nigeria" {{ old('business_country')=='Nigeria'?'selected':'' }}>Nigeria</option>
                <option value="Ghana" {{ old('business_country')=='Ghana'?'selected':'' }}>Ghana</option>
                <option value="Other" {{ old('business_country')=='Other'?'selected':'' }}>Other</option>
              </select>
            </div>
          </div>
          <div class="field">
            <label>Currency <span class="req">*</span></label>
            <div class="input-box">
              <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              <select name="currency" class="fc @error('currency') err @enderror" style="padding-left:2.2rem;">
                <option value="">Currency</option>
                <option value="TZS" {{ old('currency')=='TZS'?'selected':'' }}>TZS — Shilling</option>
                <option value="KES" {{ old('currency')=='KES'?'selected':'' }}>KES — Kenyan Sh.</option>
                <option value="UGX" {{ old('currency')=='UGX'?'selected':'' }}>UGX — Uganda Sh.</option>
                <option value="USD" {{ old('currency')=='USD'?'selected':'' }}>USD — US Dollar</option>
                <option value="EUR" {{ old('currency')=='EUR'?'selected':'' }}>EUR — Euro</option>
                <option value="GBP" {{ old('currency')=='GBP'?'selected':'' }}>GBP — Pound</option>
                <option value="ZAR" {{ old('currency')=='ZAR'?'selected':'' }}>ZAR — Rand</option>
                <option value="NGN" {{ old('currency')=='NGN'?'selected':'' }}>NGN — Naira</option>
                <option value="GHS" {{ old('currency')=='GHS'?'selected':'' }}>GHS — Cedi</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Free trial badge -->
        <div style="display:flex;align-items:center;gap:.5rem;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:.65rem .875rem;margin:.5rem 0 .25rem;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <span style="font-size:.75rem;font-weight:600;color:#15803d;">Free 14-day trial — no credit card required</span>
        </div>

        <div class="btn-row">
          <button type="button" class="btn btn-back" onclick="goBack()">← Back</button>
          <button type="submit" class="btn btn-main" id="subBtn">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            Create Account
          </button>
        </div>
      </div>

    </form>
  </div>

  <!-- ── Sign in link ── -->
  <div class="signin-row">
    Already have an account? <a href="{{ route('login') }}">Sign in</a>
  </div>

</div>

<script>
// ── Toast (top-right) ─────────────────────────────
const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 4500,
  timerProgressBar: true,
});
const toast = (icon, msg) => Toast.fire({ icon, title: msg });

// ── Steps ─────────────────────────────────────────
function setStep(s) {
  document.getElementById('p1').classList.toggle('on', s === 1);
  document.getElementById('p2').classList.toggle('on', s === 2);
  const d1 = document.getElementById('sd1'), n1 = document.getElementById('sn1');
  const d2 = document.getElementById('sd2'), n2 = document.getElementById('sn2');
  const sl = document.getElementById('sl');
  if (s === 1) {
    d1.className = 'step-dot active'; d1.textContent = '1'; n1.className = 'step-label active';
    d2.className = 'step-dot'; d2.textContent = '2'; n2.className = 'step-label';
    sl.className = 'step-line';
  } else {
    d1.className = 'step-dot done'; d1.innerHTML = '&#10003;'; n1.className = 'step-label done';
    d2.className = 'step-dot active'; d2.textContent = '2'; n2.className = 'step-label active';
    sl.className = 'step-line done';
  }
}
function goBack() { setStep(1); }

// ── Step 1 validation ─────────────────────────────
function goNext() {
  const fn = document.getElementById('fn').value.trim();
  const ln = document.getElementById('ln').value.trim();
  const ph = document.getElementById('ph').value.trim();
  const em = document.getElementById('em').value.trim();
  const pw = document.getElementById('pw').value;
  const pc = document.getElementById('pc').value;

  if (!fn) { toast('warning', 'Please enter your first name.'); document.getElementById('fn').focus(); return; }
  if (!ln) { toast('warning', 'Please enter your last name.'); document.getElementById('ln').focus(); return; }
  if (!ph) { toast('warning', 'Phone number is required.'); document.getElementById('ph').focus(); return; }
  if (!ph.startsWith('+')) { toast('info', 'Phone must include country code, e.g. +255…'); document.getElementById('ph').focus(); return; }
  if (!em || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(em)) { toast('warning', 'Please enter a valid email address.'); document.getElementById('em').focus(); return; }
  if (pw.length < 8) { toast('warning', 'Password must be at least 8 characters.'); document.getElementById('pw').focus(); return; }
  if (pw !== pc) { toast('error', 'Passwords do not match.'); document.getElementById('pc').focus(); return; }

  setStep(2);
  setTimeout(() => document.getElementById('bn').focus(), 200);
}

// ── Password toggle ───────────────────────────────
function togPwd(id) {
  const el = document.getElementById(id);
  el.type = el.type === 'password' ? 'text' : 'password';
}

// ── Password strength ─────────────────────────────
function chkStr(el) {
  const v = el.value; let s = 0;
  if (v.length >= 8) s++; if (/[A-Z]/.test(v)) s++; if (/[0-9]/.test(v)) s++; if (/[^A-Za-z0-9]/.test(v)) s++;
  const C = ['','#ef4444','#f59e0b','#16a34a','#16a34a'];
  const L = ['','Weak','Fair','Good','Strong'];
  const f = document.getElementById('sf'), l = document.getElementById('sl2');
  f.style.width = (s * 25) + '%'; f.style.background = C[s] || '';
  l.textContent = v ? L[s] || 'Strong' : ''; l.style.color = C[s] || '';
}

// ── Submit ────────────────────────────────────────
document.getElementById('regForm').addEventListener('submit', function(e) {
  const bn = document.getElementById('bn').value.trim();
  const country = this.querySelector('[name="business_country"]').value;
  const currency = this.querySelector('[name="currency"]').value;
  if (!bn) { e.preventDefault(); toast('warning', 'Please enter your business name.'); document.getElementById('bn').focus(); return; }
  if (!country) { e.preventDefault(); toast('warning', 'Please select your country.'); return; }
  if (!currency) { e.preventDefault(); toast('warning', 'Please select your currency.'); return; }
  const btn = document.getElementById('subBtn');
  btn.disabled = true;
  btn.innerHTML = '<span class="spin-ico"></span> Creating…';
});

// ── Server errors → toasts ────────────────────────
@if($errors->any())
  document.addEventListener('DOMContentLoaded', function() {
    @foreach($errors->all() as $err)
      toast('error', @json($err));
    @endforeach
  });
@endif

// ── Jump to step 2 on server errors ──────────────
@if($errors->has('business_name') || $errors->has('business_country') || $errors->has('currency'))
  document.addEventListener('DOMContentLoaded', function() { setStep(2); });
@endif
</script>
</body>
</html>
