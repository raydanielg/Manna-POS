@extends('layouts.dashboard')
@section('page_title','Product Trends')
@section('content')
<div class="dash-content">

    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Product Trends</h1>
            <p class="text-sm text-gray-500">{{ $from->format('M d, Y') }} — {{ $to->format('M d, Y') }}</p>
        </div>
        <div class="flex gap-2 no-print">
            <button type="button" onclick="window.print()" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print
            </button>
        </div>
    </div>

    <form method="GET" class="flex flex-wrap items-end gap-3 mb-6 p-4 bg-white rounded-lg border border-gray-200 no-print">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">From Date</label>
            <input type="date" name="from_date" value="{{ request('from_date',$from->format('Y-m-d')) }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm h-9 px-3 border">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">To Date</label>
            <input type="date" name="to_date" value="{{ request('to_date',$to->format('Y-m-d')) }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm h-9 px-3 border">
        </div>
        <button type="submit" class="h-9 px-4 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">Generate</button>
        <a href="{{ route('dashboard.reports.product-trends-report') }}" class="h-9 px-4 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 inline-flex items-center">Reset</a>
    </form>

    @php
        $totalProducts = $trends->count();
        $totalRevenue = $trends->sum('total_revenue');
        $totalQty = $trends->sum('total_qty');
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Products Sold</div>
            <div class="text-2xl font-bold text-blue-600 mt-1">{{ number_format($totalProducts) }}</div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Revenue</div>
            <div class="text-2xl font-bold text-green-600 mt-1">{{ $userCurrency }} {{ number_format($totalRevenue,2) }}</div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Qty Sold</div>
            <div class="text-2xl font-bold text-yellow-600 mt-1">{{ number_format($totalQty) }}</div>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-700">Top Selling Products</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Qty Sold</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Revenue</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Sale Count</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($trends as $t)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-400">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3 text-sm font-semibold text-gray-900">{{ $t->product_name }}</td>
                        <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format($t->total_qty) }}</td>
                        <td class="px-4 py-3 text-sm text-right font-semibold text-green-600">{{ $userCurrency }} {{ number_format($t->total_revenue,2) }}</td>
                        <td class="px-4 py-3 text-sm text-right text-gray-600">{{ number_format($t->sales_count) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-12 text-center text-sm text-gray-500">No product trends data found for this period.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
