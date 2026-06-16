@extends('admin.layouts.app')
@section('page_title', 'Admin Dashboard')
@section('content')
<div id="kpi-section">
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-icon" style="background:#eff6ff;">
                <svg width="24" height="24" fill="none" stroke="#2563eb" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 12m-8 0a8 8 0 1 0 16 0a8 8 0 1 0 -16 0"/><path d="M12 8v4l2 2"/></svg>
            </div>
            <div>
                <div class="kpi-val" id="totalRevenue">-</div>
                <div class="kpi-label">Total Revenue</div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon" style="background:#f0fdf4;">
                <svg width="24" height="24" fill="none" stroke="#16a34a" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"/><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/></svg>
            </div>
            <div>
                <div class="kpi-val" id="totalUsers">-</div>
                <div class="kpi-label">Total Users</div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon" style="background:#fdf4ff;">
                <svg width="24" height="24" fill="none" stroke="#9333ea" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7m0 1a1 1 0 0 1 1 -h16a1 1 0 0 1 1 1v2a1 1 0 0 1 -1 1h-16a1 1 0 0 1 -1 -1z"/><path d="M5 11v7a1 1 0 0 0 1 1h3"/><path d="M19 11v7a1 1 0 0 1 -1 1h-3"/></svg>
            </div>
            <div>
                <div class="kpi-val" id="totalBusinesses">-</div>
                <div class="kpi-label">Total Businesses</div>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon" style="background:#fffbeb;">
                <svg width="24" height="24" fill="none" stroke="#d97706" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3l8 4.5v9l-8 4.5l-8 -4.5v-9l8 -4.5"/><path d="M12 12l8 -4.5"/><path d="M12 12v9"/></svg>
            </div>
            <div>
                <div class="kpi-val" id="activeSubs">-</div>
                <div class="kpi-label">Active Subscriptions</div>
            </div>
        </div>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-number" id="newUsersThisMonth">-</div>
        <div class="stat-label">New Users This Month</div>
    </div>
    <div class="stat-card">
        <div class="stat-number" id="newBizThisMonth">-</div>
        <div class="stat-label">New Businesses This Month</div>
    </div>
    <div class="stat-card">
        <div class="stat-number" id="pendingTickets">-</div>
        <div class="stat-label">Open Support Tickets</div>
    </div>
    <div class="stat-card">
        <div class="stat-number" id="pendingVerifications">-</div>
        <div class="stat-label">Pending Verifications</div>
    </div>
</div>

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

<style>
    .kpi-grid { display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:1.25rem; }
    .kpi-card { background:#fff;border-radius:14px;padding:1rem 1.1rem;border:1px solid #e9edf5;display:flex;align-items:center;gap:0.85rem; }
    .kpi-icon { width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
    .kpi-val { font-size:1.25rem;font-weight:800;color:#0f172a;line-height:1;letter-spacing:-0.02em; }
    .kpi-label { font-size:0.72rem;color:#94a3b8;margin-top:0.2rem;font-weight:500; }
    .stats-grid { display:grid;grid-template-columns:repeat(4,1fr);gap:1.25rem;margin-bottom:1.75rem; }
    .stat-card { background:#fff;border-radius:14px;border:1px solid #e9edf5;padding:1.25rem; }
    .stat-number { font-size:1.75rem;font-weight:800;color:#0f172a;letter-spacing:-0.03em; }
    .stat-label { font-size:0.75rem;color:#94a3b8;margin-top:0.25rem;font-weight:500; }
    .charts-row { display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.75rem; }
    .chart-card { background:#fff;border-radius:14px;border:1px solid #e9edf5;padding:1.4rem 1.5rem; }
    .chart-title { font-size:0.92rem;font-weight:700;color:#0f172a;margin-bottom:1rem; }
    @media (max-width:1200px) { .kpi-grid,.stats-grid { grid-template-columns:repeat(2,1fr); } .charts-row { grid-template-columns:1fr; } }
    @media (max-width:768px) { .kpi-grid,.stats-grid { grid-template-columns:1fr; } }
</style>
@endsection
@section('scripts')
const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

async function loadDashboard() {
    try {
        const users = await apiFetch('/api/admin/users?per_page=1');
        if (users.total) {
            document.getElementById('totalUsers').textContent = users.total.toLocaleString();
        }
    } catch(e) {}
    try {
        const biz = await apiFetch('/api/admin/business');
        if (Array.isArray(biz)) {
            document.getElementById('totalBusinesses').textContent = biz.length.toLocaleString();
        }
    } catch(e) {}
    try {
        const subs = await apiFetch('/api/admin/subscriptions');
        if (subs.data) {
            const active = subs.data.filter(s => s.status === 'active').length;
            document.getElementById('activeSubs').textContent = active.toLocaleString();
        }
    } catch(e) {}
    try {
        const tickets = await apiFetch('/api/admin/support/tickets');
        if (Array.isArray(tickets)) {
            document.getElementById('pendingTickets').textContent = tickets.filter(t => t.status === 'open').length;
        }
    } catch(e) {}
    try {
        const fin = await apiFetch('/api/admin/finance/revenue');
        if (fin.year_total) document.getElementById('totalRevenue').textContent = fin.year_total;
    } catch(e) {}
    try {
        const from = new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0];
        const today = new Date().toISOString().split('T')[0];
        const sales = await apiFetch(`/api/dashboard/sales?from=${from}&to=${today}&per_page=1`);
        if (sales.total) document.getElementById('newBizThisMonth').textContent = sales.total;
    } catch(e) {}
    try {
        const ver = await apiFetch('/api/admin/business/verifications');
        if (Array.isArray(ver)) document.getElementById('pendingVerifications').textContent = ver.filter(v => v.status === 'pending').length;
    } catch(e) {}
}

// Charts with sample data
new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: {
        labels: months,
        datasets: [{ label: 'Revenue', data: Array(12).fill(0), borderColor: '#e03057', backgroundColor: 'rgba(224,48,87,0.1)', fill: true, tension: 0.4 }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' } }, x: { grid: { display: false } } } }
});

new Chart(document.getElementById('userChart'), {
    type: 'line',
    data: {
        labels: months,
        datasets: [{ label: 'Users', data: Array(12).fill(0), borderColor: '#2563eb', backgroundColor: 'rgba(37,99,235,0.1)', fill: true, tension: 0.4 }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' } }, x: { grid: { display: false } } } }
});

loadDashboard();
@endsection
