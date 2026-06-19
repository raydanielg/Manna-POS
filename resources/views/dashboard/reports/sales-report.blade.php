@extends('layouts.dashboard')
@section('page_title','Sales Report')
@section('content')
<div class="dash-content">

    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Sales Report</h1>
            <p class="text-sm text-gray-500">{{ $from->format('M d, Y') }} — {{ $to->format('M d, Y') }}</p>
        </div>
        <div class="flex gap-2 no-print">
            <a href="{{ route('dashboard.reports.sales-report.pdf', ['from_date' => request('from_date'), 'to_date' => request('to_date')]) }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                Download PDF
            </a>
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
        <a href="{{ route('dashboard.reports.sales-report') }}" class="h-9 px-4 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 inline-flex items-center">Reset</a>
    </form>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Sales</div>
            <div class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($summary['total_sales']) }}</div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Revenue</div>
            <div class="text-2xl font-bold text-gray-900 mt-1">{{ $userCurrency }} {{ number_format($summary['total_revenue'],2) }}</div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Paid</div>
            <div class="text-2xl font-bold text-green-600 mt-1">{{ $userCurrency }} {{ number_format($summary['total_paid'],2) }}</div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Outstanding</div>
            <div class="text-2xl font-bold text-red-600 mt-1">{{ $userCurrency }} {{ number_format($summary['total_outstanding'],2) }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Daily Sales Trend</h3>
            <div class="h-64"><canvas id="salesChart"></canvas></div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Top Products by Revenue</h3>
            <div class="h-64"><canvas id="productsChart"></canvas></div>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-700">Sales Details</h3>
            <span class="text-xs text-gray-500">{{ $sales->firstItem() ?? 0 }}–{{ $sales->lastItem() ?? 0 }} of {{ $sales->total() }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="salesTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Paid</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($sales as $s)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-400">{{ $loop->iteration + ($sales->currentPage()-1)*$sales->perPage() }}</td>
                        <td class="px-4 py-3 text-sm font-mono text-blue-600 font-semibold">{{ $s->reference ?? $s->id }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $s->customer->name ?? 'Walk-in' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 whitespace-nowrap">{{ $s->sale_date ? \Carbon\Carbon::parse($s->sale_date)->format('M d, Y') : '—' }}</td>
                        <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900">{{ $userCurrency }} {{ number_format($s->total,2) }}</td>
                        <td class="px-4 py-3 text-sm text-right font-semibold text-green-600">{{ $userCurrency }} {{ number_format($s->paid,2) }}</td>
                        <td class="px-4 py-3 text-sm"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700">{{ $s->payment_method ?? '—' }}</span></td>
                        <td class="px-4 py-3 text-sm">
                            @if($s->status=='completed')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-50 text-green-700">Completed</span>
                            @elseif($s->status=='pending')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-50 text-yellow-700">Pending</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-50 text-gray-600">{{ ucfirst($s->status ?? 'Draft') }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center text-sm text-gray-500">No sales found for this period.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($sales->hasPages())
        <div class="px-4 py-3 border-t border-gray-200 no-print">{{ $sales->links() }}</div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const salesLabels = {!! json_encode($dailySales->pluck('date')) !!};
const salesRevenue = {!! json_encode($dailySales->pluck('revenue')) !!};
const salesCount = {!! json_encode($dailySales->pluck('count')) !!};

new Chart(document.getElementById('salesChart'), {
    type: 'line',
    data: {
        labels: salesLabels,
        datasets: [
            { label: 'Revenue', data: salesRevenue, borderColor: '#2563eb', backgroundColor: 'rgba(37,99,235,0.08)', fill: true, tension: 0.4, pointRadius: 3, pointBackgroundColor: '#2563eb' },
            { label: 'Orders', data: salesCount, borderColor: '#16a34a', backgroundColor: 'transparent', tension: 0.4, pointRadius: 3, pointBackgroundColor: '#16a34a', yAxisID: 'y1' }
        ]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: { legend: { position:'top', labels:{ usePointStyle:true, boxWidth:8 } } },
        scales: {
            x: { grid:{ display:false }, ticks:{ font:{size:10} } },
            y: { beginAtZero:true, grid:{ color:'#f1f5f9' }, ticks:{ callback:v=>window.__USER_CURRENCY+' '+Number(v).toLocaleString() } },
            y1: { position:'right', beginAtZero:true, grid:{ display:false }, ticks:{ stepSize:1 } }
        }
    }
});

new Chart(document.getElementById('productsChart'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($topProducts->pluck('product_name')->take(6)) !!},
        datasets: [{ data: {!! json_encode($topProducts->pluck('total_revenue')->take(6)) !!}, backgroundColor: ['#0a192f','#1e3a8a','#2563eb','#60a5fa','#93c5fd','#dbeafe'], borderWidth:0 }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { position:'right', labels:{ boxWidth:10, font:{size:10} } } }
    }
});
</script>
@endsection
