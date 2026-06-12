<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('page_title', 'Dashboard') — {{ config('app.name', 'MannaPOS') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('icons8-dynamics-365-100.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
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
    @yield('head_scripts')
    <style>
        body { background: #f1f4fb; }
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }

        /* ── Sidebar ──────────────────────────────────── */
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
            font-size: 0.6rem; font-weight: 800;
            letter-spacing: 0.14em; text-transform: uppercase;
            color: #b0b8cc;
            padding: 1rem 0.5rem 0.3rem;
            user-select: none;
        }

        /* Plain nav item */
        .nav-item {
            display: flex; align-items: center; gap: 0.7rem;
            padding: 0.52rem 0.75rem;
            font-size: 0.82rem; font-weight: 500;
            color: #4b5675;
            border-radius: 8px;
            cursor: pointer; text-decoration: none;
            transition: background 0.15s, color 0.15s;
            white-space: nowrap; margin-bottom: 1px;
        }
        .nav-item:hover { background: #f6f7fb; color: #0f172a; }
        .nav-item svg { width: 17px; height: 17px; flex-shrink: 0; color: #94a3b8; transition: color 0.15s; }
        .nav-item:hover svg { color: #475569; }
        .nav-item.active { background: #fff0f3; color: #e03057; font-weight: 600; }
        .nav-item.active svg { color: #e03057; }

        /* Dropdown wrapper */
        .dropdown { margin-bottom: 1px; }

        /* Dropdown toggle */
        .dropdown-toggle {
            display: flex; align-items: center; gap: 0.7rem;
            padding: 0.52rem 0.75rem;
            font-size: 0.82rem; font-weight: 500;
            color: #4b5675; border-radius: 8px;
            cursor: pointer;
            transition: background 0.15s, color 0.15s;
            white-space: nowrap; user-select: none;
        }
        .dropdown-toggle:hover { background: #f6f7fb; color: #0f172a; }
        .dropdown-toggle svg:first-child { width: 17px; height: 17px; flex-shrink: 0; color: #94a3b8; transition: color 0.15s; }
        .dropdown-toggle:hover svg:first-child { color: #475569; }
        .dropdown-toggle .chevron {
            margin-left: auto; width: 14px; height: 14px;
            color: #c4cad8; transition: transform 0.25s ease, color 0.15s; flex-shrink: 0;
        }
        .dropdown.open .dropdown-toggle { color: #0f172a; background: #f6f7fb; }
        .dropdown.open .dropdown-toggle svg:first-child { color: #475569; }
        .dropdown.open .dropdown-toggle .chevron { transform: rotate(90deg); color: #94a3b8; }

        /* Children panel */
        .dropdown-children {
            display: none;
            position: relative;
            padding: 0.3rem 0 0.5rem 2.5rem;
            margin-top: 2px;
        }
        .dropdown.open .dropdown-children { display: block; }
        .dropdown-children::before {
            content: ''; position: absolute;
            left: 1.3rem; top: 0; bottom: 0;
            width: 1.5px;
            background: linear-gradient(to bottom, #e2e8f0, transparent);
            border-radius: 2px;
        }
        .dropdown-children .child-item {
            display: flex; align-items: center;
            font-size: 0.8rem; font-weight: 500;
            color: #64748b; padding: 0.38rem 0.5rem;
            border-radius: 6px;
            transition: background 0.15s, color 0.15s;
            cursor: pointer; text-decoration: none; white-space: nowrap;
        }
        .dropdown-children .child-item::before {
            content: ''; width: 5px; height: 5px; border-radius: 50%;
            background: #d1d9e6; margin-right: 0.6rem; flex-shrink: 0;
            transition: background 0.15s;
        }
        .dropdown-children .child-item:hover { background: #f6f7fb; color: #0f172a; }
        .dropdown-children .child-item:hover::before { background: #e03057; }
        .dropdown-children .child-item.active { color: #e03057; font-weight: 600; background: #fff0f3; }
        .dropdown-children .child-item.active::before { background: #e03057; }

        /* Sign out */
        .sidebar-bottom {
            margin-top: auto; padding: 0.75rem;
            border-top: 1px solid #f1f5f9; flex-shrink: 0;
        }
        .sign-out-btn {
            display: flex; align-items: center; gap: 0.65rem;
            padding: 0.55rem 0.75rem;
            font-size: 0.82rem; font-weight: 600; color: #e03057;
            width: 100%; border-radius: 8px;
            background: none; border: none; cursor: pointer;
            transition: background 0.15s;
        }
        .sign-out-btn:hover { background: #fff0f3; }
        .sign-out-btn svg { width: 17px; height: 17px; flex-shrink: 0; }

        /* ── Main ─────────────────────────────────────── */
        .main-wrap { margin-left: 240px; min-height: 100vh; display: flex; flex-direction: column; }
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
        .notif-dot { position: absolute; top: 8px; right: 8px; width: 7px; height: 7px; border-radius: 50%; background: #e03057; border: 1.5px solid #fff; }
        .user-chip {
            display: flex; align-items: center; gap: 0.6rem;
            padding: 0.35rem 0.75rem 0.35rem 0.4rem;
            border-radius: 12px; background: #f8fafc;
            border: 1px solid #e9edf5; cursor: pointer;
            transition: background 0.15s;
        }
        .user-chip:hover { background: #f1f5f9; }
        .user-avatar {
            width: 30px; height: 30px; border-radius: 8px;
            background: linear-gradient(135deg,#2563eb,#7c3aed);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 0.8rem; color: #fff; flex-shrink: 0;
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
        .btn-delete { background:#fff1f2; color:#e03057; } .btn-delete:hover { background:#ffe4e6; }
        .btn-view   { background:#f0fdf4; color:#16a34a; } .btn-view:hover   { background:#dcfce7; }

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
        @media (max-width:1280px) { .sidebar { width:220px; min-width:220px; } .main-wrap { margin-left:220px; } }
        @media (max-width:768px)  { .sidebar { transform:translateX(-100%); transition:transform 0.3s; } .sidebar.open { transform:translateX(0); } .main-wrap { margin-left:0; } .form-row { grid-template-columns:1fr; } }

        @yield('page_styles')
    </style>
</head>
<body class="font-sans antialiased">

@php
    $isHome        = request()->routeIs('dashboard');
    $isUserMgmt    = request()->routeIs('dashboard.user-management.*');
    $isContacts    = request()->routeIs('dashboard.contacts.*');
    $isInventory   = request()->routeIs('dashboard.inventory.*');
    $isPurchases   = request()->routeIs('dashboard.purchases.*');
    $isSell        = request()->routeIs('dashboard.sell.*');
    $isStockTrans  = request()->routeIs('dashboard.stock-transfer.*');
    $isStockAdj    = request()->routeIs('dashboard.stock-adjustment.*');
    $isExpenses    = request()->routeIs('dashboard.expenses.*');
    $isReports     = request()->routeIs('dashboard.reports.*');
    $isNotifTpl    = request()->routeIs('dashboard.notification-templates');
    $isSettings    = request()->routeIs('dashboard.settings.*');

    function sidebarChildActive($route) {
        return request()->routeIs($route) ? 'active' : '';
    }
@endphp

{{-- ═══════════════════════════  SIDEBAR  ═══════════════════════════ --}}
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

        <a href="{{ route('dashboard') }}" class="nav-item {{ $isHome ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12l-2 0l9-9l9 9l-2 0"/><path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-7"/><path d="M10 12h4v4h-4z"/></svg>
            Dashboard
        </a>

        <div class="nav-section-label">Management</div>

        {{-- User Management --}}
        <div class="dropdown {{ $isUserMgmt ? 'open' : '' }}" id="dropdown-user">
            <div class="dropdown-toggle" onclick="toggleDropdown('dropdown-user')">
                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0-8 0"/><path d="M3 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/><path d="M21 21v-2a4 4 0 0 0-3-3.85"/></svg>
                User Management
                <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6l6 6"/></svg>
            </div>
            <div class="dropdown-children">
                <a href="{{ route('dashboard.user-management.users') }}" class="child-item {{ request()->routeIs('dashboard.user-management.users') ? 'active' : '' }}">Users</a>
                <a href="{{ route('dashboard.user-management.roles') }}" class="child-item {{ request()->routeIs('dashboard.user-management.roles') ? 'active' : '' }}">Roles</a>
                <a href="{{ route('dashboard.user-management.sales-commission-agents') }}" class="child-item {{ request()->routeIs('dashboard.user-management.sales-commission-agents') ? 'active' : '' }}">Sales Commission Agents</a>
            </div>
        </div>

        {{-- Contacts --}}
        <div class="dropdown {{ $isContacts ? 'open' : '' }}" id="dropdown-contacts">
            <div class="dropdown-toggle" onclick="toggleDropdown('dropdown-contacts')">
                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 6v12a2 2 0 0 1-2 2h-10a2 2 0 0 1-2-2v-12a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/><path d="M10 16h6"/><path d="M13 11m-2 0a2 2 0 1 0 4 0a2 2 0 1 0-4 0"/><path d="M4 8h3"/><path d="M4 12h3"/><path d="M4 16h3"/></svg>
                Contacts
                <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6l6 6"/></svg>
            </div>
            <div class="dropdown-children">
                <a href="{{ route('dashboard.contacts.suppliers') }}" class="child-item {{ request()->routeIs('dashboard.contacts.suppliers') ? 'active' : '' }}">Suppliers</a>
                <a href="{{ route('dashboard.contacts.customers') }}" class="child-item {{ request()->routeIs('dashboard.contacts.customers') ? 'active' : '' }}">Customers</a>
                <a href="{{ route('dashboard.contacts.customer-groups') }}" class="child-item {{ request()->routeIs('dashboard.contacts.customer-groups') ? 'active' : '' }}">Customer Groups</a>
                <a href="{{ route('dashboard.contacts.import-contacts') }}" class="child-item {{ request()->routeIs('dashboard.contacts.import-contacts') ? 'active' : '' }}">Import Contacts</a>
            </div>
        </div>

        {{-- Products --}}
        <div class="dropdown {{ $isInventory ? 'open' : '' }}" id="dropdown-products">
            <div class="dropdown-toggle" onclick="toggleDropdown('dropdown-products')">
                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3l8 4.5v9l-8 4.5l-8-4.5v-9l8-4.5"/><path d="M12 12l8-4.5"/><path d="M8.2 9.8l7.6-4.6"/><path d="M12 12v9"/><path d="M12 12l-8-4.5"/></svg>
                Products
                <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6l6 6"/></svg>
            </div>
            <div class="dropdown-children">
                <a href="{{ route('dashboard.inventory.list-products') }}" class="child-item {{ request()->routeIs('dashboard.inventory.list-products') ? 'active' : '' }}">List Products</a>
                <a href="{{ route('dashboard.inventory.add-product') }}" class="child-item {{ request()->routeIs('dashboard.inventory.add-product') ? 'active' : '' }}">Add Product</a>
                <a href="{{ route('dashboard.inventory.update-price') }}" class="child-item {{ request()->routeIs('dashboard.inventory.update-price') ? 'active' : '' }}">Update Price</a>
                <a href="{{ route('dashboard.inventory.print-labels') }}" class="child-item {{ request()->routeIs('dashboard.inventory.print-labels') ? 'active' : '' }}">Print Labels</a>
                <a href="{{ route('dashboard.inventory.variations') }}" class="child-item {{ request()->routeIs('dashboard.inventory.variations') ? 'active' : '' }}">Variations</a>
                <a href="{{ route('dashboard.inventory.import-products') }}" class="child-item {{ request()->routeIs('dashboard.inventory.import-products') ? 'active' : '' }}">Import Products</a>
                <a href="{{ route('dashboard.inventory.import-opening-stock') }}" class="child-item {{ request()->routeIs('dashboard.inventory.import-opening-stock') ? 'active' : '' }}">Import Opening Stock</a>
                <a href="{{ route('dashboard.inventory.selling-price-group') }}" class="child-item {{ request()->routeIs('dashboard.inventory.selling-price-group') ? 'active' : '' }}">Selling Price Group</a>
                <a href="{{ route('dashboard.inventory.units') }}" class="child-item {{ request()->routeIs('dashboard.inventory.units') ? 'active' : '' }}">Units</a>
                <a href="{{ route('dashboard.inventory.product-categories') }}" class="child-item {{ request()->routeIs('dashboard.inventory.product-categories') ? 'active' : '' }}">Categories</a>
                <a href="{{ route('dashboard.inventory.brands') }}" class="child-item {{ request()->routeIs('dashboard.inventory.brands') ? 'active' : '' }}">Brands</a>
                <a href="{{ route('dashboard.inventory.warranties') }}" class="child-item {{ request()->routeIs('dashboard.inventory.warranties') ? 'active' : '' }}">Warranties</a>
            </div>
        </div>

        {{-- Purchases --}}
        <div class="dropdown {{ $isPurchases ? 'open' : '' }}" id="dropdown-purchases">
            <div class="dropdown-toggle" onclick="toggleDropdown('dropdown-purchases')">
                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12"/><path d="M16 11l-4 4l-4-4"/><path d="M3 12a9 9 0 0 0 18 0"/></svg>
                Purchases
                <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6l6 6"/></svg>
            </div>
            <div class="dropdown-children">
                <a href="{{ route('dashboard.purchases.list-purchases') }}" class="child-item {{ request()->routeIs('dashboard.purchases.list-purchases') ? 'active' : '' }}">List Purchases</a>
                <a href="{{ route('dashboard.purchases.add-purchase') }}" class="child-item {{ request()->routeIs('dashboard.purchases.add-purchase') ? 'active' : '' }}">Add Purchase</a>
                <a href="{{ route('dashboard.purchases.list-purchase-return') }}" class="child-item {{ request()->routeIs('dashboard.purchases.list-purchase-return') ? 'active' : '' }}">List Purchase Return</a>
            </div>
        </div>

        {{-- Sell --}}
        <div class="dropdown {{ $isSell ? 'open' : '' }}" id="dropdown-sell">
            <div class="dropdown-toggle" onclick="toggleDropdown('dropdown-sell')">
                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v-12"/><path d="M16 7l-4-4l-4 4"/><path d="M3 12a9 9 0 0 0 18 0"/></svg>
                Sell
                <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6l6 6"/></svg>
            </div>
            <div class="dropdown-children">
                <a href="{{ route('dashboard.sell.all-sales') }}" class="child-item {{ request()->routeIs('dashboard.sell.all-sales') ? 'active' : '' }}">All Sales</a>
                <a href="{{ route('dashboard.sell.add-sale') }}" class="child-item {{ request()->routeIs('dashboard.sell.add-sale') ? 'active' : '' }}">Add Sale</a>
                <a href="{{ route('dashboard.sell.list-pos') }}" class="child-item {{ request()->routeIs('dashboard.sell.list-pos') ? 'active' : '' }}">List POS</a>
                <a href="{{ route('dashboard.sell.pos') }}" class="child-item {{ request()->routeIs('dashboard.sell.pos') ? 'active' : '' }}">POS</a>
                <a href="{{ route('dashboard.sell.add-draft') }}" class="child-item {{ request()->routeIs('dashboard.sell.add-draft') ? 'active' : '' }}">Add Draft</a>
                <a href="{{ route('dashboard.sell.list-drafts') }}" class="child-item {{ request()->routeIs('dashboard.sell.list-drafts') ? 'active' : '' }}">List Drafts</a>
                <a href="{{ route('dashboard.sell.add-quotation') }}" class="child-item {{ request()->routeIs('dashboard.sell.add-quotation') ? 'active' : '' }}">Add Quotation</a>
                <a href="{{ route('dashboard.sell.list-quotations') }}" class="child-item {{ request()->routeIs('dashboard.sell.list-quotations') ? 'active' : '' }}">List Quotations</a>
                <a href="{{ route('dashboard.sell.list-sell-return') }}" class="child-item {{ request()->routeIs('dashboard.sell.list-sell-return') ? 'active' : '' }}">List Sell Return</a>
                <a href="{{ route('dashboard.sell.shipments') }}" class="child-item {{ request()->routeIs('dashboard.sell.shipments') ? 'active' : '' }}">Shipments</a>
                <a href="{{ route('dashboard.sell.discounts') }}" class="child-item {{ request()->routeIs('dashboard.sell.discounts') ? 'active' : '' }}">Discounts</a>
                <a href="{{ route('dashboard.sell.import-sales') }}" class="child-item {{ request()->routeIs('dashboard.sell.import-sales') ? 'active' : '' }}">Import Sales</a>
            </div>
        </div>

        {{-- Stock Transfers --}}
        <div class="dropdown {{ $isStockTrans ? 'open' : '' }}" id="dropdown-stock-transfers">
            <div class="dropdown-toggle" onclick="toggleDropdown('dropdown-stock-transfers')">
                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0-4 0"/><path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0-4 0"/><path d="M5 17h-2v-4m-1-8h11v12m-4 0h6m4 0h2v-6h-8m0-5h5l3 5"/><path d="M3 9l4 0"/></svg>
                Stock Transfers
                <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6l6 6"/></svg>
            </div>
            <div class="dropdown-children">
                <a href="{{ route('dashboard.stock-transfer.list-stock-transfer') }}" class="child-item {{ request()->routeIs('dashboard.stock-transfer.list-stock-transfer') ? 'active' : '' }}">List Stock Transfers</a>
                <a href="{{ route('dashboard.stock-transfer.add-stock-transfer') }}" class="child-item {{ request()->routeIs('dashboard.stock-transfer.add-stock-transfer') ? 'active' : '' }}">Add Stock Transfer</a>
            </div>
        </div>

        {{-- Stock Adjustment --}}
        <div class="dropdown {{ $isStockAdj ? 'open' : '' }}" id="dropdown-stock-adjustment">
            <div class="dropdown-toggle" onclick="toggleDropdown('dropdown-stock-adjustment')">
                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6m-8 0a8 3 0 1 0 16 0a8 3 0 1 0-16 0"/><path d="M4 6v6a8 3 0 0 0 16 0v-6"/><path d="M4 12v6a8 3 0 0 0 16 0v-6"/></svg>
                Stock Adjustment
                <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6l6 6"/></svg>
            </div>
            <div class="dropdown-children">
                <a href="{{ route('dashboard.stock-adjustment.list-stock-adjustment') }}" class="child-item {{ request()->routeIs('dashboard.stock-adjustment.list-stock-adjustment') ? 'active' : '' }}">List Stock Adjustments</a>
                <a href="{{ route('dashboard.stock-adjustment.add-stock-adjustment') }}" class="child-item {{ request()->routeIs('dashboard.stock-adjustment.add-stock-adjustment') ? 'active' : '' }}">Add Stock Adjustment</a>
            </div>
        </div>

        {{-- Expenses --}}
        <div class="dropdown {{ $isExpenses ? 'open' : '' }}" id="dropdown-expenses">
            <div class="dropdown-toggle" onclick="toggleDropdown('dropdown-expenses')">
                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 21v-16a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16l-3-2l-2 2l-2-2l-2 2l-2-2l-3 2"/><path d="M14.8 8a2 2 0 0 0-1.8-1h-2a2 2 0 1 0 0 4h2a2 2 0 1 1 0 4h-2a2 2 0 0 1-1.8-1"/><path d="M12 6v10"/></svg>
                Expenses
                <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6l6 6"/></svg>
            </div>
            <div class="dropdown-children">
                <a href="{{ route('dashboard.expenses.list-expenses') }}" class="child-item {{ request()->routeIs('dashboard.expenses.list-expenses') ? 'active' : '' }}">List Expenses</a>
                <a href="{{ route('dashboard.expenses.add-expense') }}" class="child-item {{ request()->routeIs('dashboard.expenses.add-expense') ? 'active' : '' }}">Add Expense</a>
                <a href="{{ route('dashboard.expenses.expense-categories') }}" class="child-item {{ request()->routeIs('dashboard.expenses.expense-categories') ? 'active' : '' }}">Expense Categories</a>
            </div>
        </div>

        <div class="nav-section-label">Analytics</div>

        {{-- Reports --}}
        <div class="dropdown {{ $isReports ? 'open' : '' }}" id="dropdown-reports">
            <div class="dropdown-toggle" onclick="toggleDropdown('dropdown-reports')">
                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 5h-2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h5.697"/><path d="M18 14v4h4"/><path d="M18 11v-4a2 2 0 0 0-2-2h-2"/><path d="M8 3m0 2a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v0a2 2 0 0 1-2 2h-2a2 2 0 0 1-2-2z"/><path d="M18 18m-4 0a4 4 0 1 0 8 0a4 4 0 1 0-8 0"/><path d="M8 11h4"/><path d="M8 15h3"/></svg>
                Reports
                <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6l6 6"/></svg>
            </div>
            <div class="dropdown-children">
                <a href="{{ route('dashboard.reports.profit-loss-report') }}" class="child-item {{ request()->routeIs('dashboard.reports.profit-loss-report') ? 'active' : '' }}">Profit / Loss Report</a>
                <a href="{{ route('dashboard.reports.purchase-report') }}" class="child-item {{ request()->routeIs('dashboard.reports.purchase-report') ? 'active' : '' }}">Purchase &amp; Sale</a>
                <a href="{{ route('dashboard.reports.sales-report') }}" class="child-item {{ request()->routeIs('dashboard.reports.sales-report') ? 'active' : '' }}">Tax Report</a>
                <a href="{{ route('dashboard.reports.sales-report') }}" class="child-item {{ request()->routeIs('dashboard.reports.sales-report') ? 'active' : '' }}">Supplier &amp; Customer Report</a>
                <a href="{{ route('dashboard.reports.inventory-report') }}" class="child-item {{ request()->routeIs('dashboard.reports.inventory-report') ? 'active' : '' }}">Stock Report</a>
                <a href="{{ route('dashboard.reports.inventory-report') }}" class="child-item {{ request()->routeIs('dashboard.reports.inventory-report') ? 'active' : '' }}">Items Report</a>
                <a href="{{ route('dashboard.reports.purchase-report') }}" class="child-item {{ request()->routeIs('dashboard.reports.purchase-report') ? 'active' : '' }}">Product Purchase Report</a>
                <a href="{{ route('dashboard.reports.sales-report') }}" class="child-item {{ request()->routeIs('dashboard.reports.sales-report') ? 'active' : '' }}">Product Sell Report</a>
                <a href="{{ route('dashboard.reports.expense-report') }}" class="child-item {{ request()->routeIs('dashboard.reports.expense-report') ? 'active' : '' }}">Expense Report</a>
                <a href="{{ route('dashboard.reports.profit-loss-report') }}" class="child-item {{ request()->routeIs('dashboard.reports.profit-loss-report') ? 'active' : '' }}">Sales Representative Report</a>
                <a href="{{ route('dashboard.reports.sales-report') }}" class="child-item {{ request()->routeIs('dashboard.reports.sales-report') ? 'active' : '' }}">Activity Log</a>
            </div>
        </div>

        <div class="nav-section-label">System</div>

        {{-- Notification Templates --}}
        <a href="{{ route('dashboard.notification-templates') }}" class="nav-item {{ $isNotifTpl ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-14a2 2 0 0 1-2-2v-10z"/><path d="M3 7l9 6l9-6"/></svg>
            Notification Templates
        </a>

        {{-- Settings --}}
        <div class="dropdown {{ $isSettings ? 'open' : '' }}" id="dropdown-settings">
            <div class="dropdown-toggle" onclick="toggleDropdown('dropdown-settings')">
                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 0 0-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 0 0-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 0 0-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 0 0-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 0 0 1.066-2.573c-.94-1.543.826-3.31 2.37-2.37c1 .608 2.296.07 2.572-1.065z"/><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0-6 0"/></svg>
                Settings
                <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6l6 6"/></svg>
            </div>
            <div class="dropdown-children">
                <a href="{{ route('dashboard.settings.general') }}" class="child-item {{ request()->routeIs('dashboard.settings.general') ? 'active' : '' }}">Business Settings</a>
                <a href="{{ route('dashboard.settings.business-location') }}" class="child-item {{ request()->routeIs('dashboard.settings.business-location') ? 'active' : '' }}">Business Locations</a>
                <a href="{{ route('dashboard.settings.invoice-settings') }}" class="child-item {{ request()->routeIs('dashboard.settings.invoice-settings') ? 'active' : '' }}">Invoice Settings</a>
                <a href="{{ route('dashboard.settings.barcode-settings') }}" class="child-item {{ request()->routeIs('dashboard.settings.barcode-settings') ? 'active' : '' }}">Barcode Settings</a>
                <a href="{{ route('dashboard.settings.tax-rates') }}" class="child-item {{ request()->routeIs('dashboard.settings.tax-rates') ? 'active' : '' }}">Tax Rates</a>
            </div>
        </div>

    </div>{{-- /sidebar-content --}}

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

{{-- ═══════════════════════════  MAIN  ═══════════════════════════ --}}
<div class="main-wrap" id="main-wrap">

    {{-- Top Header --}}
    <header class="top-header">
        <div class="flex items-center gap-3">
            {{-- Mobile Sidebar Toggle --}}
            <button class="md:hidden p-1.5 rounded-lg hover:bg-slate-100" onclick="document.getElementById('sidebar').classList.toggle('open')">
                <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            
            {{-- Desktop Sidebar Collapse --}}
            <button class="hidden lg:block p-1.5 rounded-lg hover:bg-slate-100" onclick="document.getElementById('sidebar').classList.toggle('collapsed')">
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
                <div class="header-dropdown-menu" style="min-width:180px;">
                    <a href="#" class="header-dropdown-item">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 5m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z"/><path d="M16 3l0 4"/><path d="M8 3l0 4"/><path d="M4 11l16 0"/><path d="M11 15l0 3"/><path d="M12 15l0 3"/></svg>
                        Calendar
                    </a>
                    <a href="#" class="header-dropdown-item">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z"/><path d="M9 12l2 2l4 -4"/></svg>
                        Add To Do
                    </a>
                    <a href="#" class="header-dropdown-item">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/><path d="M12 17l0 .01"/><path d="M12 13.5a1.5 1.5 0 0 1 1 -1.5a2.6 2.6 0 1 0 -3 -4"/></svg>
                        Application Tour
                    </a>
                </div>
            </div>

            {{-- Calculator Button --}}
            <button class="notif-btn" title="Calculator">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 3m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z"/><path d="M8 7m0 1a1 1 0 0 1 1 -1h6a1 1 0 0 1 1 1v1a1 1 0 0 1 -1 1h-6a1 1 0 0 1 -1 -1z"/><path d="M8 14l0 .01"/><path d="M12 14l0 .01"/><path d="M16 14l0 .01"/><path d="M8 17l0 .01"/><path d="M12 17l0 .01"/><path d="M16 17l0 .01"/></svg>
            </button>

            {{-- POS Button --}}
            <a href="{{ route('dashboard.sell.pos') }}" class="btn btn-primary" style="gap:0.5rem;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"/><path d="M14 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"/><path d="M4 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"/><path d="M14 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"/></svg>
                <span class="hidden md:inline">POS</span>
            </a>

            {{-- Today's Profit Button --}}
            <button class="notif-btn" title="Today's Profit">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"/><path d="M3 6m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z"/><path d="M18 12l.01 0"/><path d="M6 12l.01 0"/></svg>
            </button>

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
                    <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}</div>
                    <span class="hidden md:block text-xs font-semibold text-slate-700">{{ Auth::user()->name ?? 'Admin' }}</span>
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
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="header-dropdown-item" style="width:100%;text-align:left;background:none;border:none;cursor:pointer;padding:0.6rem 1rem;">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2"/><path d="M9 12h12l-3 -3"/><path d="M18 15l3 -3"/></svg>
                            Sign Out
                        </button>
                    </form>
                    <a href="{{ route('dashboard.settings.general') }}" class="header-dropdown-item" style="padding:0.6rem 1rem;">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 0 0-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 0 0-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 0 0-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 0 0-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 0 0 1.066-2.573c-.94-1.543.826-3.31 2.37-2.37c1 .608 2.296.07 2.572-1.065z"/><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0-6 0"/></svg>
                        Settings
                    </a>
                    <div class="header-dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="header-dropdown-item" style="width:100%; border:none; background:none; color:#e03057; cursor:pointer; padding:0.6rem 1rem;">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    {{-- Page Content --}}
    @yield('content')

</div>{{-- /main-wrap --}}

{{-- ══ TOAST CONTAINER ══════════════════════════════════ --}}
<div id="toast-container"></div>

{{-- ══ CONFIRM DIALOG ═══════════════════════════════════ --}}
<div class="confirm-overlay" id="confirm-overlay">
    <div class="confirm-box">
        <div class="confirm-icon">
            <svg width="28" height="28" fill="none" stroke="#e03057" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
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
        headers: { 'Content-Type':'application/json', 'Accept':'application/json', 'X-CSRF-TOKEN': CSRF },
        ...opts,
        headers: { 'Content-Type':'application/json', 'Accept':'application/json', 'X-CSRF-TOKEN': CSRF, ...(opts.headers||{}) }
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
        closeConfirm();
    }
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
</script>
@yield('scripts')

</body>
</html>
