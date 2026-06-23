@extends('layouts.dashboard')
@section('page_title','Product Trends')

@section('page_styles')
@include('dashboard.reports._styles')
@endsection

@section('content')
<div class="dash-content rpt-page">

  @php
    $totalProducts = $trends->count();
    $totalRevenue  = $trends->sum('total_revenue');
    $totalQty      = $trends->sum('total_qty');
    $maxRevenue    = $trends->max('total_revenue') ?: 1;
  @endphp

  <div class="rpt-header no-print">
    <div class="rpt-header-left">
      <div class="rpt-header-tag">
        <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
        Trends
      </div>
      <h1>Product Trends Report</h1>
      <div class="rpt-header-sub">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
        {{ $from->format('M d, Y') }} — {{ $to->format('M d, Y') }}
        <span>&bull; Generated {{ now()->format('M d, Y H:i') }}</span>
      </div>
    </div>
    <div class="rpt-header-right">
      <a href="{{ route('dashboard.reports.product-trends-report.pdf', ['from_date'=>request('from_date'),'to_date'=>request('to_date')]) }}" class="rpt-btn rpt-btn-primary">
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
      <a href="{{ route('dashboard.reports.product-trends-report') }}" class="rpt-filter-btn rpt-filter-btn-reset">Reset</a>
    </div>
    <div class="rpt-presets">
      <button type="button" class="rpt-preset" onclick="setPreset('week')">This Week</button>
      <button type="button" class="rpt-preset" onclick="setPreset('month')">This Month</button>
      <button type="button" class="rpt-preset" onclick="setPreset('year')">This Year</button>
    </div>
  </form>

  <div class="rpt-kpis cols-3">
    <div class="rpt-kpi">
      <div class="rpt-kpi-icon blue"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg></div>
      <div class="rpt-kpi-body"><div class="rpt-kpi-label">Unique Products Sold</div><div class="rpt-kpi-value">{{ number_format($totalProducts) }}</div><div class="rpt-kpi-sub">Distinct product lines</div></div>
    </div>
    <div class="rpt-kpi">
      <div class="rpt-kpi-icon green"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
      <div class="rpt-kpi-body"><div class="rpt-kpi-label">Total Revenue</div><div class="rpt-kpi-value green">{{ $userCurrency }} {{ number_format($totalRevenue,2) }}</div><div class="rpt-kpi-sub">From product sales</div></div>
    </div>
    <div class="rpt-kpi">
      <div class="rpt-kpi-icon amber"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg></div>
      <div class="rpt-kpi-body"><div class="rpt-kpi-label">Total Units Sold</div><div class="rpt-kpi-value amber">{{ number_format($totalQty) }}</div><div class="rpt-kpi-sub">Total quantity sold</div></div>
    </div>
  </div>

  <div class="rpt-table-card">
    <div class="rpt-card-head">
      <span class="rpt-card-title">Top Selling Products</span>
      <span class="rpt-card-sub">Ranked by revenue</span>
    </div>
    <div style="overflow-x:auto;">
      <table class="rpt-table">
        <thead><tr>
          <th>#</th><th>Product</th>
          <th class="t-right">Qty Sold</th><th class="t-right">Revenue</th>
          <th class="t-right">Orders</th><th style="width:180px;">Revenue Share</th>
        </tr></thead>
        <tbody>
          @forelse($trends as $t)
          <tr>
            <td>
              @if($loop->iteration <= 3)
                <span class="rpt-badge {{ $loop->iteration==1 ? 'rpt-badge-amber' : ($loop->iteration==2 ? 'rpt-badge-slate' : 'rpt-badge-slate') }}" style="{{ $loop->iteration==1 ? 'background:#fef3c7;color:#92400e;border-color:#fde68a;' : '' }}">{{ $loop->iteration }}</span>
              @else
                <span class="t-num">{{ $loop->iteration }}</span>
              @endif
            </td>
            <td class="t-name">{{ $t->product_name }}</td>
            <td class="t-right t-muted">{{ number_format($t->total_qty) }}</td>
            <td class="t-right t-amt-green">{{ $userCurrency }} {{ number_format($t->total_revenue,2) }}</td>
            <td class="t-right t-muted">{{ number_format($t->sales_count) }}</td>
            <td style="padding-right:1.4rem;">
              <div style="display:flex;align-items:center;gap:.5rem;">
                <div class="rpt-bar-bg" style="flex:1;"><div class="rpt-bar-fill" style="width:{{ round($t->total_revenue/$maxRevenue*100) }}%;background:linear-gradient(90deg,#22c55e,#16a34a);"></div></div>
                <span style="font-size:.68rem;color:#94a3b8;width:36px;text-align:right;">{{ round($t->total_revenue/max($totalRevenue,1)*100) }}%</span>
              </div>
            </td>
          </tr>
          @empty
          <tr><td colspan="6"><div class="rpt-empty"><svg width="48" height="48" fill="none" stroke="#94a3b8" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg><p>No product trends data found for this period</p><span>Try a different date range</span></div></td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection

@section('scripts')
<script>
function setPreset(p) {
    const now = new Date(), f = document.getElementById('f_from'), t = document.getElementById('f_to');
    const fmt = d => d.toISOString().split('T')[0];
    t.value = fmt(now);
    if (p === 'week') { const d = new Date(now); d.setDate(d.getDate() - d.getDay()); f.value = fmt(d); }
    else if (p === 'month') { f.value = fmt(new Date(now.getFullYear(), now.getMonth(), 1)); }
    else if (p === 'year')  { f.value = fmt(new Date(now.getFullYear(), 0, 1)); }
    document.querySelectorAll('.rpt-preset').forEach(el => el.classList.remove('active'));
    event.target.classList.add('active');
}
</script>
@endsection
