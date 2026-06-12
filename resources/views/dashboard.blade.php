<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard — {{ config('app.name', 'MannaPOS') }}</title>
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

        /* Sidebar */
        .sidebar { width: 220px; min-width: 220px; height: 100vh; position: fixed; top: 0; left: 0; background: #fff; border-right: 1px solid #e9edf5; display: flex; flex-direction: column; z-index: 40; }
        .sidebar-logo { padding: 1.5rem; border-bottom: 1px solid #f1f5f9; }
        .sidebar-content { flex: 1; padding: 0.75rem 0.5rem; overflow-y: auto; }
        
        /* Nav Items */
        .nav-item { display: flex; align-items: center; gap: 0.75rem; padding: 0.6rem 0.75rem; font-size: 0.875rem; font-weight: 500; color: #475569; border-radius: 0.5rem; cursor: pointer; text-decoration: none; transition: all 0.2s; white-space: nowrap; }
        .nav-item:hover { background: #f8fafc; color: #0f172a; }
        .nav-item.active { background: #e9edf5; color: #0f172a; font-weight: 600; }
        .nav-item svg { width: 20px; height: 20px; flex-shrink: 0; color: #64748b; }
        .nav-item.active svg { color: #0f172a; }
        
        /* Dropdown */
        .dropdown { margin-bottom: 0.25rem; }
        .dropdown-toggle { display: flex; align-items: center; gap: 0.75rem; padding: 0.6rem 0.75rem; font-size: 0.875rem; font-weight: 500; color: #475569; border-radius: 0.5rem; cursor: pointer; transition: all 0.2s; white-space: nowrap; }
        .dropdown-toggle:hover { background: #f8fafc; color: #0f172a; }
        .dropdown-toggle svg { width: 20px; height: 20px; flex-shrink: 0; color: #64748b; }
        .dropdown-toggle .chevron { margin-left: auto; width: 16px; height: 16px; color: #9ca3af; transition: transform 0.3s; }
        .dropdown.open .dropdown-toggle .chevron { transform: rotate(90deg); }
        
        /* Dropdown Children */
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

        /* Main */
        .main-wrap { margin-left: 220px; min-height: 100vh; display: flex; flex-direction: column; }
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

        /* Content */
        .dash-content { padding: 1.75rem 2rem; flex: 1; }

        /* Collapsible Sections */
        .dash-section { background: #fff; border-radius: 14px; border: 1px solid #e9edf5; margin-bottom: 1.25rem; overflow: hidden; }
        .dash-section-header { display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.25rem; cursor: pointer; background: #fafbff; border-bottom: 1px solid #f1f5f9; transition: background 0.2s; }
        .dash-section-header:hover { background: #f8fafc; }
        .dash-section-title { font-size: 0.92rem; font-weight: 700; color: #0f172a; }
        .dash-section-icon { width: 20px; height: 20px; color: #94a3b8; transition: transform 0.3s; }
        .dash-section.collapsed .dash-section-icon { transform: rotate(-90deg); }
        .dash-section-content { padding: 1.25rem; transition: all 0.3s ease; }
        .dash-section.collapsed .dash-section-content { display: none; }

        /* KPI Cards */
        .kpi-grid { display: grid; grid-template-columns: repeat(6,1fr); gap: 1rem; margin-bottom: 1rem; }
        .kpi-grid-2 { display: grid; grid-template-columns: repeat(4,1fr); gap: 1rem; margin-bottom: 1.75rem; }
        .kpi-card { background: #fff; border-radius: 14px; padding: 1rem 1.1rem; border: 1px solid #e9edf5; display: flex; align-items: center; gap: 0.85rem; transition: box-shadow 0.2s, transform 0.2s; }
        .kpi-card:hover { box-shadow: 0 8px 24px rgba(15,23,42,0.08); transform: translateY(-2px); }
        .kpi-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .kpi-icon img { width: 24px; height: 24px; object-fit: contain; }
        .kpi-val { font-size: 1.25rem; font-weight: 800; color: #0f172a; line-height: 1; letter-spacing: -0.02em; }
        .kpi-label { font-size: 0.72rem; color: #94a3b8; margin-top: 0.2rem; font-weight: 500; }

        /* Charts row */
        .charts-row { display: grid; grid-template-columns: 1fr 320px; gap: 1.25rem; margin-bottom: 1.75rem; }
        .chart-card { background: #fff; border-radius: 14px; border: 1px solid #e9edf5; padding: 1.4rem 1.5rem; }
        .chart-title { font-size: 0.92rem; font-weight: 700; color: #0f172a; margin-bottom: 1rem; }

        /* Tables row */
        .tables-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.75rem; }
        .table-card { background: #fff; border-radius: 14px; border: 1px solid #e9edf5; overflow: hidden; }
        .table-head { padding: 1rem 1.25rem 0.75rem; border-bottom: 1px solid #f1f5f9; }
        .table-title { font-size: 0.92rem; font-weight: 700; color: #0f172a; }
        .tbl { width: 100%; border-collapse: collapse; }
        .tbl th { font-size: 0.68rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: #94a3b8; padding: 0.6rem 1.25rem; text-align: left; }
        .tbl td { font-size: 0.8rem; color: #374151; padding: 0.65rem 1.25rem; border-top: 1px solid #f8fafc; }
        .tbl tr:hover td { background: #fafbff; }
        .tbl-empty { text-align: center; color: #94a3b8; font-size: 0.82rem; padding: 2.5rem 1rem; }
        .badge-success { font-size: 0.68rem; font-weight: 600; padding: 0.2rem 0.6rem; border-radius: 9999px; background: #dcfce7; color: #16a34a; }
        .badge-pending { font-size: 0.68rem; font-weight: 600; padding: 0.2rem 0.6rem; border-radius: 9999px; background: #fef9c3; color: #ca8a04; }
        .badge-info    { font-size: 0.68rem; font-weight: 600; padding: 0.2rem 0.6rem; border-radius: 9999px; background: #dbeafe; color: #2563eb; }

        /* Responsive */
        @media (max-width: 1200px) {
            .kpi-grid   { grid-template-columns: repeat(3,1fr); }
            .kpi-grid-2 { grid-template-columns: repeat(2,1fr); }
            .charts-row { grid-template-columns: 1fr; }
            .tables-row { grid-template-columns: 1fr; }
        }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); transition: transform 0.3s; }
            .sidebar.open { transform: translateX(0); }
            .main-wrap { margin-left: 0; }
            .kpi-grid   { grid-template-columns: repeat(2,1fr); }
            .kpi-grid-2 { grid-template-columns: repeat(2,1fr); }
        }
    </style>
</head>
<body class="font-sans antialiased">

{{-- ══════════════════════════════════════════════════════
     SIDEBAR
══════════════════════════════════════════════════════ --}}
<aside class="sidebar" id="sidebar">

    {{-- Logo --}}
    <div class="sidebar-logo">
        <div class="flex items-center justify-center">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-600 to-violet-600 flex items-center justify-center flex-shrink-0">
                <img src="{{ asset('icons8-dynamics-365-96.png') }}" alt="Logo" class="w-6 h-6 object-contain brightness-0 invert">
            </div>
        </div>
    </div>

    {{-- Nav --}}
    <div class="sidebar-content">

        {{-- Home --}}
        <a href="{{ route('dashboard') }}" class="nav-item active">
            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12l-2 0l9 -9l9 9l-2 0"/><path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7"/><path d="M10 12h4v4h-4z"/></svg>
            Home
        </a>

        {{-- User Management Dropdown --}}
        <div class="dropdown" id="dropdown-user">
            <div class="dropdown-toggle" onclick="toggleDropdown('dropdown-user')">
                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"/><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/><path d="M21 21v-2a4 4 0 0 0 -3 -3.85"/></svg>
                User Management
                <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6l6 6"/></svg>
            </div>
            <div class="dropdown-children">
                <a href="#" class="child-item">Users</a>
                <a href="#" class="child-item">Roles</a>
                <a href="#" class="child-item">Sales Commission Agents</a>
            </div>
        </div>

        {{-- Contacts Dropdown --}}
        <div class="dropdown" id="dropdown-contacts">
            <div class="dropdown-toggle" onclick="toggleDropdown('dropdown-contacts')">
                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 6v12a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2z"/><path d="M10 16h6"/><path d="M13 11m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/><path d="M4 8h3"/><path d="M4 12h3"/><path d="M4 16h3"/></svg>
                Contacts
                <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6l6 6"/></svg>
            </div>
            <div class="dropdown-children">
                <a href="#" class="child-item">Suppliers</a>
                <a href="#" class="child-item">Customers</a>
                <a href="#" class="child-item">Customer Groups</a>
                <a href="#" class="child-item">Import Contacts</a>
            </div>
        </div>

        {{-- Products Dropdown --}}
        <div class="dropdown" id="dropdown-products">
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
                <a href="#" class="child-item">Brands</a>
                <a href="#" class="child-item">Warranties</a>
            </div>
        </div>

        {{-- Purchases Dropdown --}}
        <div class="dropdown" id="dropdown-purchases">
            <div class="dropdown-toggle" onclick="toggleDropdown('dropdown-purchases')">
                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12"/><path d="M16 11l-4 4l-4 -4"/><path d="M3 12a9 9 0 0 0 18 0"/></svg>
                Purchases
                <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6l6 6"/></svg>
            </div>
            <div class="dropdown-children">
                <a href="#" class="child-item">List Purchases</a>
                <a href="#" class="child-item">Add Purchase</a>
                <a href="#" class="child-item">List Purchase Return</a>
            </div>
        </div>

        {{-- Sell Dropdown --}}
        <div class="dropdown" id="dropdown-sell">
            <div class="dropdown-toggle" onclick="toggleDropdown('dropdown-sell')">
                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v-12"/><path d="M16 7l-4 -4l-4 4"/><path d="M3 12a9 9 0 0 0 18 0"/></svg>
                Sell
                <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6l6 6"/></svg>
            </div>
            <div class="dropdown-children">
                <a href="#" class="child-item">All Sales</a>
                <a href="#" class="child-item">Add Sale</a>
                <a href="#" class="child-item">List POS</a>
                <a href="#" class="child-item">POS</a>
                <a href="#" class="child-item">Add Draft</a>
                <a href="#" class="child-item">List Drafts</a>
                <a href="#" class="child-item">Add Quotation</a>
                <a href="#" class="child-item">List Quotations</a>
                <a href="#" class="child-item">List Sell Return</a>
                <a href="#" class="child-item">Shipments</a>
                <a href="#" class="child-item">Discounts</a>
                <a href="#" class="child-item">Import Sales</a>
            </div>
        </div>

        {{-- Stock Transfers Dropdown --}}
        <div class="dropdown" id="dropdown-stock-transfers">
            <div class="dropdown-toggle" onclick="toggleDropdown('dropdown-stock-transfers')">
                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/><path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/><path d="M5 17h-2v-4m-1 -8h11v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5"/><path d="M3 9l4 0"/></svg>
                Stock Transfers
                <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6l6 6"/></svg>
            </div>
            <div class="dropdown-children">
                <a href="#" class="child-item">List Stock Transfers</a>
                <a href="#" class="child-item">Add Stock Transfer</a>
            </div>
        </div>

        {{-- Stock Adjustment Dropdown --}}
        <div class="dropdown" id="dropdown-stock-adjustment">
            <div class="dropdown-toggle" onclick="toggleDropdown('dropdown-stock-adjustment')">
                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6m-8 0a8 3 0 1 0 16 0a8 3 0 1 0 -16 0"/><path d="M4 6v6a8 3 0 0 0 16 0v-6"/><path d="M4 12v6a8 3 0 0 0 16 0v-6"/></svg>
                Stock Adjustment
                <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6l6 6"/></svg>
            </div>
            <div class="dropdown-children">
                <a href="#" class="child-item">List Stock Adjustments</a>
                <a href="#" class="child-item">Add Stock Adjustment</a>
            </div>
        </div>

        {{-- Expenses Dropdown --}}
        <div class="dropdown" id="dropdown-expenses">
            <div class="dropdown-toggle" onclick="toggleDropdown('dropdown-expenses')">
                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16l-3 -2l-2 2l-2 -2l-2 2l-2 -2l-3 2"/><path d="M14.8 8a2 2 0 0 0 -1.8 -1h-2a2 2 0 1 0 0 4h2a2 2 0 1 1 0 4h-2a2 2 0 0 1 -1.8 -1"/><path d="M12 6v10"/></svg>
                Expenses
                <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6l6 6"/></svg>
            </div>
            <div class="dropdown-children">
                <a href="#" class="child-item">List Expenses</a>
                <a href="#" class="child-item">Add Expense</a>
                <a href="#" class="child-item">Expense Categories</a>
            </div>
        </div>

        {{-- Reports Dropdown --}}
        <div class="dropdown" id="dropdown-reports">
            <div class="dropdown-toggle" onclick="toggleDropdown('dropdown-reports')">
                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h5.697"/><path d="M18 14v4h4"/><path d="M18 11v-4a2 2 0 0 0 -2 -2h-2"/><path d="M8 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"/><path d="M18 18m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"/><path d="M8 11h4"/><path d="M8 15h3"/></svg>
                Reports
                <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6l6 6"/></svg>
            </div>
            <div class="dropdown-children">
                <a href="#" class="child-item">Profit / Loss Report</a>
                <a href="#" class="child-item">Purchase & Sale</a>
                <a href="#" class="child-item">Tax Report</a>
                <a href="#" class="child-item">Supplier & Customer Report</a>
                <a href="#" class="child-item">Customer Groups Report</a>
                <a href="#" class="child-item">Stock Report</a>
                <a href="#" class="child-item">Stock Adjustment Report</a>
                <a href="#" class="child-item">Trending Products</a>
                <a href="#" class="child-item">Items Report</a>
                <a href="#" class="child-item">Product Purchase Report</a>
                <a href="#" class="child-item">Product Sell Report</a>
                <a href="#" class="child-item">Purchase Payment Report</a>
                <a href="#" class="child-item">Sell Payment Report</a>
                <a href="#" class="child-item">Expense Report</a>
                <a href="#" class="child-item">Register Report</a>
                <a href="#" class="child-item">Sales Representative Report</a>
                <a href="#" class="child-item">Activity Log</a>
            </div>
        </div>

        {{-- Notification Templates --}}
        <a href="#" class="nav-item">
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
                <a href="#" class="child-item">Business Settings</a>
                <a href="#" class="child-item">Business Locations</a>
                <a href="#" class="child-item">Invoice Settings</a>
                <a href="#" class="child-item">Barcode Settings</a>
                <a href="#" class="child-item">Receipt Printers</a>
                <a href="#" class="child-item">Tax Rates</a>
                <a href="#" class="child-item">Package Subscription</a>
            </div>
        </div>

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

{{-- ══════════════════════════════════════════════════════
     MAIN
══════════════════════════════════════════════════════ --}}
<div class="main-wrap">

    {{-- Top Header --}}
    <header class="top-header">
        <div class="flex items-center gap-3">
            {{-- Mobile toggle --}}
            <button class="md:hidden p-1.5 rounded-lg hover:bg-slate-100" onclick="document.getElementById('sidebar').classList.toggle('open')">
                <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <h1 class="page-title">Dashboard</h1>
        </div>
        <div class="header-right">
            {{-- Date --}}
            <span class="hidden sm:block text-xs text-slate-400 font-medium">{{ now()->format('D, M j Y') }}</span>
            {{-- Notifications --}}
            <div class="notif-btn">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                <span class="notif-dot"></span>
            </div>
            {{-- User --}}
            <div class="user-chip">
                <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}</div>
                <div>
                    <div class="user-name">{{ Auth::user()->name ?? 'Admin' }}</div>
                    <div class="user-role">{{ ucfirst(Auth::user()->role ?? 'user') }}</div>
                </div>
                <svg class="w-3.5 h-3.5 text-slate-400 ml-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </div>
        </div>
    </header>

    {{-- Dashboard Content --}}
    <div class="dash-content">

        {{-- ── KPI Section ─────────────────────────────── --}}
        <div class="dash-section" id="kpi-section">
            <div class="dash-section-header" onclick="toggleSection('kpi-section')">
                <div class="dash-section-title">Key Performance Indicators</div>
                <svg class="dash-section-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </div>
            <div class="dash-section-content">
                <div class="kpi-grid">

            <div class="kpi-card">
                <div class="kpi-icon" style="background:#eff6ff;">
                    <img src="https://cdn-icons-png.flaticon.com/512/3500/3500460.png" alt="Sales">
                </div>
                <div>
                    <div class="kpi-val">TSh 0</div>
                    <div class="kpi-label">Sales Today</div>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon" style="background:#f0fdf4;">
                    <img src="https://cdn-icons-png.flaticon.com/512/2489/2489756.png" alt="Orders">
                </div>
                <div>
                    <div class="kpi-val">0</div>
                    <div class="kpi-label">Orders Today</div>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon" style="background:#fdf4ff;">
                    <img src="https://cdn-icons-png.flaticon.com/512/1256/1256650.png" alt="Customers">
                </div>
                <div>
                    <div class="kpi-val">0</div>
                    <div class="kpi-label">Total Customers</div>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon" style="background:#fff7ed;">
                    <img src="https://cdn-icons-png.flaticon.com/512/4149/4149646.png" alt="New Customers">
                </div>
                <div>
                    <div class="kpi-val">0</div>
                    <div class="kpi-label">New Customers</div>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon" style="background:#ecfeff;">
                    <img src="https://cdn-icons-png.flaticon.com/512/3588/3588592.png" alt="Products">
                </div>
                <div>
                    <div class="kpi-val">0</div>
                    <div class="kpi-label">Total Products</div>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon" style="background:#fff1f2;">
                    <img src="https://cdn-icons-png.flaticon.com/512/564/564619.png" alt="Low Stock">
                </div>
                <div>
                    <div class="kpi-val text-red-500">3</div>
                    <div class="kpi-label">Low Stock Alerts</div>
                </div>
            </div>

        </div>

        <div class="kpi-grid-2">

            <div class="kpi-card">
                <div class="kpi-icon" style="background:#eff6ff;">
                    <img src="https://cdn-icons-png.flaticon.com/512/2920/2920277.png" alt="Revenue">
                </div>
                <div>
                    <div class="kpi-val">TSh 0</div>
                    <div class="kpi-label">Monthly Revenue (MTD)</div>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon" style="background:#f0fdf4;">
                    <img src="https://cdn-icons-png.flaticon.com/512/2645/2645890.png" alt="Payments">
                </div>
                <div>
                    <div class="kpi-val">TSh 0</div>
                    <div class="kpi-label">Payments (MTD)</div>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon" style="background:#fdf4ff;">
                    <img src="https://cdn-icons-png.flaticon.com/512/3064/3064197.png" alt="Active Users">
                </div>
                <div>
                    <div class="kpi-val">1</div>
                    <div class="kpi-label">Active Users</div>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon" style="background:#fff7ed;">
                    <img src="https://cdn-icons-png.flaticon.com/512/9195/9195785.png" alt="Avg Sale">
                </div>
                <div>
                    <div class="kpi-val">TSh 0</div>
                    <div class="kpi-label">Avg Transaction</div>
                </div>
            </div>

        </div>
            </div>
        </div>

        {{-- ── Charts Section ───────────────────────────── --}}
        <div class="dash-section" id="charts-section">
            <div class="dash-section-header" onclick="toggleSection('charts-section')">
                <div class="dash-section-title">Sales Analytics</div>
                <svg class="dash-section-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </div>
            <div class="dash-section-content">
                <div class="charts-row">

            {{-- Activity Trend --}}
            <div class="chart-card">
                <div class="flex items-center justify-between mb-1">
                    <div class="chart-title">Sales Trend (Last 14 Days)</div>
                    <div class="flex items-center gap-3 text-xs text-slate-400">
                        <span class="flex items-center gap-1"><span class="w-3 h-0.5 bg-blue-500 inline-block rounded"></span>Sales</span>
                        <span class="flex items-center gap-1"><span class="w-3 h-0.5 bg-green-500 inline-block rounded"></span>Orders</span>
                        <span class="flex items-center gap-1"><span class="w-3 h-0.5 bg-violet-400 inline-block rounded"></span>Customers</span>
                    </div>
                </div>
                <canvas id="trendChart" height="100"></canvas>
            </div>

            {{-- Distribution --}}
            <div class="chart-card flex flex-col">
                <div class="chart-title">Sales Distribution</div>
                <div class="flex-1 flex flex-col items-center justify-center">
                    <canvas id="donutChart" style="max-width:180px;max-height:180px;"></canvas>
                    <div class="text-xs text-slate-400 mt-4 font-medium" id="donut-no-data">No data yet</div>
                </div>
            </div>

        </div>
            </div>
        </div>

        {{-- ── Transactions Section ──────────────────────── --}}
        <div class="dash-section" id="transactions-section">
            <div class="dash-section-header" onclick="toggleSection('transactions-section')">
                <div class="dash-section-title">Recent Transactions</div>
                <svg class="dash-section-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </div>
            <div class="dash-section-content">
                <div class="table-head flex items-center justify-between">
                    <div class="table-title">Recent Transactions</div>
                    <a href="#" class="text-xs text-blue-600 font-semibold hover:underline">View all</a>
                </div>
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Ref</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="5" class="tbl-empty">No transactions yet.</td></tr>
                    </tbody>
                </table>
            </div>
            </div>
        </div>

        {{-- ── Customers Section ─────────────────────────── --}}
        <div class="dash-section" id="customers-section">
            <div class="dash-section-header" onclick="toggleSection('customers-section')">
                <div class="dash-section-title">Recent Customers</div>
                <svg class="dash-section-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </div>
            <div class="dash-section-content">
                <div class="table-card">
                <div class="table-head flex items-center justify-between">
                    <div class="table-title">Recent Customers</div>
                    <a href="#" class="text-xs text-blue-600 font-semibold hover:underline">View all</a>
                </div>
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Joined</th>
                            <th>Orders</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="4" class="tbl-empty">No customers yet.</td></tr>
                    </tbody>
                </table>
            </div>
            </div>
        </div>

        {{-- ── Alerts Section ─────────────────────────────── --}}
        <div class="dash-section" id="alerts-section">
            <div class="dash-section-header" onclick="toggleSection('alerts-section')">
                <div class="dash-section-title">Stock Alerts & Activity</div>
                <svg class="dash-section-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </div>
            <div class="dash-section-content">
                <div class="tables-row">

            {{-- Low Stock Alerts --}}
            <div class="table-card">
                <div class="table-head flex items-center justify-between">
                    <div class="table-title">Low Stock Alerts</div>
                    <span class="text-xs font-bold text-red-500 bg-red-50 px-2.5 py-1 rounded-full">3 items</span>
                </div>
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Stock</th>
                            <th>Min Level</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-medium">Coca Cola 500ml</td>
                            <td>Beverages</td>
                            <td><span class="text-red-600 font-bold">2</span></td>
                            <td class="text-slate-400">10</td>
                        </tr>
                        <tr>
                            <td class="font-medium">Bread Loaf</td>
                            <td>Bakery</td>
                            <td><span class="text-orange-500 font-bold">4</span></td>
                            <td class="text-slate-400">10</td>
                        </tr>
                        <tr>
                            <td class="font-medium">Sugar 1kg</td>
                            <td>Groceries</td>
                            <td><span class="text-red-600 font-bold">1</span></td>
                            <td class="text-slate-400">5</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Recent Logins --}}
            <div class="table-card">
                <div class="table-head">
                    <div class="table-title">Recent Logins</div>
                </div>
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Role</th>
                            <th>Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-medium">{{ Auth::user()->name ?? 'Admin' }}</td>
                            <td class="text-slate-400">{{ ucfirst(Auth::user()->role ?? 'user') }}</td>
                            <td class="text-slate-400">{{ now()->format('g:ia') }}</td>
                            <td><span class="badge-success">Active</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
            </div>
        </div>

    </div>{{-- /dash-content --}}
</div>{{-- /main-wrap --}}

<script>
// ── Section Toggle ───────────────────────────────────────
function toggleSection(sectionId) {
    const section = document.getElementById(sectionId);
    section.classList.toggle('collapsed');
}

// ── Trend Chart ──────────────────────────────────────────
(function() {
    const labels = [];
    const now = new Date();
    for (let i = 13; i >= 0; i--) {
        const d = new Date(now);
        d.setDate(d.getDate() - i);
        labels.push(d.toLocaleDateString('en-US', { month:'short', day:'numeric' }));
    }

    const salesData    = [0,0,0,0,0,0,0,0,0,0,0,0,0,0];
    const ordersData   = [0,0,0,0,0,0,0,0,0,0,0,0,0,0];
    const custData     = [0,0,0,0,0,0,0,0,0,0,0,0,0,0];

    const ctx = document.getElementById('trendChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: 'Sales (TSh)',
                    data: salesData,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59,130,246,0.08)',
                    borderWidth: 2.5,
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    pointBackgroundColor: '#3b82f6',
                    tension: 0.4,
                    fill: true,
                },
                {
                    label: 'Orders',
                    data: ordersData,
                    borderColor: '#22c55e',
                    backgroundColor: 'transparent',
                    borderWidth: 2,
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    pointBackgroundColor: '#22c55e',
                    tension: 0.4,
                },
                {
                    label: 'Customers',
                    data: custData,
                    borderColor: '#a78bfa',
                    backgroundColor: 'transparent',
                    borderWidth: 2,
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    pointBackgroundColor: '#a78bfa',
                    tension: 0.4,
                },
            ]
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleColor: '#94a3b8',
                    bodyColor: '#f8fafc',
                    padding: 10,
                    cornerRadius: 8,
                }
            },
            scales: {
                x: {
                    grid: { color: '#f1f5f9', drawBorder: false },
                    ticks: { color: '#94a3b8', font: { size: 10 }, maxRotation: 0 },
                },
                y: {
                    grid: { color: '#f1f5f9', drawBorder: false },
                    ticks: { color: '#94a3b8', font: { size: 10 } },
                    beginAtZero: true,
                }
            }
        }
    });
})();

// ── Donut Chart ──────────────────────────────────────────
(function() {
    const hasData = false; // replace with real data check
    const ctx = document.getElementById('donutChart').getContext('2d');

    if (!hasData) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['No Data'],
                datasets: [{ data: [1], backgroundColor: ['#e2e8f0'], borderWidth: 0, hoverBackgroundColor: ['#e2e8f0'] }]
            },
            options: {
                responsive: true,
                cutout: '72%',
                plugins: { legend: { display: false }, tooltip: { enabled: false } }
            }
        });
    } else {
        document.getElementById('donut-no-data').style.display = 'none';
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Cash','Mobile Money','Card'],
                datasets: [{ data: [55, 35, 10], backgroundColor: ['#3b82f6','#22c55e','#a78bfa'], borderWidth: 0, hoverOffset: 4 }]
            },
            options: {
                responsive: true,
                cutout: '72%',
                plugins: {
                    legend: { position: 'bottom', labels: { font: { size: 11 }, color: '#64748b', padding: 12 } },
                    tooltip: { backgroundColor: '#1e293b', padding: 8, cornerRadius: 8 }
                }
            }
        });
    }
})();
</script>

</body>
</html>
