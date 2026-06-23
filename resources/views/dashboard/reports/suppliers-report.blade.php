@extends('layouts.dashboard')
@section('page_title','Suppliers Report')

@section('page_styles')
@include('dashboard.reports._styles')
@endsection

@section('content')
<div class="dash-content rpt-page">

  @php
    $totalSuppliers     = $suppliers->count();
    $activeSuppliers    = $suppliers->where('status','active')->count();
    $totalPurchaseValue = $suppliers->sum('purchases_total');
    $totalOrders        = $suppliers->sum('purchases_count');
  @endphp

  <div class="rpt-header no-print">
    <div class="rpt-header-left">
      <div class="rpt-header-tag">
        <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        Suppliers
      </div>
      <h1>Suppliers Report</h1>
      <div class="rpt-header-sub">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
        {{ $from->format('M d, Y') }} — {{ $to->format('M d, Y') }}
        <span>&bull; Generated {{ now()->format('M d, Y H:i') }}</span>
      </div>
    </div>
    <div class="rpt-header-right">
      <a href="{{ route('dashboard.reports.suppliers-report.pdf', ['from_date'=>request('from_date'),'to_date'=>request('to_date')]) }}" class="rpt-btn rpt-btn-primary">
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
      <div class="rpt-kpi-icon blue"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
      <div class="rpt-kpi-body"><div class="rpt-kpi-label">Total Suppliers</div><div class="rpt-kpi-value">{{ number_format($totalSuppliers) }}</div><div class="rpt-kpi-sub">All suppliers</div></div>
    </div>
    <div class="rpt-kpi">
      <div class="rpt-kpi-icon green"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
      <div class="rpt-kpi-body"><div class="rpt-kpi-label">Active Suppliers</div><div class="rpt-kpi-value green">{{ number_format($activeSuppliers) }}</div><div class="rpt-kpi-sub">Currently active</div></div>
    </div>
    <div class="rpt-kpi">
      <div class="rpt-kpi-icon amber"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
      <div class="rpt-kpi-body"><div class="rpt-kpi-label">Total Purchased</div><div class="rpt-kpi-value amber">{{ $userCurrency }} {{ number_format($totalPurchaseValue,2) }}</div><div class="rpt-kpi-sub">Total spend</div></div>
    </div>
    <div class="rpt-kpi">
      <div class="rpt-kpi-icon purple"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg></div>
      <div class="rpt-kpi-body"><div class="rpt-kpi-label">Total Orders</div><div class="rpt-kpi-value">{{ number_format($totalOrders) }}</div><div class="rpt-kpi-sub">Purchase orders</div></div>
    </div>
  </div>

  <div class="rpt-table-card">
    <div class="rpt-card-head"><span class="rpt-card-title">Supplier Details</span><span class="rpt-card-sub">{{ $totalSuppliers }} suppliers</span></div>
    <div style="overflow-x:auto;">
      <table class="rpt-table">
        <thead><tr>
          <th>#</th><th>Supplier</th><th>Company</th>
          <th class="t-right">Orders</th><th class="t-right">Total Amount</th>
          <th class="t-right">Balance</th><th>Status</th>
        </tr></thead>
        <tbody>
          @forelse($suppliers as $s)
          <tr>
            <td class="t-num">{{ $loop->iteration }}</td>
            <td class="t-name">{{ $s->name }}</td>
            <td class="t-muted">{{ $s->company ?? '—' }}</td>
            <td class="t-right t-muted">{{ number_format($s->purchases_count ?? 0) }}</td>
            <td class="t-right t-amt">{{ $userCurrency }} {{ number_format($s->purchases_total ?? 0,2) }}</td>
            <td class="t-right {{ ($s->balance ?? 0) > 0 ? 't-amt-red' : 't-amt-green' }}">{{ $userCurrency }} {{ number_format($s->balance ?? 0,2) }}</td>
            <td><span class="rpt-badge {{ $s->status === 'active' ? 'rpt-badge-green' : 'rpt-badge-slate' }}">{{ ucfirst($s->status ?? 'active') }}</span></td>
          </tr>
          @empty
          <tr><td colspan="7"><div class="rpt-empty"><svg width="48" height="48" fill="none" stroke="#94a3b8" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg><p>No suppliers found</p></div></td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection
