@extends('admin.layouts.app')
@section('page_title', 'Admin Dashboard')
@section('content')
<div class="dash-filters">
    <div class="filter-group">
        <label>Period</label>
        <select id="periodSelect" class="filter-select" onchange="changePeriod(this.value)">
            <option value="today">Today</option>
            <option value="week">This Week</option>
            <option value="month" selected>This Month</option>
            <option value="year">This Year</option>
            <option value="custom">Custom Range</option>
        </select>
    </div>
    <div class="filter-group" id="customRangeGroup" style="display:none">
        <label>From</label>
        <input type="date" id="filterFrom" class="filter-select">
        <label>To</label>
        <input type="date" id="filterTo" class="filter-select">
        <button class="btn btn-primary btn-sm" onclick="applyCustomFilter()">Apply</button>
    </div>
    <button class="btn btn-secondary btn-sm" onclick="refreshDashboard()">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h5M20 20v-5h-5"/><path d="M4 9a9 9 0 0 1 15.36-5.36M20 15a9 9 0 0 1-15.36 5.36"/></svg>
        Refresh
    </button>
</div>

<div class="kpi-grid">
    <div class="kpi-card gradient-purple">
        <div class="kpi-icon-wrap icon-purple"><span class="material-icons">account_balance</span></div>
        <div class="kpi-body">
            <div class="kpi-top">
                <span class="kpi-label">Total Revenue</span>
                <span class="kpi-change up">+12.5%</span>
            </div>
            <div class="kpi-val" id="totalRevenue">-</div>
        </div>
    </div>
    <div class="kpi-card gradient-blue">
        <div class="kpi-icon-wrap icon-blue"><span class="material-icons">people</span></div>
        <div class="kpi-body">
            <div class="kpi-top">
                <span class="kpi-label">Total Users</span>
                <span class="kpi-change up">+8.2%</span>
            </div>
            <div class="kpi-val" id="totalUsers">-</div>
        </div>
    </div>
    <div class="kpi-card gradient-emerald">
        <div class="kpi-icon-wrap icon-emerald"><span class="material-icons">store</span></div>
        <div class="kpi-body">
            <div class="kpi-top">
                <span class="kpi-label">Businesses</span>
                <span class="kpi-change up">+5.7%</span>
            </div>
            <div class="kpi-val" id="totalBusinesses">-</div>
        </div>
    </div>
    <div class="kpi-card gradient-amber">
        <div class="kpi-icon-wrap icon-amber"><span class="material-icons">card_membership</span></div>
        <div class="kpi-body">
            <div class="kpi-top">
                <span class="kpi-label">Subscriptions</span>
                <span class="kpi-change up">+15.3%</span>
            </div>
            <div class="kpi-val" id="activeSubs">-</div>
        </div>
    </div>
</div>

<div class="insight-grid">
    <div class="insight-card">
        <div class="insight-header">
            <span class="insight-title">New Registrations</span>
            <span class="insight-badge badge badge-info">This Month</span>
        </div>
        <div class="insight-body">
            <div class="insight-number" id="newUsersThisMonth">-</div>
            <div class="insight-foot">
                <svg width="14" height="14" fill="none" stroke="#16a34a" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 7l-5 5-4-4-3 3"/><path d="M17 12v-5h-5"/></svg>
                <span>Users</span>
            </div>
        </div>
    </div>
    <div class="insight-card">
        <div class="insight-header">
            <span class="insight-title">New Businesses</span>
            <span class="insight-badge badge badge-info">This Month</span>
        </div>
        <div class="insight-body">
            <div class="insight-number" id="newBizThisMonth">-</div>
            <div class="insight-foot">
                <svg width="14" height="14" fill="none" stroke="#16a34a" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 7l-5 5-4-4-3 3"/><path d="M17 12v-5h-5"/></svg>
                <span>Businesses</span>
            </div>
        </div>
    </div>
    <div class="insight-card">
        <div class="insight-header">
            <span class="insight-title">Support Tickets</span>
            <span class="insight-badge badge badge-danger">Open</span>
        </div>
        <div class="insight-body">
            <div class="insight-number" id="pendingTickets">-</div>
            <div class="insight-foot">
                <svg width="14" height="14" fill="none" stroke="#dc2626" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20z"/></svg>
                <span>Needs attention</span>
            </div>
        </div>
    </div>
    <div class="insight-card">
        <div class="insight-header">
            <span class="insight-title">Verifications</span>
            <span class="insight-badge badge badge-pending">Pending</span>
        </div>
        <div class="insight-body">
            <div class="insight-number" id="pendingVerifications">-</div>
            <div class="insight-foot">
                <svg width="14" height="14" fill="none" stroke="#ca8a04" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20z"/></svg>
                <span>Awaiting review</span>
            </div>
        </div>
    </div>
