<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Choose Your Plan — MannaPOS</title>
<link rel="icon" type="image/png" href="{{ asset('icons8-dynamics-365-100.png') }}">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Inter',sans-serif;background:linear-gradient(135deg,#f0f4ff 0%,#e8f5e9 100%);min-height:100vh;padding:2rem 1rem;}
.sub-wrap{max-width:1100px;margin:0 auto;}
.sub-header{text-align:center;margin-bottom:3rem;}
.sub-brand{display:inline-flex;align-items:center;gap:.65rem;margin-bottom:2rem;}
.sub-brand-icon{width:40px;height:40px;background:linear-gradient(135deg,#10b981,#059669);border-radius:11px;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(16,185,129,.4);}
.sub-brand-icon svg{width:24px;height:24px;color:#fff;}
.sub-brand-name{font-size:1.3rem;font-weight:800;color:#1e293b;}
.sub-header h1{font-size:2rem;font-weight:800;color:#0f172a;margin-bottom:.75rem;}
.sub-header p{color:#64748b;font-size:1rem;max-width:500px;margin:0 auto;}
/* Trial banner */
.trial-banner{background:linear-gradient(135deg,#0f2748,#1e3a5f);border-radius:16px;padding:1.5rem 2rem;color:#fff;margin-bottom:2.5rem;display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;}
.trial-banner-left h3{font-size:1.1rem;font-weight:700;margin-bottom:.25rem;}
.trial-banner-left p{color:#94b3d4;font-size:.875rem;}
.trial-countdown{display:flex;align-items:center;gap:.5rem;background:rgba(16,185,129,.15);border:1px solid rgba(16,185,129,.3);border-radius:10px;padding:.5rem 1.25rem;}
.trial-countdown .days{font-size:2rem;font-weight:800;color:#34d399;line-height:1;}
.trial-countdown .days-label{font-size:.75rem;color:#94b3d4;font-weight:600;}
/* Toggle */
.billing-toggle{display:flex;align-items:center;justify-content:center;gap:1rem;margin-bottom:2rem;}
.toggle-track{position:relative;width:56px;height:28px;background:#e2e8f0;border-radius:50px;cursor:pointer;transition:background .3s;}
.toggle-track.on{background:#2563eb;}
.toggle-knob{position:absolute;top:3px;left:3px;width:22px;height:22px;background:#fff;border-radius:50%;transition:left .3s;box-shadow:0 2px 6px rgba(0,0,0,.15);}
.toggle-track.on .toggle-knob{left:31px;}
.toggle-label{font-size:.9rem;font-weight:600;color:#64748b;}
.toggle-label.active{color:#0f172a;}
.save-badge{background:#dcfce7;color:#16a34a;font-size:.7rem;font-weight:700;padding:.2rem .5rem;border-radius:50px;}
/* Plan cards */
.plans-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:1.25rem;margin-bottom:2rem;}
.plan-card{background:#fff;border-radius:20px;padding:1.75rem;border:2px solid #e9edf5;position:relative;transition:transform .2s,box-shadow .2s;}
.plan-card:hover{transform:translateY(-4px);box-shadow:0 20px 40px rgba(0,0,0,.1);}
.plan-card.featured{border-color:#2563eb;box-shadow:0 8px 30px rgba(37,99,235,.15);}
.plan-card.current-plan{border-color:#10b981;box-shadow:0 8px 30px rgba(16,185,129,.15);}
.plan-badge{position:absolute;top:-1px;left:50%;transform:translateX(-50%);background:#2563eb;color:#fff;font-size:.7rem;font-weight:700;padding:.3rem .9rem;border-radius:0 0 10px 10px;}
.plan-badge.green{background:#10b981;}
.plan-name{font-size:1rem;font-weight:700;color:#1e293b;margin-bottom:.35rem;}
.plan-desc{font-size:.8rem;color:#64748b;margin-bottom:1.25rem;min-height:2.4rem;}
.plan-price{margin-bottom:1.25rem;}
.plan-price .amount{font-size:2rem;font-weight:800;color:#0f172a;line-height:1;}
.plan-price .currency{font-size:.9rem;font-weight:600;color:#64748b;vertical-align:top;margin-top:.3rem;display:inline-block;}
.plan-price .period{font-size:.8rem;color:#94a3b8;}
.plan-price .yearly-price{font-size:.75rem;color:#64748b;margin-top:.25rem;}
.plan-features{margin-bottom:1.5rem;}
.plan-feature{display:flex;align-items:center;gap:.5rem;font-size:.82rem;color:#374151;margin-bottom:.5rem;}
.plan-feature svg{width:15px;height:15px;color:#10b981;flex-shrink:0;}
.plan-limit{font-size:.75rem;color:#94a3b8;margin-top:.25rem;padding-left:1.25rem;}
.btn-choose{width:100%;padding:.75rem;border-radius:11px;font-size:.875rem;font-weight:700;border:none;cursor:pointer;transition:all .2s;font-family:inherit;}
.btn-primary-plan{background:linear-gradient(135deg,#2563eb,#1d4ed8);color:#fff;box-shadow:0 4px 12px rgba(37,99,235,.3);}
.btn-primary-plan:hover{background:linear-gradient(135deg,#1d4ed8,#1e40af);transform:translateY(-1px);}
.btn-free-plan{background:linear-gradient(135deg,#10b981,#059669);color:#fff;box-shadow:0 4px 12px rgba(16,185,129,.3);}
.btn-free-plan:hover{background:linear-gradient(135deg,#059669,#047857);}
.btn-current{background:#f1f5f9;color:#94a3b8;cursor:default;}
.btn-outline-plan{background:#fff;color:#2563eb;border:2px solid #2563eb;}
.btn-outline-plan:hover{background:#eff6ff;}
/* Dashboard link */
.goto-dash{text-align:center;margin-top:1.5rem;}
.goto-dash a{color:#64748b;font-size:.875rem;text-decoration:none;display:inline-flex;align-items:center;gap:.4rem;}
.goto-dash a:hover{color:#2563eb;}
/* No plans */
.no-plans{text-align:center;padding:3rem;color:#64748b;}
/* Alert */
.alert{padding:1rem 1.5rem;border-radius:12px;margin-bottom:1.5rem;font-size:.875rem;font-weight:500;}
.alert-success{background:#dcfce7;color:#166534;border:1px solid #bbf7d0;}
</style>
</head>
<body>
<div class="sub-wrap">
  <div class="sub-header">
    <div class="sub-brand">
      <div class="sub-brand-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
      </div>
      <span class="sub-brand-name">MannaPOS</span>
    </div>
    <h1>Choose Your Plan</h1>
    <p>Power your business with the right plan. Upgrade or downgrade anytime.</p>
  </div>

  @if(session('subscribed'))
  <div class="alert alert-success">{{ session('subscribed') }} You're ready to go!</div>
  @endif

  @if($current)
  <div class="trial-banner">
    <div class="trial-banner-left">
      <h3>
        @if($current->status === 'trial') Your Free Trial is Active @else Your {{ $current->plan->name ?? 'Subscription' }} is Active @endif
      </h3>
      <p>{{ $current->plan->name ?? 'Free Trial' }} plan —
        @if($current->expires_at) Expires {{ $current->expires_at->format('M d, Y') }} @else Never expires @endif
      </p>
    </div>
    @if($daysLeft !== null)
    <div class="trial-countdown">
      <div>
        <div class="days">{{ $daysLeft }}</div>
        <div class="days-label">days left</div>
      </div>
    </div>
    @endif
  </div>
  @endif

  <!-- Billing toggle -->
  <div class="billing-toggle">
    <span class="toggle-label active" id="lbl-monthly">Monthly</span>
    <div class="toggle-track" id="billingToggle" onclick="toggleBilling()">
      <div class="toggle-knob"></div>
    </div>
    <span class="toggle-label" id="lbl-yearly">Yearly <span class="save-badge">Save 17%</span></span>
  </div>

  @if($plans->isEmpty())
  <div class="no-plans">
    <p>No subscription plans available yet. Please check back soon.</p>
    <a href="/dashboard" style="color:#2563eb;font-weight:600;text-decoration:none;">→ Go to Dashboard</a>
  </div>
  @else
  <div class="plans-grid">
    @foreach($plans as $plan)
    @php
      $isCurrent = $current && $current->subscription_plan_id == $plan->id && in_array($current->status, ['active','trial']);
      $isFree    = $plan->price_monthly == 0;
      $features  = is_array($plan->features) ? $plan->features : [];
    @endphp
    <div class="plan-card {{ $plan->is_featured ? 'featured' : '' }} {{ $isCurrent ? 'current-plan' : '' }}">
      @if($plan->is_featured && !$isCurrent)<div class="plan-badge">Most Popular</div>@endif
      @if($isCurrent)<div class="plan-badge green">Current Plan</div>@endif

      <div class="plan-name">{{ $plan->name }}</div>
      <div class="plan-desc">{{ $plan->description }}</div>

      <div class="plan-price">
        <span class="currency">{{ $plan->currency }}</span>
        <span class="amount monthly-amount">{{ number_format($plan->price_monthly, 0) }}</span>
        <span class="amount yearly-amount" style="display:none;">{{ number_format($plan->price_yearly / 12, 0) }}</span>
        <span class="period"> / month</span>
        @if($plan->price_yearly > 0)
        <div class="yearly-price yearly-note" style="display:none;">{{ $plan->currency }} {{ number_format($plan->price_yearly, 0) }} billed yearly</div>
        @endif
      </div>

      <div class="plan-features">
        @foreach($features as $feat)
        <div class="plan-feature">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
          {{ $feat }}
        </div>
        @endforeach
        @if($plan->max_users)<div class="plan-feature"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>Up to {{ $plan->max_users }} user{{ $plan->max_users > 1 ? 's' : '' }}</div>@endif
        @if($plan->max_products)<div class="plan-feature"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>Up to {{ $plan->max_products == 999999 ? 'unlimited' : number_format($plan->max_products) }} products</div>@endif
      </div>

      @if($isCurrent)
      <button class="btn-choose btn-current" disabled>✓ Current Plan</button>
      @elseif($isFree)
      <form method="POST" action="/subscription/choose">
        @csrf
        <input type="hidden" name="plan_id" value="{{ $plan->id }}">
        <input type="hidden" name="billing_cycle" value="monthly">
        <button type="submit" class="btn-choose btn-free-plan">Start 14-Day Free Trial</button>
      </form>
      @else
      <form method="POST" action="/subscription/choose" class="choose-form" data-plan="{{ $plan->id }}">
        @csrf
        <input type="hidden" name="plan_id" value="{{ $plan->id }}">
        <input type="hidden" name="billing_cycle" value="monthly" class="billing-input">
        <button type="submit" class="btn-choose {{ $plan->is_featured ? 'btn-primary-plan' : 'btn-outline-plan' }}">
          Get {{ $plan->name }}
        </button>
      </form>
      @endif
    </div>
    @endforeach
  </div>
  @endif

  <div class="goto-dash">
    <a href="/dashboard">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
      Skip for now — go to dashboard
    </a>
  </div>
</div>

<script>
let yearly=false;
function toggleBilling(){
  yearly=!yearly;
  const t=document.getElementById('billingToggle');
  t.classList.toggle('on',yearly);
  document.getElementById('lbl-monthly').classList.toggle('active',!yearly);
  document.getElementById('lbl-yearly').classList.toggle('active',yearly);
  document.querySelectorAll('.monthly-amount').forEach(e=>e.style.display=yearly?'none':'');
  document.querySelectorAll('.yearly-amount').forEach(e=>e.style.display=yearly?'':'none');
  document.querySelectorAll('.yearly-note').forEach(e=>e.style.display=yearly?'':'none');
  document.querySelectorAll('.billing-input').forEach(e=>e.value=yearly?'yearly':'monthly');
}
</script>
</body>
</html>
