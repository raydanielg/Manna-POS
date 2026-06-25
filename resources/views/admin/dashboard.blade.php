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
            <span class="insight-title"><span class="material-icons" style="font-size:14px;margin-right:4px;color:#2563eb;vertical-align:middle">person_add</span> New Registrations</span>
            <span class="insight-badge badge badge-info">This Month</span>
        </div>
        <div class="insight-body">
            <div class="insight-number" id="newUsersThisMonth">-</div>
            <div class="insight-foot">
                <span class="material-icons" style="font-size:14px;color:#16a34a;vertical-align:middle">trending_up</span>
                <span>Users</span>
            </div>
        </div>
    </div>
    <div class="insight-card">
        <div class="insight-header">
            <span class="insight-title"><span class="material-icons" style="font-size:14px;margin-right:4px;color:#059669;vertical-align:middle">add_business</span> New Businesses</span>
            <span class="insight-badge badge badge-info">This Month</span>
        </div>
        <div class="insight-body">
            <div class="insight-number" id="newBizThisMonth">-</div>
            <div class="insight-foot">
                <span class="material-icons" style="font-size:14px;color:#16a34a;vertical-align:middle">trending_up</span>
                <span>Businesses</span>
            </div>
        </div>
    </div>
    <div class="insight-card">
        <div class="insight-header">
            <span class="insight-title"><span class="material-icons" style="font-size:14px;margin-right:4px;color:#dc2626;vertical-align:middle">contact_support</span> Support Tickets</span>
            <span class="insight-badge badge badge-danger">Open</span>
        </div>
        <div class="insight-body">
            <div class="insight-number" id="pendingTickets">-</div>
            <div class="insight-foot">
                <span class="material-icons" style="font-size:14px;color:#dc2626;vertical-align:middle">priority_high</span>
                <span>Needs attention</span>
            </div>
        </div>
    </div>
    <div class="insight-card">
        <div class="insight-header">
            <span class="insight-title"><span class="material-icons" style="font-size:14px;margin-right:4px;color:#ca8a04;vertical-align:middle">fact_check</span> Verifications</span>
            <span class="insight-badge badge badge-pending">Pending</span>
        </div>
        <div class="insight-body">
            <div class="insight-number" id="pendingVerifications">-</div>
            <div class="insight-foot">
                <span class="material-icons" style="font-size:14px;color:#ca8a04;vertical-align:middle">hourglass_empty</span>
                <span>Awaiting review</span>
            </div>
        </div>
    </div>
</div>

{{-- ════ PREMIUM CHARTS ROW ════ --}}
<div class="charts-row">
    {{-- Revenue Trends --}}
    <div class="chart-card">
        <div class="chart-header">
            <div class="chart-header-left">
                <span class="chart-title">Revenue Trends</span>
                <span class="chart-subtitle" id="revenueSubtitle">Last 12 Months</span>
            </div>
            <select class="chart-range" id="revenueRange" onchange="updateRevenueChart(this.value)">
                <option value="12">Last 12 Months</option>
                <option value="6">Last 6 Months</option>
                <option value="3">Last Quarter</option>
            </select>
        </div>
        <div class="chart-stats">
            <div class="chart-stat">
                <span class="chart-stat-label">Total</span>
                <span class="chart-stat-value" id="revenueTotal">—</span>
            </div>
            <div class="chart-stat">
                <span class="chart-stat-label">Avg / Month</span>
                <span class="chart-stat-value" id="revenueAvg">—</span>
            </div>
        </div>
        <div class="chart-wrap">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    {{-- User Growth --}}
    <div class="chart-card">
        <div class="chart-header">
            <div class="chart-header-left">
                <span class="chart-title">User Growth</span>
                <span class="chart-subtitle" id="userSubtitle">Last 12 Months</span>
            </div>
            <select class="chart-range" id="userRange" onchange="updateUserChart(this.value)">
                <option value="12">Last 12 Months</option>
                <option value="6">Last 6 Months</option>
                <option value="3">Last Quarter</option>
            </select>
        </div>
        <div class="chart-stats">
            <div class="chart-stat">
                <span class="chart-stat-label">Total Users</span>
                <span class="chart-stat-value" id="userTotal">—</span>
            </div>
            <div class="chart-stat">
                <span class="chart-stat-label">New This Month</span>
                <span class="chart-stat-value" id="userNew">—</span>
            </div>
        </div>
        <div class="chart-wrap">
            <canvas id="userChart"></canvas>
        </div>
    </div>
</div>

