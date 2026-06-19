@extends('dashboard.reports.pdf.layouts.a4')
@section('title', 'Product Trends Report')
@section('subtitle', 'Top selling products by revenue and quantity')
@section('date_range', $from->format('M d, Y') . ' — ' . $to->format('M d, Y'))
@section('content')
@php
    $totalProducts = $trends->count();
    $totalRevenue = $trends->sum('total_revenue');
    $totalQty = $trends->sum('total_qty');
@endphp
<div class="summary-grid">
    <div class="summary-card"><div class="label">Products Sold</div><div class="value text-blue">{{ number_format($totalProducts) }}</div></div>
    <div class="summary-card"><div class="label">Total Revenue</div><div class="value text-green">{{ $userCurrency }} {{ number_format($totalRevenue,2) }}</div></div>
    <div class="summary-card"><div class="label">Total Qty Sold</div><div class="value" style="color:#d97706;">{{ number_format($totalQty) }}</div></div>
</div>

<table>
    <thead><tr><th>#</th><th>Product</th><th class="text-right">Qty Sold</th><th class="text-right">Revenue</th><th class="text-right">Sale Count</th></tr></thead>
    <tbody>
        @forelse($trends as $i => $t)
        <tr>
            <td>{{ $i+1 }}</td>
            <td><strong>{{ $t->product_name }}</strong></td>
            <td class="text-right">{{ number_format($t->total_qty) }}</td>
            <td class="text-right text-green">{{ $userCurrency }} {{ number_format($t->total_revenue,2) }}</td>
            <td class="text-right">{{ number_format($t->sales_count) }}</td>
        </tr>
        @empty
        <tr><td colspan="5" class="text-center">No product trends data found.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
