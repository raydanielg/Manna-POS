@extends('layouts.dashboard')
@section('page_title','Profit & Loss Report')
@section('content')
<div class="dash-content">

    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Profit & Loss Report</h1>
            <p class="text-sm text-gray-500">{{ $from->format('M d, Y') }} — {{ $to->format('M d, Y') }}</p>
        </div>
        <div class="flex gap-2 no-print">
            <a href="{{ route('dashboard.reports.profit-loss-report.pdf', ['from_date' => request('from_date'), 'to_date' => request('to_date')]) }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
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
        <a href="{{ route('dashboard.reports.profit-loss-report') }}" class="h-9 px-4 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 inline-flex items-center">Reset</a>
    </form>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</div>
            <div class="text-2xl font-bold text-green-600 mt-1">{{ $userCurrency }} {{ number_format($totalRevenue,2) }}</div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Purchase Cost</div>
            <div class="text-2xl font-bold text-red-600 mt-1">{{ $userCurrency }} {{ number_format($totalCost,2) }}</div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Expenses</div>
            <div class="text-2xl font-bold text-yellow-600 mt-1">{{ $userCurrency }} {{ number_format($totalExpenses,2) }}</div>
        </div>
        <div class="bg-white rounded-lg border border-blue-200 p-4 border-2">
            <div class="text-xs font-medium text-blue-600 uppercase tracking-wider">Net Profit</div>
            <div class="text-2xl font-bold mt-1 {{ $netProfit >= 0 ? 'text-blue-700' : 'text-red-600' }}">{{ $userCurrency }} {{ number_format($netProfit,2) }}</div>
            <div class="text-xs text-gray-500 mt-1">Gross: {{ $userCurrency }} {{ number_format($grossProfit,2) }}</div>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Monthly Profit & Loss Overview</h3>
        <div class="h-80"><canvas id="plChart"></canvas></div>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-700">Monthly Breakdown</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="plTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Month</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Revenue</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Purchase Cost</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Expenses</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Gross Profit</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Net Profit</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($monthly as $m)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-semibold text-gray-900">{{ $m['month'] }}</td>
                        <td class="px-4 py-3 text-sm text-right font-semibold text-green-600">{{ $userCurrency }} {{ number_format($m['revenue'],2) }}</td>
                        <td class="px-4 py-3 text-sm text-right font-semibold text-red-600">{{ $userCurrency }} {{ number_format($m['cost'],2) }}</td>
                        <td class="px-4 py-3 text-sm text-right font-semibold text-yellow-600">{{ $userCurrency }} {{ number_format($m['expenses'],2) }}</td>
                        <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900">{{ $userCurrency }} {{ number_format($m['revenue'] - $m['cost'],2) }}</td>
                        <td class="px-4 py-3 text-sm text-right font-bold {{ $m['profit'] >= 0 ? 'text-blue-700' : 'text-red-600' }}">{{ $userCurrency }} {{ number_format($m['profit'],2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('plChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode(array_column($monthly,'month')) !!},
        datasets: [
            { label: 'Revenue', data: {!! json_encode(array_column($monthly,'revenue')) !!}, backgroundColor: '#22c55e', borderRadius: 4 },
            { label: 'Cost', data: {!! json_encode(array_column($monthly,'cost')) !!}, backgroundColor: '#ef4444', borderRadius: 4 },
            { label: 'Expenses', data: {!! json_encode(array_column($monthly,'expenses')) !!}, backgroundColor: '#f59e0b', borderRadius: 4 },
            { label: 'Net Profit', data: {!! json_encode(array_column($monthly,'profit')) !!}, backgroundColor: '#3b82f6', borderRadius: 4 }
        ]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { position:'top', labels:{ usePointStyle:true, boxWidth:8 } } },
        scales: {
            x: { grid:{ display:false }, ticks:{ font:{size:10} } },
            y: { beginAtZero:true, grid:{ color:'#f1f5f9' }, ticks:{ callback:v=>window.__USER_CURRENCY+' '+Number(v).toLocaleString() } }
        }
    }
});
</script>
@endsection