{{-- ════ PREMIUM RECENT ACTIVITY ════ --}}
<div class="activity-card">
    <div class="activity-header">
        <span class="activity-title">Recent Activity</span>
        <a href="{{ route('admin.system.activity-logs') }}" class="btn btn-secondary btn-xs">View All</a>
    </div>
    <div class="activity-body" id="recentActivity">
        <div class="activity-empty">
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

    /* ── Premium Charts ── */
    .charts-row { display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.5rem; }
    .chart-card { background:#fff;border-radius:16px;border:1px solid #e9edf5;padding:1.25rem 1.25rem 0.75rem;transition:box-shadow 0.25s,transform 0.25s;position:relative;overflow:hidden; }
    .chart-card::before { content:'';position:absolute;top:0;left:0;right:0;height:3px;border-radius:16px 16px 0 0; }
    .chart-card:nth-child(1)::before { background:linear-gradient(90deg,#7c3aed,#a78bfa); }
    .chart-card:nth-child(2)::before { background:linear-gradient(90deg,#2563eb,#60a5fa); }
    .chart-card:hover { box-shadow:0 8px 28px rgba(15,23,42,0.06);transform:translateY(-2px); }
    .chart-header { display:flex;align-items:center;justify-content:space-between;margin-bottom:0.75rem; }
    .chart-header-left { display:flex;flex-direction:column;gap:0.15rem; }
    .chart-title { font-size:0.92rem;font-weight:800;color:#0f172a;letter-spacing:-0.01em; }
    .chart-subtitle { font-size:0.68rem;font-weight:600;color:#94a3b8; }
    .chart-range { padding:0.3rem 0.55rem;font-size:0.72rem;border:1px solid #e2e8f0;border-radius:8px;background:#f8fafc;color:#475569;outline:none;cursor:pointer;transition:all 0.15s; }
    .chart-range:hover { border-color:#cbd5e1;background:#fff; }
    .chart-range:focus { border-color:#7c3aed;box-shadow:0 0 0 3px rgba(124,58,237,0.1); }
    .chart-stats { display:flex;gap:1.5rem;margin-bottom:0.75rem;padding-bottom:0.75rem;border-bottom:1px solid #f1f5f9; }
    .chart-stat { display:flex;flex-direction:column;gap:0.15rem; }
    .chart-stat-label { font-size:0.62rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:#94a3b8; }
    .chart-stat-value { font-size:1.1rem;font-weight:800;color:#0f172a;letter-spacing:-0.02em; }
    .chart-wrap { position:relative;height:240px; }

    /* ── Premium Activity ── */
    .activity-card { background:#fff;border-radius:16px;border:1px solid #e9edf5;margin-bottom:1.5rem;overflow:hidden; }
    .activity-header { display:flex;align-items:center;justify-content:space-between;padding:0.9rem 1.25rem;border-bottom:1px solid #f1f5f9; }
    .activity-title { font-size:0.92rem;font-weight:800;color:#0f172a;letter-spacing:-0.01em; }
    .activity-body { padding:0;max-height:420px;overflow-y:auto; }
    .activity-body::-webkit-scrollbar { width:5px; }
    .activity-body::-webkit-scrollbar-thumb { background:#e2e8f0;border-radius:99px; }
    .activity-body::-webkit-scrollbar-track { background:transparent; }
    .activity-empty { display:flex;flex-direction:column;align-items:center;gap:0.5rem;padding:2.5rem;color:#94a3b8;font-size:0.82rem; }
    .activity-item { display:flex;align-items:center;gap:0.75rem;padding:0.75rem 1.25rem;border-bottom:1px solid #f8fafc;font-size:0.8rem;color:#475569;transition:background 0.12s; }
    .activity-item:hover { background:#f8fafc; }
    .activity-item:last-child { border-bottom:none; }
    .activity-avatar { width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:0.72rem;font-weight:800;color:#fff;flex-shrink:0; }
    .activity-avatar.success { background:linear-gradient(135deg,#16a34a,#22c55e); }
    .activity-avatar.info { background:linear-gradient(135deg,#2563eb,#3b82f6); }
    .activity-avatar.danger { background:linear-gradient(135deg,#dc2626,#ef4444); }
    .activity-avatar.warning { background:linear-gradient(135deg,#d97706,#f59e0b); }
    .activity-content { flex:1;min-width:0; }
    .activity-desc { font-size:0.8rem;color:#334155;font-weight:500;line-height:1.35; }
    .activity-user { font-size:0.68rem;color:#94a3b8;font-weight:600;margin-top:0.1rem; }
    .activity-dot { width:8px;height:8px;border-radius:50%;flex-shrink:0; }
    .activity-dot.success { background:#16a34a; }
    .activity-dot.warning { background:#d97706; }
    .activity-dot.info { background:#2563eb; }
    .activity-dot.danger { background:#dc2626; }
    .activity-time { margin-left:auto;font-size:0.68rem;color:#94a3b8;white-space:nowrap;flex-shrink:0; }

    .kpi-icon-wrap .material-icons { font-size:24px; }
    @media (max-width:1200px) { .kpi-grid,.insight-grid { grid-template-columns:repeat(2,1fr); } .charts-row { grid-template-columns:1fr; } }
    @media (max-width:768px) { .kpi-grid,.insight-grid { grid-template-columns:1fr; } }
</style>
@endsection
@section('scripts')
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

/* ── Chart helpers ── */
function makeGrad(ctx, color) {
    const g = ctx.createLinearGradient(0, 0, 0, 220);
    g.addColorStop(0, color + '40');
    g.addColorStop(1, color + '02');
    return g;
}

function chartOptions(currency) {
    return {
        responsive: true, maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#0f172a', titleColor: '#fff', bodyColor: '#e2e8f0',
                padding: 12, cornerRadius: 10, displayColors: false,
                titleFont: { size: 12, weight: '700' },
                bodyFont: { size: 13, weight: '600' },
                callbacks: {
                    label: ctx => {
                        let v = ctx.parsed.y;
                        if (currency) return currency + ' ' + v.toLocaleString(undefined, { maximumFractionDigits: 0 });
                        return v.toLocaleString() + ' users';
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: '#f1f5f9', drawBorder: false },
                ticks: { color: '#94a3b8', font: { size: 10, weight: '600' }, callback: v => v >= 1000 ? (v/1000).toFixed(0)+'k' : v }
            },
            x: { grid: { display: false }, ticks: { color: '#94a3b8', font: { size: 10, weight: '600' } } }
        },
        interaction: { intersect: false, mode: 'index' },
        animation: { duration: 600, easing: 'easeOutQuart' },
    };
}

/* ── Revenue Chart ── */
async function loadRevenueChart(months = 12) {
    try {
        const res = await apiFetch('/api/admin/revenue-trends?months=' + months);
        const ctx = document.getElementById('revenueChart').getContext('2d');
        if (revenueChart) revenueChart.destroy();

        revenueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: res.labels,
                datasets: [{
                    label: 'Revenue',
                    data: res.data,
                    borderColor: '#7c3aed',
                    backgroundColor: makeGrad(ctx, '#7c3aed'),
                    fill: true,
                    tension: 0.4,
                    pointRadius: 3,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#7c3aed',
                    pointBorderWidth: 2,
                    pointHoverBackgroundColor: '#7c3aed',
                    pointHoverBorderColor: '#fff',
                    borderWidth: 2.5,
                }]
            },
            options: chartOptions('TZS')
        });

        document.getElementById('revenueTotal').textContent = 'TZS ' + Number(res.total).toLocaleString(undefined, { maximumFractionDigits: 0 });
        document.getElementById('revenueAvg').textContent = 'TZS ' + Number(Math.round(res.avg)).toLocaleString(undefined, { maximumFractionDigits: 0 });
        document.getElementById('revenueSubtitle').textContent = months === 3 ? 'Last Quarter' : 'Last ' + months + ' Months';
    } catch (e) { console.warn('Revenue chart failed', e); }
}

/* ── User Growth Chart ── */
async function loadUserChart(months = 12) {
    try {
        const res = await apiFetch('/api/admin/user-growth?months=' + months);
        const ctx = document.getElementById('userChart').getContext('2d');
        if (userChart) userChart.destroy();

        userChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: res.labels,
                datasets: [{
                    label: 'Total Users',
                    data: res.data,
                    borderColor: '#2563eb',
                    backgroundColor: makeGrad(ctx, '#2563eb'),
                    fill: true,
                    tension: 0.4,
                    pointRadius: 3,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#2563eb',
                    pointBorderWidth: 2,
                    pointHoverBackgroundColor: '#2563eb',
                    pointHoverBorderColor: '#fff',
                    borderWidth: 2.5,
                }]
            },
            options: chartOptions(null)
        });

        document.getElementById('userTotal').textContent = Number(res.total).toLocaleString();
        document.getElementById('userNew').textContent = '+' + Number(res.new_users).toLocaleString();
        document.getElementById('userSubtitle').textContent = months === 3 ? 'Last Quarter' : 'Last ' + months + ' Months';
    } catch (e) { console.warn('User chart failed', e); }
}

function updateRevenueChart(val) { loadRevenueChart(parseInt(val)); }
function updateUserChart(val) { loadUserChart(parseInt(val)); }

/* ── Recent Activity ── */
async function loadRecentActivity() {
    try {
        const items = await apiFetch('/api/admin/recent-activity');
        const container = document.getElementById('recentActivity');

        if (!items || items.length === 0) {
            container.innerHTML = `
                <div class="activity-empty">
                    <svg width="40" height="40" fill="none" stroke="#cbd5e1" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-4a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                    <span>No recent activity</span>
                </div>`;
            return;
        }

        container.innerHTML = items.map(item => `
            <div class="activity-item">
                <div class="activity-avatar ${item.dot}">${item.avatar}</div>
                <div class="activity-content">
                    <div class="activity-desc">${item.description || item.action}</div>
                    <div class="activity-user">${item.user}</div>
                </div>
                <span class="activity-time">${item.time}</span>
            </div>
        `).join('');
    } catch (e) {
        console.warn('Recent activity failed', e);
        document.getElementById('recentActivity').innerHTML = `
            <div class="activity-empty">
                <svg width="40" height="40" fill="none" stroke="#cbd5e1" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-4a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                <span>Failed to load activity</span>
            </div>`;
    }
}

/* ── Init ── */
try {
    loadRevenueChart(12);
    loadUserChart(12);
} catch (e) { console.warn('Chart init failed', e); }
refreshDashboard();
loadRecentActivity();
@endsection