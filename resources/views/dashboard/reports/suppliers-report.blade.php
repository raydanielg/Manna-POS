@extends('layouts.dashboard')
@section('page_title','Suppliers Report')
@section('content')
<div class="dash-content">

    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Suppliers Report</h1>
            <p class="text-sm text-gray-500">{{ $from->format('M d, Y') }} — {{ $to->format('M d, Y') }}</p>
        </div>
        <div class="flex gap-2 no-print">
            <button type="button" onclick="window.print()" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print
            </button>
        </div>
    </div>

    @php
        $totalSuppliers = $suppliers->count();
        $activeSuppliers = $suppliers->where('status','active')->count();
        $totalPurchaseValue = $suppliers->sum('purchases_total');
        $totalOrders = $suppliers->sum('purchases_count');
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Suppliers</div>
            <div class="text-2xl font-bold text-blue-600 mt-1">{{ number_format($totalSuppliers) }}</div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Active Suppliers</div>
            <div class="text-2xl font-bold text-green-600 mt-1">{{ number_format($activeSuppliers) }}</div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Purchase Value</div>
            <div class="text-2xl font-bold text-yellow-600 mt-1">{{ $userCurrency }} {{ number_format($totalPurchaseValue,2) }}</div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Orders</div>
            <div class="text-2xl font-bold text-red-600 mt-1">{{ number_format($totalOrders) }}</div>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-700">Supplier Details</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supplier</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Company</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Orders</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Amount</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Balance</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($suppliers as $s)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-400">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3 text-sm font-semibold text-gray-900">{{ $s->name }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $s->company ?? '—' }}</td>
                        <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format($s->purchases_count ?? 0) }}</td>
                        <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900">{{ $userCurrency }} {{ number_format($s->purchases_total ?? 0,2) }}</td>
                        <td class="px-4 py-3 text-sm text-right font-semibold {{ ($s->balance ?? 0) > 0 ? 'text-red-600' : 'text-green-600' }}">{{ $userCurrency }} {{ number_format($s->balance ?? 0,2) }}</td>
                        <td class="px-4 py-3 text-sm">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $s->status === 'active' ? 'bg-green-50 text-green-700' : 'bg-gray-50 text-gray-600' }}">
                                {{ ucfirst($s->status ?? 'active') }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-sm text-gray-500">No suppliers found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