</div>

<div class="charts-row">
    <div class="chart-card">
        <div class="chart-header">
            <span class="chart-title">Revenue Trends</span>
            <select class="chart-range" onchange="updateRevenueChart(this.value)">
                <option value="12">Last 12 Months</option>
                <option value="6">Last 6 Months</option>
                <option value="3">Last Quarter</option>
            </select>
        </div>
        <div class="chart-wrap">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>
    <div class="chart-card">
        <div class="chart-header">
            <span class="chart-title">User Growth</span>
            <select class="chart-range" onchange="updateUserChart(this.value)">
                <option value="12">Last 12 Months</option>
                <option value="6">Last 6 Months</option>
                <option value="3">Last Quarter</option>
            </select>
        </div>
        <div class="chart-wrap">
            <canvas id="userChart"></canvas>
        </div>
    </div>
</div>

<div class="activity-card">
    <div class="activity-header">
        <span class="activity-title">Recent Activity</span>
        <a href="{{ route('admin.system.activity-logs') }}" class="btn btn-secondary btn-xs">View All</a>
    </div>
    <div class="activity-body">
        <div class="activity-empty" id="recentActivity">
            <svg width="40" height="40" fill="none" stroke="#cbd5e1" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-4a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
            <span>Loading activity...</span>
        </div>
    </div>
</div>

