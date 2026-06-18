<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Create Account — MannaPOS</title>
<link rel="icon" type="image/png" href="{{ asset('icons8-dynamics-365-100.png') }}">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
html{height:100%;}
body{
  font-family:'Inter',sans-serif;
  min-height:100vh;
  display:flex;align-items:center;justify-content:center;
  padding:1.25rem;
  position:relative;
  overflow-x:hidden;
  background:#0f172a;
}
/* ── Background layers ── */
body::before{
  content:'';position:fixed;inset:0;
  background:
    linear-gradient(135deg,#0f172a 0%,#1e3a8a 35%,#2563eb 65%,#0f172a 100%);
  z-index:0;
}
body::after{
  content:'';position:fixed;inset:0;
  background-image:
    radial-gradient(circle at 1px 1px,rgba(255,255,255,0.06) 1px,transparent 0);
  background-size:28px 28px;
  z-index:1;
  pointer-events:none;
}
/* decorative blobs */
.bg-blob{position:fixed;border-radius:50%;filter:blur(70px);pointer-events:none;z-index:1;}
.blob1{width:400px;height:400px;background:rgba(59,130,246,0.18);top:-120px;right:-100px;}
.blob2{width:350px;height:350px;background:rgba(37,99,235,0.12);bottom:-100px;left:-80px;}
.blob3{width:200px;height:200px;background:rgba(96,165,250,0.10);top:40%;left:5%;}

/* ── Card ── */
.card{
  position:relative;z-index:2;
  background:#fff;
  border-radius:12px;
  box-shadow:0 24px 64px rgba(0,0,0,0.28),0 4px 16px rgba(0,0,0,0.12);
  width:100%;max-width:460px;
  overflow:hidden;
}

/* ── Card header stripe ── */
.card-head{
  background:linear-gradient(135deg,#1e3a8a,#2563eb);
  padding:1.5rem 2rem 1.375rem;
  display:flex;align-items:center;gap:.875rem;
}
.brand-icon{
  width:40px;height:40px;border-radius:10px;
  background:rgba(255,255,255,0.15);
  display:flex;align-items:center;justify-content:center;
  flex-shrink:0;
}
.brand-icon svg{width:22px;height:22px;color:#fff;}
.brand-text{flex:1;}
.brand-name{font-size:1.15rem;font-weight:800;color:#fff;letter-spacing:-.4px;line-height:1;}
.brand-tag{font-size:.7rem;color:rgba(255,255,255,.65);font-weight:500;margin-top:.15rem;}
.trial-pill{
  display:flex;align-items:center;gap:.35rem;
  background:rgba(255,255,255,0.12);
  border:1px solid rgba(255,255,255,0.2);
  color:#d1fae5;padding:.3rem .65rem;
  border-radius:50px;font-size:.68rem;font-weight:600;white-space:nowrap;
}
.trial-pill svg{width:12px;height:12px;}

/* ── Card body ── */
.card-body{padding:1.75rem 2rem 2rem;}

/* ── Steps ── */
.steps{display:flex;align-items:center;gap:0;margin-bottom:1.5rem;}
.step{display:flex;align-items:center;gap:.45rem;}
.step-dot{
  width:28px;height:28px;border-radius:50%;
  font-size:.75rem;font-weight:700;
  display:flex;align-items:center;justify-content:center;
  border:2px solid #d1d5db;color:#9ca3af;background:#fff;
  transition:all .25s ease;flex-shrink:0;
}
.step-dot.active{background:#2563eb;border-color:#2563eb;color:#fff;box-shadow:0 0 0 3px rgba(37,99,235,.20);}
.step-dot.done{background:#2563eb;border-color:#2563eb;color:#fff;}
.step-name{font-size:.72rem;font-weight:600;color:#9ca3af;transition:color .25s;}
.step-name.active,.step-name.done{color:#2563eb;}
.step-line{flex:1;height:2px;background:#e5e7eb;margin:0 .5rem;transition:background .25s;}
.step-line.done{background:#2563eb;}

/* ── Form ── */
.step-head{margin-bottom:1.25rem;}
.step-title{font-size:1.1rem;font-weight:800;color:#111827;margin-bottom:.2rem;}
.step-sub{font-size:.8rem;color:#6b7280;}

.form-row{display:grid;grid-template-columns:1fr 1fr;gap:.75rem;}
.form-group{margin-bottom:.875rem;}
.form-label{display:block;font-size:.76rem;font-weight:600;color:#374151;margin-bottom:.35rem;}
.form-label .req{color:#ef4444;}

.input-box{position:relative;}
.input-box .ico{
  position:absolute;left:.75rem;top:50%;transform:translateY(-50%);
  width:15px;height:15px;color:#9ca3af;pointer-events:none;flex-shrink:0;
}
.form-control{
  width:100%;
  padding:.6rem .8rem .6rem 2.35rem;
  border:1.5px solid #d1d5db;
  border-radius:8px;
  font-size:.83rem;color:#111827;background:#f9fafb;
  font-family:inherit;outline:none;
  transition:border-color .2s,box-shadow .2s,background .2s;
  appearance:none;
}
.form-control:focus{
  border-color:#2563eb;background:#fff;
  box-shadow:0 0 0 3px rgba(37,99,235,0.12);
}
.form-control.err{border-color:#ef4444;background:#fff;}
select.form-control{cursor:pointer;}

/* password toggle */
.pwd-btn{
  position:absolute;right:.7rem;top:50%;transform:translateY(-50%);
  background:none;border:none;cursor:pointer;color:#9ca3af;padding:0;
  display:flex;line-height:0;
}
.pwd-btn svg{width:15px;height:15px;}
.pwd-btn:hover{color:#374151;}

/* strength bar */
.strength{height:2px;background:#e5e7eb;border-radius:2px;margin-top:.35rem;overflow:hidden;}
.strength-fill{height:100%;width:0;border-radius:2px;transition:width .3s,background .3s;}
.strength-lbl{font-size:.68rem;color:#9ca3af;margin-top:.2rem;}

/* phone hint */
.hint{font-size:.7rem;color:#9ca3af;margin-top:.2rem;}

/* ── Buttons ── */
.btn-row{display:flex;gap:.6rem;margin-top:1.25rem;}
.btn{
  padding:.65rem 1.25rem;border-radius:8px;font-size:.83rem;font-weight:600;
  cursor:pointer;border:none;font-family:inherit;
  transition:transform .15s,box-shadow .15s,background .15s;
  display:flex;align-items:center;justify-content:center;gap:.4rem;
}
.btn-blue{
  flex:1;
  background:linear-gradient(135deg,#2563eb,#1e3a8a);color:#fff;
  box-shadow:0 4px 14px rgba(37,99,235,.35);
}
.btn-blue:hover{background:linear-gradient(135deg,#1d4ed8,#1e40af);transform:translateY(-1px);box-shadow:0 6px 18px rgba(37,99,235,.4);}
.btn-green:active{transform:translateY(0);}
.btn-green:disabled{opacity:.6;cursor:not-allowed;transform:none;box-shadow:none;}
.btn-ghost{background:#f3f4f6;color:#374151;min-width:80px;}
.btn-ghost:hover{background:#e5e7eb;}

/* ── Login link ── */
.login-link{text-align:center;margin-top:1.25rem;font-size:.8rem;color:#6b7280;}
.login-link a{color:#2563eb;font-weight:600;text-decoration:none;}
.login-link a:hover{text-decoration:underline;}

/* ── Step panels ── */
.panel{display:none;}
.panel.active{display:block;}

/* ── Divider ── */
.divider{height:1px;background:#f3f4f6;margin:.5rem 0 1rem;}

/* ── Spinner ── */
@keyframes spin{from{transform:rotate(0deg)}to{transform:rotate(360deg)}}
.spin{animation:spin .8s linear infinite;display:inline-block;}

/* ── Mobile ── */
@media(max-width:480px){
  .card-body{padding:1.25rem 1.25rem 1.5rem;}
  .card-head{padding:1.25rem 1.25rem 1.125rem;}
  .form-row{grid-template-columns:1fr;}
  .trial-pill{display:none;}
}
</style>
</head>
<body>
<div class="bg-blob blob1"></div>
<div class="bg-blob blob2"></div>
<div class="bg-blob blob3"></div>

<div class="card">
  <!-- ── Header ── -->
  <div class="card-head">
    <div class="brand-icon">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
      </svg>
    </div>
    <div class="brand-text">
      <div class="brand-name">MannaPOS</div>
      <div class="brand-tag">Business Management System</div>
    </div>
    <div class="trial-pill">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      14-day free trial
    </div>
  </div>

  <!-- ── Body ── -->
  <div class="card-body">

    <!-- Step indicator -->
    <div class="steps">
      <div class="step">
        <div class="step-dot active" id="sd1">1</div>
        <span class="step-name active" id="sn1">Account</span>
      </div>
      <div class="step-line" id="sline"></div>
      <div class="step">
        <div class="step-dot" id="sd2">2</div>
        <span class="step-name" id="sn2">Business</span>
      </div>
    </div>

    <form method="POST" action="{{ route('register') }}" id="regForm" novalidate>
      @csrf

      <!-- ── STEP 1 ── -->
      <div class="panel active" id="p1">
        <div class="step-head">
          <div class="step-title">Create your account</div>
          <div class="step-sub">Enter your details — takes less than 2 minutes</div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">First Name <span class="req">*</span></label>
            <div class="input-box">
              <svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
              <input type="text" name="first_name" id="fn" class="form-control @error('first_name') err @enderror" value="{{ old('first_name') }}" placeholder="John" autocomplete="given-name">
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Last Name <span class="req">*</span></label>
            <div class="input-box">
              <svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
              <input type="text" name="last_name" id="ln" class="form-control @error('last_name') err @enderror" value="{{ old('last_name') }}" placeholder="Doe" autocomplete="family-name">
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">
            Phone Number <span class="req">*</span>
          </label>
          <div class="input-box">
            <svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
            <input type="tel" name="phone" id="ph" class="form-control @error('phone') err @enderror" value="{{ old('phone') }}" placeholder="+255 712 345 678" autocomplete="tel">
          </div>
          <div class="hint">Include country code — e.g. +255 for Tanzania</div>
        </div>

        <div class="form-group">
          <label class="form-label">Email Address <span class="req">*</span></label>
          <div class="input-box">
            <svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            <input type="email" name="email" id="em" class="form-control @error('email') err @enderror" value="{{ old('email') }}" placeholder="name@business.com" autocomplete="email">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Password <span class="req">*</span></label>
            <div class="input-box">
              <svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
              <input type="password" name="password" id="pw" class="form-control @error('password') err @enderror" placeholder="Min 8 characters" autocomplete="new-password" oninput="checkStr(this)">
              <button type="button" class="pwd-btn" onclick="togPwd('pw',this)" tabindex="-1">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              </button>
            </div>
            <div class="strength"><div class="strength-fill" id="sFill"></div></div>
            <div class="strength-lbl" id="sLbl"></div>
          </div>
          <div class="form-group">
            <label class="form-label">Confirm Password <span class="req">*</span></label>
            <div class="input-box">
              <svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              <input type="password" name="password_confirmation" id="pc" class="form-control" placeholder="Repeat password" autocomplete="new-password">
              <button type="button" class="pwd-btn" onclick="togPwd('pc',this)" tabindex="-1">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              </button>
            </div>
          </div>
        </div>

        <div class="btn-row">
          <button type="button" class="btn btn-green" onclick="next()">
            Continue
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
          </button>
        </div>
      </div>

      <!-- ── STEP 2 ── -->
      <div class="panel" id="p2">
        <div class="step-head">
          <div class="step-title">Set up your business</div>
          <div class="step-sub">Help us personalize MannaPOS for you</div>
        </div>

        <div class="form-group">
          <label class="form-label">Business Name <span class="req">*</span></label>
          <div class="input-box">
            <svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            <input type="text" name="business_name" id="bn" class="form-control @error('business_name') err @enderror" value="{{ old('business_name') }}" placeholder="e.g. Manna Supermarket">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Business Type</label>
          <div class="input-box">
            <svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
            <select name="business_type" class="form-control" style="padding-left:2.3rem;">
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

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Country <span class="req">*</span></label>
            <div class="input-box">
              <svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/></svg>
              <select name="business_country" class="form-control @error('business_country') err @enderror" style="padding-left:2.3rem;">
                <option value="">Select country</option>
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
          <div class="form-group">
            <label class="form-label">Currency <span class="req">*</span></label>
            <div class="input-box">
              <svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              <select name="currency" class="form-control @error('currency') err @enderror" style="padding-left:2.3rem;">
                <option value="">Select currency</option>
                <option value="TZS" {{ old('currency')=='TZS'?'selected':'' }}>TZS — Tanzanian Shilling</option>
                <option value="KES" {{ old('currency')=='KES'?'selected':'' }}>KES — Kenyan Shilling</option>
                <option value="UGX" {{ old('currency')=='UGX'?'selected':'' }}>UGX — Ugandan Shilling</option>
                <option value="USD" {{ old('currency')=='USD'?'selected':'' }}>USD — US Dollar</option>
                <option value="EUR" {{ old('currency')=='EUR'?'selected':'' }}>EUR — Euro</option>
                <option value="GBP" {{ old('currency')=='GBP'?'selected':'' }}>GBP — British Pound</option>
                <option value="ZAR" {{ old('currency')=='ZAR'?'selected':'' }}>ZAR — South African Rand</option>
                <option value="NGN" {{ old('currency')=='NGN'?'selected':'' }}>NGN — Nigerian Naira</option>
                <option value="GHS" {{ old('currency')=='GHS'?'selected':'' }}>GHS — Ghanaian Cedi</option>
              </select>
            </div>
          </div>
        </div>

        <div class="divider"></div>

        <div class="btn-row">
          <button type="button" class="btn btn-ghost" onclick="back()">← Back</button>
          <button type="submit" class="btn btn-green" id="subBtn">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Create Account &amp; Start Free Trial
          </button>
        </div>
      </div>

    </form>

    <div class="login-link">
      Already have an account? <a href="{{ route('login') }}">Sign in here</a>
    </div>
  </div>
</div>

<script>
// ── Toast helper ──────────────────────────────────
const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 4500,
  timerProgressBar: true,
  customClass: { popup: 'toast-popup' },
});
function toast(icon, msg) {
  Toast.fire({ icon, title: msg });
}

// ── Step state ────────────────────────────────────
let step = 1;
function setStep(s) {
  step = s;
  // panels
  document.getElementById('p1').classList.toggle('active', s === 1);
  document.getElementById('p2').classList.toggle('active', s === 2);
  // dot 1
  const d1 = document.getElementById('sd1'), n1 = document.getElementById('sn1');
  const d2 = document.getElementById('sd2'), n2 = document.getElementById('sn2');
  const sl = document.getElementById('sline');
  if (s === 1) {
    d1.className = 'step-dot active'; d1.textContent = '1'; n1.className = 'step-name active';
    d2.className = 'step-dot'; d2.textContent = '2'; n2.className = 'step-name';
    sl.className = 'step-line';
  } else {
    d1.className = 'step-dot done'; d1.innerHTML = '&#10003;'; n1.className = 'step-name done';
    d2.className = 'step-dot active'; d2.textContent = '2'; n2.className = 'step-name active';
    sl.className = 'step-line done';
  }
}
function back() { setStep(1); }

// ── Step 1 → 2 validation ─────────────────────────
function next() {
  const fn = document.getElementById('fn').value.trim();
  const ln = document.getElementById('ln').value.trim();
  const ph = document.getElementById('ph').value.trim();
  const em = document.getElementById('em').value.trim();
  const pw = document.getElementById('pw').value;
  const pc = document.getElementById('pc').value;

  if (!fn || !ln) { toast('warning', 'Please enter your first and last name.'); document.getElementById('fn').focus(); return; }
  if (!ph) { toast('warning', 'Phone number is required — include country code.'); document.getElementById('ph').focus(); return; }
  if (!ph.startsWith('+')) { toast('info', 'Phone should include country code, e.g. +255…'); document.getElementById('ph').focus(); return; }
  if (!em || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(em)) { toast('warning', 'Please enter a valid email address.'); document.getElementById('em').focus(); return; }
  if (pw.length < 8) { toast('warning', 'Password must be at least 8 characters.'); document.getElementById('pw').focus(); return; }
  if (pw !== pc) { toast('error', 'Passwords do not match — please re-enter.'); document.getElementById('pc').focus(); return; }

  setStep(2);
  document.getElementById('bn').focus();
}

// ── Password toggle ───────────────────────────────
function togPwd(id, btn) {
  const el = document.getElementById(id);
  el.type = el.type === 'password' ? 'text' : 'password';
}

// ── Password strength ─────────────────────────────
function checkStr(el) {
  const v = el.value;
  const fill = document.getElementById('sFill');
  const lbl = document.getElementById('sLbl');
  let s = 0;
  if (v.length >= 8) s++; if (/[A-Z]/.test(v)) s++; if (/[0-9]/.test(v)) s++; if (/[^A-Za-z0-9]/.test(v)) s++;
  const colors = ['', '#ef4444', '#f59e0b', '#16a34a', '#16a34a'];
  const labels = ['', 'Weak', 'Fair', 'Good', 'Strong'];
  fill.style.width = (s * 25) + '%'; fill.style.background = colors[s] || '';
  lbl.textContent = v.length ? labels[s] || 'Strong' : '';
  lbl.style.color = colors[s] || '';
}

// ── Submit spinner ────────────────────────────────
document.getElementById('regForm').addEventListener('submit', function (e) {
  const bn = document.getElementById('bn').value.trim();
  const country = document.querySelector('[name="business_country"]').value;
  const currency = document.querySelector('[name="currency"]').value;
  if (!bn) { e.preventDefault(); toast('warning', 'Please enter your business name.'); document.getElementById('bn').focus(); return; }
  if (!country) { e.preventDefault(); toast('warning', 'Please select your country.'); return; }
  if (!currency) { e.preventDefault(); toast('warning', 'Please select your currency.'); return; }
  const btn = document.getElementById('subBtn');
  btn.disabled = true;
  btn.innerHTML = '<span class="spin">&#9696;</span> Creating account…';
});

// ── Server-side errors → toasts ───────────────────
@if($errors->any())
  @foreach($errors->all() as $err)
    toast('error', @json($err));
  @endforeach
@endif

// ── Jump to step 2 if step-2 field had errors ─────
@if($errors->has('business_name') || $errors->has('business_country') || $errors->has('currency'))
  setStep(2);
@endif
</script>
</body>
</html>
