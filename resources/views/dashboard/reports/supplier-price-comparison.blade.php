@extends('layouts.dashboard')
@section('page_title','Supplier Price Comparison')

@section('page_styles')
@include('dashboard.reports._styles')
@endsection

@section('content')
<div class="dash-content rpt-page">

  <div class="rpt-header no-print">
    <div class="rpt-header-left">
      <div class="rpt-header-tag">
        <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        Price Intelligence
      </div>
      <h1>Supplier Price Comparison</h1>
      <div class="rpt-header-sub">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
        Compare supplier pricing across products
        <span>&bull; {{ now()->format('M d, Y H:i') }}</span>
      </div>
    </div>
    <div class="rpt-header-right">
      <a href="{{ route('dashboard.reports.supplier-price-comparison.pdf') }}" class="rpt-btn rpt-btn-primary">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Download PDF
      </a>
      <button onclick="window.print()" class="rpt-btn rpt-btn-ghost">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        Print
      </button>
    </div>
  </div>

  <div class="rpt-table-card">
    <div class="rpt-card-head">
      <span class="rpt-card-title">Product &amp; Supplier Pricing</span>
      <span class="rpt-card-sub">Avg, lowest &amp; highest cost by supplier</span>
    </div>
    <div style="overflow-x:auto;">
      <table class="rpt-table">
        <thead><tr>
          <th>Product</th><th>SKU</th><th>Supplier</th>
          <th class="t-right">Avg Price</th><th class="t-right">Lowest</th>
          <th class="t-right">Highest</th><th class="t-right">Purchases</th>
        </tr></thead>
        <tbody>
          @forelse($products as $product)
            @php $items = $product->purchaseItems ?? collect(); $count = $items->count(); @endphp
            @foreach($items as $idx => $pi)
            <tr>
              @if($idx === 0)
              <td class="t-name" rowspan="{{ $count ?: 1 }}">{{ $product->name }}</td>
              <td style="font-family:monospace;font-size:.75rem;color:#94a3b8;" rowspan="{{ $count ?: 1 }}">{{ $product->sku ?? '—' }}</td>
              @endif
              <td class="t-muted">{{ $pi->supplier->name ?? 'N/A' }}</td>
              <td class="t-right t-amt">{{ $userCurrency }} {{ number_format($pi->avg_price,2) }}</td>
              <td class="t-right t-amt-green">{{ $userCurrency }} {{ number_format($pi->min_price,2) }}</td>
              <td class="t-right t-amt-red">{{ $userCurrency }} {{ number_format($pi->max_price,2) }}</td>
              <td class="t-right t-muted">{{ number_format($pi->purchases_count) }}</td>
            </tr>
            @endforeach
            @if($count === 0)
            <tr>
              <td class="t-name">{{ $product->name }}</td>
              <td style="font-family:monospace;font-size:.75rem;color:#94a3b8;">{{ $product->sku ?? '—' }}</td>
              <td colspan="5" class="t-muted" style="font-style:italic;">No purchase data available</td>
            </tr>
            @endif
          @empty
          <tr><td colspan="7"><div class="rpt-empty">
            <svg width="48" height="48" fill="none" stroke="#94a3b8" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <p>No products found</p><span>No supplier pricing data available</span>
          </div></td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection
