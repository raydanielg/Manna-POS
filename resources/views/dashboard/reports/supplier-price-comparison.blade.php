@extends('layouts.dashboard')
@section('page_title','Supplier Price Comparison')
@section('content')
<div class="dash-content">

    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Supplier Price Comparison</h1>
            <p class="text-sm text-gray-500">Compare supplier pricing across products</p>
        </div>
        <div class="flex gap-2 no-print">
            <button type="button" onclick="window.print()" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print
            </button>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-700">Product & Supplier Pricing</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supplier</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Avg Price</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Lowest</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Highest</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Purchases</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($products as $product)
                        @php $items = $product->purchaseItems ?? collect(); $count = $items->count(); @endphp
                        @foreach($items as $idx => $pi)
                        <tr class="hover:bg-gray-50">
                            @if($idx === 0)
                            <td class="px-4 py-3 text-sm font-semibold text-gray-900" rowspan="{{ $count ?: 1 }}">{{ $product->name }}</td>
                            <td class="px-4 py-3 text-sm font-mono text-gray-400" rowspan="{{ $count ?: 1 }}">{{ $product->sku ?? '—' }}</td>
                            @endif
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $pi->supplier->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-sm text-right text-gray-700">{{ $userCurrency }} {{ number_format($pi->avg_price,2) }}</td>
                            <td class="px-4 py-3 text-sm text-right text-green-600 font-semibold">{{ $userCurrency }} {{ number_format($pi->min_price,2) }}</td>
                            <td class="px-4 py-3 text-sm text-right text-red-600 font-semibold">{{ $userCurrency }} {{ number_format($pi->max_price,2) }}</td>
                            <td class="px-4 py-3 text-sm text-right text-gray-600">{{ number_format($pi->purchases_count) }}</td>
                        </tr>
                        @endforeach
                        @if($count === 0)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-semibold text-gray-900">{{ $product->name }}</td>
                            <td class="px-4 py-3 text-sm font-mono text-gray-400">{{ $product->sku ?? '—' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-400" colspan="5">No purchase data</td>
                        </tr>
                        @endif
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-sm text-gray-500">No products found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
