@extends('layouts.dashboard')

@section('page_title', 'My Subscription')

@section('page_styles')
<style>
.sub-header-section{text-align:center;margin-bottom:2.5rem;}
.sub-header-section h1{font-size:1.75rem;font-weight:800;color:#0f172a;letter-spacing:-0.02em;}
.sub-header-section p{color:#64748b;font-size:0.92rem;margin-top:0.35rem;}
.billing-toggle{display:flex;align-items:center;justify-content:center;gap:0.85rem;margin-bottom:2rem;}
.toggle-track{position:relative;width:52px;height:26px;background:#e2e8f0;border-radius:50px;cursor:pointer;transition:background .3s;}
.toggle-track.on{background:#2563eb;}
.toggle-knob{position:absolute;top:3px;left:3px;width:20px;height:20px;background:#fff;border-radius:50%;transition:left .3s;box-shadow:0 2px 6px rgba(0,0,0,.18);}
.toggle-track.on .toggle-knob{left:29px;}
.toggle-label{font-size:.85rem;font-weight:600;color:#94a3b8;transition:color .2s;}
.toggle-label.active{color:#0f172a;}
.save-badge{background:#dcfce7;color:#16a34a;font-size:.65rem;font-weight:700;padding:.18rem .45rem;border-radius:50px;margin-left:.3rem;}
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
@endphp

<div class="dash-content">

  {{-- Header --}}
  <div class="sub-header-section">
    <h1>Choose Your Plan</h1>
    <p>Power your business with the right plan. Upgrade, downgrade or switch anytime.</p>
  </div>

  @if(session('subscribed'))
  <div class="alert" style="background:#dcfce7;color:#166534;border:1px solid #bbf7d0;padding:.85rem 1.25rem;border-radius:12px;font-size:.875rem;font-weight:500;margin-bottom:1.5rem;">
    {{ session('subscribed') }}
  </div>
  @endif

  {{-- Current Plan Banner --}}
  @if($__current)
  <div style="background:linear-gradient(135deg,#0f2748,#1e3a5f);border-radius:16px;padding:1.25rem 2rem;color:#fff;margin-bottom:2rem;display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
    <div>
      <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.25rem;">
        <span style="background:rgba(16,185,129,.2);color:#34d399;font-size:.65rem;font-weight:700;padding:.15rem .5rem;border-radius:50px;text-transform:uppercase;letter-spacing:.05em;">
          @if($__current->status === 'trial') Free Trial @else Active @endif
        </span>
        <span style="font-weight:700;font-size:1.05rem;">{{ $__current->plan->name ?? 'Current Plan' }}</span>
      </div>
      <div style="font-size:.82rem;color:#94b3d4;">
        @if($__current->status === 'trial')
          Trial plan — 
        @endif
        @if($__current->expires_at)
          @if($__daysLeft !== null && $__daysLeft > 0)
            {{ $__daysLeft }} day{{ $__daysLeft != 1 ? 's' : '' }} remaining
          @else
            Expired
          @endif
        @else
          Never expires
        @endif
        &middot; {{ $__current->billing_cycle ?? 'monthly' }} billing
      </div>
    </div>
    <div style="display:flex;align-items:center;gap:.75rem;">
      @if($__daysLeft !== null && $__daysLeft > 0 && $__daysLeft <= 7)
      <span style="background:rgba(239,68,68,.2);color:#fca5a5;font-size:.75rem;font-weight:700;padding:.25rem .65rem;border-radius:50px;">
        ⚠ {{ $__daysLeft }} day{{ $__daysLeft != 1 ? 's' : '' }} left
      </span>
      @endif
      @if($__daysLeft !== null)
      <div style="background:rgba(16,185,129,.15);border:1px solid rgba(16,185,129,.3);border-radius:10px;padding:.35rem 1rem;text-align:center;">
        <div style="font-size:1.5rem;font-weight:800;color:#34d399;line-height:1;">{{ $__daysLeft }}</div>
        <div style="font-size:.65rem;color:#94b3d4;font-weight:600;">days left</div>
      </div>
      @endif
    </div>
  </div>
  @elseif(\App\Models\SubscriptionPlan::where('is_active', true)->count() > 0)
  <div style="background:linear-gradient(135deg,#f59e0b,#d97706);border-radius:16px;padding:1rem 2rem;color:#fff;margin-bottom:2rem;display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
    <span style="font-size:.9rem;font-weight:600;">You don't have an active subscription yet. Choose a plan below to get started!</span>
  </div>
  @endif

  {{-- Billing toggle --}}
  <div class="billing-toggle">
    <span class="toggle-label active" id="lbl-monthly">Monthly</span>
    <div class="toggle-track" id="billingToggle" onclick="toggleBilling()">
      <div class="toggle-knob"></div>
    </div>
    <span class="toggle-label" id="lbl-yearly">Yearly <span class="save-badge">Save ~17%</span></span>
  </div>

  @if($__plans->isEmpty())
  <div style="text-align:center;padding:3rem;color:#94a3b8;">
    <svg width="48" height="48" fill="none" stroke="#cbd5e1" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 1rem;display:block;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 14h6m-3-3v6m-7 4v-16a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16l-3-2l-2 2l-2-2l-2 2l-2-2l-3 2"/></svg>
    <p style="font-weight:600;">No subscription plans available yet.</p>
    <p style="font-size:.82rem;margin-top:.25rem;">Please check back soon or contact support.</p>
    <a href="/dashboard" style="display:inline-block;margin-top:1rem;color:#2563eb;font-weight:600;font-size:.875rem;text-decoration:none;">&larr; Go to Dashboard</a>
  </div>
  @else
  <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:1.25rem;margin-bottom:2rem;">
    @foreach($__plans as $plan)
    @php
      $isCurrent = $__current && $__current->subscription_plan_id == $plan->id && in_array($__current->status, ['active','trial']);
      $isFree    = $plan->price_monthly == 0;
      $features  = is_array($plan->features) ? $plan->features : [];
      $badgeColors = ['blue'=>['bg'=>'#eff6ff','text'=>'#2563eb','light'=>'#dbeafe'],'green'=>['bg'=>'#f0fdf4','text'=>'#16a34a','light'=>'#dcfce7'],'purple'=>['bg'=>'#faf5ff','text'=>'#7c3aed','light'=>'#ede9fe'],'orange'=>['bg'=>'#fff7ed','text'=>'#ea580c','light'=>'#fed7aa']];
      $bc = $badgeColors[$plan->badge_color] ?? $badgeColors['blue'];
    @endphp
    <div style="background:#fff;border-radius:20px;border:2px solid {{ $isCurrent ? '#10b981' : ($plan->is_featured ? '#2563eb' : '#e9edf5') }};padding:1.75rem;position:relative;transition:transform .2s,box-shadow .2s;display:flex;flex-direction:column;{{ $plan->is_featured && !$isCurrent ? 'box-shadow:0 8px 30px rgba(37,99,235,.15);' : '' }}"
         onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 20px 40px rgba(0,0,0,.1)'"
         onmouseout="this.style.transform='';this.style.boxShadow='{{ $plan->is_featured && !$isCurrent ? '0 8px 30px rgba(37,99,235,.15)' : '' }}'">
      {{-- Badges --}}
      @if($isCurrent)
      <div style="position:absolute;top:-1px;left:50%;transform:translateX(-50%);background:#10b981;color:#fff;font-size:.65rem;font-weight:700;padding:.25rem .9rem;border-radius:0 0 10px 10px;">Current Plan</div>
      @elseif($plan->is_featured)
      <div style="position:absolute;top:-1px;left:50%;transform:translateX(-50%);background:#2563eb;color:#fff;font-size:.65rem;font-weight:700;padding:.25rem .9rem;border-radius:0 0 10px 10px;">Most Popular</div>
      @endif

      {{-- Plan Name & Desc --}}
      <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.35rem;">
        <div style="width:10px;height:10px;border-radius:50%;background:{{ $bc['text'] }};"></div>
        <div style="font-size:1.05rem;font-weight:800;color:#0f172a;">{{ $plan->name }}</div>
      </div>
      @if($plan->description)
      <div style="font-size:.78rem;color:#64748b;margin-bottom:1.25rem;min-height:2.2rem;">{{ $plan->description }}</div>
      @endif

      {{-- Price --}}
      <div style="margin-bottom:1.25rem;">
        <div>
          <span style="font-size:.82rem;font-weight:600;color:#64748b;vertical-align:top;margin-top:.35rem;display:inline-block;">{{ $plan->currency }}</span>
          <span class="monthly-amount" style="font-size:2rem;font-weight:900;color:#0f172a;line-height:1;letter-spacing:-.03em;">{{ number_format($plan->price_monthly, 0) }}</span>
          <span class="yearly-amount" style="font-size:2rem;font-weight:900;color:#0f172a;line-height:1;letter-spacing:-.03em;display:none;">{{ number_format($plan->price_yearly / 12, 0) }}</span>
          <span style="font-size:.78rem;color:#94a3b8;"> / month</span>
        </div>
        @if($plan->price_yearly > 0)
        <div class="yearly-note" style="font-size:.72rem;color:#16a34a;font-weight:600;margin-top:.2rem;display:none;">
          {{ $plan->currency }} {{ number_format($plan->price_yearly, 0) }} billed yearly
          @php $savings = ($plan->price_monthly * 12) - $plan->price_yearly; @endphp
          @if($savings > 0)
            <span style="color:#64748b;">(Save {{ $plan->currency }} {{ number_format($savings, 0) }})</span>
          @endif
        </div>
        @endif
      </div>

      {{-- Limits --}}
      <div style="display:flex;gap:.75rem;margin-bottom:1rem;flex-wrap:wrap;">
        @if($plan->max_users)
        <div style="flex:1;background:#f8fafc;border-radius:10px;padding:.4rem .6rem;text-align:center;">
          <div style="font-size:.7rem;color:#94a3b8;font-weight:500;">Users</div>
          <div style="font-size:.9rem;font-weight:800;color:#0f172a;">{{ $plan->max_users >= 999999 ? '∞' : $plan->max_users }}</div>
        </div>
        @endif
        @if($plan->max_products)
        <div style="flex:1;background:#f8fafc;border-radius:10px;padding:.4rem .6rem;text-align:center;">
          <div style="font-size:.7rem;color:#94a3b8;font-weight:500;">Products</div>
          <div style="font-size:.9rem;font-weight:800;color:#0f172a;">{{ $plan->max_products >= 999999 ? '∞' : number_format($plan->max_products) }}</div>
        </div>
        @endif
        @if($plan->max_locations)
        <div style="flex:1;background:#f8fafc;border-radius:10px;padding:.4rem .6rem;text-align:center;">
          <div style="font-size:.7rem;color:#94a3b8;font-weight:500;">Locations</div>
          <div style="font-size:.9rem;font-weight:800;color:#0f172a;">{{ $plan->max_locations >= 999999 ? '∞' : $plan->max_locations }}</div>
        </div>
        @endif
      </div>

      {{-- Features --}}
      @if(count($features) > 0)
      <div style="flex:1;margin-bottom:1.25rem;">
        @foreach($features as $feat)
        <div style="display:flex;align-items:center;gap:.4rem;font-size:.8rem;color:#374151;margin-bottom:.4rem;">
          <svg width="14" height="14" fill="none" stroke="#10b981" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
          {{ $feat }}
        </div>
        @endforeach
      </div>
      @endif

      {{-- Button --}}
      @if($isCurrent)
      <button style="width:100%;padding:.7rem;border-radius:11px;font-size:.85rem;font-weight:700;border:none;cursor:default;font-family:inherit;background:#f1f5f9;color:#94a3b8;">
        ✓ Current Plan
      </button>
      @elseif($isFree)
      <form method="POST" action="/subscription/choose">
        @csrf
        <input type="hidden" name="plan_id" value="{{ $plan->id }}">
        <input type="hidden" name="billing_cycle" value="monthly">
        <button type="submit" style="width:100%;padding:.7rem;border-radius:11px;font-size:.85rem;font-weight:700;border:none;cursor:pointer;font-family:inherit;background:linear-gradient(135deg,#10b981,#059669);color:#fff;box-shadow:0 4px 12px rgba(16,185,129,.3);transition:all .2s;"
                onmouseover="this.style.background='linear-gradient(135deg,#059669,#047857)';this.style.transform='translateY(-1px)'"
                onmouseout="this.style.background='linear-gradient(135deg,#10b981,#059669)';this.style.transform=''">
          Start 14-Day Free Trial
        </button>
      </form>
      @else
      <form method="POST" action="/subscription/choose" class="choose-form" data-plan="{{ $plan->id }}">
        @csrf
        <input type="hidden" name="plan_id" value="{{ $plan->id }}">
        <input type="hidden" name="billing_cycle" value="monthly" class="billing-input">
        <button type="submit" style="width:100%;padding:.7rem;border-radius:11px;font-size:.85rem;font-weight:700;border:none;cursor:pointer;font-family:inherit;transition:all .2s;{{ $plan->is_featured ? 'background:linear-gradient(135deg,#2563eb,#1d4ed8);color:#fff;box-shadow:0 4px 12px rgba(37,99,235,.3);' : 'background:#fff;color:#2563eb;border:2px solid #2563eb;' }}"
                onmouseover="this.style.transform='translateY(-1px)'"
                onmouseout="this.style.transform=''">
          Get {{ $plan->name }}
        </button>
      </form>
      @endif
    </div>
    @endforeach
  </div>
  @endif

</div>
@endsection

@section('scripts')
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
@endsection
