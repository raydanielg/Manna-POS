@extends('layouts.dashboard')
@section('page_title','Sales Report')

@section('page_styles')
@include('dashboard.reports._styles')
@endsection

@section('content')
<div class="dash-content rpt-page">

  <div class="rpt-header no-print">
    <div class="rpt-header-left">
      <div class="rpt-header-tag">
        <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        Sales Analytics
      </div>
      <h1>Sales Report</h1>
      <div class="rpt-header-sub">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
        {{ $from->format('M d, Y') }} — {{ $to->format('M d, Y') }}
        <span>&bull; Generated {{ now()->format('M d, Y H:i') }}</span>
      </div>
    </div>
    <div class="rpt-header-right">
      <a href="{{ route('dashboard.reports.sales-report.pdf', ['from_date'=>request('from_date'),'to_date'=>request('to_date')]) }}" class="rpt-btn rpt-btn-primary">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Download PDF
      </a>
      <button onclick="window.print()" class="rpt-btn rpt-btn-ghost">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        Print
      </button>
    </div>
  </div>

  <form method="GET" class="rpt-filter no-print">
    <div class="rpt-filter-group">
      <span class="rpt-filter-label">From Date</span>
      <input type="date" name="from_date" id="f_from" value="{{ request('from_date',$from->format('Y-m-d')) }}">
    </div>
    <div class="rpt-filter-group">
      <span class="rpt-filter-label">To Date</span>
      <input type="date" name="to_date" id="f_to" value="{{ request('to_date',$to->format('Y-m-d')) }}">
    </div>
    <div class="rpt-filter-actions">
      <button type="submit" class="rpt-filter-btn rpt-filter-btn-primary">Generate</button>
      <a href="{{ route('dashboard.reports.sales-report') }}" class="rpt-filter-btn rpt-filter-btn-reset">Reset</a>
    </div>
    <div class="rpt-presets">
      <button type="button" class="rpt-preset" onclick="setPreset('today')">Today</button>
      <button type="button" class="rpt-preset" onclick="setPreset('week')">This Week</button>
      <button type="button" class="rpt-preset" onclick="setPreset('month')">This Month</button>
      <button type="button" class="rpt-preset" onclick="setPreset('year')">This Year</button>
    </div>
  </form>

  <div class="rpt-kpis cols-4">
    <div class="rpt-kpi">
      <div class="rpt-kpi-icon blue"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg></div>
      <div class="rpt-kpi-body"><div class="rpt-kpi-label">Total Orders</div><div class="rpt-kpi-value">{{ number_format($summary['total_sales']) }}</div><div class="rpt-kpi-sub">All transactions</div></div>
    </div>
    <div class="rpt-kpi">
      <div class="rpt-kpi-icon indigo"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
      <div class="rpt-kpi-body"><div class="rpt-kpi-label">Total Revenue</div><div class="rpt-kpi-value">{{ $userCurrency }} {{ number_format($summary['total_revenue'],2) }}</div><div class="rpt-kpi-sub">Gross sales</div></div>
    </div>
    <div class="rpt-kpi">
      <div class="rpt-kpi-icon green"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></div>
      <div class="rpt-kpi-body"><div class="rpt-kpi-label">Total Paid</div><div class="rpt-kpi-value green">{{ $userCurrency }} {{ number_format($summary['total_paid'],2) }}</div><div class="rpt-kpi-sub">Collected payments</div></div>
    </div>
    <div class="rpt-kpi">
      <div class="rpt-kpi-icon red"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
      <div class="rpt-kpi-body"><div class="rpt-kpi-label">Outstanding</div><div class="rpt-kpi-value red">{{ $userCurrency }} {{ number_format($summary['total_outstanding'],2) }}</div><div class="rpt-kpi-sub">Pending collection</div></div>
    </div>
  </div>

  <div class="rpt-chart-grid cols-2">
    <div class="rpt-chart-card" style="margin-bottom:0;">
      <div class="rpt-card-head"><span class="rpt-card-title">Daily Sales Trend</span><span class="rpt-card-sub">Revenue &amp; order volume</span></div>
      <div class="rpt-chart-body" style="height:260px;"><canvas id="salesChart"></canvas></div>
    </div>
    <div class="rpt-chart-card" style="margin-bottom:0;">
      <div class="rpt-card-head"><span class="rpt-card-title">Top Products by Revenue</span><span class="rpt-card-sub">Best sellers</span></div>
      <div class="rpt-chart-body" style="height:260px;"><canvas id="productsChart"></canvas></div>
    </div>
  </div>
  <div style="margin-bottom:1.5rem;"></div>

  <div class="rpt-table-card">
    <div class="rpt-card-head">
      <span class="rpt-card-title">Sales Details</span>
      <span class="rpt-card-sub">{{ $sales->firstItem() ?? 0 }}–{{ $sales->lastItem() ?? 0 }} of {{ $sales->total() }} records</span>
    </div>
    <div style="overflow-x:auto;">
      <table class="rpt-table">
        <thead><tr>
          <th>#</th><th>Reference</th><th>Customer</th><th>Date</th>
          <th class="t-right">Total</th><th class="t-right">Paid</th>
          <th>Method</th><th>Status</th>
        </tr></thead>
        <tbody>
          @forelse($sales as $s)
          <tr>
            <td class="t-num">{{ $loop->iteration + ($sales->currentPage()-1)*$sales->perPage() }}</td>
            <td class="t-ref">{{ $s->reference ?? $s->id }}</td>
            <td class="t-name">{{ $s->customer->name ?? 'Walk-in' }}</td>
            <td class="t-muted" style="white-space:nowrap;">{{ $s->sale_date ? \Carbon\Carbon::parse($s->sale_date)->format('M d, Y') : '—' }}</td>
            <td class="t-right t-amt">{{ $userCurrency }} {{ number_format($s->total,2) }}</td>
            <td class="t-right t-amt-green">{{ $userCurrency }} {{ number_format($s->paid,2) }}</td>
            <td><span class="rpt-badge rpt-badge-blue">{{ $s->payment_method ?? '—' }}</span></td>
            <td>
              @if($s->status=='completed') <span class="rpt-badge rpt-badge-green">Completed</span>
              @elseif($s->status=='pending') <span class="rpt-badge rpt-badge-amber">Pending</span>
              @else <span class="rpt-badge rpt-badge-slate">{{ ucfirst($s->status ?? 'Draft') }}</span>
              @endif
            </td>
          </tr>
          @empty
          <tr><td colspan="8"><div class="rpt-empty"><svg width="48" height="48" fill="none" stroke="#94a3b8" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg><p>No sales found for this period</p><span>Try a different date range</span></div></td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($sales->hasPages())
    <div class="rpt-pagination no-print"><span>Page {{ $sales->currentPage() }} of {{ $sales->lastPage() }}</span>{{ $sales->links() }}</div>
    @endif
  </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const salesLabels = {!! json_encode($dailySales->pluck('date')) !!};
