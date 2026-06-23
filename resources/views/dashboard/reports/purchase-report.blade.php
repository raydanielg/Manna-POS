@extends('layouts.dashboard')
@section('page_title','Purchase Report')

@section('page_styles')
@include('dashboard.reports._styles')
@endsection

@section('content')
<div class="dash-content rpt-page">

  <div class="rpt-header no-print">
    <div class="rpt-header-left">
      <div class="rpt-header-tag">
        <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
        Purchases
      </div>
      <h1>Purchase Report</h1>
      <div class="rpt-header-sub">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
        {{ $from->format('M d, Y') }} — {{ $to->format('M d, Y') }}
        <span>&bull; Generated {{ now()->format('M d, Y H:i') }}</span>
      </div>
    </div>
    <div class="rpt-header-right">
      <a href="{{ route('dashboard.reports.purchase-report.pdf', ['from_date'=>request('from_date'),'to_date'=>request('to_date')]) }}" class="rpt-btn rpt-btn-primary">
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
      <a href="{{ route('dashboard.reports.purchase-report') }}" class="rpt-filter-btn rpt-filter-btn-reset">Reset</a>
    </div>
    <div class="rpt-presets">
      <button type="button" class="rpt-preset" onclick="setPreset('today')">Today</button>
      <button type="button" class="rpt-preset" onclick="setPreset('week')">This Week</button>
      <button type="button" class="rpt-preset" onclick="setPreset('month')">This Month</button>
      <button type="button" class="rpt-preset" onclick="setPreset('year')">This Year</button>
    </div>
  </form>

  <div class="rpt-kpis cols-3">
    <div class="rpt-kpi">
      <div class="rpt-kpi-icon purple"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg></div>
      <div class="rpt-kpi-body"><div class="rpt-kpi-label">Total Orders</div><div class="rpt-kpi-value">{{ number_format($summary['total_purchases']) }}</div><div class="rpt-kpi-sub">Purchase orders</div></div>
    </div>
    <div class="rpt-kpi">
      <div class="rpt-kpi-icon blue"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
      <div class="rpt-kpi-body"><div class="rpt-kpi-label">Total Amount</div><div class="rpt-kpi-value blue">{{ $userCurrency }} {{ number_format($summary['total_amount'],2) }}</div><div class="rpt-kpi-sub">Total value ordered</div></div>
    </div>
    <div class="rpt-kpi">
      <div class="rpt-kpi-icon green"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></div>
      <div class="rpt-kpi-body"><div class="rpt-kpi-label">Total Paid</div><div class="rpt-kpi-value green">{{ $userCurrency }} {{ number_format($summary['total_paid'],2) }}</div><div class="rpt-kpi-sub">Payments made</div></div>
    </div>
  </div>

  <div class="rpt-table-card">
    <div class="rpt-card-head">
      <span class="rpt-card-title">Purchase Details</span>
      <span class="rpt-card-sub">{{ $purchases->firstItem() ?? 0 }}–{{ $purchases->lastItem() ?? 0 }} of {{ $purchases->total() }} records</span>
    </div>
    <div style="overflow-x:auto;">
      <table class="rpt-table">
        <thead><tr>
          <th>#</th><th>Reference</th><th>Supplier</th><th>Date</th>
          <th class="t-right">Total</th><th>Payment</th><th>Status</th>
        </tr></thead>
        <tbody>
          @forelse($purchases as $p)
          <tr>
            <td class="t-num">{{ $loop->iteration + ($purchases->currentPage()-1)*$purchases->perPage() }}</td>
            <td class="t-ref">{{ $p->reference ?? $p->id }}</td>
            <td class="t-name">{{ $p->supplier->name ?? 'N/A' }}</td>
            <td class="t-muted" style="white-space:nowrap;">{{ $p->purchase_date ? \Carbon\Carbon::parse($p->purchase_date)->format('M d, Y') : '—' }}</td>
            <td class="t-right t-amt">{{ $userCurrency }} {{ number_format($p->total,2) }}</td>
            <td>
              @php $ps = $p->payment_status ?? ''; @endphp
              @if($ps=='paid') <span class="rpt-badge rpt-badge-green">Paid</span>
              @elseif($ps=='partial') <span class="rpt-badge rpt-badge-amber">Partial</span>
              @elseif($ps=='unpaid') <span class="rpt-badge rpt-badge-red">Unpaid</span>
              @else <span class="rpt-badge rpt-badge-slate">{{ ucfirst($ps ?: '—') }}</span>
              @endif
            </td>
            <td>
              @php $ss = $p->status ?? ''; @endphp
              @if($ss=='received') <span class="rpt-badge rpt-badge-green">Received</span>
              @elseif($ss=='pending') <span class="rpt-badge rpt-badge-amber">Pending</span>
              @elseif($ss=='cancelled') <span class="rpt-badge rpt-badge-red">Cancelled</span>
              @else <span class="rpt-badge rpt-badge-slate">{{ ucfirst($ss ?: 'Draft') }}</span>
              @endif
            </td>
          </tr>
          @empty
          <tr><td colspan="7"><div class="rpt-empty"><svg width="48" height="48" fill="none" stroke="#94a3b8" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg><p>No purchases found for this period</p><span>Try a different date range</span></div></td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($purchases->hasPages())
    <div class="rpt-pagination no-print"><span>Page {{ $purchases->currentPage() }} of {{ $purchases->lastPage() }}</span>{{ $purchases->links() }}</div>
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
