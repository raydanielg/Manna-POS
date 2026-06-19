@extends('dashboard.reports.pdf.layouts.a4')
@section('title', 'Profit & Loss Report')
@section('subtitle', 'Revenue, costs, expenses, and profitability')
@section('date_range', $from->format('M d, Y') . ' — ' . $to->format('M d, Y'))
@section('content')
<div class="summary-grid">
    <div class="summary-card"><div class="label">Revenue</div><div class="value text-green">{{ $userCurrency }} {{ number_format($totalRevenue,2) }}</div></div>
    <div class="summary-card"><div class="label">Purchase Cost</div><div class="value text-red">{{ $userCurrency }} {{ number_format($totalCost,2) }}</div></div>
    <div class="summary-card"><div class="label">Expenses</div><div class="value" style="color:#d97706;">{{ $userCurrency }} {{ number_format($totalExpenses,2) }}</div></div>
    <div class="summary-card" style="border-color: #2563eb;"><div class="label" style="color:#2563eb;">Net Profit</div><div class="value {{ $netProfit >= 0 ? 'text-blue' : 'text-red' }}">{{ $userCurrency }} {{ number_format($netProfit,2) }}</div></div>
</div>

<div style="margin-bottom:8px;"><strong style="font-size:9pt;">Gross Profit:</strong> {{ $userCurrency }} {{ number_format($grossProfit,2) }}</div>

<h3 style="font-size:9pt;margin:0 0 6px 0;color:#0f172a;">Monthly Breakdown</h3>
<table>
    <thead><tr><th>Month</th><th class="text-right">Revenue</th><th class="text-right">Cost</th><th class="text-right">Expenses</th><th class="text-right">Gross Profit</th><th class="text-right">Net Profit</th></tr></thead>
    <tbody>
        @foreach($monthly as $m)
        <tr>
            <td><strong>{{ $m['month'] }}</strong></td>
            <td class="text-right text-green">{{ $userCurrency }} {{ number_format($m['revenue'],2) }}</td>
            <td class="text-right text-red">{{ $userCurrency }} {{ number_format($m['cost'],2) }}</td>
            <td class="text-right" style="color:#d97706;">{{ $userCurrency }} {{ number_format($m['expenses'],2) }}</td>
            <td class="text-right">{{ $userCurrency }} {{ number_format($m['revenue'] - $m['cost'],2) }}</td>
            <td class="text-right font-bold {{ $m['profit'] >= 0 ? 'text-blue' : 'text-red' }}">{{ $userCurrency }} {{ number_format($m['profit'],2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
