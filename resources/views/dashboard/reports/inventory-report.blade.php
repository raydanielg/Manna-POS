@extends('layouts.dashboard')
@section('page_title','Inventory Report')

@section('page_styles')
@include('dashboard.reports._styles')
@endsection

@section('content')
<div class="dash-content rpt-page">

  <div class="rpt-header no-print">
    <div class="rpt-header-left">
      <div class="rpt-header-tag">
        <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        Inventory
      </div>
      <h1>Inventory Report</h1>
      <div class="rpt-header-sub">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
        Real-time stock levels &amp; valuations
        <span>&bull; {{ now()->format('M d, Y H:i') }}</span>
      </div>
    </div>
    <div class="rpt-header-right">
      <a href="{{ route('dashboard.reports.inventory-report.pdf') }}" class="rpt-btn rpt-btn-primary">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Download PDF
      </a>
      <button onclick="window.print()" class="rpt-btn rpt-btn-ghost">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        Print
      </button>
    </div>
  </div>

  <div class="rpt-kpis cols-4">
    <div class="rpt-kpi">
      <div class="rpt-kpi-icon blue"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg></div>
      <div class="rpt-kpi-body"><div class="rpt-kpi-label">Total Products</div><div class="rpt-kpi-value">{{ number_format($totalProducts) }}</div><div class="rpt-kpi-sub">SKUs in catalog</div></div>
    </div>
    <div class="rpt-kpi">
      <div class="rpt-kpi-icon indigo"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
      <div class="rpt-kpi-body"><div class="rpt-kpi-label">Stock Value (Cost)</div><div class="rpt-kpi-value blue">{{ $userCurrency }} {{ number_format($totalStockValue,2) }}</div><div class="rpt-kpi-sub">At purchase price</div></div>
    </div>
    <div class="rpt-kpi">
      <div class="rpt-kpi-icon green"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg></div>
      <div class="rpt-kpi-body"><div class="rpt-kpi-label">Retail Value</div><div class="rpt-kpi-value green">{{ $userCurrency }} {{ number_format($totalRetailValue,2) }}</div><div class="rpt-kpi-sub">At selling price</div></div>
    </div>
    <div class="rpt-kpi" style="border:2px solid rgba(224,48,87,.15);background:#fff8f8;">
      <div class="rpt-kpi-icon red"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg></div>
      <div class="rpt-kpi-body"><div class="rpt-kpi-label">Low Stock Alerts</div><div class="rpt-kpi-value red">{{ number_format($lowStock) }}</div><div class="rpt-kpi-sub">Need restocking</div></div>
    </div>
  </div>

  @if($categories->count())
  @php $catStockMax = $categories->max('stock') ?: 1; @endphp
  <div class="rpt-table-card">
    <div class="rpt-card-head"><span class="rpt-card-title">Stock by Category</span><span class="rpt-card-sub">{{ $categories->count() }} categories</span></div>
    <div style="overflow-x:auto;">
      <table class="rpt-table">
        <thead><tr><th>Category</th><th class="t-right">Products</th><th class="t-right">Stock Units</th><th style="width:200px;">Distribution</th></tr></thead>
        <tbody>
          @foreach($categories as $cat)
          <tr>
            <td class="t-name">{{ $cat->category }}</td>
            <td class="t-right t-muted">{{ $cat->count }}</td>
            <td class="t-right t-amt">{{ number_format($cat->stock) }}</td>
            <td style="padding-right:1.4rem;"><div class="rpt-bar-bg"><div class="rpt-bar-fill" style="width:{{ round($cat->stock/$catStockMax*100) }}%;background:#3b82f6;"></div></div></td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @endif

  <div class="rpt-table-card">
    <div class="rpt-card-head">
      <span class="rpt-card-title">Product Inventory</span>
      <span class="rpt-card-sub">{{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }} of {{ $products->total() }} products</span>
    </div>
    <div style="overflow-x:auto;">
      <table class="rpt-table">
        <thead><tr>
          <th>#</th><th>Product</th><th>SKU</th><th>Category</th>
          <th class="t-right">Stock</th><th class="t-right">Reorder</th>
          <th class="t-right">Buy Price</th><th class="t-right">Sell Price</th><th class="t-right">Stock Value</th>
        </tr></thead>
        <tbody>
          @forelse($products as $p)
          @php
            $isLow = $p->stock_quantity <= $p->reorder_level && $p->stock_quantity > 0;
            $isOut = $p->stock_quantity <= 0;
            $stockVal = $p->stock_quantity * $p->purchase_price;
          @endphp
          <tr>
            <td class="t-num">{{ $loop->iteration + ($products->currentPage()-1)*$products->perPage() }}</td>
            <td class="t-name">{{ $p->name }}</td>
            <td style="font-family:monospace;font-size:.75rem;color:#94a3b8;">{{ $p->sku ?? '—' }}</td>
            <td class="t-muted">{{ $p->category->name ?? 'N/A' }}</td>
            <td class="t-right">
              <span class="rpt-badge {{ $isOut ? 'rpt-badge-red' : ($isLow ? 'rpt-badge-amber' : 'rpt-badge-green') }}">{{ number_format($p->stock_quantity) }}</span>
            </td>
            <td class="t-right t-muted">{{ $p->reorder_level ?? 0 }}</td>
            <td class="t-right t-muted">{{ $userCurrency }} {{ number_format($p->purchase_price,2) }}</td>
            <td class="t-right t-muted">{{ $userCurrency }} {{ number_format($p->selling_price,2) }}</td>
            <td class="t-right t-amt">{{ $userCurrency }} {{ number_format($stockVal,2) }}</td>
          </tr>
          @empty
          <tr><td colspan="9"><div class="rpt-empty"><svg width="48" height="48" fill="none" stroke="#94a3b8" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg><p>No products found</p></div></td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($products->hasPages())
    <div class="rpt-pagination no-print"><span>Page {{ $products->currentPage() }} of {{ $products->lastPage() }}</span>{{ $products->links() }}</div>
    @endif
  </div>

</div>
@endsection
