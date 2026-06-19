@extends('layouts.dashboard')
@section('page_title','Expiry Date Report')
@section('content')
<div class="dash-content">

    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Expiry Date Report</h1>
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
        <a href="{{ route('dashboard.reports.expiry-report') }}" class="h-9 px-4 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 inline-flex items-center">Reset</a>
    </form>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-red-200 p-4">
            <div class="text-xs font-medium text-red-600 uppercase tracking-wider">Already Expired</div>
            <div class="text-2xl font-bold text-red-600 mt-1">{{ number_format($expired) }}</div>
        </div>
        <div class="bg-white rounded-lg border border-yellow-200 p-4">
            <div class="text-xs font-medium text-yellow-600 uppercase tracking-wider">Expiring Soon (30 days)</div>
            <div class="text-2xl font-bold text-yellow-600 mt-1">{{ number_format($expiringSoon) }}</div>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-700">Product Batches by Expiry</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Batch #</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supplier</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Qty</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expiry Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($batches as $i => $b)
                    @php
                        $expiry = $b->expiry_date ? \Carbon\Carbon::parse($b->expiry_date) : null;
                        $daysLeft = $expiry ? now()->diffInDays($expiry, false) : null;
                        if ($daysLeft === null) { $badge = 'bg-gray-50 text-gray-600'; $label = 'No Date'; }
                        elseif ($daysLeft < 0) { $badge = 'bg-red-50 text-red-700'; $label = 'Expired'; }
                        elseif ($daysLeft <= 7) { $badge = 'bg-red-50 text-red-700'; $label = $daysLeft . ' days'; }
                        elseif ($daysLeft <= 30) { $badge = 'bg-yellow-50 text-yellow-700'; $label = $daysLeft . ' days'; }
                        else { $badge = 'bg-green-50 text-green-700'; $label = $daysLeft . ' days'; }
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-400">{{ $i + 1 + ($batches->currentPage()-1)*$batches->perPage() }}</td>
                        <td class="px-4 py-3 text-sm font-semibold text-gray-900">{{ $b->product->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-sm font-mono text-gray-400">{{ $b->product->sku ?? '—' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $b->batch_number ?? '—' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $b->supplier->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format($b->quantity) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap">{{ $expiry ? $expiry->format('M d, Y') : '—' }}</td>
                        <td class="px-4 py-3 text-sm"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $badge }}">{{ $label }}</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center text-sm text-gray-500">No batches with expiry dates found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($batches->hasPages())
        <div class="px-4 py-3 border-t border-gray-200 no-print">{{ $batches->links() }}</div>
        @endif
    </div>
</div>
@endsection
