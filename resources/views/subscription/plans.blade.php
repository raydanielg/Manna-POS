@extends('layouts.dashboard')

@section('page_title', 'My Subscription')

@section('page_styles')
<style>
/* ── Page ─────────────────────────────────────── */
.sub-page{max-width:1200px;margin:0 auto;}
.sub-head{text-align:center;margin-bottom:2.5rem;}
.sub-head h1{font-size:1.85rem;font-weight:800;color:#0f172a;letter-spacing:-.03em;background:linear-gradient(135deg,#0f172a,#2563eb);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;}
.sub-head p{color:#64748b;font-size:.92rem;margin-top:.35rem;}

/* ── Current Plan Banner ─────────────────────── */
.current-banner{background:linear-gradient(135deg,#0f2748,#1e3a5f);border-radius:18px;padding:1.5rem 2rem;color:#fff;margin-bottom:2rem;display:flex;align-items:center;justify-content:space-between;gap:1.25rem;flex-wrap:wrap;position:relative;overflow:hidden;}
.current-banner::before{content:'';position:absolute;top:-60%;right:-20%;width:300px;height:300px;background:radial-gradient(circle,rgba(16,185,129,.15) 0%,transparent 70%);border-radius:50%;pointer-events:none;}
.current-banner::after{content:'';position:absolute;bottom:-40%;left:-10%;width:200px;height:200px;background:radial-gradient(circle,rgba(37,99,235,.1) 0%,transparent 70%);border-radius:50%;pointer-events:none;}
.no-plan-banner{background:linear-gradient(135deg,#f59e0b,#d97706);border-radius:18px;padding:1.15rem 2rem;color:#fff;margin-bottom:2rem;display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;}

/* ── Billing Toggle ──────────────────────────── */
.bill-wrap{display:flex;align-items:center;justify-content:center;gap:.85rem;margin-bottom:2.25rem;}
.bill-lbl{font-size:.85rem;font-weight:600;color:#94a3b8;transition:color .25s;cursor:pointer;user-select:none;}
.bill-lbl.active{color:#0f172a;}
.bill-track{position:relative;width:54px;height:28px;background:#e2e8f0;border-radius:50px;cursor:pointer;transition:all .35s;flex-shrink:0;}
.bill-track.on{background:linear-gradient(135deg,#2563eb,#1d4ed8);box-shadow:0 2px 12px rgba(37,99,235,.35);}
.bill-knob{position:absolute;top:3px;left:3px;width:22px;height:22px;background:#fff;border-radius:50%;transition:all .35s cubic-bezier(.68,-.55,.27,1.55);box-shadow:0 2px 8px rgba(0,0,0,.18);}
.bill-track.on .bill-knob{left:29px;}
.save-badge{display:inline-block;background:linear-gradient(135deg,#dcfce7,#bbf7d0);color:#16a34a;font-size:.6rem;font-weight:800;padding:.15rem .5rem;border-radius:50px;margin-left:.3rem;letter-spacing:.02em;}

/* ── Plan Cards Grid ─────────────────────────── */
.plans-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(290px,1fr));gap:1.5rem;margin-bottom:2rem;}
.plan-card{background:#fff;border-radius:22px;border:2px solid #e9edf5;padding:2rem 1.75rem;position:relative;display:flex;flex-direction:column;transition:all .3s cubic-bezier(.4,0,.2,1);cursor:default;}
.plan-card:hover{transform:translateY(-6px);box-shadow:0 24px 48px -12px rgba(0,0,0,.18);border-color:#d1d9e6;}
.plan-card.featured{border-color:#2563eb;box-shadow:0 8px 32px -8px rgba(37,99,235,.2);}
.plan-card.featured:hover{box-shadow:0 24px 48px -12px rgba(37,99,235,.25);}
.plan-card.current{border-color:#10b981;box-shadow:0 8px 32px -8px rgba(16,185,129,.18);}
.plan-card.current:hover{box-shadow:0 24px 48px -12px rgba(16,185,129,.22);}
.plan-badge-top{position:absolute;top:-1px;left:50%;transform:translateX(-50%);font-size:.64rem;font-weight:800;padding:.28rem 1rem;border-radius:0 0 12px 12px;letter-spacing:.04em;text-transform:uppercase;z-index:2;}
.plan-badge-top.green{background:linear-gradient(135deg,#10b981,#059669);color:#fff;}
.plan-badge-top.blue{background:linear-gradient(135deg,#2563eb,#1d4ed8);color:#fff;}

/* Plan header */
.plan-head{display:flex;align-items:center;gap:.5rem;margin-bottom:.25rem;}
.plan-dot{width:10px;height:10px;border-radius:50%;flex-shrink:0;}
.plan-name{font-size:1.1rem;font-weight:800;color:#0f172a;letter-spacing:-.01em;}
.plan-desc{font-size:.78rem;color:#64748b;margin-bottom:1.25rem;line-height:1.5;min-height:2.4rem;}

/* Plan price */
.plan-price{margin-bottom:1.25rem;}
.plan-price .cur{font-size:.85rem;font-weight:700;color:#94a3b8;vertical-align:top;margin-top:.4rem;display:inline-block;}
.plan-price .amt{font-size:2.2rem;font-weight:900;color:#0f172a;line-height:1;letter-spacing:-.04em;}
.plan-price .per{font-size:.78rem;color:#94a3b8;}
.plan-price .note{font-size:.72rem;color:#16a34a;font-weight:600;margin-top:.2rem;}
.plan-price .note span{color:#64748b;font-weight:500;}

/* Limits row */
.limits-row{display:flex;gap:.6rem;margin-bottom:1rem;}
.limit-box{flex:1;background:#f8fafc;border-radius:12px;padding:.45rem .5rem;text-align:center;border:1px solid #f1f5f9;}
.limit-box .lbl{font-size:.65rem;color:#94a3b8;font-weight:600;text-transform:uppercase;letter-spacing:.04em;}
.limit-box .val{font-size:.9rem;font-weight:800;color:#0f172a;margin-top:.1rem;}

/* Features */
.plan-feats{flex:1;margin-bottom:1.25rem;}
.plan-feat{display:flex;align-items:center;gap:.45rem;font-size:.8rem;color:#374151;margin-bottom:.45rem;}
.plan-feat svg{width:15px;height:15px;flex-shrink:0;}

/* Buttons */
.btn-plan{width:100%;padding:.75rem;border-radius:12px;font-size:.85rem;font-weight:700;border:none;cursor:pointer;font-family:inherit;transition:all .25s;text-align:center;display:block;}
.btn-plan:disabled{cursor:default;opacity:1;}
.btn-plan:active{transform:scale(.97);}
.btn-plan.primary{background:linear-gradient(135deg,#2563eb,#1d4ed8);color:#fff;box-shadow:0 4px 14px rgba(37,99,235,.3);}
.btn-plan.primary:hover{box-shadow:0 6px 20px rgba(37,99,235,.4);transform:translateY(-2px);}
.btn-plan.green{background:linear-gradient(135deg,#10b981,#059669);color:#fff;box-shadow:0 4px 14px rgba(16,185,129,.3);}
.btn-plan.green:hover{box-shadow:0 6px 20px rgba(16,185,129,.4);transform:translateY(-2px);}
.btn-plan.outline{background:#fff;color:#2563eb;border:2px solid #2563eb;}
.btn-plan.outline:hover{background:#eff6ff;transform:translateY(-2px);}
.btn-plan.current-btn{background:#f1f5f9;color:#94a3b8;cursor:default;}
.btn-plan.current-btn:hover{transform:none;}

/* No plans */
.no-plans{text-align:center;padding:3rem 1rem;color:#94a3b8;}

/* Alert */
.alert-custom{padding:.85rem 1.25rem;border-radius:12px;font-size:.875rem;font-weight:500;margin-bottom:1.5rem;display:flex;align-items:center;gap:.6rem;}

/* Animate.css stagger */
.stagger-1{animation-delay:.08s}
.stagger-2{animation-delay:.18s}
.stagger-3{animation-delay:.28s}
.stagger-4{animation-delay:.38s}
.stagger-5{animation-delay:.48s}

/* Mobile polish */
@media(max-width:640px){
  .sub-head h1{font-size:1.35rem;}
  .sub-head p{font-size:.85rem;}
  .plans-grid{grid-template-columns:1fr;gap:1.25rem;}
  .plan-card{padding:1.5rem 1.25rem;border-radius:18px;}
  .plan-name{font-size:1rem;}
  .plan-price .amt{font-size:1.9rem;}
  .limits-row{gap:.4rem;}
  .limit-box{padding:.35rem .4rem;}
  .limit-box .val{font-size:.82rem;}
  .current-banner{padding:1.1rem 1.15rem;flex-direction:column;text-align:center;gap:.85rem;}
  .current-banner .right{width:100%;justify-content:center;flex-wrap:wrap;}
  .no-plan-banner{padding:1rem 1.25rem;text-align:center;flex-direction:column;gap:.5rem;}
  .bill-wrap{margin-bottom:1.75rem;}
}

/* Reduced motion */
@media (prefers-reduced-motion: reduce) {
  .plan-card, .stat-card { transition: none !important; animation: none !important; }
}
</style>
@endsection

@section('content')
@php
$__user = auth()->user();
$__current = $__user->activeSubscription();
$__plans = \App\Models\SubscriptionPlan::where('is_active', true)->orderBy('sort_order')->orderBy('price_monthly')->get();
$__daysLeft = null;
if ($__current && $__current->expires_at) {
    $__daysLeft = max(0, now()->diffInDays($__current->expires_at, false));
}
$__badgeColors = [
  'blue'=>['bg'=>'#eff6ff','text'=>'#2563eb','light'=>'#dbeafe'],
  'green'=>['bg'=>'#f0fdf4','text'=>'#16a34a','light'=>'#dcfce7'],
  'purple'=>['bg'=>'#faf5ff','text'=>'#7c3aed','light'=>'#ede9fe'],
  'orange'=>['bg'=>'#fff7ed','text'=>'#ea580c','light'=>'#fed7aa'],
  'red'=>['bg'=>'#fff1f2','text'=>'#e03057','light'=>'#ffe4e6'],
  'gray'=>['bg'=>'#f1f5f9','text'=>'#475569','light'=>'#e2e8f0'],
];
@endphp

<div class="dash-content">

<div class="sub-page">

  {{-- Header --}}
  <div class="sub-head">
    <h1>Choose Your Subscription Plan</h1>
    <p>Pick the plan that fits your business. Upgrade or switch anytime — no lock-in.</p>
  </div>

  @if(session('subscribed'))
  <div class="alert-custom" style="background:#dcfce7;color:#166534;border:1px solid #bbf7d0;">
    <svg width="18" height="18" fill="none" stroke="#16a34a" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    {{ session('subscribed') }}
  </div>
  @endif

  {{-- Current Plan Banner --}}
  @if($__current)
  <div class="current-banner">
    <div style="position:relative;z-index:1;">
      <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.3rem;flex-wrap:wrap;">
        <span style="background:rgba(16,185,129,.2);color:#34d399;font-size:.62rem;font-weight:800;padding:.18rem .55rem;border-radius:50px;text-transform:uppercase;letter-spacing:.06em;">
          {{ $__current->status === 'trial' ? 'FREE TRIAL' : 'ACTIVE' }}
        </span>
        <span style="font-weight:700;font-size:1.05rem;">{{ $__current->plan->name ?? 'Current Plan' }}</span>
        @if($__current->billing_cycle)
        <span style="background:rgba(255,255,255,.1);color:#94b3d4;font-size:.65rem;padding:.12rem .5rem;border-radius:50px;">
          {{ $__current->billing_cycle }}
        </span>
        @endif
      </div>
      <div style="font-size:.82rem;color:#94b3d4;">
        @if($__current->expires_at)
          @if($__daysLeft !== null && $__daysLeft > 0)
            <strong style="color:#fff;">{{ $__daysLeft }}</strong> day{{ $__daysLeft != 1 ? 's' : '' }} remaining
          @else
            This plan has expired
          @endif
        @else
          Unlimited access &middot; No expiry
        @endif
        &middot; Started {{ $__current->starts_at ? $__current->starts_at->format('M d, Y') : '—' }}
      </div>
    </div>
    <div class="right" style="display:flex;align-items:center;gap:.75rem;position:relative;z-index:1;">
      @if($__daysLeft !== null && $__daysLeft > 0 && $__daysLeft <= 3)
      <span style="background:rgba(239,68,68,.25);color:#fca5a5;font-size:.72rem;font-weight:700;padding:.25rem .7rem;border-radius:50px;display:flex;align-items:center;gap:.3rem;white-space:nowrap;">
        <svg width="14" height="14" fill="none" stroke="#fca5a5" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        Only {{ $__daysLeft }} day{{ $__daysLeft != 1 ? 's' : '' }} left
      </span>
      @endif
      @if($__daysLeft !== null)
      <div style="background:rgba(16,185,129,.12);border:1px solid rgba(16,185,129,.25);border-radius:12px;padding:.3rem 1rem;text-align:center;min-width:64px;">
        <div style="font-size:1.6rem;font-weight:800;color:#34d399;line-height:1.2;">{{ $__daysLeft }}</div>
        <div style="font-size:.6rem;color:#94b3d4;font-weight:700;text-transform:uppercase;letter-spacing:.04em;">Days</div>
      </div>
      @endif
    </div>
  </div>
  @elseif($__plans->count() > 0)
  <div class="no-plan-banner">
    <div style="display:flex;align-items:center;gap:.6rem;">
      <svg width="22" height="22" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
      <span style="font-weight:600;font-size:.9rem;">You don't have an active subscription. Pick a plan below to get started!</span>
    </div>
  </div>
  @endif

  {{-- Billing Toggle --}}
  <div class="bill-wrap">
    <span class="bill-lbl active" id="lbl-m">Monthly</span>
    <div class="bill-track" id="billTrack" onclick="toggleBilling()" role="switch" aria-checked="false" tabindex="0">
      <div class="bill-knob" id="billKnob"></div>
    </div>
    <span class="bill-lbl" id="lbl-y">
      Yearly
      <span class="save-badge">Save ~17%</span>
    </span>
  </div>

  {{-- Plans Grid --}}
  @if($__plans->isEmpty())
  <div class="no-plans">
    <svg width="52" height="52" fill="none" stroke="#cbd5e1" stroke-width="1.2" viewBox="0 0 24 24" style="margin:0 auto 1rem;display:block;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 14h6m-3-3v6m-7 4v-16a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16l-3-2l-2 2l-2-2l-2 2l-2-2l-3 2"/></svg>
    <p style="font-weight:600;color:#64748b;">No subscription plans available</p>
    <p style="font-size:.82rem;margin-top:.15rem;">Please check back soon or contact support.</p>
    <a href="/dashboard" style="display:inline-flex;align-items:center;gap:.35rem;margin-top:1rem;color:#2563eb;font-weight:600;font-size:.875rem;text-decoration:none;">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
      Go to Dashboard
    </a>
  </div>
  @else
  <div class="plans-grid">
    @foreach($__plans as $plan)
    @php
      $isCurrent = $__current && $__current->subscription_plan_id == $plan->id && in_array($__current->status, ['active','trial']);
      $isFree    = $plan->price_monthly == 0;
      $features  = is_array($plan->features) ? $plan->features : [];
      $bc = $__badgeColors[$plan->badge_color] ?? $__badgeColors['blue'];
    @endphp
    <div class="plan-card {{ $plan->is_featured && !$isCurrent ? 'featured' : '' }} {{ $isCurrent ? 'current' : '' }} animate__animated animate__fadeInUp stagger-{{ ($loop->iteration % 5) + 1 }}">

      {{-- Badges --}}
      @if($isCurrent)
      <div class="plan-badge-top green">✓ Current Plan</div>
      @elseif($plan->is_featured)
      <div class="plan-badge-top blue">Most Popular</div>
      @endif

      {{-- Header --}}
      <div class="plan-head">
        <span class="plan-dot" style="background:{{ $bc['text'] }};box-shadow:0 0 0 3px {{ $bc['light'] }};"></span>
        <span class="plan-name">{{ $plan->name }}</span>
      </div>
      @if($plan->description)
      <div class="plan-desc">{{ $plan->description }}</div>
      @else
      <div class="plan-desc">&nbsp;</div>
      @endif

      {{-- Price --}}
      <div class="plan-price">
        <div>
          <span class="cur">{{ $plan->currency }}</span>
          <span class="amt m-amt">{{ number_format($plan->price_monthly, 0) }}</span>
          <span class="amt y-amt" style="display:none;">{{ number_format($plan->price_yearly / 12, 0) }}</span>
          <span class="per">/mo</span>
        </div>
        @if($plan->price_yearly > 0)
        <div class="note y-note" style="display:none;">
          {{ $plan->currency }} {{ number_format($plan->price_yearly, 0) }}/yr
          @php $savings = ($plan->price_monthly * 12) - $plan->price_yearly; @endphp
          @if($savings > 0)<span>(save {{ $plan->currency }} {{ number_format($savings, 0) }}/yr)</span>@endif
        </div>
        @endif
      </div>

      {{-- Limits --}}
      <div class="limits-row">
        @if($plan->max_users)
        <div class="limit-box">
          <div class="lbl">Users</div>
          <div class="val">{{ $plan->max_users >= 999999 ? '∞' : $plan->max_users }}</div>
        </div>
        @endif
        @if($plan->max_products)
        <div class="limit-box">
          <div class="lbl">Products</div>
          <div class="val">{{ $plan->max_products >= 999999 ? '∞' : number_format($plan->max_products) }}</div>
        </div>
        @endif
        @if($plan->max_locations)
        <div class="limit-box">
          <div class="lbl">Locations</div>
          <div class="val">{{ $plan->max_locations >= 999999 ? '∞' : $plan->max_locations }}</div>
        </div>
        @endif
      </div>

      {{-- Features --}}
      @if(count($features) > 0)
      <div class="plan-feats">
        @foreach($features as $feat)
        <div class="plan-feat">
          <svg viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
          {{ $feat }}
        </div>
        @endforeach
      </div>
      @else
      <div class="plan-feats">
        <div style="font-size:.75rem;color:#cbd5e1;font-style:italic;">No additional features listed</div>
      </div>
      @endif

      {{-- Action --}}
      @if($isCurrent)
      <button class="btn-plan current-btn" disabled>
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="display:inline;vertical-align:middle;margin-right:.3rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        Current Plan
      </button>
      @elseif($isFree)
      <button class="btn-plan green" onclick="choosePlan({{ $plan->id }}, 'monthly', 'Start 14-Day Free Trial')">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline;vertical-align:middle;margin-right:.35rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        Start 14-Day Free Trial
      </button>
      @else
      <button class="btn-plan {{ $plan->is_featured ? 'primary' : 'outline' }}" onclick="choosePlan({{ $plan->id }}, document.querySelector('.bill-input[data-plan=\'{{ $plan->id }}\']')?.value || 'monthly', 'Subscribe to {{ $plan->name }}')">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline;vertical-align:middle;margin-right:.35rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        Subscribe to {{ $plan->name }}
      </button>
      <input type="hidden" class="bill-input" data-plan="{{ $plan->id }}" value="monthly">
      @endif
    </div>
    @endforeach
  </div>
  @endif

</div>{{-- /sub-page --}}

</div>{{-- /dash-content --}}
@endsection

@section('scripts')
<script>
/* ── SweetAlert2 Toast Config ─────────────────────────── */
const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 3500,
  timerProgressBar: true,
  didOpen: (toast) => {
    toast.addEventListener('mouseenter', Swal.stopTimer);
    toast.addEventListener('mouseleave', Swal.resumeTimer);
  }
});

/* ── Billing Toggle ───────────────────────────────────── */
(function(){
  const track = document.getElementById('billTrack');
  const lblM  = document.getElementById('lbl-m');
  const lblY  = document.getElementById('lbl-y');
  let yearly  = false;

  window.toggleBilling = function(){
    yearly = !yearly;
    track.classList.toggle('on', yearly);
    track.setAttribute('aria-checked', yearly);
    lblM.classList.toggle('active', !yearly);
    lblY.classList.toggle('active', yearly);

    document.querySelectorAll('.m-amt').forEach(el => el.style.display = yearly ? 'none' : '');
    document.querySelectorAll('.y-amt').forEach(el => el.style.display = yearly ? '' : 'none');
    document.querySelectorAll('.y-note').forEach(el => el.style.display = yearly ? '' : 'none');
    document.querySelectorAll('.bill-input').forEach(el => el.value = yearly ? 'yearly' : 'monthly');
  };

  track.addEventListener('keydown', function(e){
    if(e.key === 'Enter' || e.key === ' ') { e.preventDefault(); toggleBilling(); }
  });
})();

/* ── AJAX Plan Selection ──────────────────────────────── */
async function choosePlan(planId, cycle, actionLabel) {
  const result = await Swal.fire({
    title: actionLabel + '?',
    text: 'You will be subscribed to this plan immediately.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#2563eb',
    cancelButtonColor: '#94a3b8',
    confirmButtonText: 'Yes, subscribe',
    cancelButtonText: 'Cancel',
    reverseButtons: true,
    backdrop: 'rgba(15,23,42,.35)',
    customClass: { popup: 'rounded-2xl' }
  });

  if (!result.isConfirmed) return;

  Swal.fire({
    title: 'Subscribing…',
    text: 'Please wait a moment',
    allowOutsideClick: false,
    allowEscapeKey: false,
    didOpen: () => Swal.showLoading()
  });

  try {
    const resp = await fetch('/subscription/choose', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
      },
      body: JSON.stringify({ plan_id: planId, billing_cycle: cycle })
    });

    const data = await resp.json();

    if (resp.ok) {
      if (data.checkout_url) {
        // Paid plan — redirect to Snippe checkout
        Swal.fire({
          title: 'Redirecting to payment...',
          html: `
            <div style="text-align:center;">
              <div style="width:60px;height:60px;border-radius:50%;border:4px solid #e2e8f0;border-top-color:#2563eb;animation:spin 0.8s linear infinite;margin:0 auto 1rem;"></div>
              <p style="color:#64748b;font-size:.88rem;margin-bottom:.5rem;">You will be redirected to Snippe secure checkout to complete payment.</p>
              <p style="color:#94a3b8;font-size:.78rem;">After payment, your subscription will be activated automatically.</p>
            </div>
          `,
          allowOutsideClick: false,
          allowEscapeKey: false,
          showConfirmButton: false,
          didOpen: () => {
            window.open(data.checkout_url, '_blank');
            setTimeout(() => {
              Swal.fire({
                title: 'Payment Started',
                html: `
                  <p style="color:#64748b;font-size:.88rem;">Checkout opened in a new tab.</p>
                  <p style="color:#94a3b8;font-size:.78rem;margin-top:.3rem;">Complete payment there, then refresh this page.</p>
                `,
                icon: 'info',
                confirmButtonColor: '#2563eb',
                confirmText: 'I\'ve completed payment',
                showCancelButton: true,
                cancelButtonText: 'Cancel',
                reverseButtons: true,
              }).then((r) => {
                if (r.isConfirmed) window.location.reload();
              });
            }, 1500);
          }
        });
      } else {
        // Free / Trial — activated immediately
        Swal.close();
        Toast.fire({ icon: 'success', title: data.message || 'Subscribed successfully!' });
        setTimeout(() => window.location.reload(), 1200);
      }
    } else {
      Swal.close();
      Toast.fire({ icon: 'error', title: data.message || 'Subscription failed. Please try again.' });
      if (data.checkout_url) {
        // Fallback: still provide checkout link
        window.open(data.checkout_url, '_blank');
      }
    }
  } catch (err) {
    Swal.close();
    Toast.fire({ icon: 'error', title: 'Network error. Please check your connection.' });
  }
}
</script>
@endsection