const salesRevenue = {!! json_encode($dailySales->pluck('revenue')) !!};
const salesCount = {!! json_encode($dailySales->pluck('count')) !!};

new Chart(document.getElementById('salesChart'), {
    type: 'line',
    data: {
        labels: salesLabels,
        datasets: [
            { label: 'Revenue', data: salesRevenue, borderColor: '#2563eb', backgroundColor: 'rgba(37,99,235,0.08)', fill: true, tension: 0.4, pointRadius: 3, pointBackgroundColor: '#2563eb' },
            { label: 'Orders', data: salesCount, borderColor: '#16a34a', backgroundColor: 'transparent', tension: 0.4, pointRadius: 3, pointBackgroundColor: '#16a34a', yAxisID: 'y1' }
        ]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: { legend: { position:'top', labels:{ usePointStyle:true, boxWidth:8 } } },
        scales: {
            x: { grid:{ display:false }, ticks:{ font:{size:10} } },
            y: { beginAtZero:true, grid:{ color:'#f1f5f9' }, ticks:{ callback:v=>window.__USER_CURRENCY+' '+Number(v).toLocaleString() } },
            y1: { position:'right', beginAtZero:true, grid:{ display:false }, ticks:{ stepSize:1 } }
        }
    }
});

new Chart(document.getElementById('productsChart'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($topProducts->pluck('product_name')->take(6)) !!},
        datasets: [{ data: {!! json_encode($topProducts->pluck('total_revenue')->take(6)) !!}, backgroundColor: ['#0a192f','#1e3a8a','#2563eb','#60a5fa','#93c5fd','#dbeafe'], borderWidth:0 }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { position:'right', labels:{ boxWidth:10, font:{size:10} } } }
    }
});

function setPreset(p) {
    const now = new Date(), f = document.getElementById('f_from'), t = document.getElementById('f_to');
    const fmt = d => d.toISOString().split('T')[0];
    t.value = fmt(now);
    if (p === 'today') { f.value = fmt(now); }
    else if (p === 'week') { const d = new Date(now); d.setDate(d.getDate() - d.getDay()); f.value = fmt(d); }
    else if (p === 'month') { f.value = fmt(new Date(now.getFullYear(), now.getMonth(), 1)); }
    else if (p === 'year') { f.value = fmt(new Date(now.getFullYear(), 0, 1)); }
    document.querySelectorAll('.rpt-preset').forEach(el => el.classList.remove('active'));
    event.target.classList.add('active');
}
</script>
@endsection
