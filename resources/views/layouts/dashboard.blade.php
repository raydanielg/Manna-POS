<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('page_title', 'Dashboard') — {{ config('app.name', 'MannaPOS') }}</title>
    <meta name="theme-color" content="#2563eb">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="MannaPOS">
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="icon" type="image/png" href="{{ asset('icons8-dynamics-365-100.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css"/>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>window.__USER_CURRENCY = @json(Auth::user()->currency ?? 'TZS');</script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter','ui-sans-serif','system-ui','sans-serif'] },
                    colors: {
                        brand: { 50:'#eff6ff', 100:'#dbeafe', 500:'#2563eb', 600:'#1d4ed8', 700:'#1e40af' }
                    }
                }
            }
        }
    </script>
    @yield('head_scripts')
    <style>
        body { background: #f1f4fb; }
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }

        /* ── Global Links ──────────────────────────────── */
        a {
            color: #2563eb;
            text-decoration: none;
            transition: color 0.2s ease, transform 0.2s ease;
            position: relative;
        }
        a:hover { color: #1e40af; }
        /* Animated underline for inline links (excludes nav/buttons) */
        a:not(.nav-item):not(.child-item):not(.btn):not(.sign-out-btn):not(.header-dropdown-item):not(.notif-btn):not(.user-chip):not([onclick])::after {
            content: '';
            position: absolute;
            bottom: -2px; left: 0;
            width: 0; height: 2px;
            background: linear-gradient(90deg, #2563eb, #60a5fa);
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 2px;
        }
        a:not(.nav-item):not(.child-item):not(.btn):not(.sign-out-btn):not(.header-dropdown-item):not(.notif-btn):not(.user-chip):not([onclick]):hover::after {
            width: 100%;
        }
        a:active { transform: translateY(1px); }

        /* ── Sidebar ──────────────────────────────────── */
        .sidebar {
            width: 260px; min-width: 260px; height: 100vh;
            position: fixed; top: 0; left: 0;
            background: #ffffff;
            border-right: 1px solid #eef0f6;
            display: flex; flex-direction: column;
            z-index: 40;
            box-shadow: 2px 0 12px rgba(15,23,42,0.04);
        }
        .sidebar-logo {
            padding: 1.1rem 1.25rem 1rem;
            border-bottom: 1px solid #f1f5f9;
            flex-shrink: 0;
        }
        .sidebar-content {
            flex: 1;
            padding: 0.6rem 0.85rem 0.5rem;
            overflow-y: auto;
            overflow-x: hidden;
        }
        .nav-section-label {
            font-size: 0.58rem; font-weight: 800;
            letter-spacing: 0.18em; text-transform: uppercase;
            color: #b0b8cc;
            padding: 1.1rem 0.6rem 0.35rem;
            user-select: none;
        }

        /* Plain nav item */
        .nav-item {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.55rem 0.85rem;
            font-size: 0.82rem; font-weight: 500;
            color: #4b5675;
            border-radius: 10px;
            cursor: pointer; text-decoration: none;
            transition: all 0.18s ease;
            white-space: nowrap; margin-bottom: 2px;
            position: relative;
        }
        .nav-item::before {
            content: ''; position: absolute; left: 0; top: 50%; transform: translateY(-50%);
            width: 3px; height: 0; border-radius: 0 3px 3px 0;
            background: linear-gradient(180deg, #60a5fa, #2563eb);
            transition: height 0.2s ease;
        }
        .nav-item:hover { background: #f6f7fb; color: #0f172a; }
        .nav-item:hover::before { height: 60%; }
        .nav-item svg { width: 18px; height: 18px; flex-shrink: 0; color: #94a3b8; transition: color 0.15s; }
        .nav-item:hover svg { color: #475569; }
        .nav-item.active { background: #eff6ff; color: #2563eb; font-weight: 600; }
        .nav-item.active::before { height: 70%; }
        .nav-item.active svg { color: #2563eb; }

        /* Dropdown wrapper */
        .dropdown { margin-bottom: 2px; }

        /* Dropdown toggle */
        .dropdown-toggle {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.55rem 0.85rem;
            font-size: 0.82rem; font-weight: 500;
            color: #4b5675; border-radius: 10px;
            cursor: pointer;
            transition: all 0.18s ease;
            white-space: nowrap; user-select: none;
            position: relative;
        }
        .dropdown-toggle::before {
            content: ''; position: absolute; left: 0; top: 50%; transform: translateY(-50%);
            width: 3px; height: 0; border-radius: 0 3px 3px 0;
            background: linear-gradient(180deg, #60a5fa, #2563eb);
            transition: height 0.2s ease;
        }
        .dropdown-toggle:hover { background: #f6f7fb; color: #0f172a; }
        .dropdown-toggle:hover::before { height: 60%; }
        .dropdown-toggle svg:first-child { width: 18px; height: 18px; flex-shrink: 0; color: #94a3b8; transition: color 0.15s; }
        .dropdown-toggle:hover svg:first-child { color: #475569; }
        .dropdown-toggle .chevron {
            margin-left: auto; width: 14px; height: 14px;
            color: #c4cad8; transition: transform 0.25s ease, color 0.15s; flex-shrink: 0;
        }
        .dropdown.open .dropdown-toggle { color: #0f172a; background: #f6f7fb; }
        .dropdown.open .dropdown-toggle::before { height: 60%; }
        .dropdown.open .dropdown-toggle svg:first-child { color: #475569; }
        .dropdown.open .dropdown-toggle .chevron { transform: rotate(90deg); color: #94a3b8; }

        /* Children panel */
        .dropdown-children {
            display: none;
            position: relative;
            padding: 0.35rem 0 0.6rem 2.7rem;
            margin-top: 2px;
        }
        .dropdown.open .dropdown-children { display: block; }
        .dropdown-children::before {
            content: ''; position: absolute;
            left: 1.4rem; top: 0; bottom: 0;
            width: 1.5px;
            background: linear-gradient(to bottom, #e2e8f0, transparent);
            border-radius: 2px;
        }
        .dropdown-children .child-item {
            display: flex; align-items: center;
            font-size: 0.79rem; font-weight: 500;
            color: #64748b; padding: 0.4rem 0.6rem;
            border-radius: 8px;
            transition: all 0.15s ease;
            cursor: pointer; text-decoration: none; white-space: nowrap;
        }
        .dropdown-children .child-item::before {
            content: ''; width: 5px; height: 5px; border-radius: 50%;
            background: #d1d9e6; margin-right: 0.65rem; flex-shrink: 0;
            transition: all 0.15s ease;
        }
        .dropdown-children .child-item:hover { background: #f6f7fb; color: #0f172a; }
        .dropdown-children .child-item:hover::before { background: #2563eb; }
        .dropdown-children .child-item.active { color: #2563eb; font-weight: 600; background: #eff6ff; }
        .dropdown-children .child-item.active::before { background: #2563eb; }
        .dd-group-label {
            font-size: 0.58rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em;
            color: rgba(148,163,184,0.55); padding: 0.55rem 0.6rem 0.15rem;
            pointer-events: none; user-select: none;
        }
        .soon-badge {
            margin-left: auto; font-size: 0.55rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.06em; background: #f1f5f9; color: #94a3b8;
            padding: 0.1rem 0.4rem; border-radius: 99px; flex-shrink: 0;
        }
        .child-soon { opacity: 0.6; cursor: default; pointer-events: none; }

        /* Sign out */
        .sidebar-bottom {
            margin-top: auto; padding: 0.85rem;
            border-top: 1px solid #f1f5f9; flex-shrink: 0;
        }
        .sign-out-btn {
            display: flex; align-items: center; gap: 0.65rem;
            padding: 0.55rem 0.85rem;
            font-size: 0.82rem; font-weight: 600; color: #2563eb;
            width: 100%; border-radius: 10px;
            background: none; border: none; cursor: pointer;
            transition: all 0.15s ease;
        }
        .sign-out-btn:hover { background: #eff6ff; color: #1d4ed8; }
        .sign-out-btn svg { width: 18px; height: 18px; flex-shrink: 0; }

        /* ── Main ─────────────────────────────────────── */
        .main-wrap { margin-left: 260px; min-height: 100vh; display: flex; flex-direction: column; }
        .top-header {
            background: #fff; border-bottom: 1px solid #e9edf5;
            height: 60px; display: flex; align-items: center;
            justify-content: space-between; padding: 0 2rem;
            position: sticky; top: 0; z-index: 30;
        }
        .page-title { font-size: 1.3rem; font-weight: 800; color: #0f172a; letter-spacing: -0.02em; }
        .header-right { display: flex; align-items: center; gap: 0.75rem; }
        .notif-btn {
            position: relative; width: 36px; height: 36px; border-radius: 10px;
            background: #f8fafc; border: 1px solid #e9edf5;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: background 0.15s;
        }
        .notif-btn:hover { background: #f1f5f9; }
        .notif-dot { position: absolute; top: 8px; right: 8px; width: 7px; height: 7px; border-radius: 50%; background: #2563eb; border: 1.5px solid #fff; }
        .user-chip {
            display: flex; align-items: center; gap: 0.6rem;
            padding: 0.35rem 0.75rem 0.35rem 0.4rem;
            border-radius: 12px; background: #f8fafc;
            border: 1px solid #e9edf5; cursor: pointer;
            transition: background 0.15s;
        }
        .user-chip:hover { background: #f1f5f9; }
        .user-avatar {
            width: 32px; height: 32px; border-radius: 10px;
            background: linear-gradient(135deg, #2563eb, #7c3aed);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 0.75rem; color: #fff; flex-shrink: 0;
            position: relative; overflow: hidden;
            box-shadow: 0 0 0 2px #fff, 0 0 0 4px rgba(37,99,235,0.15), 0 2px 6px rgba(37,99,235,0.12);
            transition: box-shadow 0.2s ease, transform 0.2s ease;
        }
        .user-avatar img {
            width: 100%; height: 100%;
            object-fit: cover;
        }
        .user-chip:hover .user-avatar {
            box-shadow: 0 0 0 2px #fff, 0 0 0 4px rgba(37,99,235,0.3), 0 4px 10px rgba(37,99,235,0.18);
            transform: scale(1.05);
        }
        .user-name { font-size: 0.8rem; font-weight: 600; color: #0f172a; }
        .user-role { font-size: 0.68rem; color: #94a3b8; }

        /* Header dropdowns */
        .header-dropdown { position: relative; }
        .header-dropdown-menu {
            display: none; position: absolute;
            top: calc(100% + 12px); right: 0;
            min-width: 280px; background: #fff;
            border-radius: 12px; border: 1px solid #e9edf5;
            box-shadow: 0 4px 20px rgba(15,23,42,0.08);
            z-index: 50; overflow: hidden;
        }
        .header-dropdown.open .header-dropdown-menu { display: block; animation: hdSlideDown 0.2s ease; }
        @keyframes hdSlideDown { from { opacity:0; transform:translateY(-8px); } to { opacity:1; transform:translateY(0); } }
        .header-dropdown-header { padding: 1rem 1.25rem; border-bottom: 1px solid #f1f5f9; background: #fafbff; }
        .header-dropdown-title { font-size: 0.85rem; font-weight: 700; color: #0f172a; }
        .header-dropdown-item {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.75rem 1.25rem; font-size: 0.82rem; color: #475569;
            transition: background 0.15s; cursor: pointer; text-decoration: none;
        }
        .header-dropdown-item:hover { background: #f8fafc; color: #0f172a; }
        .header-dropdown-item svg { width: 18px; height: 18px; color: #94a3b8; }
        .header-dropdown-item:hover svg { color: #475569; }
        .header-dropdown-divider { height: 1px; background: #f1f5f9; margin: 0.25rem 0; }
        .header-dropdown-footer { padding: 0.75rem 1.25rem; border-top: 1px solid #f1f5f9; background: #fafbff; }
        .notification-item { padding: 0.85rem 1.25rem; border-bottom: 1px solid #f1f5f9; transition: background 0.15s; cursor: pointer; }
        .notification-item:hover { background: #f8fafc; }
        .notification-item:last-child { border-bottom: none; }
        .notification-title { font-size: 0.82rem; font-weight: 600; color: #0f172a; margin-bottom: 0.25rem; }
        .notification-desc { font-size: 0.75rem; color: #64748b; }
        .notification-time { font-size: 0.68rem; color: #94a3b8; margin-top: 0.35rem; }
        .notification-unread { background: #f0f9ff; }
        .notification-unread .notification-title { color: #0284c7; }

        /* Content */
        .dash-content { padding: 1.75rem 2rem; flex: 1; }

        /* ── Cards & Tables ──────────────────────────── */
        .page-card { background:#fff; border-radius:14px; border:1px solid #e9edf5; overflow:hidden; }
        .card-header { display:flex; align-items:center; justify-content:space-between; padding:1rem 1.5rem; border-bottom:1px solid #f1f5f9; background:#fafbff; gap:1rem; flex-wrap:wrap; }
        .card-title { font-size:0.95rem; font-weight:700; color:#0f172a; }
        .card-body { padding:1.5rem; }
        .tbl { width:100%; border-collapse:collapse; }
        .tbl th { font-size:0.68rem; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; color:#94a3b8; padding:0.65rem 1rem; text-align:left; border-bottom:1px solid #f1f5f9; background:#fafbff; }
        .tbl td { font-size:0.82rem; color:#374151; padding:0.7rem 1rem; border-bottom:1px solid #f8fafc; vertical-align:middle; }
        .tbl tr:last-child td { border-bottom:none; }
        .tbl tr:hover td { background:#fafbff; }
        .tbl-empty { text-align:center; color:#94a3b8; font-size:0.82rem; padding:3rem 1rem !important; }

        /* ── Badges ─────────────────────────────────── */
        .badge { font-size:0.68rem; font-weight:600; padding:0.22rem 0.65rem; border-radius:9999px; display:inline-flex; align-items:center; }
        .badge-success { background:#dcfce7; color:#16a34a; }
        .badge-danger  { background:#fee2e2; color:#dc2626; }
        .badge-warning { background:#fef9c3; color:#ca8a04; }
        .badge-info    { background:#dbeafe; color:#2563eb; }
        .badge-gray    { background:#f1f5f9; color:#64748b; }

        /* ── Buttons ─────────────────────────────────── */
        .btn { display:inline-flex; align-items:center; gap:0.4rem; padding:0.5rem 1rem; border-radius:8px; font-size:0.82rem; font-weight:600; cursor:pointer; border:none; transition:all 0.15s; text-decoration:none; }
        .btn-primary { background:#2563eb; color:#fff; } .btn-primary:hover { background:#1d4ed8; }
        .btn-success { background:#10b981; color:#fff; } .btn-success:hover { background:#059669; }
        .btn-danger  { background:#ef4444; color:#fff; } .btn-danger:hover  { background:#dc2626; }
        .btn-secondary { background:#f1f5f9; color:#475569; border:1px solid #e2e8f0; } .btn-secondary:hover { background:#e2e8f0; }
        .btn-sm { padding:0.3rem 0.65rem; font-size:0.75rem; }
        .btn-icon { width:32px; height:32px; padding:0; border-radius:8px; justify-content:center; }
        .btn-edit   { background:#eff6ff; color:#2563eb; } .btn-edit:hover   { background:#dbeafe; }
        .btn-delete { background:#eff6ff; color:#2563eb; } .btn-delete:hover { background:#dbeafe; }
        .btn-view   { background:#eff6ff; color:#2563eb; } .btn-view:hover   { background:#dbeafe; }

        /* ── Staggered Animations ─────────────────────── */
        .stagger-1 { --animate-delay: 0.1s; animation-delay: 0.1s; }
        .stagger-2 { --animate-delay: 0.25s; animation-delay: 0.25s; }
        .stagger-3 { --animate-delay: 0.4s; animation-delay: 0.4s; }
        .stagger-4 { --animate-delay: 0.55s; animation-delay: 0.55s; }

        /* ── KPI Cards ─────────────────────────────── */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1.25rem;
            margin-bottom: 1.5rem;
        }
        .kpi-grid-2 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.25rem;
            margin-bottom: 2rem;
        }
        .kpi-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 1.25rem;
            border: 1px solid #eef2f6;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        .kpi-card::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 16px;
            opacity: 0;
            box-shadow: 0 12px 30px rgba(37, 99, 235, 0.08);
            transition: opacity 0.3s ease;
            pointer-events: none;
        }
        .kpi-card:hover {
            transform: translateY(-3px);
            border-color: #dbeafe;
        }
        .kpi-card:hover::after {
            opacity: 1;
        }
        .kpi-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .kpi-card:hover .kpi-icon {
            transform: scale(1.1) rotate(2deg);
        }
        .kpi-icon img {
            width: 26px;
            height: 26px;
            object-fit: contain;
        }
        .kpi-val {
            font-size: 1.4rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.1;
            letter-spacing: -0.02em;
        }
        .kpi-label {
            font-size: 0.75rem;
            color: #64748b;
            margin-top: 0.25rem;
            font-weight: 600;
            letter-spacing: 0.01em;
        }

        /* ── Dash Sections ─────────────────────────── */
        .dash-section {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #eef2f6;
            margin-bottom: 1.5rem;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.01);
            transition: box-shadow 0.3s ease;
        }
        .dash-section:hover {
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.02);
        }
        .dash-section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.1rem 1.5rem;
            cursor: pointer;
            background: #fcfdfe;
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.2s ease;
        }
        .dash-section-header:hover {
            background: #f8fafc;
        }
        .dash-section-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.01em;
        }
        .dash-section-icon {
            width: 18px;
            height: 18px;
            color: #94a3b8;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .dash-section.collapsed .dash-section-icon {
            transform: rotate(-90deg);
        }
        .dash-section-content {
            padding: 1.5rem;
            transition: all 0.3s ease;
        }
        .dash-section.collapsed .dash-section-content {
            display: none;
        }

        /* ── Charts ────────────────────────────────── */
        .charts-row {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 1.5rem;
        }
        .chart-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #eef2f6;
            padding: 1.5rem;
            transition: border-color 0.3s ease;
        }
        .chart-card:hover {
            border-color: #e2e8f0;
        }
        .chart-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 1.25rem;
            letter-spacing: -0.01em;
        }
        .chart-container {
            position: relative;
            height: 280px;
            width: 100%;
        }

        /* ── Tables Row & Table Card ──────────────── */
        .tables-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }
        .table-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #eef2f6;
            overflow: hidden;
        }
        .table-head {
            padding: 1.1rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            background: #fcfdfe;
        }
        .table-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.01em;
        }
        .tbl-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* ── Empty States ─────────────────────────── */
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 1rem;
            text-align: center;
        }
        .empty-icon {
            width: 48px;
            height: 48px;
            color: #cbd5e1;
            margin-bottom: 0.75rem;
        }
        .empty-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: #64748b;
        }
        .empty-desc {
            font-size: 0.75rem;
            color: #94a3b8;
            margin-top: 0.25rem;
            max-width: 240px;
            line-height: 1.4;
        }

        /* ── Screen adaptations ───────────────────── */
        @media (max-width: 1200px) {
            .charts-row { grid-template-columns: 1fr; }
            .tables-row { grid-template-columns: 1fr; }
        }

        /* ── Search bar ─────────────────────────────── */
        .search-wrap { position:relative; }
        .search-wrap input { padding:0.5rem 1rem 0.5rem 2.4rem; border-radius:8px; border:1px solid #e2e8f0; font-size:0.82rem; color:#374151; outline:none; background:#f8fafc; width:240px; transition:border-color 0.15s; }
        .search-wrap input:focus { border-color:#2563eb; background:#fff; }
        .search-wrap svg { position:absolute; left:0.7rem; top:50%; transform:translateY(-50%); width:16px; height:16px; color:#94a3b8; pointer-events:none; }

        /* ── Filters row ─────────────────────────────── */
        .filters-row { display:flex; align-items:center; gap:0.75rem; flex-wrap:wrap; }

        /* ── Modal ──────────────────────────────────── */
        .modal-overlay { display:none; position:fixed; inset:0; background:rgba(15,23,42,0.45); z-index:100; align-items:center; justify-content:center; backdrop-filter:blur(2px); }
        .modal-overlay.open { display:flex; }
        .modal { background:#fff; border-radius:16px; width:100%; max-width:520px; box-shadow:0 20px 60px rgba(15,23,42,0.18); overflow:hidden; animation:modalIn 0.22s ease; }
        .modal-lg { max-width:700px; } .modal-xl { max-width:920px; }
        @keyframes modalIn { from { opacity:0; transform:translateY(-16px) scale(0.97); } to { opacity:1; transform:none; } }
        .modal-header { display:flex; align-items:center; justify-content:space-between; padding:1.25rem 1.5rem; border-bottom:1px solid #f1f5f9; }
        .modal-title { font-size:1rem; font-weight:700; color:#0f172a; }
        .modal-close { width:30px; height:30px; border-radius:8px; background:#f1f5f9; border:none; cursor:pointer; display:flex; align-items:center; justify-content:center; color:#64748b; transition:background 0.15s; flex-shrink:0; }
        .modal-close:hover { background:#e2e8f0; color:#0f172a; }
        .modal-body { padding:1.5rem; max-height:70vh; overflow-y:auto; }
        .modal-footer { padding:1rem 1.5rem; border-top:1px solid #f1f5f9; display:flex; align-items:center; justify-content:flex-end; gap:0.75rem; background:#fafbff; }

        /* ── Forms ──────────────────────────────────── */
        .form-group { margin-bottom:1rem; }
        .form-label { display:block; font-size:0.78rem; font-weight:600; color:#374151; margin-bottom:0.35rem; }
        .form-control { width:100%; padding:0.55rem 0.85rem; border:1px solid #e2e8f0; border-radius:8px; font-size:0.82rem; color:#0f172a; outline:none; transition:border-color 0.15s; background:#fff; box-sizing:border-box; }
        .form-control:focus { border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,0.08); }
        .form-control.is-invalid { border-color:#ef4444; }
        .invalid-feedback { font-size:0.72rem; color:#ef4444; margin-top:0.25rem; display:none; }
        .form-row { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
        select.form-control { cursor:pointer; appearance:auto; }
        textarea.form-control { resize:vertical; min-height:80px; }

        /* ── Toast ──────────────────────────────────── */
        #toast-container { position:fixed; bottom:1.5rem; right:1.5rem; z-index:200; display:flex; flex-direction:column; gap:0.5rem; pointer-events:none; }
        .toast { display:flex; align-items:center; gap:0.75rem; padding:0.85rem 1.25rem; border-radius:12px; font-size:0.82rem; font-weight:500; min-width:260px; max-width:380px; box-shadow:0 8px 24px rgba(15,23,42,0.12); animation:toastIn 0.25s ease; border:1px solid transparent; pointer-events:all; }
        @keyframes toastIn { from { opacity:0; transform:translateX(20px); } to { opacity:1; transform:none; } }
        .toast-success { background:#f0fdf4; color:#15803d; border-color:#bbf7d0; }
        .toast-error   { background:#fff1f2; color:#be123c; border-color:#fecdd3; }
        .toast-info    { background:#eff6ff; color:#1e40af; border-color:#bfdbfe; }
        .toast svg { width:18px; height:18px; flex-shrink:0; }

        /* ── Confirm dialog ─────────────────────────── */
        .confirm-overlay { display:none; position:fixed; inset:0; background:rgba(15,23,42,0.5); z-index:150; align-items:center; justify-content:center; backdrop-filter:blur(2px); }
        .confirm-overlay.open { display:flex; }
        .confirm-box { background:#fff; border-radius:16px; padding:2rem; max-width:380px; width:90%; box-shadow:0 20px 60px rgba(15,23,42,0.2); text-align:center; animation:modalIn 0.2s ease; }
        .confirm-icon { width:56px; height:56px; border-radius:50%; background:#fff1f2; display:flex; align-items:center; justify-content:center; margin:0 auto 1rem; }
        .confirm-title { font-size:1rem; font-weight:700; color:#0f172a; margin-bottom:0.5rem; }
        .confirm-desc  { font-size:0.82rem; color:#64748b; margin-bottom:1.5rem; }
        .confirm-actions { display:flex; gap:0.75rem; justify-content:center; }

        /* ── Responsive ─────────────────────────────── */
        @media (max-width:1280px) { .sidebar { width:240px; min-width:240px; } .main-wrap { margin-left:240px; } }

        @media (max-width:768px) {
            /* Sidebar as mobile drawer */
            .sidebar { transform:translateX(-100%); transition:transform 0.3s ease; z-index:9999; }
            .sidebar.open { transform:translateX(0); }
            .main-wrap { margin-left:0; }
            .form-row { grid-template-columns:1fr; }

            /* Mobile sidebar overlay backdrop */
            .sidebar-backdrop {
                display: none; position: fixed; inset: 0;
                background: rgba(15,23,42,0.45); backdrop-filter: blur(3px);
                z-index: 9998; opacity: 0; transition: opacity 0.3s ease;
            }
            .sidebar-backdrop.show { display: block; opacity: 1; }

            /* Header */
            .top-header { padding: 0 1rem; height: 54px; }
            .page-title { font-size: 1rem; }
            .user-chip { gap: 0.4rem; padding: 0.25rem; }

            /* Content */
            .dash-content { padding: 1rem 1rem 2rem; }

            /* KPI Cards - force 2 columns on mobile */
            .kpi-grid { grid-template-columns: repeat(2, 1fr); gap: 0.75rem; margin-bottom: 1rem; }
            .kpi-grid-2 { grid-template-columns: repeat(2, 1fr); gap: 0.75rem; }
            .kpi-card { padding: 0.85rem; gap: 0.6rem; }
            .kpi-icon { width: 36px; height: 36px; }
            .kpi-icon img { width: 18px; height: 18px; }
            .kpi-val { font-size: 0.95rem; }
            .kpi-label { font-size: 0.65rem; }

            /* Charts - stack vertically */
            .charts-row { grid-template-columns: 1fr; gap: 1rem; }
            .chart-card { padding: 1rem; }
            .chart-title { font-size: 0.85rem; }
            .chart-container { height: 220px; }

            /* Tables - stack & horizontal scroll */
            .tables-row { grid-template-columns: 1fr; gap: 1rem; margin-top: 1rem !important; }
            .table-card { padding: 1rem; }
            .table-head { padding-bottom: 0.75rem; margin-bottom: 0.75rem; }
            .table-title { font-size: 0.85rem; }
            .tbl-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }
            .tbl { min-width: 500px; }
            .tbl th, .tbl td { padding: 0.6rem 0.75rem; font-size: 0.75rem; }

            /* Sections */
            .dash-section { padding: 1rem; }
            .dash-section-header { padding: 0.75rem 1rem; font-size: 0.85rem; }

            /* Empty states */
            .empty-state { padding: 2rem 1rem; }
            .empty-icon { width: 36px; height: 36px; }
            .empty-title { font-size: 0.85rem; }
            .empty-desc { font-size: 0.72rem; }

            /* Modals */
            .modal { max-width: calc(100vw - 2rem); margin: 1rem; }

            /* Search bar */
            .search-bar { min-width: auto; width: 100%; max-width: 200px; }
        }

        /* Extra small screens */
        @media (max-width:480px) {
            .kpi-grid { grid-template-columns: repeat(2, 1fr); gap: 0.6rem; }
            .kpi-grid-2 { grid-template-columns: repeat(2, 1fr); gap: 0.6rem; }
            .kpi-card { padding: 0.7rem; }
            .chart-container { height: 180px; }
            .top-header { padding: 0 0.75rem; }
        }

        /* ── Sidebar collapse ───────────────────── */
        .sidebar { transition: width 0.25s ease; }
        .sidebar.collapsed { width: 64px; min-width: 64px; }
        .sidebar.collapsed .nav-section-label { display: none; }
        .sidebar.collapsed .nav-label,
        .sidebar.collapsed .logo-text,
        .sidebar.collapsed .sign-out-text { display: none; }
        .sidebar.collapsed .chevron { display: none !important; }
        .sidebar.collapsed .dropdown.open .dropdown-children { display: none !important; }
        .sidebar.collapsed .nav-item,
        .sidebar.collapsed .dropdown-toggle { justify-content: center; padding: 0.6rem; gap: 0; }
        .sidebar.collapsed .sign-out-btn { justify-content: center; gap: 0; }
        .sidebar.collapsed .nav-item[data-tip]:hover::after,
        .sidebar.collapsed .dropdown-toggle[data-tip]:hover::after {
            content: attr(data-tip);
            position: fixed; left: 72px;
            background: #1e293b; color: #fff;
            padding: 5px 14px; border-radius: 8px;
            font-size: 0.75rem; font-weight: 600;
            white-space: nowrap; z-index: 999;
            pointer-events: none;
            box-shadow: 0 4px 16px rgba(0,0,0,0.25);
        }
        .main-wrap { transition: margin-left 0.25s ease; }
        .main-wrap.sidebar-collapsed { margin-left: 64px !important; }

        /* ── Calculator ──────────────────────────── */
        .calc-overlay { display:none; position:fixed; inset:0; background:rgba(15,23,42,0.5); z-index:200; align-items:center; justify-content:center; backdrop-filter:blur(3px); }
        .calc-overlay.open { display:flex; animation:none; }
        .calc-box { background:#fff; border-radius:20px; overflow:hidden; width:290px; box-shadow:0 24px 60px rgba(15,23,42,0.22); animation:modalIn 0.22s ease; }
        .calc-display { background:linear-gradient(135deg,#1e293b,#0f172a); color:#fff; padding:1.25rem 1.25rem 1rem; text-align:right; }
        .calc-expr { font-size:0.72rem; color:#94a3b8; min-height:18px; word-break:break-all; margin-bottom:4px; }
        .calc-val { font-size:2.4rem; font-weight:800; letter-spacing:-1px; word-break:break-all; line-height:1; }
        .calc-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:1px; background:#e2e8f0; }
        .cbtn { padding:1.05rem 0.5rem; font-size:1rem; font-weight:600; border:none; background:#fff; cursor:pointer; transition:background 0.12s; user-select:none; }
        .cbtn:hover { background:#f8fafc; }
        .cbtn:active { background:#f1f5f9; }
        .cbtn.op  { color:#2563eb; background:#eff6ff; } .cbtn.op:hover  { background:#dbeafe; }
        .cbtn.eq  { color:#fff;    background:#2563eb; } .cbtn.eq:hover  { background:#1d4ed8; }
        .cbtn.clr { color:#ef4444; background:#fff1f2; } .cbtn.clr:hover { background:#fee2e2; }
        .cbtn.zero { grid-column:span 2; }

        /* ── Profit popup ────────────────────────── */
        .profit-popup { display:none; position:absolute; top:calc(100% + 12px); right:0; min-width:240px; background:#fff; border-radius:14px; border:1px solid #e9edf5; box-shadow:0 8px 30px rgba(15,23,42,0.12); z-index:60; overflow:hidden; animation:hdSlideDown 0.2s ease; }
        .profit-popup.open { display:block; }

        /* ── Report Print / A4 PDF Styles ───────────── */
        .report-page { max-width: 100%; }
        .report-header-bar {
            display: flex; align-items: center; justify-content: space-between;
            padding: 1.25rem 1.75rem; border-radius: 16px; margin-bottom: 1.5rem; color: #fff;
            background: linear-gradient(135deg, rgba(15,23,42,0.95) 0%, rgba(30,58,138,0.92) 50%, rgba(37,99,235,0.85) 100%);
            backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.08); box-shadow: 0 12px 32px rgba(15,23,42,0.18), inset 0 1px 0 rgba(255,255,255,0.1);
            position: relative; overflow: hidden;
        }
        .report-header-bar::before {
            content: ''; position: absolute; inset: 0;
            background: radial-gradient(circle at 80% 20%, rgba(59,130,246,0.25) 0%, transparent 60%);
            pointer-events: none;
        }
        .report-header-bar h1 { font-size: 1.35rem; font-weight: 800; margin: 0; position: relative; letter-spacing: -0.03em; }
        .report-header-bar p { font-size: 0.82rem; opacity: 0.82; margin: 0.3rem 0 0; position: relative; }
        .report-actions { display: flex; gap: 0.5rem; flex-wrap: wrap; position: relative; }
        .report-actions .btn {
            border-radius: 10px; padding: 0.55rem 1rem; font-size: 0.8rem; font-weight: 700;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: all 0.2s ease;
            border: 1px solid rgba(255,255,255,0.12);
        }
        .report-actions .btn:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(0,0,0,0.18); }
        .report-filters {
            background: rgba(255,255,255,0.7); border-radius: 16px;
            border: 1px solid rgba(226,232,240,0.6); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);
            padding: 1.25rem; margin-bottom: 1.5rem; display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;
            box-shadow: 0 4px 16px rgba(15,23,42,0.04);
        }
        .report-filters label { font-size: 0.68rem; font-weight: 800; text-transform: uppercase; color: #475569; letter-spacing: 0.08em; margin-bottom: 0.4rem; display: block; }
        .report-filters input, .report-filters select {
            border: 1px solid rgba(226,232,240,0.8); border-radius: 12px; padding: 0.6rem 0.9rem;
            font-size: 0.85rem; color: #0f172a; background: rgba(248,250,252,0.8); outline: none;
            transition: all 0.2s ease; box-shadow: inset 0 1px 2px rgba(0,0,0,0.02);
        }
        .report-filters input:focus, .report-filters select:focus {
            border-color: #3b82f6; background: #fff; box-shadow: 0 0 0 3px rgba(59,130,246,0.1), inset 0 1px 2px rgba(0,0,0,0.02);
        }
        .report-summary {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;
        }
        .report-summary-card {
            background: rgba(255,255,255,0.85); border-radius: 16px; padding: 1.35rem 1.5rem;
            border: 1px solid rgba(226,232,240,0.5); position: relative; overflow: hidden;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);
            box-shadow: 0 2px 8px rgba(15,23,42,0.03);
        }
        .report-summary-card:hover {
            transform: translateY(-4px); box-shadow: 0 12px 32px rgba(15,23,42,0.08);
            background: rgba(255,255,255,0.95); border-color: rgba(59,130,246,0.2);
        }
        .report-summary-card .rsc-label { font-size: 0.72rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: #64748b; margin-bottom: 0.4rem; }
        .report-summary-card .rsc-value { font-size: 1.55rem; font-weight: 800; color: #0f172a; line-height: 1.15; letter-spacing: -0.02em; }
        .report-summary-card .rsc-bar { position: absolute; left: 0; top: 0; bottom: 0; width: 4px; border-radius: 16px 0 0 16px; }
        .report-summary-card .rsc-bar.green { background: linear-gradient(180deg, #4ade80, #22c55e); }
        .report-summary-card .rsc-bar.blue { background: linear-gradient(180deg, #60a5fa, #3b82f6); }
        .report-summary-card .rsc-bar.amber { background: linear-gradient(180deg, #fbbf24, #f59e0b); }
        .report-summary-card .rsc-bar.red { background: linear-gradient(180deg, #f87171, #ef4444); }
        .report-summary-card .rsc-bar.violet { background: linear-gradient(180deg, #a78bfa, #8b5cf6); }
        .report-summary-card .rsc-bar.emerald { background: linear-gradient(180deg, #34d399, #10b981); }

        .report-chart-row {
            display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;
        }
        .report-chart-card {
            background: rgba(255,255,255,0.85); border-radius: 16px; border: 1px solid rgba(226,232,240,0.5);
            overflow: hidden; backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);
            box-shadow: 0 2px 12px rgba(15,23,42,0.03); transition: all 0.3s ease;
        }
        .report-chart-card:hover { box-shadow: 0 8px 28px rgba(15,23,42,0.06); border-color: rgba(59,130,246,0.15); }
        .report-chart-card .rch-head {
            padding: 1.1rem 1.5rem; border-bottom: 1px solid rgba(241,245,249,0.6); background: rgba(252,253,254,0.6);
            font-size: 0.92rem; font-weight: 800; color: #0f172a; letter-spacing: -0.01em;
        }
        .report-chart-card .rch-body { padding: 1.25rem; height: 300px; position: relative; }

        .report-table-wrap {
            background: rgba(255,255,255,0.85); border-radius: 16px; border: 1px solid rgba(226,232,240,0.5);
            overflow: hidden; margin-bottom: 1.5rem; backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);
            box-shadow: 0 2px 12px rgba(15,23,42,0.03); transition: all 0.3s ease;
        }
        .report-table-wrap:hover { box-shadow: 0 8px 28px rgba(15,23,42,0.05); border-color: rgba(59,130,246,0.15); }
        .report-table-wrap .rtw-head {
            padding: 1.1rem 1.5rem; border-bottom: 1px solid rgba(241,245,249,0.6); background: rgba(252,253,254,0.6);
            display: flex; align-items: center; justify-content: space-between;
        }
        .report-table-wrap .rtw-head .rtw-title { font-size: 0.92rem; font-weight: 800; color: #0f172a; letter-spacing: -0.01em; }
        .report-table-wrap .rtw-body { padding: 0; }
        .report-table { width: 100%; border-collapse: collapse; }
        .report-table thead th {
            font-size: 0.68rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em;
            color: #64748b; padding: 0.85rem 1.25rem; text-align: left; border-bottom: 1px solid rgba(241,245,249,0.6); background: rgba(250,251,255,0.6);
        }
        .report-table tbody td {
            font-size: 0.82rem; color: #374151; padding: 0.85rem 1.25rem; border-bottom: 1px solid rgba(248,250,252,0.6); vertical-align: middle;
        }
        .report-table tbody tr:last-child td { border-bottom: none; }
        .report-table tbody tr:hover td { background: rgba(250,251,255,0.8); }
        .report-table .text-right { text-align: right; }
        .report-table .text-center { text-align: center; }

        /* ── Print / A4 PDF Styles ──────────────────── */
        @media print {
            @page { size: A4; margin: 12mm; }
            body { background: #fff !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .sidebar, .header, .report-actions, .report-filters, .no-print { display: none !important; }
            .main-content { margin-left: 0 !important; padding: 0 !important; }
            .dash-content { padding: 0 !important; }
            .report-page { max-width: 100%; }
            .report-header-bar {
                background: #1e3a8a !important; -webkit-print-color-adjust: exact; print-color-adjust: exact;
                border-radius: 0 !important; margin-bottom: 1rem !important;
            }
            .report-summary-card, .report-chart-card, .report-table-wrap {
                box-shadow: none !important; border: 1px solid #e2e8f0 !important;
                break-inside: avoid; page-break-inside: avoid;
            }
            .report-chart-card .rch-body { height: 220px !important; }
            .report-table tbody tr { break-inside: avoid; }
            .report-table thead { display: table-header-group; }
            .report-summary { grid-template-columns: 1fr 1fr !important; gap: 0.75rem !important; }
            .report-chart-row { grid-template-columns: 1fr !important; gap: 1rem !important; }
            h1, h2, h3 { break-after: avoid; }
            .page-break { page-break-before: always; }
        }

        /* ── PDF.js Preview Modal ──────────────────── */
        .pdf-preview-overlay {
            position: fixed; inset: 0; background: rgba(15,23,42,0.75); backdrop-filter: blur(8px);
            z-index: 9999; display: none; align-items: center; justify-content: center; padding: 1rem;
        }
        .pdf-preview-overlay.open { display: flex; }
        .pdf-preview-box {
            background: linear-gradient(145deg, #f1f5f9, #e2e8f0); border-radius: 20px;
            width: 100%; max-width: 960px; height: 92vh;
            display: flex; flex-direction: column; overflow: hidden;
            box-shadow: 0 32px 64px rgba(0,0,0,0.3), inset 0 1px 0 rgba(255,255,255,0.6);
            border: 1px solid rgba(255,255,255,0.3);
        }
        .pdf-preview-head {
            padding: 1rem 1.5rem; border-bottom: 1px solid rgba(0,0,0,0.05); display: flex;
            align-items: center; justify-content: space-between; background: rgba(255,255,255,0.6); backdrop-filter: blur(12px);
        }
        .pdf-preview-head h3 { font-size: 0.95rem; font-weight: 800; color: #0f172a; letter-spacing: -0.02em; }
        .pdf-preview-head .pdf-actions { display: flex; gap: 0.5rem; align-items: center; }
        .pdf-preview-head button {
            background: rgba(255,255,255,0.8); border: 1px solid rgba(0,0,0,0.06); border-radius: 10px;
            padding: 0.5rem 1rem; font-size: 0.78rem; font-weight: 700; color: #475569; cursor: pointer;
            transition: all 0.2s ease; display: inline-flex; align-items: center; gap: 0.35rem;
        }
        .pdf-preview-head button:hover { background: #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.08); transform: translateY(-1px); }
        .pdf-preview-head button.primary { background: linear-gradient(135deg, #2563eb, #1d4ed8); color: #fff; border: none; }
        .pdf-preview-head button.primary:hover { background: linear-gradient(135deg, #1d4ed8, #1e40af); box-shadow: 0 4px 16px rgba(37,99,235,0.35); }
        .pdf-preview-body {
            flex: 1; overflow: auto; padding: 1.5rem; display: flex; justify-content: center; align-items: flex-start;
            background: repeating-linear-gradient(45deg, #e2e8f0 0, #e2e8f0 1px, #cbd5e1 1px, #cbd5e1 2px);
            background-size: 20px 20px;
        }
        .pdf-preview-frame {
            width: 210mm; min-height: 297mm; background: #fff;
            box-shadow: 0 8px 30px rgba(0,0,0,0.18), 0 0 0 1px rgba(0,0,0,0.05);
            border-radius: 0; padding: 12mm; transform-origin: top center;
            transition: transform 0.3s ease;
        }

        @yield('page_styles')
    </style>
</head>
<body class="font-sans antialiased">

@php
    $userCurrency = Auth::user()->currency ?? 'TZS';
    $isHome          = request()->routeIs('dashboard');
    $isOnlineOrders  = request()->routeIs('dashboard.online-orders');
    $isUserMgmt      = request()->routeIs('dashboard.user-management.*');
    $isStaff         = request()->routeIs('dashboard.staff.*');
    $isPlanMgmt      = request()->routeIs('dashboard.plan-management.*');
    $isContacts      = request()->routeIs('dashboard.contacts.*');
    $isInventory     = request()->routeIs('dashboard.inventory.*');
    $isPurchases     = request()->routeIs('dashboard.purchases.*');
    $isSell          = request()->routeIs('dashboard.sell.*');
    $isStockTrans    = request()->routeIs('dashboard.stock-transfer.*');
    $isStockAdj      = request()->routeIs('dashboard.stock-adjustment.*');
    $isInventoryMgmt = $isStockTrans || $isStockAdj;
    $isExpenses      = request()->routeIs('dashboard.expenses.*');
    $isBanking       = request()->routeIs('dashboard.banking.*');
    $isMicrofinance  = request()->routeIs('dashboard.microfinance.*');
    $isFinance       = $isExpenses || $isBanking || $isMicrofinance;
    $isSmsCampaigns  = request()->routeIs('dashboard.sms-campaigns.*');
    $isCrm           = request()->routeIs('dashboard.crm.*') || $isContacts || $isSmsCampaigns;
    $isFileCabinet   = request()->routeIs('dashboard.file-cabinet.*');
    $isPayroll       = request()->routeIs('dashboard.payroll.*');
    $isManufacturing = request()->routeIs('dashboard.manufacturing.*');
    $isMgmt          = $isUserMgmt || $isStaff || $isPlanMgmt || $isPayroll || $isManufacturing;
    $isReports       = request()->routeIs('dashboard.reports.*');
    $isNotifTpl      = request()->routeIs('dashboard.notification-templates');
    $isSettings      = request()->routeIs('dashboard.settings.*');
    $isProfile       = request()->routeIs('dashboard.profile');
    $isFeedback      = request()->routeIs('dashboard.feedback.*') || request()->routeIs('dashboard.feedback');
    $isAdminFeedback = request()->routeIs('dashboard.feedback.admin.*');

    function sidebarChildActive($route) {
        return request()->routeIs($route) ? 'active' : '';
    }
@endphp

{{-- ═══════════════════════════  SIDEBAR  ═══════════════════════════ --}}
<aside class="sidebar" id="sidebar">

    {{-- Logo --}}
    <div class="sidebar-logo">
        <div class="flex items-center gap-3">
            <img src="{{ asset('icons8-dynamics-365-96.png') }}" alt="Logo" class="w-8 h-8 object-contain">
            <div class="min-w-0 logo-text">
                <div class="text-[1rem] font-extrabold text-slate-900 leading-none tracking-tight">{{ config('app.name','MannaPOS') }}</div>
                <div class="text-[0.6rem] font-bold tracking-[0.14em] uppercase text-slate-400 mt-0.5">Admin Panel</div>
            </div>
        </div>
    </div>

    {{-- Nav --}}
    <div class="sidebar-content">

        {{-- ── MAIN ──────────────────────────────────────── --}}
        <div class="nav-section-label">Main</div>

        @can('dashboard.view')
        <a href="{{ route('dashboard') }}" class="nav-item {{ $isHome ? 'active' : '' }}" data-tip="Dashboard">
            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12l-2 0l9-9l9 9l-2 0"/><path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-7"/><path d="M10 12h4v4h-4z"/></svg>
            <span class="nav-label">Dashboard</span>
        </a>
        @endcan

        {{-- ── CRM ──────────────────────────────────────── --}}
        <div class="nav-section-label">CRM</div>

        <div class="dropdown {{ $isCrm ? 'open' : '' }}" id="dropdown-crm">
            <div class="dropdown-toggle" onclick="toggleDropdown('dropdown-crm')" data-tip="CRM">
                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg>
                <span class="nav-label">CRM</span>
                <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6l6 6"/></svg>
            </div>
            <div class="dropdown-children">
                <div class="dd-group-label">Customers</div>
                @can('contacts.view')<a href="{{ route('dashboard.contacts.customers') }}" class="child-item {{ request()->routeIs('dashboard.contacts.customers') ? 'active' : '' }}">Customers</a>@endcan
                @can('contacts.view')<a href="{{ route('dashboard.contacts.customer-groups') }}" class="child-item {{ request()->routeIs('dashboard.contacts.customer-groups') ? 'active' : '' }}">Customer Groups</a>@endcan
                @can('contacts.create')<a href="{{ route('dashboard.contacts.import-contacts') }}" class="child-item {{ request()->routeIs('dashboard.contacts.import-contacts') ? 'active' : '' }}">Import Contacts</a>@endcan
                <div class="dd-group-label">Sales & Loyalty</div>
                @can('sales.view')<a href="{{ route('dashboard.sell.all-sales') }}?payment_status=credit" class="child-item">Credit Sales</a>@endcan
                <a href="#" class="child-item child-soon">Customer Payments <span class="soon-badge">Soon</span></a>
                <a href="#" class="child-item child-soon">Loyalty Points <span class="soon-badge">Soon</span></a>
                <div class="dd-group-label">Communication</div>
                @can('crm.view')<a href="{{ route('dashboard.crm.activities') }}" class="child-item {{ request()->routeIs('dashboard.crm.activities') ? 'active' : '' }}">Activities</a>@endcan
                @can('crm.view')<a href="{{ route('dashboard.crm.dashboard') }}" class="child-item {{ request()->routeIs('dashboard.crm.dashboard') ? 'active' : '' }}">CRM Dashboard</a>@endcan
                @can('sms.view')<a href="{{ route('dashboard.sms-campaigns') }}" class="child-item {{ $isSmsCampaigns ? 'active' : '' }}">SMS Campaigns</a>@endcan
                <a href="#" class="child-item child-soon">Customer Reports <span class="soon-badge">Soon</span></a>
            </div>
        </div>

        {{-- ── SALES ────────────────────────────────────── --}}
        <div class="nav-section-label">Sales</div>

        @can('sales.view')
        <div class="dropdown {{ $isSell || $isOnlineOrders ? 'open' : '' }}" id="dropdown-sell">
            <div class="dropdown-toggle" onclick="toggleDropdown('dropdown-sell')" data-tip="Sales">
                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span class="nav-label">Sales</span>
                @php $pendingCount = collect(json_decode(Auth::user()->store_settings ?? '{}', true)['orders'] ?? [])->where('status', 'pending')->count(); @endphp
                @if($pendingCount > 0)<span style="margin-left:auto;margin-right:0.3rem;background:#ef4444;color:#fff;font-size:0.6rem;font-weight:700;padding:0.1rem 0.4rem;border-radius:99px;">{{ $pendingCount }}</span>@endif
                <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6l6 6"/></svg>
            </div>
            <div class="dropdown-children">
                <div class="dd-group-label">Transactions</div>
                @can('sales.view')<a href="{{ route('dashboard.sell.all-sales') }}" class="child-item {{ request()->routeIs('dashboard.sell.all-sales') ? 'active' : '' }}">All Sales</a>@endcan
                @can('sales.create')<a href="{{ route('dashboard.sell.add-sale') }}" class="child-item {{ request()->routeIs('dashboard.sell.add-sale') ? 'active' : '' }}">Add Sale</a>@endcan
                @can('sales.view')<a href="{{ route('dashboard.sell.list-sell-return') }}" class="child-item {{ request()->routeIs('dashboard.sell.list-sell-return') ? 'active' : '' }}">Sell Returns</a>@endcan
                @can('sales.view')<a href="{{ route('dashboard.sell.discounts') }}" class="child-item {{ request()->routeIs('dashboard.sell.discounts') ? 'active' : '' }}">Discounts</a>@endcan
                @can('sales.view')<a href="{{ route('dashboard.sell.shipments') }}" class="child-item {{ request()->routeIs('dashboard.sell.shipments') ? 'active' : '' }}">Shipments</a>@endcan
                @can('sales.create')<a href="{{ route('dashboard.sell.import-sales') }}" class="child-item {{ request()->routeIs('dashboard.sell.import-sales') ? 'active' : '' }}">Import Sales</a>@endcan
                <div class="dd-group-label">POS</div>
                @can('sales.create')<a href="{{ route('dashboard.sell.pos') }}" class="child-item {{ request()->routeIs('dashboard.sell.pos') ? 'active' : '' }}">POS Terminal</a>@endcan
                @can('sales.view')<a href="{{ route('dashboard.sell.list-pos') }}" class="child-item {{ request()->routeIs('dashboard.sell.list-pos') ? 'active' : '' }}">List POS</a>@endcan
                <div class="dd-group-label">Drafts & Quotations</div>
                @can('sales.create')<a href="{{ route('dashboard.sell.add-draft') }}" class="child-item {{ request()->routeIs('dashboard.sell.add-draft') ? 'active' : '' }}">Add Draft</a>@endcan
                @can('sales.view')<a href="{{ route('dashboard.sell.list-drafts') }}" class="child-item {{ request()->routeIs('dashboard.sell.list-drafts') ? 'active' : '' }}">List Drafts</a>@endcan
                @can('sales.create')<a href="{{ route('dashboard.sell.add-quotation') }}" class="child-item {{ request()->routeIs('dashboard.sell.add-quotation') ? 'active' : '' }}">Add Quotation</a>@endcan
                @can('sales.view')<a href="{{ route('dashboard.sell.list-quotations') }}" class="child-item {{ request()->routeIs('dashboard.sell.list-quotations') ? 'active' : '' }}">List Quotations</a>@endcan
                <div class="dd-group-label">Online</div>
                <a href="{{ route('dashboard.online-orders') }}" class="child-item {{ $isOnlineOrders ? 'active' : '' }}">Online Orders @if($pendingCount > 0)<span style="background:#ef4444;color:#fff;font-size:0.6rem;font-weight:700;padding:0.1rem 0.35rem;border-radius:99px;margin-left:4px;">{{ $pendingCount }}</span>@endif</a>
            </div>
        </div>
        @endcan

        {{-- ── PRODUCTS ─────────────────────────────────── --}}
        <div class="nav-section-label">Products</div>

        @can('products.view')
        <div class="dropdown {{ $isInventory ? 'open' : '' }}" id="dropdown-products">
            <div class="dropdown-toggle" onclick="toggleDropdown('dropdown-products')" data-tip="Products">
                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3l8 4.5v9l-8 4.5l-8-4.5v-9l8-4.5"/><path d="M12 12l8-4.5"/><path d="M8.2 9.8l7.6-4.6"/><path d="M12 12v9"/><path d="M12 12l-8-4.5"/></svg>
                <span class="nav-label">Products</span>
                <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6l6 6"/></svg>
            </div>
            <div class="dropdown-children">
                <div class="dd-group-label">Management</div>
                @can('products.view')<a href="{{ route('dashboard.inventory.list-products') }}" class="child-item {{ request()->routeIs('dashboard.inventory.list-products') ? 'active' : '' }}">List Products</a>@endcan
                @can('products.create')<a href="{{ route('dashboard.inventory.add-product') }}" class="child-item {{ request()->routeIs('dashboard.inventory.add-product') ? 'active' : '' }}">Add Product</a>@endcan
                @can('products.edit')<a href="{{ route('dashboard.inventory.update-price') }}" class="child-item {{ request()->routeIs('dashboard.inventory.update-price') ? 'active' : '' }}">Update Price</a>@endcan
                @can('products.view')<a href="{{ route('dashboard.inventory.print-labels') }}" class="child-item {{ request()->routeIs('dashboard.inventory.print-labels') ? 'active' : '' }}">Print Labels</a>@endcan
                @can('products.view')<a href="{{ route('dashboard.inventory.variations') }}" class="child-item {{ request()->routeIs('dashboard.inventory.variations') ? 'active' : '' }}">Variations</a>@endcan
                @can('products.view')<a href="{{ route('dashboard.inventory.selling-price-group') }}" class="child-item {{ request()->routeIs('dashboard.inventory.selling-price-group') ? 'active' : '' }}">Selling Price Group</a>@endcan
                <div class="dd-group-label">Import</div>
                @can('products.create')<a href="{{ route('dashboard.inventory.import-products') }}" class="child-item {{ request()->routeIs('dashboard.inventory.import-products') ? 'active' : '' }}">Import Products</a>@endcan
                @can('products.create')<a href="{{ route('dashboard.inventory.import-opening-stock') }}" class="child-item {{ request()->routeIs('dashboard.inventory.import-opening-stock') ? 'active' : '' }}">Import Opening Stock</a>@endcan
                <div class="dd-group-label">Classification</div>
                @can('products.view')<a href="{{ route('dashboard.inventory.units') }}" class="child-item {{ request()->routeIs('dashboard.inventory.units') ? 'active' : '' }}">Units</a>@endcan
                @can('products.view')<a href="{{ route('dashboard.inventory.product-categories') }}" class="child-item {{ request()->routeIs('dashboard.inventory.product-categories') ? 'active' : '' }}">Categories</a>@endcan
                <a href="#" class="child-item child-soon">Subcategories <span class="soon-badge">Soon</span></a>
                @can('products.view')<a href="{{ route('dashboard.inventory.brands') }}" class="child-item {{ request()->routeIs('dashboard.inventory.brands') ? 'active' : '' }}">Brands</a>@endcan
                <a href="#" class="child-item child-soon">Sub Brands <span class="soon-badge">Soon</span></a>
                @can('products.view')<a href="{{ route('dashboard.inventory.warranties') }}" class="child-item {{ request()->routeIs('dashboard.inventory.warranties') ? 'active' : '' }}">Warranties</a>@endcan
                <div class="dd-group-label">Expiry</div>
                <a href="{{ route('dashboard.reports.expiry-report') }}" class="child-item {{ request()->routeIs('dashboard.reports.expiry-report') ? 'active' : '' }}">MFG & EXP Register</a>
                <a href="{{ route('dashboard.reports.expiry-report') }}?alert=1" class="child-item">MFG & EXP Alert</a>
            </div>
        </div>
        @endcan

        {{-- ── PURCHASES ────────────────────────────────── --}}
        <div class="nav-section-label">Purchases</div>

        @can('purchases.view')
        <div class="dropdown {{ $isPurchases ? 'open' : '' }}" id="dropdown-purchases">
            <div class="dropdown-toggle" onclick="toggleDropdown('dropdown-purchases')" data-tip="Purchases">
                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12"/><path d="M16 11l-4 4l-4-4"/><path d="M3 12a9 9 0 0 0 18 0"/></svg>
                <span class="nav-label">Purchases</span>
                <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6l6 6"/></svg>
            </div>
            <div class="dropdown-children">
                @can('purchases.view')<a href="{{ route('dashboard.purchases.list-purchases') }}" class="child-item {{ request()->routeIs('dashboard.purchases.list-purchases') ? 'active' : '' }}">List Purchases</a>@endcan
                @can('purchases.create')<a href="{{ route('dashboard.purchases.add-purchase') }}" class="child-item {{ request()->routeIs('dashboard.purchases.add-purchase') ? 'active' : '' }}">Add Purchase</a>@endcan
                @can('purchases.view')<a href="{{ route('dashboard.purchases.list-purchase-return') }}" class="child-item {{ request()->routeIs('dashboard.purchases.list-purchase-return') ? 'active' : '' }}">List Purchase Return</a>@endcan
                @can('contacts.view')<a href="{{ route('dashboard.contacts.suppliers') }}" class="child-item {{ request()->routeIs('dashboard.contacts.suppliers') ? 'active' : '' }}">Suppliers</a>@endcan
            </div>
        </div>
        @endcan

        {{-- ── INVENTORY ────────────────────────────────── --}}
        <div class="nav-section-label">Inventory</div>

        <div class="dropdown {{ $isInventoryMgmt ? 'open' : '' }}" id="dropdown-inventory">
            <div class="dropdown-toggle" onclick="toggleDropdown('dropdown-inventory')" data-tip="Inventory">
                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h7"/><path d="M15 15l2 2 4-4"/></svg>
                <span class="nav-label">Inventory</span>
                <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6l6 6"/></svg>
            </div>
            <div class="dropdown-children">
                <div class="dd-group-label">Stock Levels</div>
                @can('products.view')<a href="{{ route('dashboard.reports.inventory-report') }}" class="child-item {{ request()->routeIs('dashboard.reports.inventory-report') ? 'active' : '' }}">Stock Levels</a>@endcan
                <a href="#" class="child-item child-soon">Count by Category <span class="soon-badge">Soon</span></a>
                <a href="#" class="child-item child-soon">Count by Location <span class="soon-badge">Soon</span></a>
                <a href="#" class="child-item child-soon">Record Physical Qty <span class="soon-badge">Soon</span></a>
                <div class="dd-group-label">Adjustment</div>
                @can('stock_adjustments.view')<a href="{{ route('dashboard.stock-adjustment.list-stock-adjustment') }}" class="child-item {{ request()->routeIs('dashboard.stock-adjustment.list-stock-adjustment') ? 'active' : '' }}">List Adjustments</a>@endcan
                @can('stock_adjustments.view')<a href="{{ route('dashboard.stock-adjustment.add-stock-adjustment') }}" class="child-item {{ request()->routeIs('dashboard.stock-adjustment.add-stock-adjustment') ? 'active' : '' }}">Add Adjustment</a>@endcan
                <div class="dd-group-label">Transfer</div>
                @can('stock_transfers.view')<a href="{{ route('dashboard.stock-transfer.list-stock-transfer') }}" class="child-item {{ request()->routeIs('dashboard.stock-transfer.list-stock-transfer') ? 'active' : '' }}">List Transfers</a>@endcan
                @can('stock_transfers.view')<a href="{{ route('dashboard.stock-transfer.add-stock-transfer') }}" class="child-item {{ request()->routeIs('dashboard.stock-transfer.add-stock-transfer') ? 'active' : '' }}">Add Transfer</a>@endcan
            </div>
        </div>

        {{-- ── FINANCE ──────────────────────────────────── --}}
        <div class="nav-section-label">Finance</div>

        @can('expenses.view')
        <div class="dropdown {{ $isExpenses ? 'open' : '' }}" id="dropdown-expenses">
            <div class="dropdown-toggle" onclick="toggleDropdown('dropdown-expenses')" data-tip="Expenses">
                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 21v-16a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16l-3-2l-2 2l-2-2l-2 2l-2-2l-3 2"/><path d="M14.8 8a2 2 0 0 0-1.8-1h-2a2 2 0 1 0 0 4h2a2 2 0 1 1 0 4h-2a2 2 0 0 1-1.8-1"/><path d="M12 6v10"/></svg>
                <span class="nav-label">Expenses</span>
                <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6l6 6"/></svg>
            </div>
            <div class="dropdown-children">
                @can('expenses.view')<a href="{{ route('dashboard.expenses.list-expenses') }}" class="child-item {{ request()->routeIs('dashboard.expenses.list-expenses') ? 'active' : '' }}">List Expenses</a>@endcan
                @can('expenses.create')<a href="{{ route('dashboard.expenses.add-expense') }}" class="child-item {{ request()->routeIs('dashboard.expenses.add-expense') ? 'active' : '' }}">Add Expense</a>@endcan
                @can('expenses.view')<a href="{{ route('dashboard.expenses.expense-categories') }}" class="child-item {{ request()->routeIs('dashboard.expenses.expense-categories') ? 'active' : '' }}">Expense Categories</a>@endcan
            </div>
        </div>
        @endcan

        @can('banking.view')
        <div class="dropdown {{ $isBanking ? 'open' : '' }}" id="dropdown-banking">
            <div class="dropdown-toggle" onclick="toggleDropdown('dropdown-banking')" data-tip="Banking">
                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l8-4l8 4v14"/><path d="M5 11h14"/><path d="M10 11v10"/><path d="M14 11v10"/></svg>
                <span class="nav-label">Banking</span>
                <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6l6 6"/></svg>
            </div>
            <div class="dropdown-children">
                <a href="{{ route('dashboard.banking') }}" class="child-item {{ request()->routeIs('dashboard.banking') ? 'active' : '' }}">Dashboard</a>
                <a href="{{ route('dashboard.banking.accounts') }}" class="child-item {{ request()->routeIs('dashboard.banking.accounts') ? 'active' : '' }}">Accounts</a>
                <a href="{{ route('dashboard.banking.transactions') }}" class="child-item {{ request()->routeIs('dashboard.banking.transactions') ? 'active' : '' }}">Transactions</a>
            </div>
        </div>
        @endcan

        @can('microfinance.view')
        <div class="dropdown {{ $isMicrofinance ? 'open' : '' }}" id="dropdown-microfinance">
            <div class="dropdown-toggle" onclick="toggleDropdown('dropdown-microfinance')" data-tip="Loans">
                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="nav-label">Microfinance</span>
                <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6l6 6"/></svg>
            </div>
            <div class="dropdown-children">
                <a href="{{ route('dashboard.microfinance') }}" class="child-item {{ request()->routeIs('dashboard.microfinance') ? 'active' : '' }}">Dashboard</a>
                <a href="{{ route('dashboard.microfinance.loans') }}" class="child-item {{ request()->routeIs('dashboard.microfinance.loans') ? 'active' : '' }}">Loans</a>
                <a href="{{ route('dashboard.microfinance.products') }}" class="child-item {{ request()->routeIs('dashboard.microfinance.products') ? 'active' : '' }}">Loan Products</a>
                <a href="{{ route('dashboard.microfinance.guarantors') }}" class="child-item {{ request()->routeIs('dashboard.microfinance.guarantors') ? 'active' : '' }}">Guarantors</a>
            </div>
        </div>
        @endcan

        {{-- ── MANAGEMENT ───────────────────────────────── --}}
        <div class="nav-section-label">Management</div>

        <div class="dropdown {{ $isMgmt ? 'open' : '' }}" id="dropdown-mgmt">
            <div class="dropdown-toggle" onclick="toggleDropdown('dropdown-mgmt')" data-tip="Management">
                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0-8 0"/><path d="M3 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/><path d="M21 21v-2a4 4 0 0 0-3-3.85"/></svg>
                <span class="nav-label">Management</span>
                <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6l6 6"/></svg>
            </div>
            <div class="dropdown-children">
                <div class="dd-group-label">Users</div>
                @can('users.view')<a href="{{ route('dashboard.user-management.users') }}" class="child-item {{ request()->routeIs('dashboard.user-management.users') ? 'active' : '' }}">Users</a>@endcan
                @can('roles.view')<a href="{{ route('dashboard.user-management.roles') }}" class="child-item {{ request()->routeIs('dashboard.user-management.roles') ? 'active' : '' }}">Roles & Permissions</a>@endcan
                <a href="{{ route('dashboard.user-management.sales-commission-agents') }}" class="child-item {{ request()->routeIs('dashboard.user-management.sales-commission-agents') ? 'active' : '' }}">Commission Agents</a>
                @if(Auth::user()->isOwner())
                <a href="{{ route('dashboard.staff.index') }}" class="child-item {{ $isStaff ? 'active' : '' }}">Staff Management</a>
                <div class="dd-group-label">Subscription</div>
                <a href="{{ route('dashboard.plan-management.plans') }}" class="child-item {{ request()->routeIs('dashboard.plan-management.plans') ? 'active' : '' }}">Subscription Plans</a>
                <a href="{{ route('dashboard.plan-management.subscriptions') }}" class="child-item {{ request()->routeIs('dashboard.plan-management.subscriptions') ? 'active' : '' }}">Subscriptions</a>
                @endif
                <div class="dd-group-label">HR & Operations</div>
                @can('payroll.view')<a href="{{ route('dashboard.payroll') }}" class="child-item {{ $isPayroll ? 'active' : '' }}">Payroll</a>@endcan
                <a href="#" class="child-item child-soon">KPI <span class="soon-badge">Soon</span></a>
                <a href="#" class="child-item child-soon">Job Descriptions <span class="soon-badge">Soon</span></a>
                @can('manufacturing.view')
                <div class="dd-group-label">Production</div>
                <a href="{{ route('dashboard.manufacturing') }}" class="child-item {{ request()->routeIs('dashboard.manufacturing') ? 'active' : '' }}">Manufacturing</a>
                <a href="{{ route('dashboard.manufacturing.recipes') }}" class="child-item {{ request()->routeIs('dashboard.manufacturing.recipes') ? 'active' : '' }}">Recipes & BOM</a>
                <a href="{{ route('dashboard.manufacturing.production') }}" class="child-item {{ request()->routeIs('dashboard.manufacturing.production') ? 'active' : '' }}">Production Runs</a>
                @endcan
                <div class="dd-group-label">Files & Docs</div>
                @can('files.view')<a href="{{ route('dashboard.file-cabinet') }}" class="child-item {{ $isFileCabinet ? 'active' : '' }}">File Cabinet</a>@endcan
                @canany(['approvals.view','approvals.approve'])<a href="{{ route('dashboard.approvals.index') }}" class="child-item {{ request()->routeIs('dashboard.approvals.*') ? 'active' : '' }}">Approval Requests</a>@endcanany
            </div>
        </div>

        {{-- ── ANALYTICS ────────────────────────────────── --}}
        <div class="nav-section-label">Analytics</div>

        @can('reports.view')
        <div class="dropdown {{ $isReports ? 'open' : '' }}" id="dropdown-reports">
            <div class="dropdown-toggle" onclick="toggleDropdown('dropdown-reports')" data-tip="Reports">
                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/><path d="M5 17v-2a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/><path d="M13 17v-2a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/><path d="M3 17v-4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v4"/><path d="M3 7a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M12 3v4"/></svg>
                <span class="nav-label">Reports</span>
                <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6l6 6"/></svg>
            </div>
            <div class="dropdown-children">
                <div class="dd-group-label">Financial</div>
                <a href="{{ route('dashboard.reports.profit-loss-report') }}" class="child-item {{ request()->routeIs('dashboard.reports.profit-loss-report') ? 'active' : '' }}">Profit / Loss</a>
                <a href="{{ route('dashboard.reports.sales-report') }}" class="child-item {{ request()->routeIs('dashboard.reports.sales-report') ? 'active' : '' }}">Sales Report</a>
                <a href="{{ route('dashboard.reports.purchase-report') }}" class="child-item {{ request()->routeIs('dashboard.reports.purchase-report') ? 'active' : '' }}">Purchase Report</a>
                <a href="{{ route('dashboard.reports.expense-report') }}" class="child-item {{ request()->routeIs('dashboard.reports.expense-report') ? 'active' : '' }}">Expense Report</a>
                <div class="dd-group-label">Inventory</div>
                <a href="{{ route('dashboard.reports.inventory-report') }}" class="child-item {{ request()->routeIs('dashboard.reports.inventory-report') ? 'active' : '' }}">Stock Report</a>
                <a href="{{ route('dashboard.reports.expiry-report') }}" class="child-item {{ request()->routeIs('dashboard.reports.expiry-report') ? 'active' : '' }}">Expiry Report</a>
                <a href="{{ route('dashboard.reports.product-trends-report') }}" class="child-item {{ request()->routeIs('dashboard.reports.product-trends-report') ? 'active' : '' }}">Product Trends</a>
                <div class="dd-group-label">Suppliers</div>
                <a href="{{ route('dashboard.reports.suppliers-report') }}" class="child-item {{ request()->routeIs('dashboard.reports.suppliers-report') ? 'active' : '' }}">Suppliers Report</a>
                <a href="{{ route('dashboard.reports.supplier-price-comparison') }}" class="child-item {{ request()->routeIs('dashboard.reports.supplier-price-comparison') ? 'active' : '' }}">Price Comparison</a>
            </div>
        </div>
        @endcan

        {{-- ── SYSTEM ───────────────────────────────────── --}}
        <div class="nav-section-label">System</div>

        <a href="{{ route('dashboard.calendar') }}" class="nav-item {{ request()->routeIs('dashboard.calendar') ? 'active' : '' }}" data-tip="Calendar">
            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span class="nav-label">Calendar & Tasks</span>
        </a>

        <a href="{{ route('dashboard.notification-templates') }}" class="nav-item {{ $isNotifTpl ? 'active' : '' }}" data-tip="Notifications">
            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-14a2 2 0 0 1-2-2v-10z"/><path d="M3 7l9 6l9-6"/></svg>
            <span class="nav-label">Notification Templates</span>
        </a>

        <a href="/subscription/plans" class="nav-item {{ request()->is('subscription/plans') ? 'active' : '' }}" data-tip="My Plan">
            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
            <span class="nav-label">My Subscription</span>
        </a>

        <a href="{{ route('dashboard.profile') }}" class="nav-item {{ $isProfile ? 'active' : '' }}" data-tip="Profile">
            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4z"/><path d="M4 20c0-2.21 3.58-4 8-4s8 1.79 8 4"/></svg>
            <span class="nav-label">My Profile</span>
        </a>

        @can('settings.view')
        <div class="dropdown {{ $isSettings ? 'open' : '' }}" id="dropdown-settings">
            <div class="dropdown-toggle" onclick="toggleDropdown('dropdown-settings')" data-tip="Settings">
                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 0 0-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 0 0-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 0 0-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 0 0-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 0 0 1.066-2.573c-.94-1.543.826-3.31 2.37-2.37c1 .608 2.296.07 2.572-1.065z"/><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0-6 0"/></svg>
                <span class="nav-label">Settings</span>
                <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6l6 6"/></svg>
            </div>
            <div class="dropdown-children">
                @can('settings.edit')<a href="{{ route('dashboard.settings.general') }}" class="child-item {{ request()->routeIs('dashboard.settings.general') ? 'active' : '' }}">Business Settings</a>@endcan
                @can('settings.edit')<a href="{{ route('dashboard.settings.business-location') }}" class="child-item {{ request()->routeIs('dashboard.settings.business-location') ? 'active' : '' }}">Business Locations</a>@endcan
                @can('settings.edit')<a href="{{ route('dashboard.settings.invoice-settings') }}" class="child-item {{ request()->routeIs('dashboard.settings.invoice-settings') ? 'active' : '' }}">Invoice Settings</a>@endcan
                @can('settings.edit')<a href="{{ route('dashboard.settings.barcode-settings') }}" class="child-item {{ request()->routeIs('dashboard.settings.barcode-settings') ? 'active' : '' }}">Barcode Settings</a>@endcan
                @can('settings.view')<a href="{{ route('dashboard.settings.tax-rates') }}" class="child-item {{ request()->routeIs('dashboard.settings.tax-rates') ? 'active' : '' }}">Tax Rates</a>@endcan
            </div>
        </div>
        @endcan

        {{-- ── SUPPORT ──────────────────────────────────── --}}
        <div class="nav-section-label">Support</div>

        <div class="dropdown {{ $isFeedback ? 'open' : '' }}" id="dropdown-support">
            <div class="dropdown-toggle" onclick="toggleDropdown('dropdown-support')" data-tip="Support">
                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m0 0V12m0 0h8.485"/><circle cx="12" cy="12" r="3"/></svg>
                <span class="nav-label">Support & Feedback</span>
                <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6l6 6"/></svg>
            </div>
            <div class="dropdown-children">
                <a href="{{ route('dashboard.feedback.index') }}" class="child-item {{ $isFeedback && !$isAdminFeedback ? 'active' : '' }}">My Feedback</a>
                <a href="{{ route('dashboard.feedback.admin.index') }}" class="child-item {{ $isAdminFeedback ? 'active' : '' }}">Support Inbox</a>
            </div>
        </div>

    </div>{{-- /sidebar-content --}}

    {{-- Sign out --}}
    <div class="sidebar-bottom">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sign-out-btn">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                <span class="sign-out-text">Sign Out</span>
            </button>
        </form>
    </div>
</aside>

{{-- Mobile Sidebar Backdrop --}}
<div id="sidebarBackdrop" class="sidebar-backdrop" onclick="closeSidebar()"></div>

{{-- ═══════════════════════════  MAIN  ═══════════════════════════ --}}
<div class="main-wrap" id="main-wrap">

    {{-- Top Header --}}
    <header class="top-header">
        <div class="flex items-center gap-3">
            {{-- Mobile Sidebar Toggle --}}
            <button class="md:hidden p-1.5 rounded-lg hover:bg-slate-100" onclick="toggleMobileSidebar()">
                <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            
            {{-- Desktop Sidebar Collapse --}}
            <button class="hidden lg:block p-1.5 rounded-lg hover:bg-slate-100" onclick="toggleSidebar()" title="Toggle Sidebar">
                <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z"/><path d="M15 4v16"/><path d="M10 10l-2 2l2 2"/></svg>
            </button>
            
            <h1 class="page-title">@yield('page_title', 'Dashboard')</h1>
        </div>
        
        <div class="header-right">
            {{-- Quick Actions Dropdown --}}
            <div class="header-dropdown" id="hdr-quick">
                <div class="notif-btn" onclick="toggleHeaderDropdown('hdr-quick')">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"/><path d="M9 12h6"/><path d="M12 9v6"/></svg>
                </div>
                <div class="header-dropdown-menu" style="min-width:200px;">
                    <a href="{{ route('dashboard.calendar') }}" class="header-dropdown-item">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <div><div style="font-weight:600;">Calendar & Tasks</div><div style="font-size:.68rem;color:#94a3b8;margin-top:1px;">View your schedule</div></div>
                    </a>
                    <a href="{{ route('dashboard.calendar') }}?add=1" class="header-dropdown-item" id="quickAddTodo">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        <div><div style="font-weight:600;">Quick Add Task</div><div style="font-size:.68rem;color:#94a3b8;margin-top:1px;">Create a new to-do item</div></div>
                    </a>
                    <div class="header-dropdown-divider"></div>
                    <a href="{{ route('dashboard.crm.activities') }}" class="header-dropdown-item">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h-11a3 3 0 0 1 -3-3v-11a3 3 0 0 1 3-3h11a3 3 0 0 1 3 3v11a3 3 0 0 1-3 3z"/><path d="M9 9l2 2l4-4"/></svg>
                        <div><div style="font-weight:600;">CRM Activities</div><div style="font-size:.68rem;color:#94a3b8;margin-top:1px;">Track customer interactions</div></div>
                    </a>
                </div>
            </div>

            {{-- Calendar Button --}}
            <a href="{{ route('dashboard.calendar') }}" class="notif-btn" title="Calendar & Tasks">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </a>

            {{-- Calculator Button --}}
            <button class="notif-btn" title="Calculator (Alt+C)" onclick="openCalc()">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 3m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z"/><path d="M8 7m0 1a1 1 0 0 1 1 -1h6a1 1 0 0 1 1 1v1a1 1 0 0 1 -1 1h-6a1 1 0 0 1 -1 -1z"/><path d="M8 14l0 .01"/><path d="M12 14l0 .01"/><path d="M16 14l0 .01"/><path d="M8 17l0 .01"/><path d="M12 17l0 .01"/><path d="M16 17l0 .01"/></svg>
            </button>

            {{-- POS Button --}}
            <a href="{{ route('dashboard.sell.pos') }}" class="btn btn-primary" style="gap:0.5rem;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"/><path d="M14 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"/><path d="M4 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"/><path d="M14 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"/></svg>
                <span class="hidden md:inline">POS</span>
            </a>

            <div class="header-dropdown" id="hdr-profit" style="position:relative;">
                <button class="notif-btn" title="Today's Profit" onclick="toggleHeaderDropdown('hdr-profit'); loadProfit()">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"/><path d="M3 6m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z"/><path d="M18 12l.01 0"/><path d="M6 12l.01 0"/></svg>
                </button>
                <div class="profit-popup header-dropdown-menu" id="profit-popup">
                    <div class="header-dropdown-header"><div class="header-dropdown-title">Today's Summary</div></div>
                    <div id="profit-content" style="padding:1rem 1.25rem;">
                        <div style="text-align:center;color:#94a3b8;font-size:0.8rem;">Loading...</div>
                    </div>
                </div>
            </div>

            {{-- Date Display --}}
            <span class="hidden lg:inline-block text-xs text-slate-400 font-medium px-3 py-1.5 rounded-lg bg-slate-100">{{ now()->format('m/d/Y') }}</span>

            {{-- Notifications --}}
            <div class="header-dropdown" id="hdr-notif">
                <div class="notif-btn" onclick="toggleHeaderDropdown('hdr-notif')">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6"/><path d="M9 17v1a3 3 0 0 0 6 0v-1"/></svg>
                    <span class="notif-dot"></span>
                </div>
                <div class="header-dropdown-menu">
                    <div class="header-dropdown-header"><div class="header-dropdown-title">Notifications</div></div>
                    <div class="notification-item notification-unread">
                        <div class="notification-title">New Sale — #INV-001</div>
                        <div class="notification-desc">Sale of TSh 1,250 completed</div>
                        <div class="notification-time">2 minutes ago</div>
                    </div>
                    <div class="notification-item notification-unread">
                        <div class="notification-title">Low Stock Alert</div>
                        <div class="notification-desc">Wireless Mouse is running low (5 units)</div>
                        <div class="notification-time">15 minutes ago</div>
                    </div>
                    <div class="notification-item">
                        <div class="notification-title">New Customer Registered</div>
                        <div class="notification-desc">John Doe registered as a new customer</div>
                        <div class="notification-time">1 hour ago</div>
                    </div>
                    <div class="header-dropdown-footer">
                        <a href="#" class="header-dropdown-item" style="justify-content:center; padding:0.5rem;">View All Notifications</a>
                    </div>
                </div>
            </div>

            {{-- Profile --}}
            <div class="header-dropdown" id="hdr-profile">
                <div class="user-chip" onclick="toggleHeaderDropdown('hdr-profile')">
                    <div class="user-avatar">
                        @php $__av = Auth::user()->avatar; @endphp
                        @if($__av)
                            <img src="{{ asset('storage/avatars/'.$__av) }}" alt="Avatar" style="width:100%;height:100%;border-radius:8px;object-fit:cover;">
                        @else
                            {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                        @endif
                    </div>
                    <div class="hidden md:flex flex-col items-end">
                        <span class="text-xs font-semibold text-slate-700">{{ Auth::user()->name ?? 'Admin' }}</span>
                        <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded">{{ $userCurrency }}</span>
                    </div>
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </div>
                <div class="header-dropdown-menu" style="min-width:200px;">
                    <div class="header-dropdown-header" style="padding:0.75rem 1rem;">
                        <div style="font-size:0.75rem;color:#64748b;">Signed in as</div>
                        <div style="font-size:0.8rem;font-weight:700;color:#0f172a;">{{ Auth::user()->name ?? 'Admin' }}</div>
                    </div>
                    <a href="{{ route('dashboard.profile') }}" class="header-dropdown-item" style="padding:0.6rem 1rem;">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/><path d="M12 10m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"/><path d="M6.168 18.849a4 4 0 0 1 3.832 -2.849h4a4 4 0 0 1 3.834 2.855"/></svg>
                        Profile
                    </a>
                    <a href="{{ route('dashboard.settings.general') }}" class="header-dropdown-item" style="padding:0.6rem 1rem;">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 0 0-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 0 0-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 0 0-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 0 0-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 0 0 1.066-2.573c-.94-1.543.826-3.31 2.37-2.37c1 .608 2.296.07 2.572-1.065z"/><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0-6 0"/></svg>
                        Settings
                    </a>
                    <div class="header-dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="header-dropdown-item" style="width:100%; border:none; background:none; color:#2563eb; cursor:pointer; padding:0.6rem 1rem;">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2"/><path d="M9 12h12l-3 -3"/><path d="M18 15l3 -3"/></svg>
                            Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    {{-- Trial / Subscription warning banner --}}
    @php
        $__sub = auth()->user()->activeSubscription();
        $__daysLeft = null;
        if ($__sub && $__sub->expires_at) {
            $__daysLeft = (int) now()->diffInDays($__sub->expires_at, false);
        }
    @endphp
    @if($__sub && $__sub->status === 'trial' && $__daysLeft !== null)
    <div style="background:linear-gradient(90deg,#0f2748,#1a365d);color:#fff;padding:.65rem 1.5rem;display:flex;align-items:center;justify-content:space-between;font-size:.82rem;gap:1rem;flex-wrap:wrap;position:relative;overflow:hidden;">
        <div style="position:absolute;top:-50%;right:-10%;width:200px;height:200px;background:radial-gradient(circle,rgba(16,185,129,.12) 0%,transparent 70%);border-radius:50%;pointer-events:none;"></div>
        <span style="position:relative;z-index:1;display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;">
            @if($__daysLeft > 3)
                <span style="background:rgba(16,185,129,.2);color:#34d399;padding:.18rem .55rem;border-radius:6px;font-weight:800;font-size:.68rem;letter-spacing:.03em;">FREE TRIAL</span>
                <span><strong>{{ $__daysLeft }}</strong> days remaining in your free trial</span>
            @else
                <span style="background:rgba(239,68,68,.25);color:#fca5a5;padding:.18rem .55rem;border-radius:6px;font-weight:800;font-size:.68rem;letter-spacing:.03em;display:flex;align-items:center;gap:.25rem;">
                    <svg width="14" height="14" fill="none" stroke="#fca5a5" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    TRIAL EXPIRING
                </span>
                <span>Only <strong>{{ $__daysLeft }}</strong> day{{ $__daysLeft != 1 ? 's' : '' }} left!</span>
            @endif
        </span>
        <a href="/subscription/plans" style="position:relative;z-index:1;background:linear-gradient(135deg,#10b981,#059669);color:#fff;padding:.35rem 1rem;border-radius:8px;font-weight:700;font-size:.78rem;text-decoration:none;white-space:nowrap;display:inline-flex;align-items:center;gap:.35rem;transition:all .2s;box-shadow:0 2px 8px rgba(16,185,129,.3);"
           onmouseover="this.style.background='linear-gradient(135deg,#059669,#047857)';this.style.transform='translateY(-1px)'"
           onmouseout="this.style.background='linear-gradient(135deg,#10b981,#059669)';this.style.transform=''">
            Upgrade Now
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-7-7l7 7-7 7"/></svg>
        </a>
    </div>
    @elseif(!$__sub)
    <div style="background:linear-gradient(90deg,#dc2626,#b91c1c);color:#fff;padding:.65rem 1.5rem;display:flex;align-items:center;justify-content:space-between;font-size:.82rem;gap:1rem;flex-wrap:wrap;position:relative;overflow:hidden;">
        <div style="position:absolute;top:-50%;left:-10%;width:200px;height:200px;background:radial-gradient(circle,rgba(255,255,255,.08) 0%,transparent 70%);border-radius:50%;pointer-events:none;"></div>
        <span style="position:relative;z-index:1;display:flex;align-items:center;gap:.5rem;">
            <svg width="18" height="18" fill="none" stroke="#fca5a5" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>Your subscription has expired. Some features may be limited.</span>
        </span>
        <a href="/subscription/plans" style="position:relative;z-index:1;background:#fff;color:#dc2626;padding:.35rem 1rem;border-radius:8px;font-weight:700;font-size:.78rem;text-decoration:none;display:inline-flex;align-items:center;gap:.35rem;transition:all .2s;"
           onmouseover="this.style.background='#fee2e2';this.style.transform='translateY(-1px)'"
           onmouseout="this.style.background='#fff';this.style.transform=''">
            Renew Now
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-7-7l7 7-7 7"/></svg>
        </a>
    </div>
    @endif

    {{-- Page Content --}}
    @yield('content')

</div>{{-- /main-wrap --}}

{{-- ══ CALCULATOR ═══════════════════════════════════════ --}}
<div class="calc-overlay" id="calc-overlay" onclick="if(event.target===this)closeCalc()">
    <div class="calc-box">
        <div class="calc-display">
            <div class="calc-expr" id="calc-expr"></div>
            <div class="calc-val" id="calc-val">0</div>
        </div>
        <div class="calc-grid">
            <button class="cbtn clr" onclick="calcAction('AC')">AC</button>
            <button class="cbtn clr" onclick="calcAction('±')">±</button>
            <button class="cbtn clr" onclick="calcAction('%')">%</button>
            <button class="cbtn op"  onclick="calcAction('÷')">÷</button>

            <button class="cbtn" onclick="calcAction('7')">7</button>
            <button class="cbtn" onclick="calcAction('8')">8</button>
            <button class="cbtn" onclick="calcAction('9')">9</button>
            <button class="cbtn op" onclick="calcAction('×')">×</button>

            <button class="cbtn" onclick="calcAction('4')">4</button>
            <button class="cbtn" onclick="calcAction('5')">5</button>
            <button class="cbtn" onclick="calcAction('6')">6</button>
            <button class="cbtn op" onclick="calcAction('−')">−</button>

            <button class="cbtn" onclick="calcAction('1')">1</button>
            <button class="cbtn" onclick="calcAction('2')">2</button>
            <button class="cbtn" onclick="calcAction('3')">3</button>
            <button class="cbtn op" onclick="calcAction('+')">+</button>

            <button class="cbtn zero" onclick="calcAction('0')">0</button>
            <button class="cbtn" onclick="calcAction('.')">.</button>
            <button class="cbtn eq" onclick="calcAction('=')">＝</button>
        </div>
    </div>
</div>

{{-- ══ TOAST CONTAINER ══════════════════════════════════ --}}
<div id="toast-container"></div>

{{-- ══ CONFIRM DIALOG ═══════════════════════════════════ --}}
<div class="confirm-overlay" id="confirm-overlay">
    <div class="confirm-box">
        <div class="confirm-icon">
            <svg width="28" height="28" fill="none" stroke="#2563eb" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
        </div>
        <div class="confirm-title" id="confirm-title">Delete Record?</div>
        <div class="confirm-desc"  id="confirm-desc">This action cannot be undone. Are you sure?</div>
        <div class="confirm-actions">
            <button class="btn btn-secondary" onclick="closeConfirm()">Cancel</button>
            <button class="btn btn-danger"    id="confirm-ok-btn">Delete</button>
        </div>
    </div>
</div>

<script>
// ── Mobile Sidebar ────────────────────────────────────
function toggleMobileSidebar() {
    const sb = document.getElementById('sidebar');
    const bd = document.getElementById('sidebarBackdrop');
    const isOpen = sb.classList.toggle('open');
    bd.classList.toggle('show', isOpen);
}
function closeSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('sidebarBackdrop').classList.remove('show');
}

// ── Sidebar collapse ──────────────────────────────────
function toggleSidebar() {
    const sb = document.getElementById('sidebar');
    const mw = document.getElementById('main-wrap');
    // Wrap text nodes on first call
    if (!window._navWrapped) {
        document.querySelectorAll('.nav-item, .dropdown-toggle, .sign-out-btn').forEach(el => {
            Array.from(el.childNodes).forEach(n => {
                if (n.nodeType === 3 && n.textContent.trim()) {
                    const sp = document.createElement('span');
                    sp.className = 'nav-label';
                    sp.textContent = n.textContent;
                    n.replaceWith(sp);
                }
            });
        });
        window._navWrapped = true;
    }
    const col = sb.classList.toggle('collapsed');
    mw.classList.toggle('sidebar-collapsed', col);
    localStorage.setItem('sb-collapsed', col ? '1' : '0');
}
// Restore collapse state
(function() {
    if (localStorage.getItem('sb-collapsed') === '1') {
        document.getElementById('sidebar').classList.add('collapsed');
        document.getElementById('main-wrap').classList.add('sidebar-collapsed');
    }
})();

// ── Sidebar dropdown ─────────────────────────────────────
function toggleDropdown(id) {
    document.getElementById(id).classList.toggle('open');
}

// ── Header dropdown ──────────────────────────────────────
function toggleHeaderDropdown(id) {
    const el = document.getElementById(id);
    document.querySelectorAll('.header-dropdown.open').forEach(d => { if (d !== el) d.classList.remove('open'); });
    el.classList.toggle('open');
}
document.addEventListener('click', e => {
    if (!e.target.closest('.header-dropdown'))
        document.querySelectorAll('.header-dropdown.open').forEach(d => d.classList.remove('open'));
});

// ── Today's Profit ────────────────────────────────────────
async function loadProfit() {
    const el = document.getElementById('profit-content');
    try {
        const d = await apiFetch('/api/dashboard/stats');
        const rev = parseFloat(d.today_sales||0);
        const exp = parseFloat(d.month_expenses||0)/30;
        const profit = rev - exp;
        const fmt = n => 'TSh ' + n.toLocaleString('en',{minimumFractionDigits:0,maximumFractionDigits:0});
        el.innerHTML = `
            <div style="display:grid;gap:0.6rem;">
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <span style="font-size:0.78rem;color:#64748b;">Revenue Today</span>
                    <span style="font-size:0.85rem;font-weight:700;color:#16a34a;">${fmt(rev)}</span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <span style="font-size:0.78rem;color:#64748b;">Est. Expenses</span>
                    <span style="font-size:0.85rem;font-weight:700;color:#dc2626;">${fmt(exp)}</span>
                </div>
                <div style="border-top:1px solid #f1f5f9;padding-top:0.6rem;display:flex;justify-content:space-between;align-items:center;">
                    <span style="font-size:0.8rem;font-weight:700;color:#0f172a;">Net Profit</span>
                    <span style="font-size:1rem;font-weight:800;color:${profit>=0?'#16a34a':'#dc2626'};">${fmt(profit)}</span>
                </div>
                <div style="font-size:0.68rem;color:#94a3b8;text-align:right;">Today · ${new Date().toLocaleDateString()}</div>
            </div>`;
    } catch(e) { el.innerHTML = '<div style="color:#ef4444;font-size:0.8rem;">Failed to load</div>'; }
}

// ── Toast ─────────────────────────────────────────────────
function showToast(message, type = 'success') {
    const icons = {
        success: `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
        error:   `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
        info:    `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
    };
    const t = document.createElement('div');
    t.className = `toast toast-${type}`;
    t.innerHTML = (icons[type]||'') + `<span>${message}</span>`;
    document.getElementById('toast-container').appendChild(t);
    setTimeout(() => { t.style.cssText = 'opacity:0;transform:translateX(20px);transition:all .3s'; setTimeout(() => t.remove(), 320); }, 3500);
}

// ── AJAX helper ───────────────────────────────────────────
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
async function apiFetch(url, opts = {}) {
    const res = await fetch(url, {
        ...opts,
        headers: { 'Content-Type':'application/json', 'Accept':'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With':'XMLHttpRequest', ...(opts.headers||{}) }
    });
    const data = await res.json().catch(() => ({}));
    if (!res.ok) throw data;
    return data;
}

// ── Modal helpers ─────────────────────────────────────────
function openModal(id)  { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay.open').forEach(m => m.classList.remove('open'));
        closeConfirm(); closeCalc();
    }
    if ((e.altKey||e.ctrlKey) && e.key.toLowerCase() === 'c') { e.preventDefault(); openCalc(); }
});
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', e => { if (e.target === overlay) overlay.classList.remove('open'); });
});

// ── Confirm dialog ────────────────────────────────────────
let _confirmCb = null;
function showConfirm(title, desc, cb) {
    document.getElementById('confirm-title').textContent = title;
    document.getElementById('confirm-desc').textContent  = desc;
    _confirmCb = cb;
    document.getElementById('confirm-overlay').classList.add('open');
}
function closeConfirm() {
    document.getElementById('confirm-overlay').classList.remove('open');
    _confirmCb = null;
}
document.getElementById('confirm-ok-btn').addEventListener('click', () => { if (_confirmCb) { _confirmCb(); closeConfirm(); } });
document.getElementById('confirm-overlay').addEventListener('click', e => { if (e.target.id === 'confirm-overlay') closeConfirm(); });

// ── Form error helpers ────────────────────────────────────
function clearFormErrors(formId) {
    document.querySelectorAll(`#${formId} .is-invalid`).forEach(el => el.classList.remove('is-invalid'));
    document.querySelectorAll(`#${formId} .invalid-feedback`).forEach(el => { el.style.display='none'; el.textContent=''; });
}
function showFormErrors(formId, errors) {
    if (!errors) return;
    Object.entries(errors).forEach(([field, msgs]) => {
        const el = document.querySelector(`#${formId} [name="${field}"]`);
        if (el) {
            el.classList.add('is-invalid');
            const fb = el.closest('.form-group')?.querySelector('.invalid-feedback');
            if (fb) { fb.textContent = Array.isArray(msgs) ? msgs[0] : msgs; fb.style.display = 'block'; }
        }
    });
}

// ── Calculator ────────────────────────────────────────────
let _calcVal = '0', _calcExpr = '', _calcOp = '', _calcPrev = '';
function openCalc()  { document.getElementById('calc-overlay').classList.add('open'); }
function closeCalc() { document.getElementById('calc-overlay').classList.remove('open'); }
function calcAction(a) {
    const nums = '0123456789.';
    if (nums.includes(a)) {
        if (a === '.' && _calcVal.includes('.')) return;
        _calcVal = (_calcVal === '0' && a !== '.') ? a : _calcVal + a;
    } else if (a === 'AC') {
        _calcVal = '0'; _calcExpr = ''; _calcOp = ''; _calcPrev = '';
    } else if (a === '±') {
        _calcVal = _calcVal.startsWith('-') ? _calcVal.slice(1) : '-' + _calcVal;
    } else if (a === '%') {
        _calcVal = String(parseFloat(_calcVal) / 100);
    } else if (['+','−','×','÷'].includes(a)) {
        _calcPrev = _calcVal; _calcOp = a;
        _calcExpr = _calcVal + ' ' + a;
        _calcVal = '0';
    } else if (a === '=') {
        if (!_calcOp) return;
        const p = parseFloat(_calcPrev), c = parseFloat(_calcVal);
        let r = p;
        if (_calcOp==='+') r = p + c;
        else if (_calcOp==='−') r = p - c;
        else if (_calcOp==='×') r = p * c;
        else if (_calcOp==='÷') r = c !== 0 ? p / c : 0;
        _calcExpr = _calcPrev + ' ' + _calcOp + ' ' + _calcVal + ' =';
        _calcVal = String(parseFloat(r.toFixed(10)));
        _calcOp = ''; _calcPrev = '';
    }
    document.getElementById('calc-val').textContent = parseFloat(_calcVal).toLocaleString('en',{maximumFractionDigits:8});
    document.getElementById('calc-expr').textContent = _calcExpr;
}
document.addEventListener('keydown', e => {
    if (!document.getElementById('calc-overlay').classList.contains('open')) return;
    if (e.key >= '0' && e.key <= '9') calcAction(e.key);
    else if (e.key === '.') calcAction('.');
    else if (e.key === '+') calcAction('+');
    else if (e.key === '-') calcAction('−');
    else if (e.key === '*') calcAction('×');
    else if (e.key === '/') { e.preventDefault(); calcAction('÷'); }
    else if (e.key === 'Enter' || e.key === '=') calcAction('=');
    else if (e.key === 'Backspace') { _calcVal = _calcVal.length > 1 ? _calcVal.slice(0,-1) : '0'; document.getElementById('calc-val').textContent = _calcVal; }
    else if (e.key === 'Escape') closeCalc();
});
</script>
@yield('scripts')

{{-- PDF Preview Modal --}}
<div id="pdfPreviewOverlay" class="pdf-preview-overlay" onclick="closePdfPreview(event)">
    <div class="pdf-preview-box" onclick="event.stopPropagation()">
        <div class="pdf-preview-head">
            <h3 id="pdfPreviewTitle">Report Preview</h3>
            <div class="pdf-actions">
                <button onclick="zoomPdf(-0.1)" title="Zoom out">−</button>
                <span id="pdfZoomLabel" style="font-size:0.75rem;font-weight:700;color:#64748b;min-width:48px;text-align:center;">100%</span>
                <button onclick="zoomPdf(0.1)" title="Zoom in">+</button>
                <div style="width:1px;height:20px;background:rgba(0,0,0,0.08);margin:0 0.25rem;"></div>
                <button onclick="downloadPdf()" class="primary">
                    <svg class="w-4 h-4" style="display:inline;vertical-align:middle;margin-right:4px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Print / Save PDF
                </button>
                <button onclick="closePdfPreview()">Close</button>
            </div>
        </div>
        <div class="pdf-preview-body" id="pdfPreviewBody">
            <div id="pdfPreviewFrame" class="pdf-preview-frame"></div>
        </div>
    </div>
</div>

{{-- Shared Report Export Utilities --}}
<script>
let _pdfZoom = 1;
function zoomPdf(delta) {
    _pdfZoom = Math.max(0.4, Math.min(2.0, _pdfZoom + delta));
    document.getElementById('pdfZoomLabel').textContent = Math.round(_pdfZoom * 100) + '%';
    const frame = document.getElementById('pdfPreviewFrame');
    frame.style.transform = 'scale(' + _pdfZoom + ')';
}
/* Print-to-PDF with live A4 preview */
function openPdfPreview(title) {
    document.getElementById('pdfPreviewTitle').textContent = title || 'Report Preview';
    _pdfZoom = 1;
    document.getElementById('pdfZoomLabel').textContent = '100%';
    const frame = document.getElementById('pdfPreviewFrame');
    frame.style.transform = 'scale(1)';
    // Capture report-page HTML only
    const reportPage = document.querySelector('.report-page');
    if (!reportPage) {
        Swal.fire({ icon:'error', title:'Report page not found', timer:2000, showConfirmButton:false, toast:true, position:'top-end' });
        return;
    }
    // Clone and clean
    const clone = reportPage.cloneNode(true);
    // Remove no-print elements
    clone.querySelectorAll('.no-print').forEach(el => el.remove());
    // Remove AOS attributes
    clone.querySelectorAll('[data-aos]').forEach(el => { el.removeAttribute('data-aos'); el.removeAttribute('data-aos-delay'); });
    // Build A4 document
    const allStyles = Array.from(document.styleSheets)
        .filter(s => !s.href || s.href.includes(window.location.host))
        .map(s => {
            try { return Array.from(s.cssRules).map(r => r.cssText).join('\n'); } catch(e) { return ''; }
        }).join('\n');
    const html = `<!DOCTYPE html>
<html><head><meta charset="utf-8"><style>
@page { size: A4; margin: 12mm; }
body { background:#fff; margin:0; padding:0; font-family:Inter,ui-sans-serif,system-ui,sans-serif; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
${allStyles}
.report-page { max-width:100%; padding:0; }
.report-header-bar { background: linear-gradient(135deg,#0f172a,#1e3a8a) !important; border-radius:0 !important; margin-bottom:1rem !important; color:#fff; }
.report-summary-card, .report-chart-card, .report-table-wrap { box-shadow:none !important; border:1px solid #e2e8f0 !important; break-inside:avoid; page-break-inside:avoid; margin-bottom:1rem; }
.report-chart-card .rch-body { height:220px !important; }
.report-table tbody tr { break-inside:avoid; }
.report-table thead { display:table-header-group; }
.report-summary { grid-template-columns:1fr 1fr !important; gap:0.75rem !important; }
.report-chart-row { grid-template-columns:1fr !important; gap:1rem !important; }
.dash-content { padding:0 !important; }
</style></head>
<body>${clone.outerHTML}</body></html>`;
    frame.innerHTML = html;
    document.getElementById('pdfPreviewOverlay').classList.add('open');
}
function closePdfPreview(e) {
    if (e && e.target !== e.currentTarget) return;
    document.getElementById('pdfPreviewOverlay').classList.remove('open');
    document.getElementById('pdfPreviewFrame').innerHTML = '';
}
function downloadPdf() {
    window.print();
    Swal.fire({ icon:'success', title:'PDF saved! Check your downloads.', timer:2500, showConfirmButton:false, toast:true, position:'top-end' });
}

/* CSV Export */
function exportTableToCSV(tableSelector, filename) {
    const table = document.querySelector(tableSelector);
    if (!table) { Swal.fire({icon:'error',title:'Table not found'}); return; }
    let csv = [];
    const rows = table.querySelectorAll('tr');
    rows.forEach(row => {
        let cols = [];
        row.querySelectorAll('th, td').forEach(cell => {
            let text = cell.innerText.replace(/"/g, '""').trim();
            if (text.includes(',') || text.includes('"') || text.includes('\n')) text = '"' + text + '"';
            cols.push(text);
        });
        csv.push(cols.join(','));
    });
    const blob = new Blob([csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = filename;
    link.click();
    Swal.fire({ icon:'success', title:'Excel (CSV) exported!', timer:2000, showConfirmButton:false, toast:true, position:'top-end' });
}
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>AOS.init({ once: true, offset: 40, duration: 700, easing: 'ease-out-cubic' });</script>

{{-- ─── Mobile Bottom Navigation ──────────────────────────────────── --}}
<nav id="mobileBottomNav" class="mobile-bottom-nav">
    <a href="{{ route('dashboard') }}" class="mb-item" data-route="dashboard" data-ripple onclick="hideRipple()">
        <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        <span>Home</span>
    </a>
    <a href="{{ route('dashboard.sell.pos') }}" class="mb-item" data-route="sell" data-ripple onclick="hideRipple()">
        <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
        <span>POS</span>
    </a>
    <a href="{{ route('dashboard.mobile.products') }}" class="mb-item" data-route="products" data-ripple onclick="hideRipple()">
        <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        <span>Stock</span>
    </a>
    <a href="{{ route('dashboard.mobile.customers') }}" class="mb-item" data-route="customers" data-ripple onclick="hideRipple()">
        <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/></svg>
        <span>Clients</span>
    </a>
    <a href="{{ route('dashboard.mobile.sales') }}" class="mb-item" data-route="sales" data-ripple onclick="hideRipple()">
        <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        <span>Sales</span>
    </a>
    <a href="{{ route('dashboard.profile') }}" class="mb-item" data-route="profile" data-ripple onclick="hideRipple()">
        <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        <span>Profile</span>
    </a>
</nav>

<style>
/* ── Floating Action Button (Mobile) ──────────────────────────── */
.mob-fab{display:none;position:fixed;bottom:78px;right:16px;z-index:998;width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg,#2563eb,#1d4ed8);border:none;color:#fff;box-shadow:0 6px 24px rgba(37,99,235,.4);cursor:pointer;transition:all .2s;-webkit-tap-highlight-color:transparent;align-items:center;justify-content:center;}
.mob-fab:active{transform:scale(.9);}
.mob-fab svg{width:24px;height:24px;}
.mob-fab .fab-badge{position:absolute;top:-4px;right:-4px;width:20px;height:20px;border-radius:50%;background:#ef4444;color:#fff;font-size:.6rem;font-weight:800;display:flex;align-items:center;justify-content:center;border:2px solid #fff;}
@media(max-width:767px){.mob-fab{display:flex;}}

/* ── Mobile Bottom Navigation ──────────────────────────────────── */
.mobile-bottom-nav {
    display: none;
    position: fixed; bottom: 0; left: 0; right: 0;
    height: 64px;
    background: rgba(255,255,255,0.92);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border-top: 1px solid rgba(233,237,245,0.8);
    z-index: 999;
    padding: 0 4px;
    box-shadow: 0 -4px 20px rgba(15,23,42,0.06);
    justify-content: space-around;
    align-items: center;
    padding-bottom: env(safe-area-inset-bottom, 0);
}
.mobile-bottom-nav .mb-item {
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    gap: 2px; padding: 4px 6px; min-width: 48px;
    color: #94a3b8; text-decoration: none;
    transition: color 0.15s;
    -webkit-tap-highlight-color: transparent;
    position: relative;
}
.mobile-bottom-nav .mb-item svg {
    width: 22px; height: 22px; transition: all 0.2s;
}
.mobile-bottom-nav .mb-item span {
    font-size: 0.55rem; font-weight: 600;
    letter-spacing: 0.02em; text-transform: uppercase;
}
.mobile-bottom-nav .mb-item.active { color: #2563eb; }
.mobile-bottom-nav .mb-item.active svg { transform: translateY(-1px); filter: drop-shadow(0 1px 4px rgba(37,99,235,0.25)); }
.mobile-bottom-nav .mb-item::after {
    content: ''; position: absolute; top: 2px;
    width: 20px; height: 3px; border-radius: 0 0 3px 3px;
    background: linear-gradient(90deg,#2563eb,#7c3aed);
    opacity: 0; transform: scaleX(0);
    transition: all 0.25s cubic-bezier(0.4,0,0.2,1);
}
.mobile-bottom-nav .mb-item.active::after { opacity: 1; transform: scaleX(1); }

/* Show bottom nav only on mobile */
@media (max-width: 767px) {
    .mobile-bottom-nav { display: flex; }
    .main-wrap { padding-bottom: 72px; }
    .dash-content { padding-bottom: 4px; }
}
</style>

<script>
// ── Mobile Bottom Nav Active State ──────────────────────────────
(function(){
    const path = window.location.pathname;
    const items = document.querySelectorAll('.mb-item');
    items.forEach(function(el){
        const route = el.getAttribute('data-route');
        if (!route) return;
        // Exact match first
        if (path === '/dashboard/'+route || path === '/dashboard/m/'+route) {
            el.classList.add('active'); return;
        }
        // Partial match - but avoid false positives (e.g. "sales" in "profile")
        if (path.includes('/' + route + '/') || path.includes('/' + route + '?')) {
            el.classList.add('active'); return;
        }
        if (route === 'pos' && path.includes('/sell/pos')) {
            el.classList.add('active'); return;
        }
    });
    // Dashboard root
    if (path === '/dashboard' || path === '/dashboard/') {
        document.querySelector('.mb-item[data-route="dashboard"]')?.classList.add('active');
    }
})();

// ── PWA Install Prompt ──────────────────────────────────────────
let deferredPrompt = null;
window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    // Show install banner after 3 seconds
    setTimeout(() => {
        if (deferredPrompt) {
            const banner = document.getElementById('pwaInstallBanner');
            if (banner) banner.style.display = 'flex';
        }
    }, 3000);
});
async function installPWA() {
    if (!deferredPrompt) return;
    deferredPrompt.prompt();
    const result = await deferredPrompt.userChoice;
    deferredPrompt = null;
    const banner = document.getElementById('pwaInstallBanner');
    if (banner) banner.style.display = 'none';
}

// ── Service Worker Registration ─────────────────────────────────
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(() => {});
    });
}

// ── Pull-to-refresh (mobile) ────────────────────────────────────
let touchStartY = 0;
document.addEventListener('touchstart', (e) => {
    if (window.scrollY === 0) touchStartY = e.touches[0].clientY;
}, { passive: true });
document.addEventListener('touchmove', (e) => {
    if (touchStartY > 0 && window.scrollY === 0 && (e.touches[0].clientY - touchStartY) > 100) {
        touchStartY = 0;
        window.location.reload();
    }
}, { passive: true });
</script>

{{-- Floating Action Button (Mobile) --}}
<button class="mob-fab" onclick="window.location.href='{{ route('dashboard.sell.pos') }}'" title="New Sale" id="mobFab">
    <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
</button>

{{-- PWA Install Banner --}}
<div id="pwaInstallBanner" style="display:none;position:fixed;bottom:80px;left:12px;right:12px;z-index:9998;background:linear-gradient(135deg,#1e293b,#0f172a);border-radius:16px;padding:14px 18px;color:#fff;box-shadow:0 8px 32px rgba(0,0,0,0.3);align-items:center;gap:12px;backdrop-filter:blur(12px);max-width:400px;margin:0 auto;">
    <div style="display:flex;align-items:center;gap:12px;flex:1;">
        <img src="{{ asset('logo.png') }}" alt="MannaPOS" style="width:40px;height:40px;border-radius:10px;">
        <div>
            <div style="font-weight:700;font-size:.82rem;">Install MannaPOS</div>
            <div style="font-size:.68rem;color:#94a3b8;">Add to home screen for faster access</div>
        </div>
    </div>
    <div style="display:flex;gap:8px;flex-shrink:0;">
        <button onclick="document.getElementById('pwaInstallBanner').style.display='none'" style="background:rgba(255,255,255,0.1);border:none;color:#94a3b8;padding:8px 14px;border-radius:10px;font-size:.75rem;font-weight:600;cursor:pointer;">Later</button>
        <button onclick="installPWA()" style="background:linear-gradient(135deg,#2563eb,#1d4ed8);border:none;color:#fff;padding:8px 18px;border-radius:10px;font-size:.75rem;font-weight:700;cursor:pointer;box-shadow:0 4px 12px rgba(37,99,235,0.3);">Install</button>
    </div>
</div>

</body>
</html>
