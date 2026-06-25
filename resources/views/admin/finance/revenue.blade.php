@extends('admin.layouts.app')
@section('page_title', 'Revenue Overview')
@section('content')

{{-- ── KPI Grid ── --}}
<div class="rev-kpi-grid" id="kpiGrid">
    <div class="rev-kpi-card gradient-green">
        <div class="rev-kpi-top">
            <div class="rev-kpi-icon icon-green"><svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
            <div class="rev-kpi-trend" id="momTrend"></div>
        </div>
        <div class="rev-kpi-label">This Month Revenue</div>
        <div class="rev-kpi-value" id="monthRevenue">—</div>
        <div class="rev-kpi-sub"><span id="monthCount">—</span> invoices paid</div>
    </div>
    <div class="rev-kpi-card gradient-blue">
        <div class="rev-kpi-top">
            <div class="rev-kpi-icon icon-blue"><svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>
        </div>
        <div class="rev-kpi-label">Today's Revenue</div>
        <div class="rev-kpi-value" id="todayRevenue">—</div>
        <div class="rev-kpi-sub"><span id="todayCount">—</span> transactions today</div>
    </div>
    <div class="rev-kpi-card gradient-purple">
        <div class="rev-kpi-top">
            <div class="rev-kpi-icon icon-purple"><svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg></div>
        </div>
        <div class="rev-kpi-label">Year Revenue</div>
        <div class="rev-kpi-value" id="yearRevenue">—</div>
        <div class="rev-kpi-sub"><span id="yearCount">—</span> invoices this year</div>
    </div>
    <div class="rev-kpi-card gradient-amber">
        <div class="rev-kpi-top">
            <div class="rev-kpi-icon icon-amber"><svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
        </div>
        <div class="rev-kpi-label">Avg Transaction</div>
        <div class="rev-kpi-value" id="avgTransaction">—</div>
        <div class="rev-kpi-sub">All time average</div>
    </div>
</div>

{{-- ── Secondary KPI Row ── --}}
<div class="rev-kpi-grid rev-kpi-grid-sm">
    <div class="rev-mini-card">
        <div class="rev-mini-label">Month Expenses</div>
        <div class="rev-mini-value" id="monthExpenses">—</div>
    </div>
    <div class="rev-mini-card">
        <div class="rev-mini-label">Net Revenue (Month)</div>
        <div class="rev-mini-value" id="monthNet">—</div>
    </div>
    <div class="rev-mini-card">
        <div class="rev-mini-label">Year Expenses</div>
        <div class="rev-mini-value" id="yearExpenses">—</div>
    </div>
    <div class="rev-mini-card">
        <div class="rev-mini-label">Net Revenue (Year)</div>
        <div class="rev-mini-value" id="yearNet">—</div>
    </div>
    <div class="rev-mini-card">
        <div class="rev-mini-label">All-Time Revenue</div>
        <div class="rev-mini-value" id="allTimeTotal">—</div>
    </div>
    <div class="rev-mini-card">
        <div class="rev-mini-label">Pending Invoices</div>
        <div class="rev-mini-value" id="pendingInvoices">—</div>
    </div>
    <div class="rev-mini-card">
        <div class="rev-mini-label">Pending Amount</div>
        <div class="rev-mini-value" id="pendingAmount">—</div>
    </div>
    <div class="rev-mini-card">
        <div class="rev-mini-label">Pending Payouts</div>
        <div class="rev-mini-value" id="pendingPayouts">—</div>
    </div>
</div>

{{-- ── Main Chart ── --}}
<div class="rev-chart-card">
    <div class="rev-chart-header">
        <div class="rev-chart-header-left">
            <span class="rev-chart-title">Revenue vs Expenses</span>
            <span class="rev-chart-subtitle" id="chartSubtitle">Last 12 Months</span>
        </div>
        <div class="rev-chart-toggles">
            <button class="rev-toggle active" id="btnMonthly" onclick="switchChart('monthly')">Monthly</button>
            <button class="rev-toggle" id="btnWeekly" onclick="switchChart('weekly')">Weekly</button>
        </div>
    </div>
    <div class="rev-chart-stats">
        <div class="rev-chart-stat">
            <span class="rev-chart-stat-dot" style="background:#16a34a"></span>
            <span class="rev-chart-stat-label">Revenue</span>
            <span class="rev-chart-stat-value" id="chartRevenueTotal">—</span>
        </div>
        <div class="rev-chart-stat">
            <span class="rev-chart-stat-dot" style="background:#ef4444"></span>
            <span class="rev-chart-stat-label">Expenses</span>
            <span class="rev-chart-stat-value" id="chartExpensesTotal">—</span>
        </div>
        <div class="rev-chart-stat">
            <span class="rev-chart-stat-dot" style="background:#7c3aed"></span>
            <span class="rev-chart-stat-label">Net</span>
            <span class="rev-chart-stat-value" id="chartNetTotal">—</span>
        </div>
    </div>
    <div class="rev-chart-wrap">
        <canvas id="revenueChart"></canvas>
    </div>
