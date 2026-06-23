@extends('layouts.dashboard')
@section('page_title','Profit & Loss Report')

@section('page_styles')
@include('dashboard.reports._styles')
@endsection

@section('content')
<div class="dash-content rpt-page">

  {{-- Header --}}
  <div class="rpt-header no-print">
    <div class="rpt-header-left">
      <div class="rpt-header-tag">
        <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        Analytics
      </div>
      <h1>Profit &amp; Loss Report</h1>
      <div class="rpt-header-sub">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
        {{ $from->format('M d, Y') }} — {{ $to->format('M d, Y') }}
        <span>&bull; Generated {{ now()->format('M d, Y H:i') }}</span>
      </div>
    </div>
    <div class="rpt-header-right">
      <a href="{{ route('dashboard.reports.profit-loss-report.pdf', ['from_date'=>request('from_date'),'to_date'=>request('to_date')]) }}" class="rpt-btn rpt-btn-primary">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Download PDF
      </a>
      <button onclick="window.print()" class="rpt-btn rpt-btn-ghost">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        Print
      </button>
    </div>
  </div>

  {{-- Filter --}}
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
      <a href="{{ route('dashboard.reports.profit-loss-report') }}" class="rpt-filter-btn rpt-filter-btn-reset">Reset</a>
    </div>
    <div class="rpt-presets">
      <button type="button" class="rpt-preset" onclick="setPreset('today')">Today</button>
      <button type="button" class="rpt-preset" onclick="setPreset('week')">This Week</button>
      <button type="button" class="rpt-preset" onclick="setPreset('month')">This Month</button>
      <button type="button" class="rpt-preset" onclick="setPreset('year')">This Year</button>
    </div>
  </form>

  {{-- KPIs --}}
  <div class="rpt-kpis cols-4">
    <div class="rpt-kpi">
      <div class="rpt-kpi-icon green">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      </div>
      <div class="rpt-kpi-body">
        <div class="rpt-kpi-label">Total Revenue</div>
        <div class="rpt-kpi-value green">{{ $userCurrency }} {{ number_format($totalRevenue,2) }}</div>
        <div class="rpt-kpi-sub">Gross sales income</div>
      </div>
    </div>
    <div class="rpt-kpi">
      <div class="rpt-kpi-icon red">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
      </div>
      <div class="rpt-kpi-body">
        <div class="rpt-kpi-label">Purchase Cost</div>
        <div class="rpt-kpi-value red">{{ $userCurrency }} {{ number_format($totalCost,2) }}</div>
        <div class="rpt-kpi-sub">Cost of goods sold</div>
      </div>
    </div>
    <div class="rpt-kpi">
      <div class="rpt-kpi-icon amber">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
      </div>
      <div class="rpt-kpi-body">
        <div class="rpt-kpi-label">Expenses</div>
        <div class="rpt-kpi-value amber">{{ $userCurrency }} {{ number_format($totalExpenses,2) }}</div>
        <div class="rpt-kpi-sub">Operational costs</div>
      </div>
    </div>
    <div class="rpt-kpi" style="border: 2px solid {{ $netProfit >= 0 ? '#2563eb' : '#e03057' }}20; background: {{ $netProfit >= 0 ? '#eff6ff' : '#fff1f2' }};">
      <div class="rpt-kpi-icon {{ $netProfit >= 0 ? 'blue' : 'red' }}">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
      </div>
      <div class="rpt-kpi-body">
        <div class="rpt-kpi-label">Net Profit</div>
        <div class="rpt-kpi-value {{ $netProfit >= 0 ? 'blue' : 'red' }}">{{ $userCurrency }} {{ number_format($netProfit,2) }}</div>
        <div class="rpt-kpi-sub">Gross: {{ $userCurrency }} {{ number_format($grossProfit,2) }}</div>
      </div>
    </div>
  </div>

  {{-- Chart --}}
  <div class="rpt-chart-card">
    <div class="rpt-card-head">
      <span class="rpt-card-title">Monthly Profit &amp; Loss Overview</span>
      <span class="rpt-card-sub">Revenue vs Cost vs Net Profit</span>
    </div>
    <div class="rpt-chart-body" style="height:300px;"><canvas id="plChart"></canvas></div>
  </div>

  {{-- Table --}}
  <div class="rpt-table-card">
    <div class="rpt-card-head">
      <span class="rpt-card-title">Monthly Breakdown</span>
      <span class="rpt-card-sub">{{ count($monthly) }} months</span>
    </div>
    <div style="overflow-x:auto;">
      <table class="rpt-table">
        <thead>
          <tr>
            <th>Month</th>
            <th class="t-right">Revenue</th>
            <th class="t-right">Purchase Cost</th>
            <th class="t-right">Expenses</th>
            <th class="t-right">Gross Profit</th>
            <th class="t-right">Net Profit</th>
          </tr>
        </thead>
        <tbody>
          @foreach($monthly as $m)
          <tr>
            <td class="t-name">{{ $m['month'] }}</td>
            <td class="t-right t-amt-green">{{ $userCurrency }} {{ number_format($m['revenue'],2) }}</td>
            <td class="t-right t-amt-red">{{ $userCurrency }} {{ number_format($m['cost'],2) }}</td>
            <td class="t-right t-amt-amber">{{ $userCurrency }} {{ number_format($m['expenses'],2) }}</td>
            <td class="t-right t-amt">{{ $userCurrency }} {{ number_format($m['revenue'] - $m['cost'],2) }}</td>
            <td class="t-right {{ $m['profit'] >= 0 ? 't-amt-blue' : 't-amt-red' }}">{{ $userCurrency }} {{ number_format($m['profit'],2) }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('plChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode(array_column($monthly,'month')) !!},
        datasets: [
            { label: 'Revenue',    data: {!! json_encode(array_column($monthly,'revenue'))  !!}, backgroundColor: '#22c55e', borderRadius: 6, borderSkipped: false },
            { label: 'Cost',       data: {!! json_encode(array_column($monthly,'cost'))     !!}, backgroundColor: '#f87171', borderRadius: 6, borderSkipped: false },
            { label: 'Expenses',   data: {!! json_encode(array_column($monthly,'expenses')) !!}, backgroundColor: '#fbbf24', borderRadius: 6, borderSkipped: false },
            { label: 'Net Profit', data: {!! json_encode(array_column($monthly,'profit'))   !!}, backgroundColor: '#3b82f6', borderRadius: 6, borderSkipped: false }
        ]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { position:'top', labels:{ usePointStyle:true, boxWidth:8, font:{size:11} } } },
        scales: {
            x: { grid:{ display:false }, ticks:{ font:{size:10} } },
            y: { beginAtZero:true, grid:{ color:'#f1f5f9' }, ticks:{ font:{size:10}, callback: v => '{{ $userCurrency }} ' + Number(v).toLocaleString() } }
        }
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
