@extends('dashboard.reports.pdf.layouts.a4')
@section('title', 'Suppliers Report')
@section('subtitle', 'Supplier performance and purchase summary')
@section('date_range', $from->format('M d, Y') . ' — ' . $to->format('M d, Y'))
@section('content')
@php
    $totalSuppliers = $suppliers->count();
    $activeSuppliers = $suppliers->where('status','active')->count();
    $totalPurchaseValue = $suppliers->sum('purchases_total');
    $totalOrders = $suppliers->sum('purchases_count');
@endphp
<div class="summary-grid">
    <div class="summary-card"><div class="label">Total Suppliers</div><div class="value text-blue">{{ number_format($totalSuppliers) }}</div></div>
    <div class="summary-card"><div class="label">Active Suppliers</div><div class="value text-green">{{ number_format($activeSuppliers) }}</div></div>
    <div class="summary-card"><div class="label">Purchase Value</div><div class="value" style="color:#d97706;">{{ $userCurrency }} {{ number_format($totalPurchaseValue,2) }}</div></div>
    <div class="summary-card"><div class="label">Total Orders</div><div class="value text-red">{{ number_format($totalOrders) }}</div></div>
</div>

<table>
    <thead><tr><th>#</th><th>Supplier</th><th>Company</th><th class="text-right">Orders</th><th class="text-right">Total Amount</th><th class="text-right">Balance</th><th>Status</th></tr></thead>
    <tbody>
        @forelse($suppliers as $i => $s)
        <tr>
            <td>{{ $i+1 }}</td>
            <td><strong>{{ $s->name }}</strong></td>
            <td>{{ $s->company ?? '—' }}</td>
            <td class="text-right">{{ number_format($s->purchases_count ?? 0) }}</td>
            <td class="text-right">{{ $userCurrency }} {{ number_format($s->purchases_total ?? 0,2) }}</td>
            <td class="text-right {{ ($s->balance ?? 0) > 0 ? 'text-red' : 'text-green' }}">{{ $userCurrency }} {{ number_format($s->balance ?? 0,2) }}</td>
            <td><span class="badge badge-{{ $s->status === 'active' ? 'success' : 'gray' }}">{{ ucfirst($s->status ?? 'active') }}</span></td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center">No suppliers found.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
