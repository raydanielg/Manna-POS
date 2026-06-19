@extends('dashboard.reports.pdf.layouts.a4')
@section('title', 'Sales Report')
@section('subtitle', 'Sales performance, revenue trends, and top products')
@section('date_range', $from->format('M d, Y') . ' — ' . $to->format('M d, Y'))
@section('content')
<div class="summary-grid">
    <div class="summary-card"><div class="label">Total Sales</div><div class="value">{{ number_format($summary['total_sales']) }}</div></div>
    <div class="summary-card"><div class="label">Total Revenue</div><div class="value">{{ $userCurrency }} {{ number_format($summary['total_revenue'],2) }}</div></div>
    <div class="summary-card"><div class="label">Total Paid</div><div class="value text-green">{{ $userCurrency }} {{ number_format($summary['total_paid'],2) }}</div></div>
    <div class="summary-card"><div class="label">Outstanding</div><div class="value text-red">{{ $userCurrency }} {{ number_format($summary['total_outstanding'],2) }}</div></div>
</div>

@if($topProducts->count())
<h3 style="font-size:9pt;margin:0 0 6px 0;color:#0f172a;">Top Products by Revenue</h3>
<table>
    <thead><tr><th>#</th><th>Product</th><th class="text-right">Qty Sold</th><th class="text-right">Revenue</th></tr></thead>
    <tbody>
        @foreach($topProducts as $i => $p)
        <tr><td>{{ $i+1 }}</td><td>{{ $p->product_name }}</td><td class="text-right">{{ number_format($p->total_qty) }}</td><td class="text-right">{{ $userCurrency }} {{ number_format($p->total_revenue,2) }}</td></tr>
        @endforeach
    </tbody>
</table>
@endif

<h3 style="font-size:9pt;margin:0 0 6px 0;color:#0f172a;">Sales Details</h3>
<table>
    <thead><tr><th>#</th><th>Reference</th><th>Customer</th><th>Date</th><th class="text-right">Total</th><th class="text-right">Paid</th><th>Status</th></tr></thead>
    <tbody>
        @forelse($sales as $i => $s)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $s->reference_no ?? $s->id }}</td>
            <td>{{ $s->customer->name ?? 'Walk-in' }}</td>
            <td>{{ $s->sale_date ? \Carbon\Carbon::parse($s->sale_date)->format('M d, Y') : '—' }}</td>
            <td class="text-right">{{ $userCurrency }} {{ number_format($s->total_amount,2) }}</td>
            <td class="text-right text-green">{{ $userCurrency }} {{ number_format($s->paid_amount,2) }}</td>
            <td><span class="badge badge-{{ $s->status == 'completed' ? 'success' : ($s->status == 'pending' ? 'warning' : 'gray') }}">{{ ucfirst($s->status ?? 'Draft') }}</span></td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center">No sales found.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
