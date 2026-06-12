<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Point of Sale — {{ config('app.name', 'MannaPOS') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('icons8-dynamics-365-100.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter','ui-sans-serif','system-ui','sans-serif'] }
                }
            }
        }
    </script>
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
        .pos-content { padding: 1.75rem 2rem; }
        .pos-grid { display: grid; grid-template-columns: 1fr 380px; gap: 1.5rem; }
        .products-section { background: #fff; border-radius: 14px; border: 1px solid #e9edf5; padding: 1.5rem; }
        .cart-section { background: #fff; border-radius: 14px; border: 1px solid #e9edf5; padding: 1.5rem; }
        .section-title { font-size: 1rem; font-weight: 700; color: #0f172a; margin-bottom: 1rem; }
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 1rem; }
        .product-card { background: #f8fafc; border: 1px solid #e9edf5; border-radius: 10px; padding: 1rem; cursor: pointer; transition: all 0.2s; }
        .product-card:hover { border-color: #e03057; transform: translateY(-2px); }
        .product-name { font-size: 0.85rem; font-weight: 600; color: #0f172a; margin-bottom: 0.25rem; }
        .product-price { font-size: 0.9rem; font-weight: 700; color: #e03057; }
        .cart-item { display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; border-bottom: 1px solid #f1f5f9; }
        .cart-total { margin-top: 1.5rem; padding-top: 1rem; border-top: 2px solid #e9edf5; }
        .total-amount { font-size: 1.5rem; font-weight: 800; color: #0f172a; }
        .btn-checkout { width: 100%; padding: 1rem; background: #10B981; color: white; border: none; border-radius: 10px; font-weight: 600; font-size: 1rem; cursor: pointer; transition: background 0.2s; }
        .btn-checkout:hover { background: #059669; }
    </style>
</head>
<body class="font-sans antialiased">

{{-- Sidebar --}}
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
        <div class="nav-group-label">Sales</div>
        <a href="{{ route('dashboard.sales.pos') }}" class="nav-item active">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.5 6h13M10 19a1 1 0 100 2 1 1 0 000-2zm7 0a1 1 0 100 2 1 1 0 000-2z"/></svg>
            Point of Sale
        </a>
        <a href="{{ route('dashboard.sales.transactions') }}" class="nav-item">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            Transactions
        </a>
        <a href="{{ route('dashboard.sales.receipts') }}" class="nav-item">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Receipts
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
        <h1 class="page-title">Point of Sale</h1>
        <div class="user-chip">
            <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}</div>
            <div>
                <div class="user-name">{{ Auth::user()->name ?? 'Admin' }}</div>
                <div class="user-role">{{ ucfirst(Auth::user()->role ?? 'user') }}</div>
            </div>
        </div>
    </header>

    <div class="pos-content">
        <div class="pos-grid">
            <div class="products-section">
                <div class="section-title">Products</div>
                <div class="product-grid">
                    <div class="product-card">
                        <div class="product-name">Coca Cola 500ml</div>
                        <div class="product-price">TSh 1,500</div>
                    </div>
                    <div class="product-card">
                        <div class="product-name">Bread Loaf</div>
                        <div class="product-price">TSh 2,000</div>
                    </div>
                    <div class="product-card">
                        <div class="product-name">Sugar 1kg</div>
                        <div class="product-price">TSh 3,500</div>
                    </div>
                    <div class="product-card">
                        <div class="product-name">Milk 1L</div>
                        <div class="product-price">TSh 2,500</div>
                    </div>
                </div>
            </div>
            <div class="cart-section">
                <div class="section-title">Cart</div>
                <div class="cart-item">
                    <div>
                        <div class="product-name">Coca Cola 500ml</div>
                        <div class="text-sm text-gray-500">x2</div>
                    </div>
                    <div class="product-price">TSh 3,000</div>
                </div>
                <div class="cart-total">
                    <div class="flex justify-between items-center">
                        <span class="font-semibold">Total</span>
                        <span class="total-amount">TSh 3,000</span>
                    </div>
                </div>
                <button class="btn-checkout mt-4">Checkout</button>
            </div>
        </div>
    </div>
</div>

</body>
</html>