</div>

{{-- ── Bottom Row: Payment Methods + Recent Transactions ── --}}
<div class="rev-bottom-row">
    {{-- Payment Methods --}}
    <div class="rev-pm-card">
        <div class="rev-pm-header">
            <span class="rev-pm-title">Payment Methods</span>
            <span class="rev-pm-subtitle">This Month</span>
        </div>
        <div class="rev-pm-body" id="pmBody">
            <div class="rev-pm-empty">Loading...</div>
        </div>
    </div>

    {{-- Recent Transactions --}}
    <div class="rev-tx-card">
        <div class="rev-tx-header">
            <span class="rev-tx-title">Recent Transactions</span>
        </div>
        <div class="rev-tx-body">
            <table class="rev-tx-table">
                <thead><tr><th>Invoice</th><th>Customer</th><th>Amount</th><th>Date</th></tr></thead>
                <tbody id="txBody"><tr><td colspan="4" class="rev-tx-empty">Loading...</td></tr></tbody>
            </table>
        </div>
    </div>
</div>

<style>
    /* ── KPI Grid ── */
    .rev-kpi-grid { display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:1rem; }
    .rev-kpi-card {
        border-radius:16px;padding:1.25rem;position:relative;overflow:hidden;
        display:flex;flex-direction:column;gap:0.5rem;border:none;
        box-shadow:0 1px 3px rgba(0,0,0,0.04);transition:box-shadow 0.25s,transform 0.25s;
    }
    .rev-kpi-card:hover { box-shadow:0 8px 28px rgba(15,23,42,0.06);transform:translateY(-2px); }
    .rev-kpi-card::after { content:'';position:absolute;top:-40%;right:-15%;width:100px;height:100px;border-radius:50%;background:rgba(255,255,255,0.4);pointer-events:none; }
    .rev-kpi-card.gradient-green { background:linear-gradient(135deg,#ecfdf5,#d1fae5); }
    .rev-kpi-card.gradient-blue { background:linear-gradient(135deg,#eff6ff,#dbeafe); }
    .rev-kpi-card.gradient-purple { background:linear-gradient(135deg,#faf5ff,#ede9fe); }
    .rev-kpi-card.gradient-amber { background:linear-gradient(135deg,#fffbeb,#fef3c7); }
    .rev-kpi-top { display:flex;align-items:center;justify-content:space-between; }
    .rev-kpi-icon { width:42px;height:42px;border-radius:12px;background:#fff;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 2px 8px rgba(0,0,0,0.05); }
    .icon-green { color:#16a34a; }
    .icon-blue { color:#2563eb; }
    .icon-purple { color:#7c3aed; }
    .icon-amber { color:#d97706; }
    .rev-kpi-trend { font-size:0.68rem;font-weight:700;padding:0.2rem 0.55rem;border-radius:99px; }
    .rev-kpi-trend.up { background:rgba(22,163,74,0.12);color:#16a34a; }
    .rev-kpi-trend.down { background:rgba(220,38,38,0.12);color:#dc2626; }
    .rev-kpi-label { font-size:0.72rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.04em; }
    .rev-kpi-value { font-size:1.6rem;font-weight:800;color:#0f172a;letter-spacing:-0.03em;line-height:1; }
    .rev-kpi-sub { font-size:0.68rem;font-weight:600;color:#94a3b8; }

    /* ── Mini KPI Row ── */
    .rev-kpi-grid-sm { grid-template-columns:repeat(4,1fr);margin-bottom:1.25rem; }
    .rev-mini-card {
        background:#fff;border-radius:12px;border:1px solid #e9edf5;padding:0.85rem 1rem;
        display:flex;flex-direction:column;gap:0.2rem;transition:box-shadow 0.2s;
    }
    .rev-mini-card:hover { box-shadow:0 2px 12px rgba(0,0,0,0.04); }
    .rev-mini-label { font-size:0.65rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.04em; }
    .rev-mini-value { font-size:1.1rem;font-weight:800;color:#0f172a;letter-spacing:-0.02em; }

    /* ── Main Chart ── */
    .rev-chart-card {
        background:#fff;border-radius:16px;border:1px solid #e9edf5;padding:1.25rem;
        margin-bottom:1.25rem;position:relative;overflow:hidden;
    }
    .rev-chart-card::before { content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,#16a34a,#22c55e,#7c3aed);border-radius:16px 16px 0 0; }
    .rev-chart-header { display:flex;align-items:center;justify-content:space-between;margin-bottom:0.75rem; }
    .rev-chart-header-left { display:flex;flex-direction:column;gap:0.15rem; }
    .rev-chart-title { font-size:0.92rem;font-weight:800;color:#0f172a;letter-spacing:-0.01em; }
    .rev-chart-subtitle { font-size:0.68rem;font-weight:600;color:#94a3b8; }
    .rev-chart-toggles { display:flex;gap:0.25rem;background:#f1f5f9;padding:0.2rem;border-radius:10px; }
    .rev-toggle {
        padding:0.35rem 0.85rem;border-radius:8px;border:none;background:transparent;
        font-size:0.72rem;font-weight:700;color:#64748b;cursor:pointer;transition:all 0.15s;
    }
    .rev-toggle.active { background:#fff;color:#0f172a;box-shadow:0 1px 3px rgba(0,0,0,0.06); }
    .rev-toggle:hover:not(.active) { color:#0f172a; }
    .rev-chart-stats { display:flex;gap:2rem;margin-bottom:0.75rem;padding-bottom:0.75rem;border-bottom:1px solid #f1f5f9; }
    .rev-chart-stat { display:flex;align-items:center;gap:0.4rem; }
    .rev-chart-stat-dot { width:8px;height:8px;border-radius:50%;flex-shrink:0; }
    .rev-chart-stat-label { font-size:0.68rem;font-weight:600;color:#64748b; }
    .rev-chart-stat-value { font-size:0.82rem;font-weight:800;color:#0f172a; }
    .rev-chart-wrap { position:relative;height:280px; }

    /* ── Bottom Row ── */
    .rev-bottom-row { display:grid;grid-template-columns:1fr 2fr;gap:1.25rem;margin-bottom:1.5rem; }

    /* ── Payment Methods ── */
    .rev-pm-card { background:#fff;border-radius:16px;border:1px solid #e9edf5;overflow:hidden; }
    .rev-pm-header { padding:0.9rem 1.25rem;border-bottom:1px solid #f1f5f9; }
    .rev-pm-title { font-size:0.88rem;font-weight:800;color:#0f172a; }
    .rev-pm-subtitle { font-size:0.68rem;font-weight:600;color:#94a3b8;margin-left:0.5rem; }
    .rev-pm-body { padding:0.5rem 0; }
    .rev-pm-empty { padding:2rem;text-align:center;color:#94a3b8;font-size:0.82rem; }
    .rev-pm-item { display:flex;align-items:center;gap:0.75rem;padding:0.65rem 1.25rem;transition:background 0.12s; }
    .rev-pm-item:hover { background:#f8fafc; }
    .rev-pm-icon { width:34px;height:34px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:0.72rem;font-weight:800;color:#fff;flex-shrink:0; }
    .rev-pm-info { flex:1;min-width:0; }
    .rev-pm-name { font-size:0.8rem;font-weight:700;color:#0f172a;text-transform:capitalize; }
    .rev-pm-count { font-size:0.65rem;color:#94a3b8;font-weight:600; }
    .rev-pm-amount { font-size:0.82rem;font-weight:800;color:#0f172a;white-space:nowrap; }
    .rev-pm-bar-bg { height:4px;background:#f1f5f9;border-radius:99px;margin-top:0.3rem; }
    .rev-pm-bar-fill { height:100%;border-radius:99px;transition:width 0.6s ease; }

    /* ── Transactions Table ── */
    .rev-tx-card { background:#fff;border-radius:16px;border:1px solid #e9edf5;overflow:hidden; }
    .rev-tx-header { padding:0.9rem 1.25rem;border-bottom:1px solid #f1f5f9; }
    .rev-tx-title { font-size:0.88rem;font-weight:800;color:#0f172a; }
    .rev-tx-body { max-height:380px;overflow-y:auto; }
    .rev-tx-body::-webkit-scrollbar { width:5px; }
    .rev-tx-body::-webkit-scrollbar-thumb { background:#e2e8f0;border-radius:99px; }
    .rev-tx-table { width:100%;border-collapse:collapse; }
    .rev-tx-table thead { position:sticky;top:0;background:#f8fafc;z-index:1; }
    .rev-tx-table th { padding:0.6rem 1.25rem;font-size:0.65rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:0.06em;text-align:left; }
    .rev-tx-table td { padding:0.65rem 1.25rem;font-size:0.78rem;color:#334155;border-bottom:1px solid #f8fafc; }
    .rev-tx-table tr:hover td { background:#f8fafc; }
    .rev-tx-empty { text-align:center;color:#94a3b8;padding:2rem;font-size:0.82rem; }
    .rev-tx-amount { font-weight:800;color:#16a34a; }
    .rev-tx-invoice { font-weight:700;color:#2563eb;font-size:0.75rem; }
    .rev-tx-badge { font-size:0.6rem;font-weight:700;padding:0.15rem 0.5rem;border-radius:99px;background:rgba(22,163,74,0.12);color:#16a34a; }

    @media (max-width:1200px) {
        .rev-kpi-grid { grid-template-columns:repeat(2,1fr); }
        .rev-kpi-grid-sm { grid-template-columns:repeat(2,1fr); }
        .rev-bottom-row { grid-template-columns:1fr; }
    }
    @media (max-width:768px) {
        .rev-kpi-grid { grid-template-columns:1fr; }
        .rev-kpi-grid-sm { grid-template-columns:1fr; }
        .rev-chart-stats { flex-wrap:wrap;gap:1rem; }
    }
</style>
@endsection
@section('scripts')
const API = '/api/admin/finance/revenue';
let revenueChart = null;
let chartData = null;
let currentView = 'monthly';

function fmt(n) {
    return 'TZS ' + Number(n).toLocaleString(undefined, { maximumFractionDigits: 0 });
}
function fmtShort(n) {
    if (n >= 1e9) return (n/1e9).toFixed(1) + 'B';
    if (n >= 1e6) return (n/1e6).toFixed(1) + 'M';
    if (n >= 1e3) return (n/1e3).toFixed(0) + 'K';
    return Math.round(n);
}

async function loadData() {
    try {
        const data = await apiFetch(API);
        chartData = data;
        renderKPIs(data.kpis);
        renderChart('monthly');
        renderPaymentMethods(data.payment_methods);
        renderTransactions(data.recent_transactions);
    } catch (e) {
        console.warn('Revenue data load failed', e);
    }
}

function renderKPIs(k) {
    const set = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
    set('monthRevenue', fmt(k.month_total));
    set('monthCount', k.month_count);
    set('todayRevenue', fmt(k.today_total));
    set('todayCount', k.today_count);
    set('yearRevenue', fmt(k.year_total));
    set('yearCount', k.year_count);
    set('avgTransaction', fmt(k.avg_transaction));
    set('monthExpenses', fmt(k.month_expenses));
    set('monthNet', fmt(k.month_net));
    set('yearExpenses', fmt(k.year_expenses));
    set('yearNet', fmt(k.year_net));
    set('allTimeTotal', fmt(k.all_time_total));
    set('pendingInvoices', k.pending_invoices);
    set('pendingAmount', fmt(k.pending_amount));
    set('pendingPayouts', fmt(k.pending_payouts));

    // MoM trend
    const trendEl = document.getElementById('momTrend');
    if (trendEl) {
        const change = k.mom_change || 0;
        trendEl.textContent = (change >= 0 ? '↑' : '↓') + ' ' + Math.abs(change) + '%';
        trendEl.className = 'rev-kpi-trend ' + (change >= 0 ? 'up' : 'down');
    }
}

function makeGrad(ctx, color) {
    const g = ctx.createLinearGradient(0, 0, 0, 260);
    g.addColorStop(0, color + '40');
    g.addColorStop(1, color + '02');
    return g;
}

function renderChart(view) {
    currentView = view;
    const isMonthly = view === 'monthly';
    const chart = isMonthly ? chartData.monthly_chart : chartData.weekly_chart;
    const labels = chart.labels;
    const revenue = chart.revenue;
    const expenses = isMonthly ? chart.expenses : [];
    const hasExpenses = isMonthly;

    // Update stats
    const revTotal = revenue.reduce((a, b) => a + b, 0);
    const expTotal = hasExpenses ? expenses.reduce((a, b) => a + b, 0) : 0;
    const netTotal = revTotal - expTotal;
    document.getElementById('chartRevenueTotal').textContent = fmt(revTotal);
    document.getElementById('chartExpensesTotal').textContent = hasExpenses ? fmt(expTotal) : '—';
    document.getElementById('chartNetTotal').textContent = hasExpenses ? fmt(netTotal) : '—';
    document.getElementById('chartSubtitle').textContent = isMonthly ? 'Last 12 Months' : 'Last 8 Weeks';

    // Toggle buttons
    document.getElementById('btnMonthly').classList.toggle('active', isMonthly);
    document.getElementById('btnWeekly').classList.toggle('active', !isMonthly);

    const ctx = document.getElementById('revenueChart').getContext('2d');
    if (revenueChart) revenueChart.destroy();

    const datasets = [{
        label: 'Revenue',
        data: revenue,
        borderColor: '#16a34a',
        backgroundColor: makeGrad(ctx, '#16a34a'),
        fill: true,
        tension: 0.4,
        pointRadius: 3,
        pointHoverRadius: 7,
        pointBackgroundColor: '#fff',
        pointBorderColor: '#16a34a',
        pointBorderWidth: 2,
        borderWidth: 2.5,
    }];

    if (hasExpenses) {
        datasets.push({
            label: 'Expenses',
            data: expenses,
            borderColor: '#ef4444',
            backgroundColor: makeGrad(ctx, '#ef4444'),
            fill: true,
            tension: 0.4,
            pointRadius: 3,
            pointHoverRadius: 7,
            pointBackgroundColor: '#fff',
            pointBorderColor: '#ef4444',
            pointBorderWidth: 2,
            borderWidth: 2.5,
        });
    }

    revenueChart = new Chart(ctx, {
        type: 'line',
        data: { labels, datasets },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleColor: '#fff',
                    bodyColor: '#e2e8f0',
                    padding: 12,
                    cornerRadius: 10,
                    displayColors: true,
                    boxPadding: 4,
                    titleFont: { size: 12, weight: '700' },
                    bodyFont: { size: 13, weight: '600' },
                    callbacks: {
                        label: ctx => ctx.dataset.label + ': TZS ' + ctx.parsed.y.toLocaleString(undefined, { maximumFractionDigits: 0 }),
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f5f9', drawBorder: false },
                    ticks: { color: '#94a3b8', font: { size: 10, weight: '600' }, callback: v => 'TZS ' + fmtShort(v) }
                },
                x: { grid: { display: false }, ticks: { color: '#94a3b8', font: { size: 10, weight: '600' } } }
            },
            interaction: { intersect: false, mode: 'index' },
            animation: { duration: 600, easing: 'easeOutQuart' },
        }
    });
}

function switchChart(view) {
    if (chartData) renderChart(view);
}

const pmColors = ['#2563eb', '#16a34a', '#d97706', '#7c3aed', '#dc2626', '#0891b2', '#64748b'];
function renderPaymentMethods(methods) {
    const body = document.getElementById('pmBody');
    if (!methods || methods.length === 0) {
        body.innerHTML = '<div class="rev-pm-empty">No payment data this month</div>';
        return;
    }
    const total = methods.reduce((sum, m) => sum + parseFloat(m.total), 0);
    body.innerHTML = methods.map((m, i) => {
        const color = pmColors[i % pmColors.length];
        const pct = total > 0 ? (parseFloat(m.total) / total * 100) : 0;
        const name = (m.payment_method || 'Unknown').replace(/_/g, ' ');
        const initials = name.substring(0, 2).toUpperCase();
        return `
        <div class="rev-pm-item">
            <div class="rev-pm-icon" style="background:${color}">${initials}</div>
            <div class="rev-pm-info">
                <div class="rev-pm-name">${name}</div>
                <div class="rev-pm-count">${m.count} payments</div>
                <div class="rev-pm-bar-bg"><div class="rev-pm-bar-fill" style="width:${pct}%;background:${color}"></div></div>
            </div>
            <div class="rev-pm-amount">${fmt(m.total)}</div>
        </div>`;
    }).join('');
}

function renderTransactions(txns) {
    const body = document.getElementById('txBody');
    if (!txns || txns.length === 0) {
        body.innerHTML = '<tr><td colspan="4" class="rev-tx-empty">No recent transactions</td></tr>';
        return;
    }
    body.innerHTML = txns.map(t => `
        <tr>
            <td><span class="rev-tx-invoice">${t.invoice_number || '—'}</span></td>
            <td>${t.user || '—'}</td>
            <td><span class="rev-tx-amount">${t.currency || 'TZS'} ${t.amount}</span></td>
            <td>${t.paid_at || '—'}</td>
        </tr>
    `).join('');
}

loadData();
@endsection
