@extends('dashboard.reports.pdf.layouts.a4')
@section('title', 'Supplier Price Comparison')
@section('subtitle', 'Compare supplier pricing across products')
@section('content')
<table>
    <thead><tr><th>Product</th><th>SKU</th><th>Supplier</th><th class="text-right">Avg Price</th><th class="text-right">Lowest</th><th class="text-right">Highest</th><th class="text-right">Purchases</th></tr></thead>
    <tbody>
        @forelse($products as $product)
            @php $items = $product->purchaseItems ?? collect(); $count = $items->count(); @endphp
            @foreach($items as $idx => $pi)
            <tr>
                @if($idx === 0)
                <td rowspan="{{ $count ?: 1 }}"><strong>{{ $product->name }}</strong></td>
                <td rowspan="{{ $count ?: 1 }}">{{ $product->sku ?? '—' }}</td>
                @endif
                <td>{{ $pi->supplier->name ?? 'N/A' }}</td>
                <td class="text-right">{{ $userCurrency }} {{ number_format($pi->avg_price,2) }}</td>
                <td class="text-right text-green">{{ $userCurrency }} {{ number_format($pi->min_price,2) }}</td>
                <td class="text-right text-red">{{ $userCurrency }} {{ number_format($pi->max_price,2) }}</td>
                <td class="text-right">{{ number_format($pi->purchases_count) }}</td>
            </tr>
            @endforeach
            @if($count === 0)
            <tr><td>{{ $product->name }}</td><td>{{ $product->sku ?? '—' }}</td><td colspan="5">No purchase data</td></tr>
            @endif
        @empty
        <tr><td colspan="7" class="text-center">No products found.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
