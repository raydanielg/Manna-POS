<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Subscription Expired — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #0f172a 100%);
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
            padding: 1.5rem;
        }
        .expired-card {
            background: rgba(255,255,255,0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 28px;
            padding: 3rem 2.5rem;
            max-width: 520px; width: 100%;
            text-align: center;
            box-shadow: 0 25px 80px rgba(0,0,0,0.4), 0 0 0 1px rgba(255,255,255,0.03) inset;
            position: relative; overflow: hidden;
        }
        .expired-card::before {
            content: ''; position: absolute; top: -60%; left: -40%;
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(245,158,11,0.12) 0%, transparent 60%);
            border-radius: 50%; pointer-events: none;
        }
        .expired-card::after {
            content: ''; position: absolute; bottom: -40%; right: -30%;
            width: 250px; height: 250px;
            background: radial-gradient(circle, rgba(37,99,235,0.08) 0%, transparent 60%);
            border-radius: 50%; pointer-events: none;
        }
        .expired-icon-wrap {
            width: 80px; height: 80px; border-radius: 24px;
            background: linear-gradient(135deg, rgba(245,158,11,0.2), rgba(239,68,68,0.15));
            border: 1px solid rgba(245,158,11,0.2);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.5rem;
            position: relative; z-index: 1;
            animation: pulseExpire 2.5s ease-in-out infinite;
        }
        @keyframes pulseExpire {
            0%,100% { box-shadow: 0 0 0 0 rgba(245,158,11,0.3); }
            50% { box-shadow: 0 0 0 20px rgba(245,158,11,0); }
        }
        .expired-icon-wrap svg { width: 36px; height: 36px; color: #fbbf24; }
        .expired-title {
            font-size: 1.5rem; font-weight: 800; color: #f1f5f9;
            margin-bottom: 0.5rem; position: relative; z-index: 1;
        }
        .expired-sub {
            font-size: 0.85rem; color: #94a3b8; margin-bottom: 1.5rem;
            line-height: 1.6; position: relative; z-index: 1;
        }
        .expired-plans-label {
            font-size: 0.7rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.08em; color: #64748b; margin-bottom: 0.75rem;
            position: relative; z-index: 1;
        }
        .plans-row {
            display: flex; gap: 0.75rem; justify-content: center;
            flex-wrap: wrap; margin-bottom: 1.5rem;
            position: relative; z-index: 1;
        }
        .plan-chip {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 14px; padding: 1rem 1.25rem;
            text-align: left; min-width: 140px;
            transition: all 0.2s ease;
            cursor: pointer; text-decoration: none;
        }
        .plan-chip:hover {
            background: rgba(255,255,255,0.08);
            border-color: rgba(37,99,235,0.3);
            transform: translateY(-2px);
        }
        .plan-chip-name {
            font-size: 0.82rem; font-weight: 700; color: #e2e8f0;
            margin-bottom: 0.2rem;
        }
        .plan-chip-price {
            font-size: 1.1rem; font-weight: 800; color: #fbbf24;
        }
        .plan-chip-period {
            font-size: 0.68rem; color: #94a3b8;
        }
        .expired-actions {
            display: flex; flex-direction: column; gap: 0.6rem;
            position: relative; z-index: 1;
        }
        .expired-btn {
            padding: 0.75rem 1.5rem; border-radius: 12px;
            font-size: 0.82rem; font-weight: 700;
            display: inline-flex; align-items: center; justify-content: center;
            gap: 0.5rem; transition: all 0.2s ease;
            text-decoration: none; border: none; cursor: pointer;
        }
        .expired-btn:hover { transform: translateY(-1px); }
        .btn-grad-amber {
            background: linear-gradient(135deg, #f59e0b, #ef4444);
            color: #fff; box-shadow: 0 4px 16px rgba(245,158,11,0.35);
        }
        .btn-grad-amber:hover { box-shadow: 0 6px 24px rgba(245,158,11,0.45); }
        .btn-ghost {
            background: rgba(255,255,255,0.05); color: #94a3b8;
            border: 1px solid rgba(255,255,255,0.08);
        }
        .btn-ghost:hover { background: rgba(255,255,255,0.1); color: #e2e8f0; }
        .expired-footer {
            margin-top: 1.5rem; font-size: 0.72rem; color: #64748b;
            position: relative; z-index: 1;
        }
        .expired-footer a { color: #94a3b8; text-decoration: none; }
        .expired-footer a:hover { color: #cbd5e1; }
        @media (max-width: 480px) {
            .expired-card { padding: 2rem 1.5rem; }
            .plans-row { flex-direction: column; }
            .plan-chip { width: 100%; }
        }
    </style>
</head>
<body>
    <div class="expired-card">
        <div class="expired-icon-wrap">
            <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h1 class="expired-title">Free Trial Expired</h1>
        <p class="expired-sub">
            Hi <strong style="color:#e2e8f0;">{{ $user->name ?? 'there' }}</strong>, your free trial has ended. To continue using {{ config('app.name') }}, please choose a plan below.
        </p>

        @if($plans->count())
        <div class="expired-plans-label">Available Plans</div>
        <div class="plans-row">
            @foreach($plans as $plan)
            <a href="{{ route('subscription.plans') }}" class="plan-chip">
                <div class="plan-chip-name">{{ $plan->name }}</div>
                <div class="plan-chip-price">
                    @if($plan->price_monthly > 0)
                        TSh {{ number_format($plan->price_monthly, 0) }}
                    @else
                        Free
                    @endif
                </div>
                <div class="plan-chip-period">{{ $plan->price_monthly > 0 ? '/month' : 'forever' }}</div>
            </a>
            @endforeach
        </div>
        @endif

        <div class="expired-actions">
            <a href="{{ route('subscription.plans') }}" class="expired-btn btn-grad-amber">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                Upgrade Now
            </a>
            <form method="POST" action="{{ route('logout') }}" style="display:contents;">
                @csrf
                <button type="submit" class="expired-btn btn-ghost">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Sign Out
                </button>
            </form>
        </div>

        <div class="expired-footer">
            Questions? <a href="mailto:{{ config('app.support_email', 'support@mannapos.co.tz') }}">Contact Support</a>
        </div>
    </div>
</body>
</html>
