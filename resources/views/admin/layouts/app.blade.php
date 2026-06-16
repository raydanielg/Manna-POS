<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') — {{ config('app.name', 'MannaPOS') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('icons8-dynamics-365-100.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #f1f4fb; font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }
        body.overflow-hidden { overflow: hidden; }
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
        .sidebar-logo { padding: 1.1rem 1.25rem 1rem; border-bottom: 1px solid #f1f5f9; flex-shrink: 0; }
        .sidebar-content { flex: 1; padding: 0.6rem 0.75rem 0.5rem; overflow-y: auto; overflow-x: hidden; }

        .nav-section-label {
            font-size: 0.6rem; font-weight: 800; letter-spacing: 0.14em;
            text-transform: uppercase; color: #b0b8cc;
            padding: 1rem 0.5rem 0.3rem; user-select: none;
        }
        .nav-item {
            display: flex; align-items: center; gap: 0.7rem;
            padding: 0.52rem 0.75rem; font-size: 0.82rem; font-weight: 500;
            color: #4b5675; border-radius: 8px;
            cursor: pointer; text-decoration: none;
            transition: background 0.15s, color 0.15s;
            white-space: nowrap; margin-bottom: 1px;
        }
        .nav-item:hover { background: #f6f7fb; color: #0f172a; }
        .nav-item svg { width: 17px; height: 17px; flex-shrink: 0; color: #94a3b8; transition: color 0.15s; }
        .nav-item:hover svg { color: #475569; }
        .nav-item.active { background: #fff0f3; color: #e03057; font-weight: 600; }
        .nav-item.active svg { color: #e03057; }

        .dropdown { margin-bottom: 1px; }
        .dropdown-toggle {
            display: flex; align-items: center; gap: 0.7rem;
            padding: 0.52rem 0.75rem; font-size: 0.82rem; font-weight: 500;
            color: #4b5675; border-radius: 8px; cursor: pointer;
            transition: background 0.15s, color 0.15s;
            white-space: nowrap; user-select: none;
        }
        .dropdown-toggle:hover { background: #f6f7fb; color: #0f172a; }
        .dropdown-toggle svg:first-child { width: 17px; height: 17px; flex-shrink: 0; color: #94a3b8; }
        .dropdown-toggle:hover svg:first-child { color: #475569; }
        .dropdown-toggle .chevron { margin-left: auto; width: 14px; height: 14px; color: #c4cad8; transition: transform 0.25s; flex-shrink: 0; }
        .dropdown.open .dropdown-toggle { color: #0f172a; background: #f6f7fb; }
        .dropdown.open .dropdown-toggle svg:first-child { color: #475569; }
        .dropdown.open .dropdown-toggle .chevron { transform: rotate(90deg); color: #94a3b8; }

        .dropdown-children { display: none; position: relative; padding: 0.3rem 0 0.5rem 2.5rem; margin-top: 2px; }
        .dropdown.open .dropdown-children { display: block; }
        .dropdown-children::before {
            content: ''; position: absolute; left: 1.3rem; top: 0; bottom: 0;
            width: 1.5px; background: linear-gradient(to bottom, #e2e8f0, transparent); border-radius: 2px;
        }
        .dropdown-children .child-item {
            display: flex; align-items: center;
            font-size: 0.8rem; font-weight: 500; color: #64748b;
            padding: 0.38rem 0.5rem; border-radius: 6px;
            transition: background 0.15s, color 0.15s;
            cursor: pointer; text-decoration: none; white-space: nowrap;
        }
        .dropdown-children .child-item::before {
            content: ''; width: 5px; height: 5px; border-radius: 50%;
            background: #d1d9e6; margin-right: 0.6rem; flex-shrink: 0; transition: background 0.15s;
        }
        .dropdown-children .child-item:hover { background: #f6f7fb; color: #0f172a; }
        .dropdown-children .child-item:hover::before { background: #e03057; }
        .dropdown-children .child-item.active { color: #e03057; font-weight: 600; background: #fff0f3; }
        .dropdown-children .child-item.active::before { background: #e03057; }

        .sidebar-bottom { margin-top: auto; padding: 0.75rem; border-top: 1px solid #f1f5f9; flex-shrink: 0; }
        .sign-out-btn {
            display: flex; align-items: center; gap: 0.65rem; padding: 0.55rem 0.75rem;
            font-size: 0.82rem; font-weight: 600; color: #e03057;
            width: 100%; border-radius: 8px; background: none; border: none; cursor: pointer; transition: background 0.15s;
        }
        .sign-out-btn:hover { background: #fff0f3; }
        .sign-out-btn svg { width: 17px; height: 17px; flex-shrink: 0; }

        .main-wrap { margin-left: 240px; min-height: 100vh; display: flex; flex-direction: column; }
        .top-header {
            background: #fff; border-bottom: 1px solid #e9edf5; height: 60px;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 2rem; position: sticky; top: 0; z-index: 30;
        }
        .page-title { font-size: 1.3rem; font-weight: 800; color: #0f172a; letter-spacing: -0.02em; }
        .header-right { display: flex; align-items: center; gap: 0.75rem; }
        .notif-btn {
            position: relative; width: 36px; height: 36px; border-radius: 10px;
            background: #f8fafc; border: 1px solid #e9edf5;
            display: flex; align-items: center; justify-content: center; cursor: pointer; transition: background 0.15s;
        }
        .notif-btn:hover { background: #f1f5f9; }
        .user-chip {
            display: flex; align-items: center; gap: 0.6rem;
            padding: 0.35rem 0.75rem 0.35rem 0.4rem; border-radius: 12px;
            background: #f8fafc; border: 1px solid #e9edf5; cursor: pointer; transition: background 0.15s;
        }
        .user-chip:hover { background: #f1f5f9; }
        .user-avatar {
            width: 30px; height: 30px; border-radius: 8px;
            background: linear-gradient(135deg,#2563eb,#7c3aed);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 0.8rem; color: #fff; flex-shrink: 0;
        }

        .header-dropdown { position: relative; }
        .header-dropdown-menu {
            display: none; position: absolute; top: calc(100% + 12px); right: 0;
            min-width: 280px; background: #fff; border-radius: 12px;
            border: 1px solid #e9edf5; box-shadow: 0 4px 20px rgba(15,23,42,0.08);
            z-index: 50; overflow: hidden;
        }
        .header-dropdown.open .header-dropdown-menu { display: block; animation: slideDown 0.2s ease; }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }
        .header-dropdown-header { padding: 1rem 1.25rem; border-bottom: 1px solid #f1f5f9; background: #fafbff; }
        .header-dropdown-item {
            display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem;
            font-size: 0.82rem; color: #475569; transition: background 0.15s;
            cursor: pointer; text-decoration: none;
        }
        .header-dropdown-item:hover { background: #f8fafc; color: #0f172a; }
        .header-dropdown-item svg { width: 16px; height: 16px; flex-shrink: 0; }
        .header-dropdown-item .material-icons { font-size: 16px; }
        .header-dropdown-divider { height: 1px; background: #f1f5f9; margin: 0.25rem 0; }

        .dash-content { padding: 1.75rem 2rem; flex: 1; }
        .page-card { background: #fff; border-radius: 14px; border: 1px solid #e9edf5; overflow: hidden; }
        .card-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 1rem 1.25rem; border-bottom: 1px solid #f1f5f9; gap: 1rem;
        }
        .card-title { font-size: 0.95rem; font-weight: 700; color: #0f172a; }
        .filters-row { display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap; }
        .search-wrap {
            display: flex; align-items: center; gap: 0.5rem;
            background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px;
            padding: 0.45rem 0.75rem; transition: border-color 0.15s;
        }
        .search-wrap:focus-within { border-color: #e03057; }
        .search-wrap svg { width: 16px; height: 16px; color: #94a3b8; flex-shrink: 0; }
        .search-wrap input { border: none; background: none; outline: none; font-size: 0.8rem; color: #0f172a; width: 180px; }
        .search-wrap input::placeholder { color: #94a3b8; }

        .btn {
            display: inline-flex; align-items: center; gap: 0.45rem;
            padding: 0.5rem 1rem; font-size: 0.78rem; font-weight: 600;
            border-radius: 10px; border: none; cursor: pointer;
            transition: all 0.15s; text-decoration: none; white-space: nowrap;
        }
        .btn-primary { background: #e03057; color: #fff; }
        .btn-primary:hover { background: #c41f44; }
        .btn-success { background: #16a34a; color: #fff; }
        .btn-success:hover { background: #15803d; }
        .btn-warning { background: #f59e0b; color: #fff; }
        .btn-warning:hover { background: #d97706; }
        .btn-danger { background: #dc2626; color: #fff; }
        .btn-danger:hover { background: #b91c1c; }
        .btn-secondary { background: #f1f5f9; color: #475569; }
        .btn-secondary:hover { background: #e2e8f0; }
        .btn-sm { padding: 0.35rem 0.7rem; font-size: 0.72rem; }
        .btn-xs { padding: 0.25rem 0.5rem; font-size: 0.68rem; border-radius: 6px; }

        .tbl { width: 100%; border-collapse: collapse; }
        .tbl th {
            font-size: 0.68rem; font-weight: 700; letter-spacing: 0.08em;
            text-transform: uppercase; color: #94a3b8;
            padding: 0.65rem 1.25rem; text-align: left; background: #fafbff;
            border-bottom: 1px solid #f1f5f9;
        }
        .tbl td { font-size: 0.8rem; color: #374151; padding: 0.65rem 1.25rem; border-top: 1px solid #f8fafc; }
        .tbl tr:hover td { background: #fafbff; }
        .tbl-empty { text-align: center; color: #94a3b8; font-size: 0.82rem; padding: 2.5rem 1rem; }

        .badge {
            display: inline-flex; align-items: center; font-size: 0.68rem; font-weight: 600;
            padding: 0.2rem 0.6rem; border-radius: 9999px;
        }
        .badge-success { background: #dcfce7; color: #16a34a; }
        .badge-pending { background: #fef9c3; color: #ca8a04; }
        .badge-info { background: #dbeafe; color: #2563eb; }
        .badge-danger { background: #fee2e2; color: #dc2626; }
        .badge-default { background: #f1f5f9; color: #64748b; }

        .modal-overlay {
            display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.5);
            z-index: 100; align-items: center; justify-content: center;
            padding: 1rem; backdrop-filter: blur(4px);
        }
        .modal-overlay.open { display: flex; }
        .modal {
            background: #fff; border-radius: 16px; width: 100%; max-width: 560px;
            max-height: 90vh; overflow-y: auto;
            box-shadow: 0 20px 60px rgba(15,23,42,0.15);
            animation: modalIn 0.2s ease;
        }
        @keyframes modalIn { from { opacity: 0; transform: scale(0.95) translateY(10px); } to { opacity: 1; transform: scale(1) translateY(0); } }
        .modal-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 1.1rem 1.5rem; border-bottom: 1px solid #f1f5f9;
        }
        .modal-title { font-size: 0.95rem; font-weight: 700; color: #0f172a; }
        .modal-close {
            width: 28px; height: 28px; border-radius: 8px; border: none;
            background: #f1f5f9; color: #64748b; font-size: 0.9rem;
            cursor: pointer; display: flex; align-items: center; justify-content: center;
            transition: background 0.15s;
        }
        .modal-close:hover { background: #e2e8f0; color: #0f172a; }
        .modal-body { padding: 1.25rem 1.5rem; }
        .modal-footer {
            display: flex; align-items: center; justify-content: flex-end; gap: 0.75rem;
            padding: 1rem 1.5rem; border-top: 1px solid #f1f5f9;
        }

        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; font-size: 0.78rem; font-weight: 600; color: #374151; margin-bottom: 0.35rem; }
        .form-control {
            width: 100%; padding: 0.55rem 0.75rem; font-size: 0.82rem;
            border: 1px solid #e2e8f0; border-radius: 10px; background: #fff;
            color: #0f172a; transition: border-color 0.15s; outline: none;
        }
        .form-control:focus { border-color: #e03057; box-shadow: 0 0 0 3px rgba(224,48,87,0.1); }
        .form-control.is-invalid { border-color: #dc2626; }
        .invalid-feedback { font-size: 0.72rem; color: #dc2626; margin-top: 0.25rem; display: none; }
        .is-invalid ~ .invalid-feedback { display: block; }
        select.form-control { appearance: auto; }
        textarea.form-control { resize: vertical; min-height: 80px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 0 1rem; }

        .actions-cell { display: flex; gap: 0.35rem; flex-wrap: nowrap; }

        @media (max-width: 1200px) {
            .sidebar { width: 220px; min-width: 220px; }
            .main-wrap { margin-left: 220px; }
        }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); transition: transform 0.3s ease; }
            .sidebar.open { transform: translateX(0); }
            .main-wrap { margin-left: 0; }
            .form-row { grid-template-columns: 1fr; }
            .card-header { flex-direction: column; align-items: stretch; }
            .filters-row { flex-direction: column; }
            .search-wrap input { width: 100%; }
        }
        .sidebar-backdrop {
            display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.4);
            z-index: 35; backdrop-filter: blur(2px);
        }
        .sidebar-backdrop.open { display: block; }
        .sidebar-close {
            display: none; position: absolute; top: 0.75rem; right: 0.75rem;
            width: 28px; height: 28px; border-radius: 8px; border: none;
            background: #f1f5f9; color: #64748b; font-size: 1rem;
            cursor: pointer; align-items: center; justify-content: center;
            transition: background 0.15s; z-index: 1;
        }
        .sidebar-close:hover { background: #e2e8f0; color: #0f172a; }
        @media (max-width: 768px) {
            .sidebar-close { display: flex; }
        }
    </style>
</head>
<body>

{{-- ═══ SIDEBAR BACKDROP ═══ --}}
<div class="sidebar-backdrop" id="sidebar-backdrop" onclick="closeSidebar()"></div>

{{-- ═══ SIDEBAR ═══ --}}
<aside class="sidebar" id="sidebar">
    <button class="sidebar-close" onclick="closeSidebar()">&times;</button>
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

    <div class="sidebar-content">
        @include('admin.layouts.sidebar')
    </div>

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

{{-- ═══ MAIN ═══ --}}
<div class="main-wrap">
    <header class="top-header">
        <div class="flex items-center gap-3">
            <button class="md:hidden p-1.5 rounded-lg hover:bg-slate-100" onclick="toggleSidebar()">
                <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <h1 class="page-title">@yield('page_title', 'Admin Dashboard')</h1>
        </div>
        <div class="header-right">
            <span class="hidden lg:inline-block text-xs text-slate-400 font-medium px-3 py-1.5 rounded-lg bg-slate-100">{{ now()->format('m/d/Y') }}</span>
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
                    <a href="{{ route('admin.profile') }}" class="header-dropdown-item" style="padding:0.6rem 1rem;">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/><path d="M12 10m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"/><path d="M6.168 18.849a4 4 0 0 1 3.832 -2.849h4a4 4 0 0 1 3.834 2.855"/></svg>
                        Profile
                    </a>
                    <div class="header-dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="header-dropdown-item" style="width:100%;border:none;background:none;color:#e03057;cursor:pointer;padding:0.6rem 1rem;">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2"/><path d="M9 12h12l-3 -3"/><path d="M18 15l3 -3"/></svg>
                            Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <div class="dash-content">
        @yield('content')
    </div>

    <footer class="text-center py-4 text-xs text-slate-400 border-t border-slate-200">
        &copy; {{ date('Y') }} {{ config('app.name', 'MannaPOS') }}. All rights reserved.
    </footer>
</div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebar-backdrop');
        sidebar.classList.toggle('open');
        backdrop.classList.toggle('open');
        document.body.classList.toggle('overflow-hidden');
    }
    function closeSidebar() {
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('sidebar-backdrop').classList.remove('open');
        document.body.classList.remove('overflow-hidden');
    }
    function toggleDropdown(id) {
        const dd = document.getElementById(id);
        const isOpen = dd.classList.contains('open');
        document.querySelectorAll('.sidebar .dropdown.open').forEach(function(d) { d.classList.remove('open'); });
        if (!isOpen) dd.classList.add('open');
    }
    function toggleHeaderDropdown(id) { document.getElementById(id).classList.toggle('open'); }
    document.addEventListener('click', function(e) {
        document.querySelectorAll('.header-dropdown.open').forEach(function(dd) {
            if (!dd.contains(e.target)) dd.classList.remove('open');
        });
    });
    function openModal(id) { document.getElementById(id).classList.add('open'); }
    function closeModal(id) { document.getElementById(id).classList.remove('open'); }
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') document.querySelectorAll('.modal-overlay.open').forEach(function(m) { m.classList.remove('open'); });
    });

    const API_CSRF = '{{ csrf_token() }}';
    async function apiFetch(url, opts = {}) {
        const config = {
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': API_CSRF, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            ...opts,
        };
        if (config.body && typeof config.body === 'object' && !(config.body instanceof FormData)) {
            config.body = JSON.stringify(config.body);
        }
        const res = await fetch(url, config);
        const data = await res.json();
        if (!res.ok) throw { status: res.status, data };
        return data;
    }

    function clearFormErrors(formId) {
        const form = document.getElementById(formId);
        if (!form) return;
        form.querySelectorAll('.is-invalid').forEach(e => e.classList.remove('is-invalid'));
        form.querySelectorAll('.invalid-feedback').forEach(e => { e.textContent = ''; e.style.display = 'none'; });
    }
    function showFormErrors(formId, errors) {
        const form = document.getElementById(formId);
        if (!form) return;
        for (const [field, msgs] of Object.entries(errors)) {
            const input = form.querySelector(`[name="${field}"]`);
            if (input) {
                input.classList.add('is-invalid');
                const feedback = input.closest('.form-group')?.querySelector('.invalid-feedback') || input.nextElementSibling;
                if (feedback && feedback.classList.contains('invalid-feedback')) {
                    feedback.textContent = Array.isArray(msgs) ? msgs[0] : msgs;
                    feedback.style.display = 'block';
                }
            }
        }
    }

    @yield('scripts')
</script>
</body>
</html>
