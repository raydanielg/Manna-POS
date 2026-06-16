<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Create Account — MannaPOS</title>
<link rel="icon" type="image/png" href="{{ asset('icons8-dynamics-365-100.png') }}">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Inter',sans-serif;background:#f0f4ff;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:1.5rem;}
.reg-wrap{display:grid;grid-template-columns:1fr 1fr;max-width:1000px;width:100%;background:#fff;border-radius:24px;box-shadow:0 20px 60px rgba(0,0,0,.12);overflow:hidden;min-height:640px;}
/* ── Left panel ── */
.reg-left{background:linear-gradient(145deg,#1e3a5f 0%,#0f2748 50%,#0a1f3d 100%);padding:3rem;display:flex;flex-direction:column;justify-content:space-between;position:relative;overflow:hidden;}
.reg-left::before{content:'';position:absolute;width:400px;height:400px;border-radius:50%;background:rgba(255,255,255,.03);top:-100px;right:-100px;}
.reg-left::after{content:'';position:absolute;width:300px;height:300px;border-radius:50%;background:rgba(59,130,246,.08);bottom:-80px;left:-80px;}
.reg-brand{display:flex;align-items:center;gap:.75rem;margin-bottom:2.5rem;position:relative;z-index:1;}
.reg-brand-icon{width:44px;height:44px;background:linear-gradient(135deg,#10b981,#059669);border-radius:12px;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(16,185,129,.4);}
.reg-brand-icon svg{width:26px;height:26px;color:#fff;}
.reg-brand-name{font-size:1.4rem;font-weight:800;color:#fff;letter-spacing:-.5px;}
.reg-hero{position:relative;z-index:1;}
.reg-hero h1{font-size:1.75rem;font-weight:800;color:#fff;line-height:1.3;margin-bottom:.75rem;}
.reg-hero p{color:#94b3d4;font-size:.9rem;line-height:1.6;margin-bottom:2rem;}
.reg-feature{display:flex;align-items:center;gap:.75rem;margin-bottom:.875rem;}
.reg-feature-icon{width:32px;height:32px;background:rgba(16,185,129,.15);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.reg-feature-icon svg{width:16px;height:16px;color:#10b981;}
.reg-feature-text{font-size:.85rem;color:#b8d4f0;font-weight:500;}
.reg-trial-badge{display:inline-flex;align-items:center;gap:.5rem;background:rgba(16,185,129,.15);border:1px solid rgba(16,185,129,.3);color:#34d399;padding:.5rem 1rem;border-radius:50px;font-size:.8rem;font-weight:600;position:relative;z-index:1;}
/* ── Right panel ── */
.reg-right{padding:2.5rem 3rem;display:flex;flex-direction:column;justify-content:center;}
.reg-steps-bar{display:flex;align-items:center;gap:0;margin-bottom:2rem;}
.step-item{display:flex;align-items:center;gap:.5rem;}
.step-circle{width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.8rem;font-weight:700;border:2px solid #e2e8f0;color:#94a3b8;background:#fff;transition:all .3s;}
.step-circle.done{background:#10b981;border-color:#10b981;color:#fff;}
.step-circle.active{background:#2563eb;border-color:#2563eb;color:#fff;box-shadow:0 0 0 4px rgba(37,99,235,.15);}
.step-label{font-size:.75rem;font-weight:600;color:#94a3b8;}
.step-label.active{color:#2563eb;}
.step-label.done{color:#10b981;}
.step-line{flex:1;height:2px;background:#e2e8f0;margin:0 .5rem;}
.step-line.done{background:#10b981;}
.reg-title{font-size:1.5rem;font-weight:800;color:#0f172a;margin-bottom:.35rem;}
.reg-subtitle{font-size:.875rem;color:#64748b;margin-bottom:1.75rem;}
.form-group{margin-bottom:1rem;}
.form-label{display:block;font-size:.8rem;font-weight:600;color:#374151;margin-bottom:.4rem;}
.form-label span{color:#ef4444;}
.input-wrap{position:relative;}
.input-wrap svg{position:absolute;left:.85rem;top:50%;transform:translateY(-50%);width:17px;height:17px;color:#94a3b8;pointer-events:none;}
.form-control{width:100%;padding:.65rem .85rem .65rem 2.5rem;border:1.5px solid #e2e8f0;border-radius:10px;font-size:.875rem;color:#0f172a;background:#f8fafc;transition:all .2s;outline:none;font-family:inherit;}
.form-control:focus{border-color:#2563eb;background:#fff;box-shadow:0 0 0 3px rgba(37,99,235,.1);}
.form-control.is-invalid{border-color:#ef4444;background:#fff5f5;}
.invalid-feedback{font-size:.75rem;color:#ef4444;margin-top:.3rem;display:flex;align-items:center;gap:.3rem;}
.invalid-feedback svg{width:13px;height:13px;flex-shrink:0;}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:.75rem;}
.pwd-toggle{position:absolute;right:.85rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#94a3b8;padding:0;display:flex;}
.pwd-toggle svg{width:17px;height:17px;}
.step-panel{display:none;}
.step-panel.active{display:block;}
.btn-row{display:flex;gap:.75rem;margin-top:1.5rem;}
.btn{padding:.7rem 1.5rem;border-radius:10px;font-size:.875rem;font-weight:600;cursor:pointer;border:none;transition:all .2s;font-family:inherit;}
.btn-primary{background:linear-gradient(135deg,#2563eb,#1d4ed8);color:#fff;flex:1;display:flex;align-items:center;justify-content:center;gap:.5rem;box-shadow:0 4px 12px rgba(37,99,235,.3);}
.btn-primary:hover{background:linear-gradient(135deg,#1d4ed8,#1e40af);transform:translateY(-1px);}
.btn-primary:disabled{opacity:.6;cursor:not-allowed;transform:none;}
.btn-secondary{background:#f1f5f9;color:#475569;min-width:100px;}
.btn-secondary:hover{background:#e2e8f0;}
.reg-login{text-align:center;margin-top:1.5rem;font-size:.85rem;color:#64748b;}
.reg-login a{color:#2563eb;font-weight:600;text-decoration:none;}
.reg-login a:hover{text-decoration:underline;}
.phone-hint{font-size:.72rem;color:#94a3b8;margin-top:.25rem;}
.strength-bar{height:3px;background:#e2e8f0;border-radius:2px;margin-top:.4rem;overflow:hidden;}
.strength-fill{height:100%;width:0;border-radius:2px;transition:width .3s,background .3s;}
.strength-label{font-size:.7rem;color:#94a3b8;margin-top:.2rem;}
@media(max-width:700px){.reg-wrap{grid-template-columns:1fr;}.reg-left{display:none;}.reg-right{padding:2rem 1.5rem;}}
</style>
</head>
<body>
<div class="reg-wrap">
  <!-- Left decorative panel -->
  <div class="reg-left">
    <div>
      <div class="reg-brand">
        <div class="reg-brand-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        </div>
        <span class="reg-brand-name">MannaPOS</span>
      </div>
      <div class="reg-hero">
        <h1>Manage your business smarter.</h1>
        <p>Join thousands of businesses using MannaPOS to track sales, inventory, and growth — all in one place.</p>
        <div class="reg-feature">
          <div class="reg-feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg></div>
          <span class="reg-feature-text">Real-time sales & profit reports</span>
        </div>
        <div class="reg-feature">
          <div class="reg-feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg></div>
          <span class="reg-feature-text">Smart inventory management</span>
        </div>
        <div class="reg-feature">
          <div class="reg-feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
          <span class="reg-feature-text">Customer & supplier management</span>
        </div>
        <div class="reg-feature">
          <div class="reg-feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg></div>
          <span class="reg-feature-text">Easy POS with receipt printing</span>
        </div>
      </div>
    </div>
    <div class="reg-trial-badge">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      Free 14-day trial — no credit card required
    </div>
  </div>

  <!-- Right form panel -->
  <div class="reg-right">
    <!-- Step indicator -->
    <div class="reg-steps-bar">
      <div class="step-item">
        <div class="step-circle active" id="sc1">1</div>
        <span class="step-label active" id="sl1">Account</span>
      </div>
      <div class="step-line" id="sline"></div>
      <div class="step-item">
        <div class="step-circle" id="sc2">2</div>
        <span class="step-label" id="sl2">Business</span>
      </div>
    </div>

    <div id="step-title-wrap">
      <h2 class="reg-title" id="stepTitle">Create your account</h2>
      <p class="reg-subtitle" id="stepSubtitle">Enter your personal details to get started</p>
    </div>

    @if ($errors->any())
    <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:.75rem 1rem;margin-bottom:1rem;">
      <ul style="list-style:none;font-size:.8rem;color:#dc2626;">
        @foreach ($errors->all() as $error)
        <li>• {{ $error }}</li>
        @endforeach
      </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('register') }}" id="regForm">
      @csrf

      <!-- ── STEP 1: Account ── -->
      <div class="step-panel active" id="panel1">
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">First Name <span>*</span></label>
            <div class="input-wrap">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
              <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" placeholder="John" autocomplete="given-name" id="first_name">
            </div>
            @error('first_name')<div class="invalid-feedback"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>{{ $message }}</div>@enderror
          </div>
          <div class="form-group">
            <label class="form-label">Last Name <span>*</span></label>
            <div class="input-wrap">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
              <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" placeholder="Doe" autocomplete="family-name" id="last_name">
            </div>
            @error('last_name')<div class="invalid-feedback"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>{{ $message }}</div>@enderror
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Phone Number <span>*</span></label>
          <div class="input-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
            <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="+255 712 345 678" autocomplete="tel" id="phone_field">
          </div>
          <p class="phone-hint">Include country code (e.g. +255 for Tanzania)</p>
          @error('phone')<div class="invalid-feedback"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
          <label class="form-label">Email Address <span>*</span></label>
          <div class="input-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="name@business.com" autocomplete="email" id="email_field">
          </div>
          @error('email')<div class="invalid-feedback"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>{{ $message }}</div>@enderror
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Password <span>*</span></label>
            <div class="input-wrap">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
              <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Min 8 characters" autocomplete="new-password" id="pwd_field" oninput="checkStrength(this)">
              <button type="button" class="pwd-toggle" onclick="togglePwd('pwd_field',this)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></button>
            </div>
            <div class="strength-bar"><div class="strength-fill" id="strengthFill"></div></div>
            <p class="strength-label" id="strengthLabel"></p>
            @error('password')<div class="invalid-feedback"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>{{ $message }}</div>@enderror
          </div>
          <div class="form-group">
            <label class="form-label">Confirm Password <span>*</span></label>
            <div class="input-wrap">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat password" autocomplete="new-password" id="pwd_confirm">
              <button type="button" class="pwd-toggle" onclick="togglePwd('pwd_confirm',this)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></button>
            </div>
          </div>
        </div>

        <div class="btn-row">
          <button type="button" class="btn btn-primary" onclick="goStep2()">
            Continue to Business Setup
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
          </button>
        </div>
      </div>

      <!-- ── STEP 2: Business ── -->
      <div class="step-panel" id="panel2">
        <div class="form-group">
          <label class="form-label">Business Name <span>*</span></label>
          <div class="input-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            <input type="text" name="business_name" class="form-control @error('business_name') is-invalid @enderror" value="{{ old('business_name') }}" placeholder="Manna Supermarket" id="biz_name">
          </div>
          @error('business_name')<div class="invalid-feedback"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
          <label class="form-label">Business Type</label>
          <div class="input-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
            <select name="business_type" class="form-control" style="padding-left:2.5rem;">
              <option value="">Select business type</option>
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
            <label class="form-label">Country <span>*</span></label>
            <div class="input-wrap">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/></svg>
              <select name="business_country" class="form-control @error('business_country') is-invalid @enderror" style="padding-left:2.5rem;">
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
            @error('business_country')<div class="invalid-feedback"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>{{ $message }}</div>@enderror
          </div>
          <div class="form-group">
            <label class="form-label">Currency <span>*</span></label>
            <div class="input-wrap">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              <select name="currency" class="form-control @error('currency') is-invalid @enderror" style="padding-left:2.5rem;">
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
            @error('currency')<div class="invalid-feedback"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>{{ $message }}</div>@enderror
          </div>
        </div>

        <div class="btn-row">
          <button type="button" class="btn btn-secondary" onclick="goStep1()">
            ← Back
          </button>
          <button type="submit" class="btn btn-primary" id="submitBtn">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            Create Account & Start Free Trial
          </button>
        </div>
      </div>

    </form>

    <div class="reg-login">
      Already have an account? <a href="{{ route('login') }}">Sign in</a>
    </div>
  </div>
</div>

<script>
function goStep2(){
  const fn=document.getElementById('first_name').value.trim();
  const ln=document.getElementById('last_name').value.trim();
  const ph=document.getElementById('phone_field').value.trim();
  const em=document.getElementById('email_field').value.trim();
  const pw=document.getElementById('pwd_field').value;
  const pc=document.getElementById('pwd_confirm').value;
  if(!fn||!ln){alert('Please enter your full name.');return;}
  if(!ph){alert('Phone number is required.');return;}
  if(!em||!em.includes('@')){alert('Please enter a valid email.');return;}
  if(pw.length<8){alert('Password must be at least 8 characters.');return;}
  if(pw!==pc){alert('Passwords do not match.');return;}
  document.getElementById('panel1').classList.remove('active');
  document.getElementById('panel2').classList.add('active');
  document.getElementById('sc1').className='step-circle done';document.getElementById('sc1').innerHTML='✓';
  document.getElementById('sl1').className='step-label done';
  document.getElementById('sc2').className='step-circle active';
  document.getElementById('sl2').className='step-label active';
  document.getElementById('sline').className='step-line done';
  document.getElementById('stepTitle').textContent='Set up your business';
  document.getElementById('stepSubtitle').textContent='Tell us about your business so we can personalize your experience';
}
function goStep1(){
  document.getElementById('panel2').classList.remove('active');
  document.getElementById('panel1').classList.add('active');
  document.getElementById('sc1').className='step-circle active';document.getElementById('sc1').innerHTML='1';
  document.getElementById('sl1').className='step-label active';
  document.getElementById('sc2').className='step-circle';
  document.getElementById('sl2').className='step-label';
  document.getElementById('sline').className='step-line';
  document.getElementById('stepTitle').textContent='Create your account';
  document.getElementById('stepSubtitle').textContent='Enter your personal details to get started';
}
function togglePwd(id,btn){
  const f=document.getElementById(id);
  f.type=f.type==='password'?'text':'password';
}
function checkStrength(el){
  const v=el.value;const f=document.getElementById('strengthFill');const l=document.getElementById('strengthLabel');
  let score=0;
  if(v.length>=8)score++;if(/[A-Z]/.test(v))score++;if(/[0-9]/.test(v))score++;if(/[^A-Za-z0-9]/.test(v))score++;
  const colors=['','#ef4444','#f59e0b','#10b981','#10b981'];
  const labels=['','Weak','Fair','Good','Strong'];
  f.style.width=(score*25)+'%';f.style.background=colors[score]||'';l.textContent=v.length?labels[score]||'Strong':'';l.style.color=colors[score]||'';
}
document.getElementById('regForm').addEventListener('submit',function(){
  const btn=document.getElementById('submitBtn');btn.disabled=true;btn.innerHTML='<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation:spin 1s linear infinite"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg> Creating account...';
});
// If errors exist on step 2 fields, jump to step 2
@if($errors->has('business_name') || $errors->has('business_country') || $errors->has('currency'))
  goStep2();
  document.getElementById('sc1').className='step-circle done';document.getElementById('sc1').innerHTML='✓';
  document.getElementById('sl1').className='step-label done';
  document.getElementById('sline').className='step-line done';
@endif
</script>
<style>@keyframes spin{from{transform:rotate(0deg)}to{transform:rotate(360deg)}}</style>
</body>
</html>
