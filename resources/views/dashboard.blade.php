@extends('layouts.dashboard')
@section('page_title', 'Dashboard')

@section('head_scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endsection

@section('page_styles')
<style>
    /* Pulser for stock alert */
    .stock-badge {
        position: relative;
    }
    .stock-badge::after {
        content: '';
        position: absolute;
        top: 2px;
        right: 2px;
        width: 6px;
        height: 6px;
        background: #ef4444;
        border-radius: 50%;
        animation: pulseBadge 1.5s infinite;
    }
    @keyframes pulseBadge {
        0% { transform: scale(0.9); opacity: 1; }
        50% { transform: scale(1.5); opacity: 0.4; }
        100% { transform: scale(0.9); opacity: 1; }
    }

    /* Content hidden until overlay clears */
    .dash-content {
        opacity: 0;
        transform: translateY(12px);
        transition: opacity 0.5s ease, transform 0.5s ease;
    }
    .dash-content.revealed {
        opacity: 1;
        transform: translateY(0);
    }
</style>
@endsection

@section('content')

{{-- Ripple Loading Overlay --}}
<div id="rippleOverlay" class="ripple-overlay open">
    <div class="ripple-inner">
        <div class="spinner-ring"></div>
        <div class="spinner-ring-2"></div>
        <img src="{{ asset('favicon.ico') }}" alt="Loading" onerror="this.style.display='none'">
    </div>
    <div class="ripple-text">Loading<span class="ripple-dots"></span></div>
    <div class="ripple-sub">Preparing your dashboard</div>
</div>

<div id="dashContent" class="dash-content">

    {{-- ── KPI Section ─────────────────────────────── --}}
    <div class="dash-section" id="kpi-section">
        <div class="dash-section-header" onclick="toggleSection('kpi-section')">
            <div class="dash-section-title flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                Key Performance Indicators
            </div>
            <svg class="dash-section-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
        </div>
        <div class="dash-section-content">
            <div class="kpi-grid">

                <div class="kpi-card">
                    <div class="kpi-icon" style="background: #eff6ff;">
                        <img src="https://cdn-icons-png.flaticon.com/512/3500/3500460.png" alt="Sales">
                    </div>
                    <div>
                        <div class="kpi-val" id="kpi-sales-today">TSh 0</div>
                        <div class="kpi-label">Sales Today</div>
                    </div>
                </div>

                <div class="kpi-card">
                    <div class="kpi-icon" style="background: #f0fdf4;">
                        <img src="https://cdn-icons-png.flaticon.com/512/2489/2489756.png" alt="Orders">
                    </div>
                    <div>
                        <div class="kpi-val" id="kpi-orders-today">0</div>
                        <div class="kpi-label">Orders Today</div>
                    </div>
                </div>

                <div class="kpi-card">
                    <div class="kpi-icon" style="background: #fdf4ff;">
                        <img src="https://cdn-icons-png.flaticon.com/512/1256/1256650.png" alt="Customers">
                    </div>
                    <div>
                        <div class="kpi-val" id="kpi-total-customers">0</div>
                        <div class="kpi-label">Total Customers</div>
                    </div>
                </div>

                <div class="kpi-card">
                    <div class="kpi-icon" style="background: #fff7ed;">
                        <img src="https://cdn-icons-png.flaticon.com/512/4149/4149646.png" alt="New Customers">
                    </div>
                    <div>
                        <div class="kpi-val" id="kpi-new-customers">0</div>
                        <div class="kpi-label">New Customers</div>
                    </div>
                </div>

                <div class="kpi-card">
                    <div class="kpi-icon" style="background: #ecfeff;">
                        <img src="https://cdn-icons-png.flaticon.com/512/3588/3588592.png" alt="Products">
                    </div>
                    <div>
                        <div class="kpi-val" id="kpi-total-products">0</div>
                        <div class="kpi-label">Total Products</div>
                    </div>
                </div>

                <div class="kpi-card">
                    <div class="kpi-icon" style="background: #fff1f2;">
                        <img src="https://cdn-icons-png.flaticon.com/512/564/564619.png" alt="Low Stock">
                    </div>
                    <div>
                        <div class="kpi-val text-red-500" id="kpi-low-stock">0</div>
                        <div class="kpi-label">Low Stock Alerts</div>
                    </div>
                </div>

            </div>

            <div class="kpi-grid-2">

                <div class="kpi-card">
                    <div class="kpi-icon" style="background: #eff6ff;">
                        <img src="https://cdn-icons-png.flaticon.com/512/2920/2920277.png" alt="Revenue">
                    </div>
                    <div>
                        <div class="kpi-val" id="kpi-monthly-revenue">TSh 0</div>
                        <div class="kpi-label">Monthly Revenue (MTD)</div>
                    </div>
                </div>

                <div class="kpi-card">
                    <div class="kpi-icon" style="background: #f0fdf4;">
                        <img src="https://cdn-icons-png.flaticon.com/512/2645/2645890.png" alt="Payments">
                    </div>
                    <div>
                        <div class="kpi-val" id="kpi-payments-mtd">TSh 0</div>
                        <div class="kpi-label">Payments (MTD)</div>
                    </div>
                </div>

                <div class="kpi-card">
                    <div class="kpi-icon" style="background: #fdf4ff;">
                        <img src="https://cdn-icons-png.flaticon.com/512/3064/3064197.png" alt="Active Users">
                    </div>
                    <div>
                        <div class="kpi-val" id="kpi-active-users">0</div>
                        <div class="kpi-label">Active Users</div>
                    </div>
                </div>

                <div class="kpi-card">
                    <div class="kpi-icon" style="background: #fff7ed;">
                        <img src="https://cdn-icons-png.flaticon.com/512/9195/9195785.png" alt="Avg Sale">
                    </div>
                    <div>
                        <div class="kpi-val" id="kpi-avg-transaction">TSh 0</div>
                        <div class="kpi-label">Avg Transaction</div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ── Charts Section ───────────────────────────── --}}
    <div class="dash-section animate__animated animate__fadeInUp stagger-2" id="charts-section">
        <div class="dash-section-header" onclick="toggleSection('charts-section')">
            <div class="dash-section-title flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v16.5c0 .414.336.75.75.75H21m-16.5-3l2.25-3c.245-.327.67-.425 1.02-.236l2.232 1.116a1.125 1.125 0 0 0 1.221-.144l4.5-3.75M21 9.75V3m0 0h-6.75M21 3l-8.25 8.25"/></svg>
                Sales Analytics & Trends
            </div>
            <svg class="dash-section-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
        </div>
        <div class="dash-section-content">
            <div class="charts-row">

                {{-- Activity Trend --}}
                <div class="chart-card">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                        <div class="chart-title mb-0">Sales Trend (Last 14 Days)</div>
                        <div class="flex items-center gap-4 text-[0.7rem] font-semibold text-slate-500">
                            <span class="flex items-center gap-1.5"><span class="w-3 h-1 bg-blue-500 inline-block rounded-full"></span>Sales</span>
                            <span class="flex items-center gap-1.5"><span class="w-3 h-1 bg-green-500 inline-block rounded-full"></span>Orders</span>
                            <span class="flex items-center gap-1.5"><span class="w-3 h-1 bg-violet-400 inline-block rounded-full"></span>Customers</span>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>

                {{-- Distribution --}}
                <div class="chart-card flex flex-col">
                    <div class="chart-title">Payment Distribution</div>
                    <div class="flex-1 flex flex-col items-center justify-center">
                        <div class="relative w-full flex justify-center">
                            <canvas id="donutChart" style="max-width:180px;max-height:180px;"></canvas>
                        </div>
                        <div class="text-xs text-slate-400 mt-4 font-medium" id="donut-no-data">No data yet</div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ── Transactions & Customers Sections ─────────── --}}
    <div class="tables-row animate__animated animate__fadeInUp stagger-3">

        {{-- Recent Transactions --}}
        <div class="table-card">
            <div class="table-head flex items-center justify-between">
                <div class="table-title flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Recent Transactions
                </div>
                <a href="{{ route('dashboard.sell.all-sales') }}" class="text-xs text-blue-600 font-semibold hover:underline">View all</a>
            </div>
            <div class="tbl-responsive">
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
                    <tbody id="recent-transactions-body">
                        <tr><td colspan="5">
                            <div class="empty-state">
                                <svg class="empty-icon" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414a1 1 0 00-.707-.293H4"/></svg>
                                <div class="empty-title">No transactions yet</div>
                                <div class="empty-desc">When you make sales, they will appear here.</div>
                            </div>
                        </td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Recent Customers --}}
        <div class="table-card">
            <div class="table-head flex items-center justify-between">
                <div class="table-title flex items-center gap-2">
                    <svg class="w-5 h-5 text-violet-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Recent Customers
                </div>
                <a href="{{ route('dashboard.contacts.customers') }}" class="text-xs text-blue-600 font-semibold hover:underline">View all</a>
            </div>
            <div class="tbl-responsive">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Joined</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="recent-customers-body">
                        <tr><td colspan="4">
                            <div class="empty-state">
                                <svg class="empty-icon" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <div class="empty-title">No customers yet</div>
                                <div class="empty-desc">Newly registered customers will show up here.</div>
                            </div>
                        </td></tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- ── Alerts & Activities Row ─────────────────────── --}}
    <div class="tables-row mt-6 animate__animated animate__fadeInUp stagger-4">

        {{-- Low Stock Alerts --}}
        <div class="table-card">
            <div class="table-head flex items-center justify-between">
                <div class="table-title flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Low Stock Alerts
                </div>
                <span class="text-xs font-bold text-red-600 bg-red-50 px-2.5 py-1 rounded-full stock-badge" id="low-stock-count">0 items</span>
            </div>
            <div class="tbl-responsive">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Stock</th>
                            <th>Min Level</th>
                        </tr>
                    </thead>
                    <tbody id="low-stock-body">
                        <tr><td colspan="4">
                            <div class="empty-state">
                                <svg class="empty-icon" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <div class="empty-title">No low stock alerts</div>
                                <div class="empty-desc">All products are above the minimum level.</div>
                            </div>
                        </td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Recent Logins / Activity --}}
        <div class="table-card">
            <div class="table-head flex items-center justify-between">
                <div class="table-title flex items-center gap-2">
                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Recent Logins
                </div>
            </div>
            <div class="tbl-responsive">
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
                            <td class="font-semibold">{{ Auth::user()->name ?? 'Admin' }}</td>
                            <td class="text-slate-500">{{ ucfirst(Auth::user()->role ?? 'user') }}</td>
                            <td class="text-slate-400">{{ now()->format('g:ia') }}</td>
                            <td><span class="badge badge-success">Active</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection

@section('scripts')
<script>
// ── Collapsible Section toggle ────────────────────────────
function toggleSection(id) {
    const el = document.getElementById(id);
    if (!el) return;
    const coll = el.classList.toggle('collapsed');
    localStorage.setItem('dash-coll-' + id, coll ? '1' : '0');
}
// Restore collapsible sections state
(function() {
    ['kpi-section', 'charts-section'].forEach(id => {
        if (localStorage.getItem('dash-coll-' + id) === '1') {
            const el = document.getElementById(id);
            if (el) el.classList.add('collapsed');
        }
    });
})();

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
    window.trendChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: 'Sales (TSh)',
                    data: salesData,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59,130,246,0.04)',
                    borderWidth: 2.5,
                    pointRadius: 2,
                    pointHoverRadius: 5,
                    pointBackgroundColor: '#3b82f6',
                    tension: 0.35,
                    fill: true,
                },
                {
                    label: 'Orders',
                    data: ordersData,
                    borderColor: '#10b981',
                    backgroundColor: 'transparent',
                    borderWidth: 2,
                    pointRadius: 2,
                    pointHoverRadius: 5,
                    pointBackgroundColor: '#10b981',
                    tension: 0.35,
                },
                {
                    label: 'Customers',
                    data: custData,
                    borderColor: '#8b5cf6',
                    backgroundColor: 'transparent',
                    borderWidth: 2,
                    pointRadius: 2,
                    pointHoverRadius: 5,
                    pointBackgroundColor: '#8b5cf6',
                    tension: 0.35,
                },
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleColor: '#94a3b8',
                    bodyColor: '#f8fafc',
                    padding: 11,
                    cornerRadius: 10,
                    titleFont: { size: 11, weight: '700' },
                    bodyFont: { size: 12 },
                    boxPadding: 5
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
    const ctx = document.getElementById('donutChart').getContext('2d');
    window.donutChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['No Data'],
            datasets: [{ data: [1], backgroundColor: ['#f1f5f9'], borderWidth: 0 }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '76%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 10, font: { size: 11, weight: '600' }, color: '#64748b', padding: 14 }
                },
                tooltip: { enabled: false }
            }
        }
    });
})();

