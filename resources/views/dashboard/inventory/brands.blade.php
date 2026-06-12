<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Brands — {{ config('app.name', 'MannaPOS') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('icons8-dynamics-365-100.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: #f1f4fb; }
        .sidebar { width: 220px; min-width: 220px; height: 100vh; position: fixed; top: 0; left: 0; background: #fff; border-right: 1px solid #e9edf5; display: flex; flex-direction: column; z-index: 40; }
        .sidebar-logo { padding: 1.5rem; border-bottom: 1px solid #f1f5f9; }
        .sidebar-content { flex: 1; padding: 0.75rem 0.5rem; overflow-y: auto; }
        .nav-item { display: flex; align-items: center; gap: 0.75rem; padding: 0.6rem 0.75rem; font-size: 0.875rem; font-weight: 500; color: #475569; border-radius: 0.5rem; cursor: pointer; text-decoration: none; transition: all 0.2s; white-space: nowrap; }
        .nav-item:hover { background: #f8fafc; color: #0f172a; }
        .nav-item.active { background: #e9edf5; color: #0f172a; font-weight: 600; }
        .nav-item svg { width: 20px; height: 20px; flex-shrink: 0; color: #64748b; }
        .dropdown { margin-bottom: 0.25rem; }
        .dropdown-toggle { display: flex; align-items: center; gap: 0.75rem; padding: 0.6rem 0.75rem; font-size: 0.875rem; font-weight: 500; color: #475569; border-radius: 0.5rem; cursor: pointer; transition: all 0.2s; white-space: nowrap; }
        .dropdown-toggle:hover { background: #f8fafc; color: #0f172a; }
        .dropdown-toggle svg { width: 20px; height: 20px; flex-shrink: 0; color: #64748b; }
        .dropdown-toggle .chevron { margin-left: auto; width: 16px; height: 16px; color: #9ca3af; transition: transform 0.3s; }
        .dropdown.open .dropdown-toggle .chevron { transform: rotate(90deg); }
        .dropdown-children { display: none; position: relative; margin-top: 0.5rem; margin-bottom: 1rem; padding-left: 2.75rem; }
        .dropdown.open .dropdown-children { display: block; }
        .dropdown-children::before { content: ''; position: absolute; left: 1.25rem; top: 0; bottom: 0; width: 1px; background: #e5e7eb; }
        .dropdown-children .child-item { display: flex; font-size: 0.875rem; font-weight: 500; color: #64748b; padding: 0.35rem 0; transition: color 0.2s; cursor: pointer; text-decoration: none; white-space: nowrap; }
        .dropdown-children .child-item:hover { color: #0f172a; }
        .dropdown-children .child-item.active { color: #0f172a; font-weight: 600; }
        .dropdown-children .child-item + .child-item { margin-top: 0.875rem; }
        .sidebar-bottom { margin-top: auto; padding: 1rem 0.5rem 1.25rem; border-top: 1px solid #f1f5f9; }
        .sign-out-btn { display: flex; align-items: center; gap: 0.65rem; padding: 0.52rem 1.25rem; font-size: 0.84rem; font-weight: 600; color: #e03057; width: 100%; border-radius: 10px; background: none; border: none; cursor: pointer; transition: background 0.15s; }
        .sign-out-btn:hover { background: #fff0f3; }
        .sign-out-btn svg { width: 16px; height: 16px; }
        .main-wrap { margin-left: 220px; min-height: 100vh; }
        .top-header { background: #fff; border-bottom: 1px solid #e9edf5; height: 60px; display: flex; align-items: center; justify-content: space-between; padding: 0 2rem; }
        .page-title { font-size: 1.3rem; font-weight: 800; color: #0f172a; }
        .content { padding: 1.75rem 2rem; }
        .table-card { background: #fff; border-radius: 14px; border: 1px solid #e9edf5; padding: 1.5rem; }
        .section-title { font-size: 1rem; font-weight: 700; color: #0f172a; margin-bottom: 1rem; }
        .tbl { width: 100%; border-collapse: collapse; }
        .tbl th { font-size: 0.68rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: #94a3b8; padding: 0.6rem 1.25rem; text-align: left; }
        .tbl td { font-size: 0.8rem; color: #374151; padding: 0.65rem 1.25rem; border-top: 1px solid #f8fafc; }
        .btn-add { padding: 0.5rem 1rem; background: #10B981; color: white; border: none; border-radius: 8px; font-size: 0.85rem; font-weight: 600; cursor: pointer; }
        .btn-add:hover { background: #059669; }
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
    <div class="sidebar-content">
        <a href="{{ route('dashboard') }}" class="nav-item">
            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12l-2 0l9 -9l9 9l-2 0"/><path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7"/><path d="M10 12h4v4h-4z"/></svg>
            Home
        </a>
        <div class="dropdown open" id="dropdown-products">
            <div class="dropdown-toggle" onclick="toggleDropdown('dropdown-products')">
                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3l8 4.5v9l-8 4.5l-8 -4.5v-9l8 -4.5"/><path d="M12 12l8 -4.5"/><path d="M8.2 9.8l7.6 -4.6"/><path d="M12 12v9"/><path d="M12 12l-8 -4.5"/></svg>
                Products
                <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6l6 6"/></svg>
            </div>
            <div class="dropdown-children">
                <a href="#" class="child-item">List Products</a>
                <a href="#" class="child-item">Add Product</a>
                <a href="#" class="child-item">Update Price</a>
                <a href="#" class="child-item">Print Labels</a>
                <a href="#" class="child-item">Variations</a>
                <a href="#" class="child-item">Import Products</a>
                <a href="#" class="child-item">Import Opening Stock</a>
                <a href="#" class="child-item">Selling Price Group</a>
                <a href="#" class="child-item">Units</a>
                <a href="#" class="child-item">Categories</a>
                <a href="#" class="child-item active">Brands</a>
                <a href="#" class="child-item">Warranties</a>
            </div>
        </div>
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

<div class="main-wrap">
    <header class="top-header">
        <h1 class="page-title">Brands</h1>
        <div class="user-chip">
            <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}</div>
            <div>
                <div class="user-name">{{ Auth::user()->name ?? 'Admin' }}</div>
                <div class="user-role">{{ ucfirst(Auth::user()->role ?? 'user') }}</div>
            </div>
        </div>
    </header>

    <div class="content">
        <div class="table-card">
            <div class="flex justify-between items-center mb-4">
                <div class="section-title mb-0">All Brands</div>
                <button class="btn-add">+ Add Brand</button>
            </div>
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Brand Name</th>
                        <th>Description</th>
                        <th>Products Count</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="4" class="text-center text-gray-400 py-8">No brands yet.</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function toggleDropdown(dropdownId) {
    const dropdown = document.getElementById(dropdownId);
    dropdown.classList.toggle('open');
}
</script>

</body>
</html>
