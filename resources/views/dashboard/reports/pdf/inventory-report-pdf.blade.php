@extends('dashboard.reports.pdf.layouts.a4')
@section('title', 'Inventory Report')
@section('subtitle', 'Stock levels, valuations, and reorder alerts')
@section('date_range', 'As of ' . now()->format('M d, Y'))
@section('content')
<div class="summary-grid">
    <div class="summary-card"><div class="label">Total Products</div><div class="value">{{ number_format($totalProducts) }}</div></div>
    <div class="summary-card"><div class="label">Stock Value (Cost)</div><div class="value text-blue">{{ $userCurrency }} {{ number_format($totalStockValue,2) }}</div></div>
    <div class="summary-card"><div class="label">Retail Value</div><div class="value text-green">{{ $userCurrency }} {{ number_format($totalRetailValue,2) }}</div></div>
    <div class="summary-card"><div class="label">Low Stock Items</div><div class="value text-red">{{ number_format($lowStock) }}</div></div>
</div>

@if($categories->count())
<h3 style="font-size:9pt;margin:0 0 6px 0;color:#0f172a;">Stock by Category</h3>
<table>
    <thead><tr><th>Category</th><th class="text-right">Products</th><th class="text-right">Total Stock</th></tr></thead>
    <tbody>
        @foreach($categories as $cat)
        <tr><td>{{ $cat->category }}</td><td class="text-right">{{ $cat->count }}</td><td class="text-right">{{ number_format($cat->stock) }}</td></tr>
        @endforeach
    </tbody>
</table>
@endif

<h3 style="font-size:9pt;margin:0 0 6px 0;color:#0f172a;">Product Inventory</h3>
<table>
    <thead><tr><th>#</th><th>Product</th><th>SKU</th><th>Category</th><th class="text-right">Stock</th><th class="text-right">Purchase Price</th><th class="text-right">Selling Price</th><th class="text-right">Stock Value</th></tr></thead>
    <tbody>
        @forelse($products as $i => $p)
        @php $stockVal = $p->stock_quantity * $p->purchase_price; @endphp
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $p->name }}</td>
            <td>{{ $p->sku ?? '—' }}</td>
            <td>{{ $p->category->name ?? 'N/A' }}</td>
            <td class="text-right">{{ number_format($p->stock_quantity) }}</td>
            <td class="text-right">{{ $userCurrency }} {{ number_format($p->purchase_price,2) }}</td>
            <td class="text-right">{{ $userCurrency }} {{ number_format($p->selling_price,2) }}</td>
            <td class="text-right">{{ $userCurrency }} {{ number_format($stockVal,2) }}</td>
        </tr>
        @empty
        <tr><td colspan="8" class="text-center">No products found.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
