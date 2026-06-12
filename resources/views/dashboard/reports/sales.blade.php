<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sales Reports — {{ config('app.name', 'MannaPOS') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('icons8-dynamics-365-100.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: #f1f4fb; }
        .sidebar { width: 220px; min-width: 220px; height: 100vh; position: fixed; top: 0; left: 0; background: #fff; border-right: 1px solid #e9edf5; display: flex; flex-direction: column; z-index: 40; }
        .sidebar-logo { padding: 1.5rem; border-bottom: 1px solid #f1f5f9; }
        .nav-group-label { font-size: 0.62rem; font-weight: 700; letter-spacing: 0.14em; text-transform: uppercase; color: #94a3b8; padding: 1.1rem 1.25rem 0.35rem; }
        .nav-item { display: flex; align-items: center; gap: 0.65rem; padding: 0.52rem 1.25rem; font-size: 0.84rem; font-weight: 500; color: #475569; border-radius: 10px; margin: 0 0.5rem; cursor: pointer; text-decoration: none; transition: background 0.15s, color 0.15s; }
        .nav-item:hover { background: #f8fafc; color: #0f172a; }
        .nav-item.active { background: #fff0f3; color: #e03057; font-weight: 600; }
        .nav-item svg { width: 16px; height: 16px; flex-shrink: 0; opacity: 0.75; }
        .nav-item.active svg { opacity: 1; }
        .main-wrap { margin-left: 220px; min-height: 100vh; }
        .top-header { background: #fff; border-bottom: 1px solid #e9edf5; height: 60px; display: flex; align-items: center; justify-content: space-between; padding: 0 2rem; }
        .page-title { font-size: 1.3rem; font-weight: 800; color: #0f172a; }
        .content { padding: 1.75rem 2rem; }
        .report-card { background: #fff; border-radius: 14px; border: 1px solid #e9edf5; padding: 1.5rem; margin-bottom: 1.25rem; }
        .section-title { font-size: 1rem; font-weight: 700; color: #0f172a; margin-bottom: 1rem; }
        .stat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem; }
        .stat-card { background: #f8fafc; border-radius: 10px; padding: 1rem; }
        .stat-value { font-size: 1.5rem; font-weight: 800; color: #0f172a; }
        .stat-label { font-size: 0.75rem; color: #94a3b8; margin-top: 0.25rem; }
    </style>
</head>
<body class="font-sans antialiased">

<aside class="sidebar">
    <div class="sidebar-logo">
        <div class="flex items-center justify-center">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-600 to-violet-600 flex items-center justify-center">
                <img src="{{ asset('icons8-dynamics-365-96.png') }}" alt="Logo" class="w-6 h-6 object-contain brightness-0 invert">
            </div>
        </div>
    </div>
    <nav class="flex-1 py-2">
        <div class="nav-group-label">Main</div>
        <a href="{{ route('dashboard') }}" class="nav-item">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            Dashboard
        </a>
        <div class="nav-group-label">Reports</div>
        <a href="#" class="nav-item active">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            Sales Reports
        </a>
        <a href="#" class="nav-item">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/></svg>
            Inventory Reports
        </a>
    </nav>
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

<div class="main-wrap">
    <header class="top-header">
        <h1 class="page-title">Sales Reports</h1>
        <div class="user-chip">
            <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}</div>
            <div>
                <div class="user-name">{{ Auth::user()->name ?? 'Admin' }}</div>
                <div class="user-role">{{ ucfirst(Auth::user()->role ?? 'user') }}</div>
            </div>
        </div>
    </header>

    <div class="content">
        <div class="report-card">
            <div class="section-title">Overview</div>
            <div class="stat-grid">
                <div class="stat-card">
                    <div class="stat-value">TSh 0</div>
                    <div class="stat-label">Total Revenue</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">0</div>
                    <div class="stat-label">Total Orders</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">TSh 0</div>
                    <div class="stat-label">Avg Order Value</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">0</div>
                    <div class="stat-label">Customers</div>
                </div>
            </div>
        </div>
        <div class="report-card">
            <div class="section-title">Sales Trends</div>
            <div class="text-center text-gray-400 py-8">No sales data available yet.</div>
        </div>
    </div>
</div>

</body>
</html>
