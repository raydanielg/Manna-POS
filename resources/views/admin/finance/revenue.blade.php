@extends('admin.layouts.app')
@section('page_title', 'Revenue Overview')
@section('content')
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:1.5rem;" id="statCards">
    <div class="stat-card">
        <div class="stat-value" id="todayRevenue">0</div>
        <div class="stat-label">Today's Revenue</div>
    </div>
    <div class="stat-card">
        <div class="stat-value" id="monthRevenue">0</div>
        <div class="stat-label">This Month</div>
    </div>
    <div class="stat-card">
        <div class="stat-value" id="yearRevenue">0</div>
        <div class="stat-label">This Year</div>
    </div>
    <div class="stat-card">
        <div class="stat-value" id="avgTransaction">0</div>
        <div class="stat-label">Avg Transaction</div>
    </div>
</div>

<div class="page-card" style="margin-bottom:1.25rem;">
    <div class="card-header">
        <div class="card-title">Revenue Trend</div>
        <div class="filters-row">
            <select class="form-control" style="width:auto;padding:0.45rem 0.75rem;font-size:0.78rem;" id="chartPeriod" onchange="updateChart()">
                <option value="weekly">Weekly</option>
                <option value="monthly">Monthly</option>
            </select>
        </div>
    </div>
    <div style="padding:1.25rem;">
        <canvas id="revenueChart" height="200"></canvas>
    </div>
</div>

<div class="page-card">
    <div class="card-header">
        <div class="card-title">Recent Transactions</div>
        <div class="filters-row">
            <div class="search-wrap">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" id="searchInput" placeholder="Search transactions..." oninput="loadTransactions()">
            </div>
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead><tr><th>Date</th><th>Description</th><th>Amount</th><th>Status</th></tr></thead>
            <tbody id="tableBody"><tr><td colspan="4" class="tbl-empty">Loading...</td></tr></tbody>
        </table>
    </div>
</div>
@endsection
@section('scripts')
const API = '/api/admin/finance/revenue';
let revenueChart = null;

async function loadStats() {
    const data = await apiFetch(API);
    if (data.summary) {
        document.getElementById('todayRevenue').textContent = (data.summary.today || 0).toLocaleString();
        document.getElementById('monthRevenue').textContent = (data.summary.month || 0).toLocaleString();
        document.getElementById('yearRevenue').textContent = (data.summary.year || 0).toLocaleString();
        document.getElementById('avgTransaction').textContent = (data.summary.avg_transaction || 0).toLocaleString();
    }
    return data;
}

async function loadTransactions() {
    const search = document.getElementById('searchInput').value;
    const data = await apiFetch(`${API}?search=${search}`);
    const tbody = document.getElementById('tableBody');
    const txns = data.transactions || data.recent_transactions || [];
    if (!txns.length) { tbody.innerHTML = '<tr><td colspan="4" class="tbl-empty">No transactions</td></tr>'; return; }
    tbody.innerHTML = txns.map(t => `<tr>
        <td>${t.date ? new Date(t.date).toLocaleDateString() : '-'}</td>
        <td>${t.description || t.desc || '-'}</td>
        <td>${t.amount || 0}</td>
        <td><span class="badge ${t.status === 'completed' || t.status === 'paid' ? 'badge-success' : t.status === 'pending' ? 'badge-pending' : t.status === 'failed' ? 'badge-danger' : 'badge-info'}">${t.status || '-'}</span></td>
    </tr>`).join('');
}

function initChart(labels, values) {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    if (revenueChart) revenueChart.destroy();
    revenueChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels || [],
            datasets: [{
                label: 'Revenue',
                data: values || [],
                borderColor: '#16a34a',
                backgroundColor: 'rgba(22,163,74,0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                x: { grid: { display: false } }
            }
        }
    });
}

async function updateChart() {
    const period = document.getElementById('chartPeriod').value;
    const data = await apiFetch(`${API}?period=${period}`);
    const chartData = data.chart || data.revenue_chart || { labels: [], values: [] };
    initChart(chartData.labels, chartData.values);
}

(async function init() {
    await loadStats();
    await loadTransactions();
    await updateChart();
})();
@endsection
