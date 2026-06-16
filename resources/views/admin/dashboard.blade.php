<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard — {{ config('app.name', 'MannaPOS') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('icons8-dynamics-365-100.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter','ui-sans-serif','system-ui','sans-serif'] },
                    colors: {
                        brand: { 50:'#fff0f3', 100:'#ffe0e7', 500:'#e03057', 600:'#c41f44', 700:'#a01637' }
                    }
                }
            }
        }
    </script>
    <style>
        body { background: #f1f4fb; }
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }

        .sidebar {
            width: 240px; min-width: 240px; height: 100vh;
            position: fixed; top: 0; left: 0;
            background: #fff;
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
            padding: 0.6rem 0.75rem 0.5rem;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .nav-section-label {
            font-size: 0.6rem;
            font-weight: 800;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: #b0b8cc;
            padding: 1rem 0.5rem 0.3rem;
            user-select: none;
        }

        .nav-item {
            display: flex; align-items: center; gap: 0.7rem;
            padding: 0.52rem 0.75rem;
            font-size: 0.82rem; font-weight: 500;
            color: #4b5675;
            border-radius: 8px;
            cursor: pointer; text-decoration: none;
            transition: background 0.15s, color 0.15s;
            white-space: nowrap;
            margin-bottom: 1px;
        }
        .nav-item:hover { background: #f6f7fb; color: #0f172a; }
        .nav-item svg { width: 17px; height: 17px; flex-shrink: 0; color: #94a3b8; transition: color 0.15s; }
        .nav-item:hover svg { color: #475569; }

        .nav-item.active {
            background: #fff0f3;
            color: #e03057;
            font-weight: 600;
        }
        .nav-item.active svg { color: #e03057; }

        .dropdown { margin-bottom: 1px; }

        .dropdown-toggle {
            display: flex; align-items: center; gap: 0.7rem;
            padding: 0.52rem 0.75rem;
            font-size: 0.82rem; font-weight: 500;
            color: #4b5675;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.15s, color 0.15s;
            white-space: nowrap;
            user-select: none;
        }
        .dropdown-toggle:hover { background: #f6f7fb; color: #0f172a; }
        .dropdown-toggle svg:first-child { width: 17px; height: 17px; flex-shrink: 0; color: #94a3b8; transition: color 0.15s; }
        .dropdown-toggle:hover svg:first-child { color: #475569; }

        .dropdown-toggle .chevron {
            margin-left: auto;
            width: 14px; height: 14px;
            color: #c4cad8;
            transition: transform 0.25s ease, color 0.15s;
            flex-shrink: 0;
        }
        .dropdown.open .dropdown-toggle { color: #0f172a; background: #f6f7fb; }
        .dropdown.open .dropdown-toggle svg:first-child { color: #475569; }
        .dropdown.open .dropdown-toggle .chevron { transform: rotate(90deg); color: #94a3b8; }

        .dropdown-children {
            display: none;
            position: relative;
            padding: 0.3rem 0 0.5rem 2.5rem;
            margin-top: 2px;
        }
        .dropdown.open .dropdown-children { display: block; }

        .dropdown-children::before {
            content: '';
            position: absolute;
            left: 1.3rem; top: 0; bottom: 0;
            width: 1.5px;
            background: linear-gradient(to bottom, #e2e8f0, transparent);
            border-radius: 2px;
        }

        .dropdown-children .child-item {
            display: flex; align-items: center;
            font-size: 0.8rem; font-weight: 500;
            color: #64748b;
            padding: 0.38rem 0.5rem;
            border-radius: 6px;
            transition: background 0.15s, color 0.15s;
            cursor: pointer; text-decoration: none;
            white-space: nowrap;
        }
        .dropdown-children .child-item::before {
            content: '';
            width: 5px; height: 5px;
            border-radius: 50%;
            background: #d1d9e6;
            margin-right: 0.6rem;
            flex-shrink: 0;
            transition: background 0.15s;
        }
        .dropdown-children .child-item:hover {
            background: #f6f7fb;
            color: #0f172a;
        }
        .dropdown-children .child-item:hover::before { background: #e03057; }
        .dropdown-children .child-item.active {
            color: #e03057;
            font-weight: 600;
            background: #fff0f3;
        }
        .dropdown-children .child-item.active::before { background: #e03057; }

        .sidebar-bottom {
            margin-top: auto;
            padding: 0.75rem;
            border-top: 1px solid #f1f5f9;
            flex-shrink: 0;
        }
        .sign-out-btn {
            display: flex; align-items: center; gap: 0.65rem;
            padding: 0.55rem 0.75rem;
            font-size: 0.82rem; font-weight: 600;
            color: #e03057;
            width: 100%; border-radius: 8px;
            background: none; border: none; cursor: pointer;
            transition: background 0.15s;
        }
        .sign-out-btn:hover { background: #fff0f3; }
        .sign-out-btn svg { width: 17px; height: 17px; flex-shrink: 0; }

        .main-wrap { margin-left: 240px; min-height: 100vh; display: flex; flex-direction: column; }
        .top-header { background: #fff; border-bottom: 1px solid #e9edf5; height: 60px; display: flex; align-items: center; justify-content: space-between; padding: 0 2rem; position: sticky; top: 0; z-index: 30; }
        .page-title { font-size: 1.3rem; font-weight: 800; color: #0f172a; letter-spacing: -0.02em; }
        .header-right { display: flex; align-items: center; gap: 0.75rem; }
        .notif-btn { position: relative; width: 36px; height: 36px; border-radius: 10px; background: #f8fafc; border: 1px solid #e9edf5; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: background 0.15s; }
        .notif-btn:hover { background: #f1f5f9; }
        .notif-dot { position: absolute; top: 8px; right: 8px; width: 7px; height: 7px; border-radius: 50%; background: #e03057; border: 1.5px solid #fff; }
        .user-chip { display: flex; align-items: center; gap: 0.6rem; padding: 0.35rem 0.75rem 0.35rem 0.4rem; border-radius: 12px; background: #f8fafc; border: 1px solid #e9edf5; cursor: pointer; transition: background 0.15s; }
        .user-chip:hover { background: #f1f5f9; }
        .user-avatar { width: 30px; height: 30px; border-radius: 8px; background: linear-gradient(135deg,#2563eb,#7c3aed); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8rem; color: #fff; flex-shrink: 0; }
        .user-name { font-size: 0.8rem; font-weight: 600; color: #0f172a; }
        .user-role { font-size: 0.68rem; color: #94a3b8; }

        .header-dropdown { position: relative; }
        .header-dropdown-menu {
            display: none;
            position: absolute;
            top: calc(100% + 12px);
            right: 0;
            min-width: 280px;
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e9edf5;
            box-shadow: 0 4px 20px rgba(15,23,42,0.08);
            z-index: 50;
            overflow: hidden;
        }
        .header-dropdown.open .header-dropdown-menu { display: block; animation: slideDown 0.2s ease; }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .header-dropdown-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #f1f5f9;
            background: #fafbff;
        }
        .header-dropdown-title {
            font-size: 0.85rem;
            font-weight: 700;
            color: #0f172a;
        }
        .header-dropdown-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.25rem;
            font-size: 0.82rem;
            color: #475569;
            transition: background 0.15s;
            cursor: pointer;
            text-decoration: none;
        }
        .header-dropdown-item:hover { background: #f8fafc; color: #0f172a; }
        .header-dropdown-item svg { width: 18px; height: 18px; color: #94a3b8; }
        .header-dropdown-item:hover svg { color: #475569; }
        .header-dropdown-divider {
            height: 1px;
            background: #f1f5f9;
            margin: 0.25rem 0;
        }
        .header-dropdown-footer {
            padding: 0.75rem 1.25rem;
            border-top: 1px solid #f1f5f9;
            background: #fafbff;
        }

        .dash-content { padding: 1.75rem 2rem; flex: 1; }

        .dash-section { background: #fff; border-radius: 14px; border: 1px solid #e9edf5; margin-bottom: 1.25rem; overflow: hidden; }
        .dash-section-header { display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.25rem; cursor: pointer; background: #fafbff; border-bottom: 1px solid #f1f5f9; transition: background 0.2s; }
        .dash-section-header:hover { background: #f8fafc; }
        .dash-section-title { font-size: 0.92rem; font-weight: 700; color: #0f172a; }
        .dash-section-icon { width: 20px; height: 20px; color: #94a3b8; transition: transform 0.3s; }
        .dash-section.collapsed .dash-section-icon { transform: rotate(-90deg); }
        .dash-section-content { padding: 1.25rem; transition: all 0.3s ease; }
        .dash-section.collapsed .dash-section-content { display: none; }

        .kpi-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 1rem; margin-bottom: 1rem; }
        .kpi-card { background: #fff; border-radius: 14px; padding: 1rem 1.1rem; border: 1px solid #e9edf5; display: flex; align-items: center; gap: 0.85rem; transition: box-shadow 0.2s, transform 0.2s; }
        .kpi-card:hover { box-shadow: 0 8px 24px rgba(15,23,42,0.08); transform: translateY(-2px); }
        .kpi-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .kpi-icon img { width: 24px; height: 24px; object-fit: contain; }
        .kpi-val { font-size: 1.25rem; font-weight: 800; color: #0f172a; line-height: 1; letter-spacing: -0.02em; }
        .kpi-label { font-size: 0.72rem; color: #94a3b8; margin-top: 0.2rem; font-weight: 500; }

        .charts-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.75rem; }
        .chart-card { background: #fff; border-radius: 14px; border: 1px solid #e9edf5; padding: 1.4rem 1.5rem; }
        .chart-title { font-size: 0.92rem; font-weight: 700; color: #0f172a; margin-bottom: 1rem; }

        .stats-grid { display: grid; grid-template-columns: repeat(2,1fr); gap: 1.25rem; margin-bottom: 1.75rem; }
        .stat-card { background: #fff; border-radius: 14px; border: 1px solid #e9edf5; padding: 1.25rem; }
        .stat-number { font-size: 1.75rem; font-weight: 800; color: #0f172a; letter-spacing: -0.03em; }
        .stat-label { font-size: 0.75rem; color: #94a3b8; margin-top: 0.25rem; font-weight: 500; }
        .stat-change { font-size: 0.72rem; font-weight: 600; margin-top: 0.35rem; }
        .stat-change.up { color: #16a34a; }
        .stat-change.down { color: #e03057; }

        @media (max-width: 1200px) {
            .kpi-grid { grid-template-columns: repeat(2,1fr); }
            .charts-row { grid-template-columns: 1fr; }
            .stats-grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); transition: transform 0.3s; }
            .sidebar.open { transform: translateX(0); }
            .main-wrap { margin-left: 0; }
            .kpi-grid { grid-template-columns: repeat(2,1fr); }
        }
        @media (max-width: 1280px) {
            .sidebar { width: 220px; min-width: 220px; }
            .main-wrap { margin-left: 220px; }
        }
    </style>
</head>
<body class="font-sans antialiased">

{{-- SIDEBAR --}}
<aside class="sidebar" id="sidebar">

    {{-- Logo --}}
    <div class="sidebar-logo">
        <div class="flex items-center gap-2.5">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-600 to-violet-600 flex items-center justify-center flex-shrink-0 shadow-md shadow-blue-200">
                <img src="{{ asset('icons8-dynamics-365-96.png') }}" alt="Logo" class="w-5 h-5 object-contain brightness-0 invert">
            </div>
            <div class="min-w-0">
                <div class="text-[0.95rem] font-extrabold text-slate-900 leading-none tracking-tight truncate">{{ config('app.name','MannaPOS') }}</div>
                <div class="text-[0.58rem] font-bold tracking-[0.16em] uppercase text-brand-500 mt-0.5">Admin Panel</div>
            </div>
        </div>
    </div>

    {{-- Nav --}}
    <div class="sidebar-content">

        <div class="nav-section-label">Main</div>

        <a href="{{ route('admin.dashboard') }}" class="nav-item active" data-route="admin.dashboard">
            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12l-2 0l9 -9l9 9l-2 0"/><path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7"/><path d="M10 12h4v4h-4z"/></svg>
            Dashboard
        </a>

        <div class="nav-section-label">Administration</div>

        {{-- User Management Dropdown --}}
        <div class="dropdown" id="dropdown-user">
            <div class="dropdown-toggle" onclick="toggleDropdown('dropdown-user')">
                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"/><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/><path d="M21 21v-2a4 4 0 0 0 -3 -3.85"/></svg>
                User Management
                <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6l6 6"/></svg>
            </div>
            <div class="dropdown-children">
                <a href="{{ route('admin.users') }}" class="child-item">Users</a>
                <a href="{{ route('admin.roles') }}" class="child-item">Roles</a>
                <a href="{{ route('admin.sales-commission-agents') }}" class="child-item">Sales Commission Agents</a>
            </div>
        </div>

        {{-- Plan Management Dropdown --}}
        <div class="dropdown" id="dropdown-plans">
            <div class="dropdown-toggle" onclick="toggleDropdown('dropdown-plans')">
                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3l8 4.5v9l-8 4.5l-8 -4.5v-9l8 -4.5"/><path d="M12 12l8 -4.5"/><path d="M8.2 9.8l7.6 -4.6"/><path d="M12 12v9"/><path d="M12 12l-8 -4.5"/></svg>
                Plan Management
                <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6l6 6"/></svg>
            </div>
            <div class="dropdown-children">
                <a href="{{ route('admin.plans') }}" class="child-item">Plans</a>
                <a href="{{ route('admin.subscriptions') }}" class="child-item">Subscriptions</a>
            </div>
        </div>

        <div class="nav-section-label">System</div>

        {{-- Notification Templates --}}
        <a href="{{ route('admin.notification-templates') }}" class="nav-item">
            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z"/><path d="M3 7l9 6l9 -6"/></svg>
            Notification Templates
        </a>

        {{-- Settings Dropdown --}}
        <div class="dropdown" id="dropdown-settings">
            <div class="dropdown-toggle" onclick="toggleDropdown('dropdown-settings')">
                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z"/><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"/></svg>
                Settings
                <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6l6 6"/></svg>
            </div>
            <div class="dropdown-children">
                <a href="{{ route('admin.settings.general') }}" class="child-item">Business Settings</a>
                <a href="{{ route('admin.settings.business-location') }}" class="child-item">Business Locations</a>
                <a href="{{ route('admin.settings.invoice-settings') }}" class="child-item">Invoice Settings</a>
                <a href="{{ route('admin.settings.barcode-settings') }}" class="child-item">Barcode Settings</a>
                <a href="{{ route('admin.settings.tax-rates') }}" class="child-item">Tax Rates</a>
            </div>
        </div>

        {{-- Reports --}}
        <a href="{{ route('admin.reports') }}" class="nav-item">
            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h5.697"/><path d="M18 14v4h4"/><path d="M18 11v-4a2 2 0 0 0 -2 -2h-2"/><path d="M8 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"/><path d="M18 18m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"/><path d="M8 11h4"/><path d="M8 15h3"/></svg>
            Reports
        </a>

        {{-- Go to User Dashboard --}}
        <div class="nav-section-label">Quick Links</div>
        <a href="{{ route('dashboard') }}" class="nav-item">
            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"/><path d="M14 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"/><path d="M4 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"/><path d="M14 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"/></svg>
            User Dashboard
        </a>

    </div>

    {{-- Sign out --}}
    <div class="sidebar-bottom">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sign-out-btn">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Sign Out
            </button>
        </form>
    </div>
</aside>

{{-- MAIN --}}
<div class="main-wrap">

    {{-- Top Header --}}
    <header class="top-header">
        <div class="flex items-center gap-3">
            <button class="md:hidden p-1.5 rounded-lg hover:bg-slate-100" onclick="document.getElementById('sidebar').classList.toggle('open')">
                <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <h1 class="page-title" id="page-title">Admin Dashboard</h1>
        </div>

        <div class="header-right">
            {{-- Date Display --}}
            <span class="hidden lg:inline-block text-xs text-slate-400 font-medium px-3 py-1.5 rounded-lg bg-slate-100">{{ now()->format('m/d/Y') }}</span>

            {{-- Profile Dropdown --}}
            <div class="header-dropdown" id="header-profile-dropdown">
                <div class="user-chip" onclick="toggleHeaderDropdown('header-profile-dropdown')">
                    <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}</div>
                    <span class="hidden md:block text-xs font-semibold text-slate-700">{{ Auth::user()->name ?? 'Admin' }}</span>
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </div>
                <div class="header-dropdown-menu" style="min-width:200px;">
                    <div class="header-dropdown-header" style="padding:0.75rem 1rem;">
                        <div style="font-size:0.75rem;color:#64748b;">Signed in as</div>
                        <div style="font-size:0.8rem;font-weight:700;color:#0f172a;">{{ Auth::user()->name ?? 'Admin' }}</div>
                    </div>
                    <a href="{{ route('admin.dashboard') }}" class="header-dropdown-item" style="padding:0.6rem 1rem;">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/><path d="M12 10m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"/><path d="M6.168 18.849a4 4 0 0 1 3.832 -2.849h4a4 4 0 0 1 3.834 2.855"/></svg>
                        Profile
                    </a>
                    <div class="header-dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="header-dropdown-item" style="width:100%; border:none; background:none; color:#e03057; cursor:pointer; padding:0.6rem 1rem;">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2"/><path d="M9 12h12l-3 -3"/><path d="M18 15l3 -3"/></svg>
                            Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    {{-- Dashboard Content --}}
    <div class="dash-content" id="dash-content">

        {{-- KPI Section --}}
        <div class="dash-section" id="kpi-section">
            <div class="dash-section-header" onclick="toggleSection('kpi-section')">
                <div class="dash-section-title">Platform Overview</div>
                <svg class="dash-section-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </div>
            <div class="dash-section-content">
                <div class="kpi-grid">
                    <div class="kpi-card">
                        <div class="kpi-icon" style="background:#eff6ff;">
                            <img src="https://cdn-icons-png.flaticon.com/512/3500/3500460.png" alt="Revenue">
                        </div>
                        <div>
                            <div class="kpi-val">TSh 0</div>
                            <div class="kpi-label">Total Revenue</div>
                        </div>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-icon" style="background:#f0fdf4;">
                            <img src="https://cdn-icons-png.flaticon.com/512/2489/2489756.png" alt="Users">
                        </div>
                        <div>
                            <div class="kpi-val">0</div>
                            <div class="kpi-label">Total Users</div>
                        </div>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-icon" style="background:#fdf4ff;">
                            <img src="https://cdn-icons-png.flaticon.com/512/1256/1256650.png" alt="Orders">
                        </div>
                        <div>
                            <div class="kpi-val">0</div>
                            <div class="kpi-label">Total Businesses</div>
                        </div>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-icon" style="background:#fffbeb;">
                            <img src="https://cdn-icons-png.flaticon.com/512/2830/2830285.png" alt="Subscriptions">
                        </div>
                        <div>
                            <div class="kpi-val">0</div>
                            <div class="kpi-label">Active Subscriptions</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- System Stats --}}
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">0</div>
                <div class="stat-label">Registered Users</div>
                <div class="stat-change up">New this month</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">0</div>
                <div class="stat-label">Active Businesses</div>
                <div class="stat-change up">Active this month</div>
            </div>
        </div>

        {{-- Charts --}}
        <div class="charts-row">
            <div class="chart-card">
                <div class="chart-title">Monthly Revenue</div>
                <canvas id="revenueChart" height="200"></canvas>
            </div>
            <div class="chart-card">
                <div class="chart-title">User Growth</div>
                <canvas id="userChart" height="200"></canvas>
            </div>
        </div>

    </div>

    {{-- Footer --}}
    <footer class="text-center py-4 text-xs text-slate-400 border-t border-slate-200">
        &copy; {{ date('Y') }} {{ config('app.name', 'MannaPOS') }}. All rights reserved.
    </footer>
</div>

{{-- Scripts --}}
<script>
    function toggleDropdown(id) {
        var el = document.getElementById(id);
        el.classList.toggle('open');
    }

    function toggleHeaderDropdown(id) {
        var el = document.getElementById(id);
        el.classList.toggle('open');
    }

    document.addEventListener('click', function(e) {
        document.querySelectorAll('.header-dropdown.open').forEach(function(dd) {
            if (!dd.contains(e.target)) {
                dd.classList.remove('open');
            }
        });
    });

    // Active nav item highlighting
    document.querySelectorAll('.nav-item, .child-item').forEach(function(item) {
        item.addEventListener('click', function() {
            document.querySelectorAll('.nav-item.active, .child-item.active').forEach(function(a) {
                a.classList.remove('active');
            });
            this.classList.add('active');
        });
    });

    // Charts
    document.addEventListener('DOMContentLoaded', function() {
        new Chart(document.getElementById('revenueChart'), {
            type: 'line',
            data: {
                labels: ['Jan','Feb','Mar','Apr','May','Jun'],
                datasets: [{
                    label: 'Revenue',
                    data: [0, 0, 0, 0, 0, 0],
                    borderColor: '#e03057',
                    backgroundColor: 'rgba(224,48,87,0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                    x: { grid: { display: false } }
                }
            }
        });

        new Chart(document.getElementById('userChart'), {
            type: 'line',
            data: {
                labels: ['Jan','Feb','Mar','Apr','May','Jun'],
                datasets: [{
                    label: 'Users',
                    data: [0, 0, 0, 0, 0, 0],
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37,99,235,0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                    x: { grid: { display: false } }
                }
            }
        });
    });
</script>

</body>
</html>
