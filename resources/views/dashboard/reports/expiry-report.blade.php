@extends('layouts.dashboard')
@section('page_title','Expiry Date Report')

@section('page_styles')
@include('dashboard.reports._styles')
@endsection

@section('content')
<div class="dash-content rpt-page">

  <div class="rpt-header no-print">
    <div class="rpt-header-left">
      <div class="rpt-header-tag" style="color:#f87171;background:rgba(248,113,113,.15);border-color:rgba(248,113,113,.25);">
        <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        Expiry Alerts
      </div>
      <h1>Expiry Date Report</h1>
      <div class="rpt-header-sub">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
        {{ $from->format('M d, Y') }} — {{ $to->format('M d, Y') }}
        <span>&bull; Generated {{ now()->format('M d, Y H:i') }}</span>
      </div>
    </div>
    <div class="rpt-header-right">
      <a href="{{ route('dashboard.reports.expiry-report.pdf', ['from_date'=>request('from_date'),'to_date'=>request('to_date')]) }}" class="rpt-btn rpt-btn-primary">
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
      <a href="{{ route('dashboard.reports.expiry-report') }}" class="rpt-filter-btn rpt-filter-btn-reset">Reset</a>
    </div>
    <div class="rpt-presets">
      <button type="button" class="rpt-preset" onclick="setPreset('week')">Next 7 Days</button>
      <button type="button" class="rpt-preset" onclick="setPreset('month')">Next 30 Days</button>
      <button type="button" class="rpt-preset" onclick="setPreset('quarter')">Next 90 Days</button>
    </div>
  </form>

  <div class="rpt-kpis cols-2">
    <div class="rpt-kpi" style="border:2px solid rgba(224,48,87,.15);background:#fff8f8;">
      <div class="rpt-kpi-icon red"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg></div>
      <div class="rpt-kpi-body"><div class="rpt-kpi-label">Already Expired</div><div class="rpt-kpi-value red">{{ number_format($expired) }}</div><div class="rpt-kpi-sub">Batches past expiry date</div></div>
    </div>
    <div class="rpt-kpi" style="border:2px solid rgba(217,119,6,.15);background:#fffdf0;">
      <div class="rpt-kpi-icon amber"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
      <div class="rpt-kpi-body"><div class="rpt-kpi-label">Expiring Soon</div><div class="rpt-kpi-value amber">{{ number_format($expiringSoon) }}</div><div class="rpt-kpi-sub">Within the next 30 days</div></div>
    </div>
  </div>

  <div class="rpt-table-card">
    <div class="rpt-card-head">
      <span class="rpt-card-title">Product Batches by Expiry</span>
      <span class="rpt-card-sub">{{ $batches->firstItem() ?? 0 }}–{{ $batches->lastItem() ?? 0 }} of {{ $batches->total() }} batches</span>
    </div>
    <div style="overflow-x:auto;">
      <table class="rpt-table">
        <thead><tr>
          <th>#</th><th>Product</th><th>SKU</th><th>Batch #</th>
          <th>Supplier</th><th class="t-right">Qty</th><th>Expiry Date</th><th>Status</th>
        </tr></thead>
        <tbody>
          @forelse($batches as $i => $b)
          @php
            $expiry = $b->expiry_date ? \Carbon\Carbon::parse($b->expiry_date) : null;
            $daysLeft = $expiry ? now()->diffInDays($expiry, false) : null;
            if ($daysLeft === null)  { $cls = 'rpt-badge-slate'; $label = 'No Date'; }
            elseif ($daysLeft < 0)  { $cls = 'rpt-badge-red';   $label = 'Expired'; }
            elseif ($daysLeft <= 7) { $cls = 'rpt-badge-red';   $label = $daysLeft.'d left'; }
            elseif ($daysLeft <= 30){ $cls = 'rpt-badge-amber'; $label = $daysLeft.'d left'; }
            else                    { $cls = 'rpt-badge-green'; $label = $daysLeft.'d left'; }
          @endphp
          <tr>
            <td class="t-num">{{ $i + 1 + ($batches->currentPage()-1)*$batches->perPage() }}</td>
            <td class="t-name">{{ $b->product->name ?? 'N/A' }}</td>
            <td style="font-family:monospace;font-size:.75rem;color:#94a3b8;">{{ $b->product->sku ?? '—' }}</td>
            <td class="t-muted">{{ $b->batch_number ?? '—' }}</td>
            <td class="t-muted">{{ $b->supplier->name ?? '—' }}</td>
            <td class="t-right t-muted">{{ number_format($b->quantity) }}</td>
            <td class="t-muted" style="white-space:nowrap;">{{ $expiry ? $expiry->format('M d, Y') : '—' }}</td>
            <td><span class="rpt-badge {{ $cls }}">{{ $label }}</span></td>
          </tr>
          @empty
          <tr><td colspan="8"><div class="rpt-empty">
            <svg width="48" height="48" fill="none" stroke="#94a3b8" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p>No expiry records found for this period</p><span>Try adjusting the date range</span>
          </div></td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($batches->hasPages())
    <div class="rpt-pagination no-print"><span>Page {{ $batches->currentPage() }} of {{ $batches->lastPage() }}</span>{{ $batches->links() }}</div>
    @endif
  </div>

</div>
@endsection

@section('scripts')
<script>
function setPreset(p) {
    const now = new Date(), f = document.getElementById('f_from'), t = document.getElementById('f_to');
    const fmt = d => d.toISOString().split('T')[0];
    f.value = fmt(now);
    if (p === 'week')    { const d = new Date(now); d.setDate(d.getDate()+7);   t.value = fmt(d); }
    else if (p === 'month')  { const d = new Date(now); d.setDate(d.getDate()+30);  t.value = fmt(d); }
    else if (p === 'quarter'){ const d = new Date(now); d.setDate(d.getDate()+90);  t.value = fmt(d); }
    document.querySelectorAll('.rpt-preset').forEach(el => el.classList.remove('active'));
    event.target.classList.add('active');
}
</script>
@endsection
