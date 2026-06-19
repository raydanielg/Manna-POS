@extends('dashboard.reports.pdf.layouts.a4')
@section('title', 'Expiry Date Report')
@section('subtitle', 'Products approaching or past their expiry date')
@section('date_range', $from->format('M d, Y') . ' — ' . $to->format('M d, Y'))
@section('content')
<div class="summary-grid">
    <div class="summary-card" style="border-color:#dc2626;"><div class="label" style="color:#dc2626;">Already Expired</div><div class="value text-red">{{ number_format($expired) }}</div></div>
    <div class="summary-card" style="border-color:#d97706;"><div class="label" style="color:#d97706;">Expiring Soon (30 days)</div><div class="value" style="color:#d97706;">{{ number_format($expiringSoon) }}</div></div>
</div>

<table>
    <thead><tr><th>#</th><th>Product</th><th>SKU</th><th class="text-right">Stock</th><th>Expiry Date</th><th>Status</th></tr></thead>
    <tbody>
        @forelse($products as $i => $p)
        @php
            $expiry = $p->expiry_date ? \Carbon\Carbon::parse($p->expiry_date) : null;
            $daysLeft = $expiry ? now()->diffInDays($expiry, false) : null;
            if ($daysLeft === null) { $badge = 'gray'; $label = 'No Date'; }
            elseif ($daysLeft < 0) { $badge = 'danger'; $label = 'Expired'; }
            elseif ($daysLeft <= 7) { $badge = 'danger'; $label = $daysLeft . ' days'; }
            elseif ($daysLeft <= 30) { $badge = 'warning'; $label = $daysLeft . ' days'; }
            else { $badge = 'success'; $label = $daysLeft . ' days'; }
        @endphp
        <tr>
            <td>{{ $i+1 }}</td>
            <td><strong>{{ $p->name }}</strong></td>
            <td>{{ $p->sku ?? '—' }}</td>
            <td class="text-right">{{ number_format($p->current_stock) }}</td>
            <td>{{ $expiry ? $expiry->format('M d, Y') : '—' }}</td>
            <td><span class="badge badge-{{ $badge }}">{{ $label }}</span></td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center">No products with expiry dates found.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
