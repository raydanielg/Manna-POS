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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            background: linear-gradient(145deg, #0a192f 0%, #0d2d6b 30%, #0a3d8f 60%, #1565c0 100%);
        }

        .auth-brand-side::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("{{ asset('MannaPOS.png') }}");
            background-size: cover;
            background-position: center;
            z-index: 0;
        }

        @keyframes breathe {
            0% { opacity: 0.5; transform: scale(1); }
            100% { opacity: 1; transform: scale(1.05); }
        }

        .auth-brand-inner {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            height: 100%;
            padding: 2rem 2.5rem;
            justify-content: space-between;
        }

        .auth-brand-top {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .brand-icon {
            width: 64px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .brand-icon .brand-logo-image {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .brand-name {
            font-size: 1.15rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            color: #fff;
        }

        .auth-brand-bottom {
            border-top: 1px solid rgba(255,255,255,0.12);
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
            color: rgba(255,255,255,0.55);
            text-decoration: none;
            font-size: 0.78rem;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .auth-brand-links a:hover { color: #fff; }

        .auth-brand-copy {
            color: rgba(255,255,255,0.4);
            font-size: 0.72rem;
        }

        .auth-brand-copy span { color: rgba(255,255,255,0.55); }

        .auth-brand-bottom {
            border-top: 1px solid rgba(255,255,255,0.12);
            padding-top: 1.25rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.75rem;
        }

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
            border-color: #2563eb;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1), 0 1px 2px rgba(0,0,0,0.05);
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
            accent-color: #2563eb;
            cursor: pointer;
            border: 2px solid #e5e7eb;
            border-radius: 4px;
        }

        .form-check-input:checked {
            background-color: #2563eb;
            border-color: #2563eb;
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
            background: linear-gradient(135deg, #1d4ed8 0%, #1e3a8a 100%);
            color: #fff;
            box-shadow: 0 4px 15px rgba(29, 78, 216, 0.35);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(29, 78, 216, 0.45);
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
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

        .btn-link:hover { color: #2563eb; }

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
            color: #2563eb;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            transition: color 0.2s ease;
            margin-left: 0.5rem;
        }

        .auth-footer-link a:hover { color: #1d4ed8; text-decoration: underline; }

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
            background: linear-gradient(135deg, rgba(37,99,235,0.1) 0%, rgba(37,99,235,0.05) 100%);
            border: 1.5px solid rgba(37,99,235,0.25);
            color: #2563eb;
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

        .toast-icon.success { background: #eff6ff; color: #2563eb; }
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
            <div class="auth-brand-inner" style="position:relative;z-index:2;">
                <div class="auth-brand-top">
                    <div class="brand-icon" style="background:rgba(255,255,255,0.12);border-radius:12px;width:42px;height:42px;">
                        <img src="{{ asset('icons8-dynamics-365-96.png') }}" alt="MannaPOS Logo" class="brand-logo-image">
                    </div>
                    <span class="brand-name">{{ config('app.name', 'MannaPOS') }}</span>
                </div>

                <div style="flex:1;display:flex;flex-direction:column;justify-content:center;padding:2rem 0;">
                    <div style="font-size:1.6rem;font-weight:800;color:#fff;line-height:1.3;margin-bottom:.75rem;letter-spacing:-.5px;">
                        Manage your business smarter.
                    </div>
                    <div style="font-size:.88rem;color:rgba(255,255,255,.65);line-height:1.65;margin-bottom:2rem;">
                        MannaPOS gives you real-time sales tracking, smart inventory, customer management and receipts — all in one place.
                    </div>
                    <div style="width:100%;">
                        @foreach([
                            ['Sales & profit reports', 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                            ['Smart inventory management', 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                            ['Customer & supplier tools', 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                            ['Receipt & barcode printing', 'M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z'],
                        ] as [$label, $path])
                        <div style="display:flex;align-items:center;gap:.7rem;margin-bottom:.6rem;">
                            <div style="width:26px;height:26px;border-radius:6px;background:rgba(255,255,255,0.08);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#60a5fa" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $path }}"/></svg>
                            </div>
                            <span style="font-size:.8rem;color:rgba(255,255,255,.7);font-weight:500;">{{ $label }}</span>
                        </div>
                        @endforeach

                        <div style="display:inline-flex;align-items:center;gap:.4rem;margin-top:1rem;background:rgba(96,165,250,0.12);border:1px solid rgba(96,165,250,0.25);color:#60a5fa;padding:.4rem .85rem;border-radius:50px;font-size:.72rem;font-weight:600;width:fit-content;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Free 14-day trial — no credit card needed
                        </div>
                    </div>
                </div>

                <div class="auth-brand-bottom">
                    <div class="auth-brand-links">
                        <a href="/terms">Terms</a>
                        <a href="/privacy">Privacy</a>
                        <a href="#">Support</a>
                    </div>
                    <div class="auth-brand-copy">
                        &copy; {{ date('Y') }} <span>{{ config('app.name', 'MannaPOS') }}</span>
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
