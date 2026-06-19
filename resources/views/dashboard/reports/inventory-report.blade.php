@extends('layouts.dashboard')
@section('page_title','Inventory Report')
@section('content')
<div class="dash-content">

    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Inventory Report</h1>
            <p class="text-sm text-gray-500">Real-time stock levels and valuations</p>
        </div>
        <div class="flex gap-2 no-print">
            <button type="button" onclick="window.print()" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Products</div>
            <div class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalProducts) }}</div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Value (Cost)</div>
            <div class="text-2xl font-bold text-blue-600 mt-1">{{ $userCurrency }} {{ number_format($totalStockValue,2) }}</div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Retail Value</div>
            <div class="text-2xl font-bold text-green-600 mt-1">{{ $userCurrency }} {{ number_format($totalRetailValue,2) }}</div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Low Stock Items</div>
            <div class="text-2xl font-bold text-red-600 mt-1">{{ number_format($lowStock) }}</div>
        </div>
    </div>

    @if($categories->count())
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden mb-6">
        <div class="px-4 py-3 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-700">Stock by Category</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Products</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Stock</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($categories as $cat)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $cat->category }}</td>
                        <td class="px-4 py-3 text-sm text-right text-gray-600">{{ $cat->count }}</td>
                        <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900">{{ number_format($cat->stock) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-700">Product Inventory</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="inventoryTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Stock</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Reorder</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Purchase Price</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Selling Price</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Stock Value</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($products as $p)
                    @php
                        $isLow = $p->current_stock <= $p->reorder_level && $p->current_stock > 0;
                        $isOut = $p->current_stock <= 0;
                        $stockVal = $p->current_stock * $p->purchase_price;
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-400">{{ $loop->iteration + ($products->currentPage()-1)*$products->perPage() }}</td>
                        <td class="px-4 py-3 text-sm font-semibold text-gray-900">{{ $p->name }}</td>
                        <td class="px-4 py-3 text-sm font-mono text-gray-400">{{ $p->sku ?? '—' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $p->category->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-sm text-right">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium 
                                {{ $isOut ? 'bg-red-50 text-red-700' : ($isLow ? 'bg-yellow-50 text-yellow-700' : 'bg-green-50 text-green-700') }}">
                                {{ number_format($p->current_stock) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-right text-gray-500">{{ $p->reorder_level ?? 0 }}</td>
                        <td class="px-4 py-3 text-sm text-right text-gray-700">{{ $userCurrency }} {{ number_format($p->purchase_price,2) }}</td>
                        <td class="px-4 py-3 text-sm text-right text-gray-700">{{ $userCurrency }} {{ number_format($p->selling_price,2) }}</td>
                        <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900">{{ $userCurrency }} {{ number_format($stockVal,2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-12 text-center text-sm text-gray-500">No products found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($products->hasPages())
        <div class="px-4 py-3 border-t border-gray-200 no-print">{{ $products->links() }}</div>
        @endif
    </div>
</div>
@endsection
