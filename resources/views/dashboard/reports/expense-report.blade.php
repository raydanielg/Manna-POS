@extends('layouts.dashboard')
@section('page_title','Expense Report')
@section('content')
<div class="dash-content">

    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Expense Report</h1>
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
        <a href="{{ route('dashboard.reports.expense-report') }}" class="h-9 px-4 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 inline-flex items-center">Reset</a>
    </form>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Expenses</div>
            <div class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($summary['total_expenses']) }}</div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</div>
            <div class="text-2xl font-bold text-red-600 mt-1">{{ $userCurrency }} {{ number_format($summary['total_amount'],2) }}</div>
        </div>
    </div>

    @if($byCategory->count())
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden mb-6">
        <div class="px-4 py-3 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-700">Expenses by Category</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Count</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($byCategory as $cat)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $cat->category }}</td>
                        <td class="px-4 py-3 text-sm text-right text-gray-600">{{ $cat->count }}</td>
                        <td class="px-4 py-3 text-sm text-right font-semibold text-red-600">{{ $userCurrency }} {{ number_format($cat->total,2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-700">Expense Details</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="expenseTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($expenses as $e)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-400">{{ $loop->iteration + ($expenses->currentPage()-1)*$expenses->perPage() }}</td>
                        <td class="px-4 py-3 text-sm font-mono text-blue-600 font-semibold">{{ $e->reference ?? $e->id }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $e->category->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 whitespace-nowrap">{{ $e->expense_date ? \Carbon\Carbon::parse($e->expense_date)->format('M d, Y') : '—' }}</td>
                        <td class="px-4 py-3 text-sm text-right font-semibold text-red-600">{{ $userCurrency }} {{ number_format($e->amount,2) }}</td>
                        <td class="px-4 py-3 text-sm"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700">{{ $e->payment_method ?? '—' }}</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-sm text-gray-500">No expenses found for this period.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($expenses->hasPages())
        <div class="px-4 py-3 border-t border-gray-200 no-print">{{ $expenses->links() }}</div>
        @endif
    </div>
</div>
@endsection
