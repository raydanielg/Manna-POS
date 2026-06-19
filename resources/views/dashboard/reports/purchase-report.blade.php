@extends('layouts.dashboard')
@section('page_title','Purchase Report')
@section('content')
<div class="dash-content">

    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Purchase Report</h1>
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
        <a href="{{ route('dashboard.reports.purchase-report') }}" class="h-9 px-4 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 inline-flex items-center">Reset</a>
    </form>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Orders</div>
            <div class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($summary['total_purchases']) }}</div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</div>
            <div class="text-2xl font-bold text-blue-600 mt-1">{{ $userCurrency }} {{ number_format($summary['total_amount'],2) }}</div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Paid</div>
            <div class="text-2xl font-bold text-green-600 mt-1">{{ $userCurrency }} {{ number_format($summary['total_paid'],2) }}</div>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-700">Purchase Details</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="purchaseTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supplier</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($purchases as $p)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-400">{{ $loop->iteration + ($purchases->currentPage()-1)*$purchases->perPage() }}</td>
                        <td class="px-4 py-3 text-sm font-mono text-blue-600 font-semibold">{{ $p->reference ?? $p->id }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $p->supplier->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 whitespace-nowrap">{{ $p->purchase_date ? \Carbon\Carbon::parse($p->purchase_date)->format('M d, Y') : '—' }}</td>
                        <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900">{{ $userCurrency }} {{ number_format($p->total,2) }}</td>
                        <td class="px-4 py-3 text-sm">
                            @php
                                $payBadge = match($p->payment_status) { 'paid' => 'bg-green-50 text-green-700', 'partial' => 'bg-yellow-50 text-yellow-700', 'unpaid' => 'bg-red-50 text-red-700', default => 'bg-gray-50 text-gray-600' };
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $payBadge }}">{{ ucfirst($p->payment_status ?? '—') }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            @php
                                $statBadge = match($p->status) { 'received' => 'bg-green-50 text-green-700', 'pending' => 'bg-yellow-50 text-yellow-700', 'cancelled' => 'bg-red-50 text-red-700', default => 'bg-gray-50 text-gray-600' };
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $statBadge }}">{{ ucfirst($p->status ?? 'Draft') }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-sm text-gray-500">No purchases found for this period.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($purchases->hasPages())
        <div class="px-4 py-3 border-t border-gray-200 no-print">{{ $purchases->links() }}</div>
        @endif
    </div>
</div>
@endsection