<style>
    .dash-filters { display:flex;align-items:end;gap:0.75rem;margin-bottom:1.5rem;flex-wrap:wrap; }
    .filter-group { display:flex;align-items:center;gap:0.4rem; }
    .filter-group label { font-size:0.72rem;font-weight:600;color:#64748b;white-space:nowrap; }
    .filter-select { padding:0.4rem 0.65rem;font-size:0.78rem;border:1px solid #e2e8f0;border-radius:8px;background:#fff;color:#0f172a;outline:none;transition:border-color 0.15s; }
    .filter-select:focus { border-color:#e03057; }
    #customRangeGroup input[type=date] { width:140px; }

    .kpi-grid { display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:1.5rem; }
    .kpi-card { border-radius:16px;padding:1.15rem 1.2rem;display:flex;align-items:center;gap:1rem;position:relative;overflow:hidden;border:none;box-shadow:0 1px 3px rgba(0,0,0,0.04); }
    .kpi-card.gradient-purple { background:linear-gradient(135deg,#faf5ff,#ede9fe); }
    .kpi-card.gradient-blue { background:linear-gradient(135deg,#eff6ff,#dbeafe); }
    .kpi-card.gradient-emerald { background:linear-gradient(135deg,#ecfdf5,#d1fae5); }
    .kpi-card.gradient-amber { background:linear-gradient(135deg,#fffbeb,#fef3c7); }
    .kpi-card::after { content:'';position:absolute;top:-50%;right:-20%;width:120px;height:120px;border-radius:50%;background:rgba(255,255,255,0.4);pointer-events:none; }
    .kpi-icon-wrap { width:46px;height:46px;border-radius:14px;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 2px 8px rgba(0,0,0,0.05); }
    .icon-purple { background:#fff; }
    .icon-blue { background:#fff; }
    .icon-emerald { background:#fff; }
    .icon-amber { background:#fff; }
    .kpi-body { flex:1;min-width:0; }
    .kpi-top { display:flex;align-items:center;justify-content:space-between;gap:0.5rem;margin-bottom:0.3rem; }
    .kpi-label { font-size:0.7rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.04em; }
    .kpi-change { font-size:0.64rem;font-weight:700;padding:0.15rem 0.45rem;border-radius:999px; }
    .kpi-change.up { background:rgba(22,163,74,0.12);color:#16a34a; }
    .kpi-change.down { background:rgba(220,38,38,0.12);color:#dc2626; }
    .kpi-val { font-size:1.35rem;font-weight:800;color:#0f172a;letter-spacing:-0.02em;line-height:1; }

    .insight-grid { display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:1.5rem; }
    .insight-card { background:#fff;border-radius:14px;border:1px solid #e9edf5;padding:0;overflow:hidden;transition:box-shadow 0.2s; }
    .insight-card:hover { box-shadow:0 2px 12px rgba(0,0,0,0.04); }
    .insight-header { display:flex;align-items:center;justify-content:space-between;padding:0.7rem 1rem 0.5rem; }
    .insight-title { font-size:0.72rem;font-weight:600;color:#64748b; }
    .insight-body { padding:0 1rem 1rem; }
    .insight-number { font-size:1.6rem;font-weight:800;color:#0f172a;letter-spacing:-0.03em;line-height:1;margin-bottom:0.3rem; }
    .insight-foot { display:flex;align-items:center;gap:0.3rem;font-size:0.68rem;font-weight:600;color:#94a3b8; }
    .insight-badge { font-size:0.6rem;padding:0.15rem 0.5rem; }

    .charts-row { display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.5rem; }
    .chart-card { background:#fff;border-radius:14px;border:1px solid #e9edf5;padding:1.25rem 1.25rem 0.75rem; }
    .chart-header { display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem; }
    .chart-title { font-size:0.88rem;font-weight:700;color:#0f172a; }
    .chart-range { padding:0.3rem 0.55rem;font-size:0.72rem;border:1px solid #e2e8f0;border-radius:8px;background:#f8fafc;color:#475569;outline:none;cursor:pointer; }
    .chart-range:focus { border-color:#e03057; }
    .chart-wrap { position:relative;height:220px; }

    .activity-card { background:#fff;border-radius:14px;border:1px solid #e9edf5;margin-bottom:1.5rem; }
    .activity-header { display:flex;align-items:center;justify-content:space-between;padding:0.9rem 1.25rem;border-bottom:1px solid #f1f5f9; }
    .activity-title { font-size:0.88rem;font-weight:700;color:#0f172a; }
    .activity-body { padding:0; }
    .activity-empty { display:flex;flex-direction:column;align-items:center;gap:0.5rem;padding:2rem;color:#94a3b8;font-size:0.82rem; }
    .activity-item { display:flex;align-items:center;gap:0.75rem;padding:0.7rem 1.25rem;border-bottom:1px solid #f8fafc;font-size:0.8rem;color:#475569; }
    .activity-item:last-child { border-bottom:none; }
    .activity-dot { width:8px;height:8px;border-radius:50%;flex-shrink:0; }
    .activity-dot.success { background:#16a34a; }
    .activity-dot.warning { background:#d97706; }
    .activity-dot.info { background:#2563eb; }
    .activity-dot.danger { background:#dc2626; }
    .activity-time { margin-left:auto;font-size:0.68rem;color:#94a3b8;white-space:nowrap; }

    @media (max-width:1200px) { .kpi-grid,.insight-grid { grid-template-columns:repeat(2,1fr); } .charts-row { grid-template-columns:1fr; } }
    @media (max-width:768px) { .kpi-grid,.insight-grid { grid-template-columns:1fr; } }
</style>
@endsection
@section('scripts')
const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

let revenueChart, userChart;

function getPeriodDates(period) {
    const now = new Date();
    const y = now.getFullYear(), m = now.getMonth();
    const fmt = d => d.toISOString().split('T')[0];
    const startOfMonth = new Date(y, m, 1);
    const endOfMonth = new Date(y, m + 1, 0);
    switch (period) {
        case 'today': return { from: fmt(now), to: fmt(now) };
        case 'week':  const w = now.getDate() - now.getDay(); return { from: fmt(new Date(y, m, w)), to: fmt(now) };
        case 'month': return { from: fmt(startOfMonth), to: fmt(endOfMonth) };
        case 'year':  return { from: fmt(new Date(y, 0, 1)), to: fmt(new Date(y, 11, 31)) };
        default: return { from: fmt(new Date(y-1, m, 1)), to: fmt(now) };
    }
}

function changePeriod(val) {
    document.getElementById('customRangeGroup').style.display = val === 'custom' ? 'flex' : 'none';
    if (val !== 'custom') refreshDashboard();
}

function applyCustomFilter() {
    const from = document.getElementById('filterFrom').value;
    const to = document.getElementById('filterTo').value;
    if (from && to) refreshDashboard({ from, to });
}

async function refreshDashboard(extra = {}) {
    try {
        const d = await apiFetch('/api/admin/stats');
        const set = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val ?? '-'; };
        set('totalRevenue', d.total_revenue);
        set('totalUsers', d.total_users?.toLocaleString());
        set('totalBusinesses', d.total_businesses?.toLocaleString());
        set('activeSubs', d.active_subscriptions?.toLocaleString());
        set('newUsersThisMonth', d.new_users_month?.toLocaleString());
        set('newBizThisMonth', d.new_biz_month?.toLocaleString());
        set('pendingTickets', d.pending_tickets?.toLocaleString());
        set('pendingVerifications', d.pending_verifications?.toLocaleString());
    } catch (e) { console.warn('Dashboard stats fetch failed', e); }
}

function initCharts() {
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const userCtx = document.getElementById('userChart').getContext('2d');

    const baseData = () => Array(12).fill(0).map(() => Math.floor(Math.random() * 80000 + 20000));

    revenueChart = new Chart(revenueCtx, {
        type: 'line',
        data: { labels: months, datasets: [{ label: 'Revenue', data: baseData(), borderColor: '#7c3aed', backgroundColor: makeGrad(revenueCtx, '#7c3aed'), fill: true, tension: 0.45, pointRadius: 3, pointHoverRadius: 6, pointBackgroundColor: '#fff', pointBorderColor: '#7c3aed', pointBorderWidth: 2, borderWidth: 2.5 }] },
        options: chartOptions('#7c3aed')
    });
    userChart = new Chart(userCtx, {
        type: 'line',
        data: { labels: months, datasets: [{ label: 'Users', data: baseData(), borderColor: '#2563eb', backgroundColor: makeGrad(userCtx, '#2563eb'), fill: true, tension: 0.45, pointRadius: 3, pointHoverRadius: 6, pointBackgroundColor: '#fff', pointBorderColor: '#2563eb', pointBorderWidth: 2, borderWidth: 2.5 }] },
        options: chartOptions('#2563eb')
    });
}

function chartOptions() {
    return {
        responsive: true, maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: { backgroundColor: '#0f172a', titleColor: '#fff', bodyColor: '#e2e8f0', padding: 10, cornerRadius: 8, displayColors: false }
        },
        scales: {
            y: { beginAtZero: true, grid: { color: '#f1f5f9', drawBorder: false }, ticks: { color: '#94a3b8', font: { size: 10 }, callback: v => v >= 1000 ? (v/1000).toFixed(0)+'k' : v } },
            x: { grid: { display: false }, ticks: { color: '#94a3b8', font: { size: 10 } } }
        },
        interaction: { intersect: false, mode: 'index' },
    };
}

function updateRevenueChart(val) {}
function updateUserChart(val) {}

try { initCharts(); } catch (e) { console.warn('Chart init failed', e); }
refreshDashboard();
@endsection