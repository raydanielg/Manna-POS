<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Settings — {{ config('app.name', 'MannaPOS') }}</title>
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
        .settings-card { background: #fff; border-radius: 14px; border: 1px solid #e9edf5; padding: 1.5rem; margin-bottom: 1.25rem; }
        .section-title { font-size: 1rem; font-weight: 700; color: #0f172a; margin-bottom: 1rem; }
        .form-group { margin-bottom: 1rem; }
        .form-label { font-size: 0.85rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem; display: block; }
        .form-input { width: 100%; padding: 0.6rem 0.8rem; border: 1px solid #e9edf5; border-radius: 8px; font-size: 0.85rem; }
        .form-input:focus { outline: none; border-color: #e03057; }
        .btn-save { padding: 0.6rem 1.5rem; background: #10B981; color: white; border: none; border-radius: 8px; font-size: 0.85rem; font-weight: 600; cursor: pointer; }
        .btn-save:hover { background: #059669; }
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
        <div class="nav-group-label">Settings</div>
        <a href="#" class="nav-item active">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3"/></svg>
            Settings
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
        <h1 class="page-title">Settings</h1>
        <div class="user-chip">
            <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}</div>
            <div>
                <div class="user-name">{{ Auth::user()->name ?? 'Admin' }}</div>
                <div class="user-role">{{ ucfirst(Auth::user()->role ?? 'user') }}</div>
            </div>
        </div>
    </header>

    <div class="content">
        <div class="settings-card">
            <div class="section-title">Business Information</div>
            <div class="form-group">
                <label class="form-label">Business Name</label>
                <input type="text" class="form-input" placeholder="Enter business name">
            </div>
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" class="form-input" placeholder="Enter email">
            </div>
            <div class="form-group">
                <label class="form-label">Phone</label>
                <input type="tel" class="form-input" placeholder="Enter phone number">
            </div>
            <button class="btn-save">Save Changes</button>
        </div>

        <div class="settings-card">
            <div class="section-title">Tax Settings</div>
            <div class="form-group">
                <label class="form-label">Tax Label</label>
                <input type="text" class="form-input" placeholder="e.g., VAT, GST">
            </div>
            <div class="form-group">
                <label class="form-label">Tax Rate (%)</label>
                <input type="number" class="form-input" placeholder="Enter tax rate">
            </div>
            <button class="btn-save">Save Changes</button>
        </div>

        <div class="settings-card">
            <div class="section-title">Account Settings</div>
            <div class="form-group">
                <label class="form-label">Current Password</label>
                <input type="password" class="form-input" placeholder="Enter current password">
            </div>
            <div class="form-group">
                <label class="form-label">New Password</label>
                <input type="password" class="form-input" placeholder="Enter new password">
            </div>
            <div class="form-group">
                <label class="form-label">Confirm Password</label>
                <input type="password" class="form-input" placeholder="Confirm new password">
            </div>
            <button class="btn-save">Update Password</button>
        </div>
    </div>
</div>

</body>
</html>
