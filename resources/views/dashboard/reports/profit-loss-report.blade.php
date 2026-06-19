@extends('layouts.dashboard')
@section('page_title','Profit & Loss Report')
@section('content')
<div class="dash-content animate__animated animate__fadeInUp report-page">

    <div class="report-header-bar" data-aos="fade-down">
        <div>
            <h1>Profit & Loss Report</h1>
            <p>{{ $from->format('M d, Y') }} — {{ $to->format('M d, Y') }} &middot; Track revenue, costs, expenses, and profitability</p>
        </div>
        <div class="report-actions no-print">
            <button type="button" class="btn btn-primary" onclick="openPdfPreview('Profit & Loss Report')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                Preview PDF
            </button>
            <button type="button" class="btn btn-success" onclick="exportTableToCSV('#plTable', 'profit-loss-report-{{ $from->format('Y-m-d') }}.csv')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export Excel
            </button>
        </div>
    </div>

    <form method="GET" class="report-filters no-print" data-aos="fade-up" data-aos-delay="50">
        <div><label>From</label><input type="date" name="from_date" value="{{ request('from_date',$from->format('Y-m-d')) }}"></div>
        <div><label>To</label><input type="date" name="to_date" value="{{ request('to_date',$to->format('Y-m-d')) }}"></div>
        <button type="submit" class="btn btn-primary" style="height:40px;">Generate</button>
        <a href="{{ route('dashboard.reports.profit-loss-report') }}" class="btn btn-secondary" style="height:40px;">Reset</a>
    </form>

    <div class="report-summary" data-aos="fade-up" data-aos-delay="100">
        <div class="report-summary-card"><div class="rsc-bar green"></div><div class="rsc-label">Revenue</div><div class="rsc-value">{{ $userCurrency }} {{ number_format($totalRevenue,2) }}</div></div>
        <div class="report-summary-card"><div class="rsc-bar red"></div><div class="rsc-label">Purchase Cost</div><div class="rsc-value">{{ $userCurrency }} {{ number_format($totalCost,2) }}</div></div>
        <div class="report-summary-card"><div class="rsc-bar amber"></div><div class="rsc-label">Expenses</div><div class="rsc-value">{{ $userCurrency }} {{ number_format($totalExpenses,2) }}</div></div>
        <div class="report-summary-card" style="background:linear-gradient(135deg,#0f172a,#1e3a8a);color:#fff;border:none;">
            <div class="rsc-label" style="color:#93c5fd;">Net Profit</div>
            <div class="rsc-value" style="color:#fff;">{{ $userCurrency }} {{ number_format($netProfit,2) }}</div>
            <div style="font-size:0.75rem;color:#93c5fd;margin-top:0.25rem;">Gross: {{ $userCurrency }} {{ number_format($grossProfit,2) }}</div>
        </div>
    </div>

    <div class="report-chart-card" data-aos="fade-up" data-aos-delay="150" style="margin-bottom:1.5rem;">
        <div class="rch-head">Monthly Profit & Loss Overview</div>
        <div class="rch-body" style="height:320px;"><canvas id="plChart"></canvas></div>
    </div>

    <div class="report-table-wrap" data-aos="fade-up" data-aos-delay="200">
        <div class="rtw-head"><div class="rtw-title">Monthly Breakdown</div></div>
        <div class="rtw-body tbl-responsive">
            <table class="report-table" id="plTable">
                <thead>
                    <tr><th>Month</th><th class="text-right">Revenue</th><th class="text-right">Purchase Cost</th><th class="text-right">Expenses</th><th class="text-right">Gross Profit</th><th class="text-right">Net Profit</th></tr>
                </thead>
                <tbody>
                    @foreach($monthly as $m)
                    <tr data-aos="fade-up" data-aos-delay="{{ 250 + $loop->iteration * 40 }}">
                        <td style="font-weight:700;">{{ $m['month'] }}</td>
                        <td class="text-right" style="color:#16a34a;font-weight:600;">{{ $userCurrency }} {{ number_format($m['revenue'],2) }}</td>
                        <td class="text-right" style="color:#e11d48;font-weight:600;">{{ $userCurrency }} {{ number_format($m['cost'],2) }}</td>
                        <td class="text-right" style="color:#d97706;font-weight:600;">{{ $userCurrency }} {{ number_format($m['expenses'],2) }}</td>
                        <td class="text-right" style="font-weight:700;">{{ $userCurrency }} {{ number_format($m['revenue'] - $m['cost'],2) }}</td>
                        <td class="text-right" style="font-weight:800;color:{{ $m['profit'] >= 0 ? '#1d4ed8' : '#e11d48' }};">{{ $userCurrency }} {{ number_format($m['profit'],2) }}</td>
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
const ctx = document.getElementById('plChart').getContext('2d');
new Chart(ctx, {
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
