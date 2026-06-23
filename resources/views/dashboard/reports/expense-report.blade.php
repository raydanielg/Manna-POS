@extends('layouts.dashboard')
@section('page_title','Expense Report')

@section('page_styles')
@include('dashboard.reports._styles')
@endsection

@section('content')
<div class="dash-content rpt-page">

  <div class="rpt-header no-print">
    <div class="rpt-header-left">
      <div class="rpt-header-tag">
        <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
        Expenses
      </div>
      <h1>Expense Report</h1>
      <div class="rpt-header-sub">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
        {{ $from->format('M d, Y') }} — {{ $to->format('M d, Y') }}
        <span>&bull; Generated {{ now()->format('M d, Y H:i') }}</span>
      </div>
    </div>
    <div class="rpt-header-right">
      <a href="{{ route('dashboard.reports.expense-report.pdf', ['from_date'=>request('from_date'),'to_date'=>request('to_date')]) }}" class="rpt-btn rpt-btn-primary">
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
      <a href="{{ route('dashboard.reports.expense-report') }}" class="rpt-filter-btn rpt-filter-btn-reset">Reset</a>
    </div>
    <div class="rpt-presets">
      <button type="button" class="rpt-preset" onclick="setPreset('today')">Today</button>
      <button type="button" class="rpt-preset" onclick="setPreset('week')">This Week</button>
      <button type="button" class="rpt-preset" onclick="setPreset('month')">This Month</button>
      <button type="button" class="rpt-preset" onclick="setPreset('year')">This Year</button>
    </div>
  </form>

  <div class="rpt-kpis cols-2">
    <div class="rpt-kpi">
      <div class="rpt-kpi-icon amber"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg></div>
      <div class="rpt-kpi-body"><div class="rpt-kpi-label">Total Transactions</div><div class="rpt-kpi-value">{{ number_format($summary['total_expenses']) }}</div><div class="rpt-kpi-sub">Expense records</div></div>
    </div>
    <div class="rpt-kpi">
      <div class="rpt-kpi-icon red"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
      <div class="rpt-kpi-body"><div class="rpt-kpi-label">Total Amount</div><div class="rpt-kpi-value red">{{ $userCurrency }} {{ number_format($summary['total_amount'],2) }}</div><div class="rpt-kpi-sub">Total spent</div></div>
    </div>
  </div>

  @if($byCategory->count())
  @php $catMax = $byCategory->max('total') ?: 1; @endphp
  <div class="rpt-table-card">
    <div class="rpt-card-head"><span class="rpt-card-title">By Category</span><span class="rpt-card-sub">{{ $byCategory->count() }} categories</span></div>
    <div style="overflow-x:auto;">
      <table class="rpt-table">
        <thead><tr><th>Category</th><th class="t-right">Count</th><th class="t-right">Total</th><th style="width:180px;">Share</th></tr></thead>
        <tbody>
          @foreach($byCategory as $cat)
          <tr>
            <td class="t-name">{{ $cat->category }}</td>
            <td class="t-right t-muted">{{ $cat->count }}</td>
            <td class="t-right t-amt-red">{{ $userCurrency }} {{ number_format($cat->total,2) }}</td>
            <td style="padding-right:1.4rem;">
              <div class="rpt-bar-bg"><div class="rpt-bar-fill" style="width:{{ round($cat->total/$catMax*100) }}%;background:#f87171;"></div></div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @endif

  <div class="rpt-table-card">
    <div class="rpt-card-head">
      <span class="rpt-card-title">Expense Details</span>
      <span class="rpt-card-sub">{{ $expenses->firstItem() ?? 0 }}–{{ $expenses->lastItem() ?? 0 }} of {{ $expenses->total() }} records</span>
    </div>
    <div style="overflow-x:auto;">
      <table class="rpt-table">
        <thead><tr>
          <th>#</th><th>Reference</th><th>Category</th><th>Date</th>
          <th class="t-right">Amount</th><th>Payment</th>
        </tr></thead>
        <tbody>
          @forelse($expenses as $e)
          <tr>
            <td class="t-num">{{ $loop->iteration + ($expenses->currentPage()-1)*$expenses->perPage() }}</td>
            <td class="t-ref">{{ $e->reference ?? $e->id }}</td>
            <td class="t-name">{{ $e->category->name ?? 'N/A' }}</td>
            <td class="t-muted" style="white-space:nowrap;">{{ $e->expense_date ? \Carbon\Carbon::parse($e->expense_date)->format('M d, Y') : '—' }}</td>
            <td class="t-right t-amt-red">{{ $userCurrency }} {{ number_format($e->amount,2) }}</td>
            <td><span class="rpt-badge rpt-badge-blue">{{ $e->payment_method ?? '—' }}</span></td>
          </tr>
          @empty
          <tr><td colspan="6"><div class="rpt-empty"><svg width="48" height="48" fill="none" stroke="#94a3b8" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg><p>No expenses found for this period</p><span>Try a different date range</span></div></td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($expenses->hasPages())
    <div class="rpt-pagination no-print"><span>Page {{ $expenses->currentPage() }} of {{ $expenses->lastPage() }}</span>{{ $expenses->links() }}</div>
    @endif
  </div>

</div>
@endsection

@section('scripts')
<script>
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