// ── Fetch Dashboard Stats ────────────────────────────────
(async function(){
    try {
        const res = await fetch('/api/dashboard/stats', { headers: { 'Accept': 'application/json' } });
        const d = await res.json();
        if (!d || !d.kpis) return;

        const k = d.kpis;
        const fmt = n => 'TSh ' + Number(n).toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
        const fmtN = n => Number(n).toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 });

        document.getElementById('kpi-sales-today').textContent = fmt(k.sales_today || 0);
        document.getElementById('kpi-orders-today').textContent = fmtN(k.orders_today || 0);
        document.getElementById('kpi-total-customers').textContent = fmtN(k.total_customers || 0);
        document.getElementById('kpi-new-customers').textContent = fmtN(k.new_customers || 0);
        document.getElementById('kpi-total-products').textContent = fmtN(k.total_products || 0);
        document.getElementById('kpi-low-stock').textContent = fmtN(k.low_stock || 0);
        document.getElementById('kpi-monthly-revenue').textContent = fmt(k.monthly_revenue || 0);
        document.getElementById('kpi-payments-mtd').textContent = fmt(k.payments_mtd || 0);
        document.getElementById('kpi-active-users').textContent = fmtN(k.active_users || 0);
        document.getElementById('kpi-avg-transaction').textContent = fmt(k.avg_transaction || 0);

        // Trend chart
        if (window.trendChart && d.trend && d.trend.length) {
            window.trendChart.data.labels = d.trend.map(t => t.date);
            window.trendChart.data.datasets[0].data = d.trend.map(t => t.sales);
            window.trendChart.data.datasets[1].data = d.trend.map(t => t.orders);
            window.trendChart.data.datasets[2].data = d.trend.map(t => t.customers);
            window.trendChart.update();
        }

        // Donut chart
        if (window.donutChart && d.payment_distribution && d.payment_distribution.length) {
            const labels = d.payment_distribution.map(p => p.label);
            const data = d.payment_distribution.map(p => p.value);
            const colors = ['#2563eb','#10b981','#8b5cf6','#f59e0b','#ef4444','#64748b'];
            window.donutChart.data.labels = labels;
            window.donutChart.data.datasets[0].data = data;
            window.donutChart.data.datasets[0].backgroundColor = labels.map((_, i) => colors[i % colors.length]);
            window.donutChart.data.datasets[0].hoverBackgroundColor = labels.map((_, i) => colors[i % colors.length]);
            window.donutChart.options.plugins.legend.display = true;
            window.donutChart.options.plugins.tooltip.enabled = true;
            document.getElementById('donut-no-data').style.display = 'none';
            window.donutChart.update();
        }

        // Recent transactions
        const txBody = document.getElementById('recent-transactions-body');
        if (txBody) {
            if (d.recent_sales && d.recent_sales.length) {
                txBody.innerHTML = d.recent_sales.map(s => `<tr>
                    <td>${new Date(s.sale_date).toLocaleDateString('en-GB',{day:'2-digit',month:'short'})}</td>
                    <td class="font-semibold">${s.reference || '-'}</td>
                    <td>${s.customer ? s.customer.name : 'Walk-in'}</td>
                    <td class="font-bold">TSh ${Number(s.total).toLocaleString()}</td>
                    <td><span class="badge badge-success">Completed</span></td>
                </tr>`).join('');
            } else {
                txBody.innerHTML = `<tr><td colspan="5">
                    <div class="empty-state">
                        <svg class="empty-icon" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414a1 1 0 00-.707-.293H4"/></svg>
                        <div class="empty-title">No transactions yet</div>
                        <div class="empty-desc">When you make sales, they will appear here.</div>
                    </div>
                </td></tr>`;
            }
        }

        // Recent customers
        const custBody = document.getElementById('recent-customers-body');
        if (custBody) {
            if (d.recent_customers && d.recent_customers.length) {
                custBody.innerHTML = d.recent_customers.map(c => `<tr>
                    <td class="font-semibold">${c.name || '-'}</td>
                    <td class="text-slate-500 font-medium">${c.phone || '-'}</td>
                    <td class="text-slate-400">${new Date(c.created_at).toLocaleDateString('en-GB',{day:'2-digit',month:'short',year:'numeric'})}</td>
                    <td><span class="badge badge-success">Active</span></td>
                </tr>`).join('');
            } else {
                custBody.innerHTML = `<tr><td colspan="4">
                    <div class="empty-state">
                        <svg class="empty-icon" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <div class="empty-title">No customers yet</div>
                        <div class="empty-desc">Newly registered customers will show up here.</div>
                    </div>
                </td></tr>`;
            }
        }

        // Low stock
        const lsBody = document.getElementById('low-stock-body');
        if (lsBody) {
            if (d.low_stock_products && d.low_stock_products.length) {
                lsBody.innerHTML = d.low_stock_products.map(p => `<tr>
                    <td class="font-semibold">${p.name}</td>
                    <td class="text-slate-500 font-medium">${p.sku || '-'}</td>
                    <td><span class="text-red-500 font-bold bg-red-50 px-2 py-0.5 rounded">${p.stock_quantity}</span></td>
                    <td class="text-slate-400">${p.reorder_level || 0}</td>
                </tr>`).join('');
                document.getElementById('low-stock-count').textContent = (d.kpis.low_stock || 0) + ' items';
            } else {
                lsBody.innerHTML = `<tr><td colspan="4">
                    <div class="empty-state">
                        <svg class="empty-icon" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div class="empty-title">No low stock alerts</div>
                        <div class="empty-desc">All products are above the minimum level.</div>
                    </div>
                </td></tr>`;
                document.getElementById('low-stock-count').textContent = '0 items';
            }
        }
    } catch (e) {
        console.error('Dashboard stats failed', e);
    } finally {
        const overlay = document.getElementById('rippleOverlay');
        const content = document.getElementById('dashContent');
        if (overlay) overlay.classList.remove('open');
        // Small delay so overlay fade-out plays before content reveals
        setTimeout(function() {
            if (content) content.classList.add('revealed');
        }, 300);
    }
})();
</script>
@endsection
