<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Setup Your Business — MannaPOS</title>
<link rel="icon" type="image/png" href="{{ asset('icons8-dynamics-365-100.png') }}">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
body{font-family:'Inter',sans-serif;background:#f1f5f9;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:1rem}
.setup-wrap{background:#fff;border-radius:14px;box-shadow:0 4px 20px rgba(0,0,0,.07);width:100%;max-width:700px;overflow:hidden}
.setup-header{background:linear-gradient(145deg,#0f2748,#1e3a5f,#1d4ed8);padding:2rem 2.25rem;color:#fff;position:relative;overflow:hidden}
.setup-header::before{content:'';position:absolute;inset:0;background-image:radial-gradient(circle at 1px 1px,rgba(255,255,255,.07) 1px,transparent 0);background-size:22px 22px}
.wiz-circle{width:34px;height:34px;border-radius:50%;border:2px solid #e2e8f0;display:flex;align-items:center;justify-content:center;font-size:.8rem;font-weight:800;color:#94a3b8;background:#fff;transition:all .3s}
.wiz-circle.active{border-color:#1d4ed8;background:#1d4ed8;color:#fff;box-shadow:0 0 0 4px rgba(29,78,216,.15)}
.wiz-circle.done{border-color:#16a34a;background:#16a34a;color:#fff}
.wiz-step-label{font-size:.72rem;font-weight:700;color:#94a3b8;text-align:center;margin-top:.35rem}
.wiz-step-label.active{color:#1d4ed8}
.wiz-step-label.done{color:#16a34a}
.panel{display:none}
.panel.active{display:block;animation:fadeIn .35s ease}
@keyframes fadeIn{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:translateY(0)}}
.inp{width:100%;padding:.7rem .8rem .7rem 2.4rem;border:1.5px solid #e2e8f0;border-radius:10px;font-size:.88rem;color:#0f172a;background:#f8fafc;transition:all .2s;outline:none;font-family:inherit}
.inp:focus{border-color:#1d4ed8;background:#fff;box-shadow:0 0 0 3px rgba(29,78,216,.1)}
.inp:hover{border-color:#cbd5e1}
sel.inp{background-image:url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");background-position:right .5rem center;background-repeat:no-repeat;background-size:1.4em 1.4em;padding-right:2.3rem;-webkit-appearance:none;appearance:none}
.country-chip{border:1.5px solid #e2e8f0;border-radius:8px;padding:.5rem .7rem;display:flex;align-items:center;gap:.5rem;cursor:pointer;transition:all .15s;background:#fff;font-size:.82rem;font-weight:600;color:#374151}
.country-chip:hover{border-color:#93c5fd;background:#eff6ff}
.country-chip.on{border-color:#1d4ed8;background:#eff6ff;color:#1d4ed8;box-shadow:0 0 0 3px rgba(29,78,216,.08)}
.country-chip .flag{font-size:1.1rem}
.feat-card{border:1.5px solid #e2e8f0;border-radius:10px;padding:.9rem;display:flex;align-items:flex-start;gap:.65rem;cursor:pointer;transition:all .15s;background:#fff}
.feat-card:hover,.feat-card.on{border-color:#1d4ed8;background:#eff6ff}
.feat-card-icon{width:34px;height:34px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.btn-p{background:linear-gradient(135deg,#1d4ed8,#1e3a8a);color:#fff;flex:1;box-shadow:0 3px 10px rgba(29,78,216,.25)}
.btn-p:hover{box-shadow:0 5px 14px rgba(29,78,216,.35);transform:translateY(-1px)}
.btn-s{background:#f1f5f9;color:#475569}
.btn-s:hover{background:#e2e8f0}
.done-icon{width:64px;height:64px;background:linear-gradient(135deg,#22c55e,#16a34a);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.25rem;box-shadow:0 6px 20px rgba(22,163,74,.2)}
</style>
</head>
<body>
<div class="setup-wrap">
  <div class="setup-header">
    <div style="position:relative;z-index:2;">
      <div class="flex items-center gap-2.5 mb-4">
        <img src="{{ asset('icons8-dynamics-365-96.png') }}" alt="Logo" class="w-9 h-9 object-contain rounded-lg">
        <span class="text-lg font-extrabold tracking-tight">MannaPOS</span>
      </div>
      <h1 class="text-xl font-extrabold mb-1">Welcome, {{ explode(' ', $user->name)[0] }}! 👋</h1>
      <p class="text-blue-200/70 text-sm">Let's set up your business in a few quick steps.</p>
    </div>
  </div>

  <div class="px-8 pt-5 pb-2">
    <div class="flex items-center">
      <div class="flex flex-col items-center"><div class="wiz-circle active" id="wc1">1</div><span class="wiz-step-label active" id="wl1">Business</span></div>
      <div class="flex-1 h-[2px] bg-slate-200 -mt-5 mx-3" id="wline1"></div>
      <div class="flex flex-col items-center"><div class="wiz-circle" id="wc2">2</div><span class="wiz-step-label" id="wl2">Details</span></div>
      <div class="flex-1 h-[2px] bg-slate-200 -mt-5 mx-3" id="wline2"></div>
      <div class="flex flex-col items-center"><div class="wiz-circle" id="wc3">3</div><span class="wiz-step-label" id="wl3">Done</span></div>
    </div>
  </div>

  <div class="p-8">
    <form id="setupForm" method="POST" action="/setup">
      @csrf

    <!-- PANEL 1: Business Info -->
    <div class="panel active" id="wp1">
      <h3 class="text-lg font-extrabold text-slate-900 mb-1">Business Information</h3>
      <p class="text-sm text-slate-500 mb-5">Confirm your business details below.</p>

      <div class="mb-4">
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Business Name <span class="text-rose-500">*</span></label>
        <div class="relative">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
          <input type="text" name="business_name" class="inp" value="{{ $user->business_name }}" required>
        </div>
      </div>

      <div class="mb-4">
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Country <span class="text-rose-500">*</span></label>
        <div class="grid grid-cols-3 gap-2 mb-2" id="countryGrid">
          @foreach($countries as $country)
          <div class="country-chip {{ $user->business_country == $country->name ? 'on' : '' }}" onclick="pickCountry(this,'{{ $country->name }}')">
            <span class="flag">{{ $country->flag_emoji }}</span>
            <span>{{ $country->name }}</span>
          </div>
          @endforeach
        </div>
        <input type="hidden" name="business_country" id="bizCountry" value="{{ $user->business_country }}">
        <p class="text-[10px] text-slate-400 mt-1">Click your country. Showing East African countries.</p>
      </div>

      <div class="grid grid-cols-2 gap-3 mb-4">
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">City / Town</label>
          <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <input type="text" name="business_city" class="inp" value="{{ $user->business_city }}" placeholder="e.g. Dar es Salaam">
          </div>
        </div>
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Currency <span class="text-rose-500">*</span></label>
          <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <select name="currency" class="inp sel" required>
              @foreach($currencies as $c)
              <option value="{{ $c->code }}" {{ $user->currency==$c->code?'selected':'' }}>{{ $c->code }} — {{ $c->name }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>

      <div class="mb-5">
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Business Address</label>
        <div class="relative">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
          <input type="text" name="business_address" class="inp" value="{{ $user->business_address }}" placeholder="Street address, building, area">
        </div>
      </div>

      <div class="flex gap-3">
        <button type="button" class="btn-p py-2.5 px-5 rounded-lg font-bold text-sm flex items-center justify-center gap-2" onclick="goWiz(2)">
          <span>Continue</span>
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
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
