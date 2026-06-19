@extends('dashboard.reports.pdf.layouts.a4')
@section('title', 'Expense Report')
@section('subtitle', 'Spending by category over time')
@section('date_range', $from->format('M d, Y') . ' — ' . $to->format('M d, Y'))
@section('content')
<div class="summary-grid">
    <div class="summary-card"><div class="label">Total Expenses</div><div class="value">{{ number_format($summary['total_expenses']) }}</div></div>
    <div class="summary-card"><div class="label">Total Amount</div><div class="value text-red">{{ $userCurrency }} {{ number_format($summary['total_amount'],2) }}</div></div>
</div>

@if($byCategory->count())
<h3 style="font-size:9pt;margin:0 0 6px 0;color:#0f172a;">Expenses by Category</h3>
<table>
    <thead><tr><th>Category</th><th class="text-right">Count</th><th class="text-right">Total</th></tr></thead>
    <tbody>
        @foreach($byCategory as $cat)
        <tr><td>{{ $cat->category }}</td><td class="text-right">{{ $cat->count }}</td><td class="text-right text-red">{{ $userCurrency }} {{ number_format($cat->total,2) }}</td></tr>
        @endforeach
    </tbody>
</table>
@endif

<h3 style="font-size:9pt;margin:0 0 6px 0;color:#0f172a;">Expense Details</h3>
<table>
    <thead><tr><th>#</th><th>Reference</th><th>Category</th><th>Date</th><th class="text-right">Amount</th><th>Payment</th></tr></thead>
    <tbody>
        @forelse($expenses as $i => $e)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $e->reference_no ?? $e->id }}</td>
            <td>{{ $e->category->name ?? 'N/A' }}</td>
            <td>{{ $e->expense_date ? \Carbon\Carbon::parse($e->expense_date)->format('M d, Y') : '—' }}</td>
            <td class="text-right text-red">{{ $userCurrency }} {{ number_format($e->amount,2) }}</td>
            <td>{{ $e->payment_method ?? '—' }}</td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center">No expenses found.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
