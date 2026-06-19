<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Account Suspended — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
            padding: 1.5rem;
        }
        .blocked-card {
            background: rgba(255,255,255,0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 28px;
            padding: 3rem 2.5rem;
            max-width: 440px; width: 100%;
            text-align: center;
            box-shadow: 0 25px 80px rgba(0,0,0,0.4), 0 0 0 1px rgba(255,255,255,0.03) inset;
            position: relative; overflow: hidden;
        }
        .blocked-card::before {
            content: ''; position: absolute; top: -60%; left: -40%;
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(239,68,68,0.12) 0%, transparent 60%);
            border-radius: 50%; pointer-events: none;
        }
        .blocked-card::after {
            content: ''; position: absolute; bottom: -40%; right: -30%;
            width: 250px; height: 250px;
            background: radial-gradient(circle, rgba(245,158,11,0.08) 0%, transparent 60%);
            border-radius: 50%; pointer-events: none;
        }
        .blocked-icon-wrap {
            width: 80px; height: 80px; border-radius: 24px;
            background: linear-gradient(135deg, rgba(239,68,68,0.2), rgba(245,158,11,0.15));
            border: 1px solid rgba(239,68,68,0.2);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.5rem;
            position: relative; z-index: 1;
            animation: pulseBlock 2.5s ease-in-out infinite;
        }
        @keyframes pulseBlock {
            0%,100% { box-shadow: 0 0 0 0 rgba(239,68,68,0.3); }
            50% { box-shadow: 0 0 0 20px rgba(239,68,68,0); }
        }
        .blocked-icon-wrap svg { width: 36px; height: 36px; color: #f87171; }
        .blocked-title {
            font-size: 1.5rem; font-weight: 800; color: #f1f5f9;
            margin-bottom: 0.5rem; position: relative; z-index: 1;
        }
        .blocked-sub {
            font-size: 0.85rem; color: #94a3b8; margin-bottom: 1.5rem;
            line-height: 1.6; position: relative; z-index: 1;
        }
        .blocked-reason-box {
            background: rgba(239,68,68,0.08);
            border: 1px solid rgba(239,68,68,0.15);
            border-radius: 14px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            text-align: left;
            position: relative; z-index: 1;
        }
        .blocked-reason-label {
            font-size: 0.65rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.08em; color: #f87171; margin-bottom: 0.35rem;
        }
        .blocked-reason-text {
            font-size: 0.82rem; color: #cbd5e1; line-height: 1.5;
        }
        .blocked-actions {
            display: flex; flex-direction: column; gap: 0.6rem;
            position: relative; z-index: 1;
        }
        .blocked-btn {
            padding: 0.75rem 1.5rem; border-radius: 12px;
            font-size: 0.82rem; font-weight: 700;
            display: inline-flex; align-items: center; justify-content: center;
            gap: 0.5rem; transition: all 0.2s ease;
            text-decoration: none;
        }
        .blocked-btn:hover { transform: translateY(-1px); }
        .btn-primary-grad {
            background: linear-gradient(135deg, #2563eb, #7c3aed);
            color: #fff; box-shadow: 0 4px 16px rgba(37,99,235,0.35);
        }
        .btn-primary-grad:hover { box-shadow: 0 6px 24px rgba(37,99,235,0.45); }
        .btn-ghost {
            background: rgba(255,255,255,0.05); color: #94a3b8;
            border: 1px solid rgba(255,255,255,0.08);
        }
        .btn-ghost:hover { background: rgba(255,255,255,0.1); color: #e2e8f0; }
        .blocked-footer {
            margin-top: 1.5rem; font-size: 0.72rem; color: #64748b;
            position: relative; z-index: 1;
        }
        .blocked-footer a { color: #94a3b8; text-decoration: none; }
        .blocked-footer a:hover { color: #cbd5e1; }
    </style>
</head>
<body>
    <div class="blocked-card">
        <div class="blocked-icon-wrap">
            <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
            </svg>
        </div>
        <h1 class="blocked-title">Account Suspended</h1>
        <p class="blocked-sub">
            Hi <strong style="color:#e2e8f0;">{{ $name }}</strong>, your account has been temporarily suspended from accessing {{ config('app.name') }}.
        </p>

        <div class="blocked-reason-box">
            <div class="blocked-reason-label">Reason for suspension</div>
            <div class="blocked-reason-text">{{ $reason }}</div>
        </div>

        <div class="blocked-actions">
            <a href="mailto:{{ $email }}" class="blocked-btn btn-primary-grad">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Contact Support
            </a>
            <form method="POST" action="{{ route('logout') }}" style="display:contents;">
                @csrf
                <button type="submit" class="blocked-btn btn-ghost">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Sign Out
                </button>
            </form>
        </div>

        <div class="blocked-footer">
            Need help? Email us at <a href="mailto:{{ $email }}">{{ $email }}</a>
        </div>
    </div>
</body>
</html>
