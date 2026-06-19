@extends('dashboard.reports.pdf.layouts.a4')
@section('title', 'Purchase Report')
@section('subtitle', 'Supplier orders, costs, and payment status')
@section('date_range', $from->format('M d, Y') . ' — ' . $to->format('M d, Y'))
@section('content')
<div class="summary-grid">
    <div class="summary-card"><div class="label">Total Orders</div><div class="value">{{ number_format($summary['total_purchases']) }}</div></div>
    <div class="summary-card"><div class="label">Total Amount</div><div class="value text-blue">{{ $userCurrency }} {{ number_format($summary['total_amount'],2) }}</div></div>
    <div class="summary-card"><div class="label">Total Paid</div><div class="value text-green">{{ $userCurrency }} {{ number_format($summary['total_paid'],2) }}</div></div>
</div>

<table>
    <thead><tr><th>#</th><th>Reference</th><th>Supplier</th><th>Date</th><th class="text-right">Total</th><th>Payment</th><th>Status</th></tr></thead>
    <tbody>
        @forelse($purchases as $i => $p)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $p->reference ?? $p->id }}</td>
            <td>{{ $p->supplier->name ?? 'N/A' }}</td>
            <td>{{ $p->purchase_date ? \Carbon\Carbon::parse($p->purchase_date)->format('M d, Y') : '—' }}</td>
            <td class="text-right">{{ $userCurrency }} {{ number_format($p->total,2) }}</td>
            <td><span class="badge badge-{{ $p->payment_status == 'paid' ? 'success' : ($p->payment_status == 'partial' ? 'warning' : 'danger') }}">{{ ucfirst($p->payment_status ?? '—') }}</span></td>
            <td><span class="badge badge-{{ $p->status == 'received' ? 'success' : ($p->status == 'pending' ? 'warning' : 'danger') }}">{{ ucfirst($p->status ?? 'Draft') }}</span></td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center">No purchases found.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
