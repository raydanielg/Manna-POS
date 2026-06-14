<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('icons8-dynamics-365-100.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @stack('head')
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #ffffff;
            min-height: 100vh;
            display: flex;
            color: #000;
            -webkit-font-smoothing: antialiased;
        }

        .auth-wrapper {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        /* ===== LEFT BRAND PANEL ===== */
        .auth-brand-side {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 0;
            position: relative;
            overflow: hidden;
            background: linear-gradient(145deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
        }

        /* Animated mesh background */
        .auth-brand-side::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 80% 50% at 20% 20%, rgba(16,185,129,0.15) 0%, transparent 60%),
                radial-gradient(ellipse 60% 80% at 80% 80%, rgba(99,102,241,0.12) 0%, transparent 60%),
                radial-gradient(ellipse 40% 40% at 50% 50%, rgba(14,165,233,0.06) 0%, transparent 60%);
            animation: meshShift 12s ease-in-out infinite alternate;
        }
        @keyframes meshShift {
            0% { opacity: 0.7; transform: scale(1) rotate(0deg); }
            100% { opacity: 1; transform: scale(1.08) rotate(2deg); }
        }

        /* Dot grid */
        .auth-brand-side::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle, rgba(255,255,255,0.06) 1px, transparent 1px);
            background-size: 28px 28px;
            pointer-events: none;
        }

        .auth-brand-inner {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            height: 100%;
            padding: 2.25rem 2.5rem;
            justify-content: space-between;
        }

        .auth-brand-top {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .brand-icon {
            width: 44px; height: 44px;
            background: linear-gradient(135deg, #10b981, #059669);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 4px 16px rgba(16,185,129,0.4);
        }
        .brand-icon svg { width: 22px; height: 22px; color: #fff; }

        .brand-name {
            font-size: 1.1rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            color: #fff;
        }
        .brand-tagline {
            font-size: 0.72rem;
            color: rgba(255,255,255,0.45);
            font-weight: 500;
            margin-top: 1px;
        }

        /* Center hero */
        .auth-brand-hero {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 2rem 0;
        }
        .hero-headline {
            font-size: 2rem;
            font-weight: 900;
            color: #fff;
            line-height: 1.15;
            letter-spacing: -0.04em;
            margin-bottom: 0.75rem;
        }
        .hero-headline span { color: #10b981; }
        .hero-subtext {
            font-size: 0.9rem;
            color: rgba(255,255,255,0.5);
            line-height: 1.65;
            max-width: 340px;
            margin-bottom: 2rem;
        }

        /* Feature chips */
        .feature-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.65rem;
            margin-bottom: 2rem;
        }
        .feature-chip {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 10px;
            padding: 0.65rem 0.85rem;
            backdrop-filter: blur(4px);
            transition: background 0.2s;
        }
        .feature-chip:hover { background: rgba(255,255,255,0.09); }
        .feature-chip-icon {
            width: 30px; height: 30px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .feature-chip-icon svg { width: 15px; height: 15px; color: #fff; }
        .feature-chip-text { font-size: 0.78rem; font-weight: 600; color: rgba(255,255,255,0.8); }

        /* Stat strip */
        .stat-strip {
            display: flex;
            gap: 1rem;
            padding: 1rem 1.25rem;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 14px;
            backdrop-filter: blur(8px);
        }
        .stat-item { flex: 1; text-align: center; }
        .stat-num { font-size: 1.4rem; font-weight: 900; color: #10b981; letter-spacing: -0.03em; }
        .stat-lbl { font-size: 0.65rem; color: rgba(255,255,255,0.4); font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; margin-top: 2px; }
        .stat-divider { width: 1px; background: rgba(255,255,255,0.08); align-self: stretch; }

        .auth-brand-bottom {
            border-top: 1px solid rgba(255,255,255,0.08);
            padding-top: 1.25rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.75rem;
        }

        .auth-brand-links {
            display: flex;
            gap: 1.5rem;
        }

        .auth-brand-links a {
            color: rgba(255,255,255,0.35);
            text-decoration: none;
            font-size: 0.75rem;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .auth-brand-links a:hover { color: rgba(255,255,255,0.7); }

        .auth-brand-copy {
            color: rgba(255,255,255,0.25);
            font-size: 0.7rem;
        }
        .auth-brand-copy span { color: rgba(255,255,255,0.4); }

        /* ===== RIGHT FORM PANEL ===== */
        .auth-form-side {
            width: 540px;
            min-width: 540px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 3.5rem;
            background: #ffffff;
            border-left: 1px solid #e5e7eb;
            position: relative;
            box-shadow: -5px 0 25px rgba(0,0,0,0.03);
        }


        .auth-form-header {
            position: relative;
            z-index: 1;
            margin-bottom: 2.5rem;
        }

        .auth-form-header .mobile-logo {
            display: none;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .auth-form-header .mobile-logo .logo-image {
            width: 48px;
            height: 48px;
            object-fit: contain;
        }

        .auth-form-header .mobile-logo span {
            font-size: 1.1rem;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        .auth-form-title {
            font-size: 1.75rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            margin-bottom: 0.5rem;
            color: #111827;
            background: linear-gradient(135deg, #111827 0%, #374151 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .auth-form-subtitle {
            color: #6b7280;
            font-size: 0.95rem;
            line-height: 1.6;
            font-weight: 400;
        }

        .auth-form-body {
            position: relative;
            z-index: 1;
            flex: 1;
            max-width: 380px;
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        .auth-form-body.fade-out {
            opacity: 0;
            transform: translateY(-10px);
        }

        .auth-form-body.fade-in {
            opacity: 0;
            transform: translateY(10px);
        }

        .auth-form-body.fade-in.show {
            opacity: 1;
            transform: translateY(0);
        }

        .role-description {
            margin-top: 0.5rem;
            padding: 0.75rem 1rem;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 0.85rem;
            color: #6b7280;
            display: none;
        }

        .form-group { margin-bottom: 1.5rem; }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: #1f2937;
            margin-bottom: 0.625rem;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            width: 1rem;
            height: 1rem;
            color: #6b7280;
            pointer-events: none;
        }

        .form-control {
            width: 100%;
            padding: 0.625rem 0.75rem 0.625rem 2.25rem;
            background: #f3f4f6;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            color: #111827;
            font-size: 0.875rem;
            font-family: 'Inter', sans-serif;
            transition: all 0.2s ease;
            outline: none;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.5rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
        }

        select.form-control {
            padding-right: 2.5rem;
        }

        .form-control::placeholder { color: #6b7280; }

        .form-control:focus {
            border-color: #10B981;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(16,185,129,0.1), 0 1px 2px rgba(0,0,0,0.05);
        }

        .form-control.is-invalid {
            border-color: #ef4444;
            background: #fef2f2;
            box-shadow: 0 0 0 3px rgba(239,68,68,0.1);
        }

        /* Input with prefix */
        .input-group {
            display: flex;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            border-radius: 0.375rem;
        }

        .input-prefix {
            display: inline-flex;
            align-items: center;
            padding: 0 0.75rem;
            font-size: 0.875rem;
            color: #6b7280;
            background: #e5e7eb;
            border: 1px solid #d1d5db;
            border-right: none;
            border-radius: 0.375rem 0 0 0.375rem;
        }

        .input-group .form-control {
            border-radius: 0 0.375rem 0.375rem 0;
            padding-left: 0.75rem;
        }

        .invalid-feedback {
            color: #dc2626;
            font-size: 0.82rem;
            margin-top: 0.6rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            padding: 0.65rem 0.9rem;
            border-radius: 10px;
            border: 1px solid rgba(239,68,68,0.2);
            border-left: 4px solid #ef4444;
            animation: slideIn 0.3s ease-out;
            box-shadow: 0 2px 8px rgba(239,68,68,0.1);
        }

        .invalid-feedback svg { width: 16px; height: 16px; flex-shrink: 0; color: #ef4444; }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-check {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .form-check-input {
            width: 18px; height: 18px;
            accent-color: #10B981;
            cursor: pointer;
            border: 2px solid #e5e7eb;
            border-radius: 4px;
        }

        .form-check-input:checked {
            background-color: #10B981;
            border-color: #10B981;
        }

        .form-check-label {
            font-size: 0.9rem;
            color: #4b5563;
            cursor: pointer;
            font-weight: 500;
        }

        /* ===== GREEN BUTTON WITH LOADING ===== */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 0.9rem 1.75rem;
            border: none;
            border-radius: 12px;
            font-size: 0.95rem;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            position: relative;
            gap: 0.6rem;
            letter-spacing: 0.01em;
        }

        .btn-primary {
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
            color: #fff;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.35);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.45);
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
        }

        .btn-primary:active { transform: translateY(0); }

        .btn-primary.loading {
            pointer-events: none;
            opacity: 0.85;
        }

        .btn-primary .spinner {
            display: none;
            width: 18px; height: 18px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        .btn-primary.loading .spinner { display: block; }
        .btn-primary.loading .btn-text { opacity: 0.7; }

        @keyframes spin { to { transform: rotate(360deg); } }

        .btn-primary::after {
            content: '';
            position: absolute;
            inset: -1px;
            border-radius: 11px;
            background: linear-gradient(135deg, rgba(255,255,255,0.15), transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
        }

        .btn-primary:hover::after { opacity: 1; }

        .btn-link {
            background: none;
            color: #6b7280;
            font-weight: 500;
            font-size: 0.83rem;
            width: auto;
            padding: 0.5rem;
        }

        .btn-link:hover { color: #10B981; }

        .auth-footer {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e5e7eb;
        }

        .auth-footer span {
            color: #6b7280;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .auth-footer-link a {
            color: #10B981;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            transition: color 0.2s ease;
            margin-left: 0.5rem;
        }

        .auth-footer-link a:hover { color: #059669; text-decoration: underline; }

        .divider {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin: 1.25rem 0;
            color: #9ca3af;
            font-size: 0.78rem;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e5e7eb;
        }

        .alert {
            padding: 1rem 1.25rem;
            border-radius: 12px;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
            line-height: 1.5;
            font-weight: 500;
        }

        .alert-success {
            background: linear-gradient(135deg, rgba(16,185,129,0.1) 0%, rgba(16,185,129,0.05) 100%);
            border: 1.5px solid rgba(16,185,129,0.25);
            color: #059669;
            animation: slideIn 0.3s ease-out;
        }

        .alert-info {
            background: linear-gradient(135deg, rgba(59,130,246,0.1) 0%, rgba(59,130,246,0.05) 100%);
            border: 1.5px solid rgba(59,130,246,0.25);
            color: #2563eb;
            animation: slideIn 0.3s ease-out;
        }

        .alert-danger {
            background: linear-gradient(135deg, rgba(239,68,68,0.1) 0%, rgba(239,68,68,0.05) 100%);
            border: 1.5px solid rgba(239,68,68,0.25);
            color: #dc2626;
            animation: slideIn 0.3s ease-out;
        }

        .input-icon-wrap {
            position: relative;
        }

        .input-icon-wrap .input-icon {
            position: absolute;
            left: 0.9rem;
            top: 50%;
            transform: translateY(-50%);
            width: 16px; height: 16px;
            opacity: 0.3;
            pointer-events: none;
        }

        .input-icon-wrap .form-input { padding-left: 2.5rem; }

        /* ===== TOAST ===== */
        .toast-container {
            position: fixed;
            bottom: 1.5rem;
            right: 1.5rem;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .toast {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
            border: 1.5px solid #e5e7eb;
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
            font-size: 0.9rem;
            font-weight: 500;
            color: #374151;
            min-width: 300px;
            max-width: 420px;
            transform: translateX(calc(100% + 2rem));
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .toast.show {
            transform: translateX(0);
            opacity: 1;
        }

        .toast.hiding {
            transform: translateX(calc(100% + 2rem));
            opacity: 0;
        }

        .toast-icon {
            width: 24px; height: 24px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .toast-icon.success { background: #ecfdf5; color: #10B981; }
        .toast-icon.error { background: #fef2f2; color: #ef4444; }
        .toast-icon.info { background: #eff6ff; color: #3b82f6; }

        .toast-icon svg { width: 14px; height: 14px; }

        .toast-close {
            margin-left: auto;
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            padding: 4px;
            line-height: 1;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .toast-close:hover {
            color: #6b7280;
            background: #f3f4f6;
        }

        @media (max-width: 968px) {
            .auth-brand-side { display: none; }
            .auth-form-side {
                width: 100%;
                min-width: unset;
                border-left: none;
                padding: 2rem 1.5rem;
            }
            .auth-form-header .mobile-logo { display: flex; }
            .auth-form-body { max-width: 100%; }
            .toast { min-width: auto; max-width: calc(100vw - 2rem); }
        }
    </style>
</head>
<body>
    <div class="toast-container" id="toastContainer"></div>

    <div class="auth-wrapper">
        <div class="auth-brand-side">
            <div class="auth-brand-inner">

                {{-- Logo --}}
                <div class="auth-brand-top">
                    <div class="brand-icon">
                        <svg fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016 2.993 2.993 0 002.25-1.016 3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z"/></svg>
                    </div>
                    <div>
                        <div class="brand-name">{{ config('app.name', 'MannaPOS') }}</div>
                        <div class="brand-tagline">Business Management Platform</div>
                    </div>
                </div>

                {{-- Hero content --}}
                <div class="auth-brand-hero">
                    <div class="hero-headline">
                        Run your business<br><span>smarter & faster</span>
                    </div>
                    <p class="hero-subtext">
                        Complete point-of-sale, inventory tracking, customer management, and real-time reports — all in one place.
                    </p>

                    <div class="feature-grid">
                        <div class="feature-chip">
                            <div class="feature-chip-icon" style="background:rgba(16,185,129,0.2);">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div class="feature-chip-text">POS Terminal</div>
                        </div>
                        <div class="feature-chip">
                            <div class="feature-chip-icon" style="background:rgba(99,102,241,0.25);">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
                            </div>
                            <div class="feature-chip-text">Inventory</div>
                        </div>
                        <div class="feature-chip">
                            <div class="feature-chip-icon" style="background:rgba(14,165,233,0.25);">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
                            </div>
                            <div class="feature-chip-text">Reports</div>
                        </div>
                        <div class="feature-chip">
                            <div class="feature-chip-icon" style="background:rgba(245,158,11,0.25);">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                            </div>
                            <div class="feature-chip-text">Customers</div>
                        </div>
                        <div class="feature-chip">
                            <div class="feature-chip-icon" style="background:rgba(239,68,68,0.2);">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.955 11.955 0 003 10c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                            </div>
                            <div class="feature-chip-text">Role Access</div>
                        </div>
                        <div class="feature-chip">
                            <div class="feature-chip-icon" style="background:rgba(16,185,129,0.15);">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                            </div>
                            <div class="feature-chip-text">Multi-Location</div>
                        </div>
                    </div>

                    <div class="stat-strip">
                        <div class="stat-item"><div class="stat-num">99%</div><div class="stat-lbl">Uptime</div></div>
                        <div class="stat-divider"></div>
                        <div class="stat-item"><div class="stat-num">Fast</div><div class="stat-lbl">Real-time</div></div>
                        <div class="stat-divider"></div>
                        <div class="stat-item"><div class="stat-num">TZS</div><div class="stat-lbl">Tanzania Ready</div></div>
                    </div>
                </div>

                <div class="auth-brand-bottom">
                    <div class="auth-brand-links">
                        <a href="#">Terms</a>
                        <a href="#">Privacy</a>
                        <a href="#">Support</a>
                    </div>
                    <div class="auth-brand-copy">
                        &copy; {{ date('Y') }} <span>{{ config('app.name', 'MannaPOS') }}</span>. All rights reserved.
                    </div>
                </div>
            </div>
        </div>

        <div class="auth-form-side">
            <div class="auth-form-header">
                <div class="mobile-logo">
                    <img src="{{ asset('icons8-dynamics-365-96.png') }}" alt="MannaPOS Logo" class="logo-image">
                    <span>{{ config('app.name', 'MannaPOS') }}</span>
                </div>
                @yield('form-header')
            </div>
            <div class="auth-form-body">
                @yield('form-content')
            </div>
        </div>
    </div>

    <script>
        function showToast(message, type, duration) {
            type = type || 'success';
            duration = duration || 4000;

            var container = document.getElementById('toastContainer');
            var toast = document.createElement('div');
            toast.className = 'toast';

            var icons = {
                success: '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>',
                error: '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>',
                info: '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
            };

            toast.innerHTML =
                '<div class="toast-icon ' + type + '">' + (icons[type] || icons.info) + '</div>' +
                '<span>' + message + '</span>' +
                '<button class="toast-close" onclick="this.parentElement.classList.add(\'hiding\');setTimeout(function(){this.parentElement.remove()}.bind(this),300)">' +
                '<svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>' +
                '</button>';

            container.appendChild(toast);

            requestAnimationFrame(function() {
                toast.classList.add('show');
            });

            setTimeout(function() {
                toast.classList.add('hiding');
                setTimeout(function() {
                    if (toast.parentElement) toast.remove();
                }, 300);
            }, duration);
        }

        // AJAX Navigation for Auth Pages
        function navigateToPage(url) {
            var formBody = document.querySelector('.auth-form-body');
            var formHeader = document.querySelector('.auth-form-header');

            // Fade out
            formBody.classList.add('fade-out');

            setTimeout(function() {
                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(function(response) {
                    if (response.redirected) {
                        window.location.href = response.url;
                        return;
                    }
                    return response.text();
                })
                .then(function(html) {
                    var parser = new DOMParser();
                    var doc = parser.parseFromString(html, 'text/html');
                    
                    var newHeader = doc.querySelector('.auth-form-header');
                    var newBody = doc.querySelector('.auth-form-body');

                    if (newHeader) {
                        formHeader.innerHTML = newHeader.innerHTML;
                    }
                    
                    if (newBody) {
                        formBody.innerHTML = newBody.innerHTML;
                    }

                    // Update URL without reload
                    window.history.pushState({}, '', url);

                    // Fade in
                    formBody.classList.remove('fade-out');
                    formBody.classList.add('fade-in');
                    
                    requestAnimationFrame(function() {
                        formBody.classList.add('show');
                    });

                    setTimeout(function() {
                        formBody.classList.remove('fade-in', 'show');
                    }, 300);

                    // Re-attach event listeners
                    attachFormListeners();
                    attachLinkListeners();
                })
                .catch(function(error) {
                    console.error('Navigation error:', error);
                    window.location.href = url;
                });
            }, 300);
        }

        function attachLinkListeners() {
            document.querySelectorAll('.auth-footer-link a, .btn-link').forEach(function(link) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    var url = this.getAttribute('href');
                    if (url && url !== '#') {
                        navigateToPage(url);
                    }
                });
            });
        }

        function attachFormListeners() {
            document.querySelectorAll('form').forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    var btn = this.querySelector('.btn-primary');
                    if (btn && !btn.classList.contains('loading')) {
                        btn.classList.add('loading');
                        btn.innerHTML = '<div class="spinner"></div><span class="btn-text">' + btn.textContent.trim() + '</span>';
                    }
                });
            });

            document.addEventListener('invalid', function(e) {
                e.target.classList.add('is-invalid');
            }, true);
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            attachLinkListeners();
            attachFormListeners();
        });

        // Handle browser back/forward buttons
        window.addEventListener('popstate', function() {
            window.location.reload();
        });
    </script>
</body>
</html>
