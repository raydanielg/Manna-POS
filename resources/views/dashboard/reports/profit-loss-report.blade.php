@extends('layouts.dashboard')
@section('page_title','Profit & Loss Report')
@section('content')
<div class="dash-content">
    <div class="page-header">
        <div>
            <h1 class="page-title">Profit & Loss Report</h1>
            <p class="page-subtitle">Track revenue, costs, expenses, and profitability over time</p>
        </div>
    </div>

    {{-- Date Filter --}}
    <form method="GET" style="display:flex;gap:0.75rem;flex-wrap:wrap;align-items:flex-end;margin-bottom:1.5rem;background:#fff;padding:1rem;border-radius:12px;border:1px solid #e2e8f0;">
        <div class="form-group" style="margin:0;"><label class="form-label" style="font-size:0.72rem;">From</label><input type="date" name="from_date" class="form-control" value="{{ request('from_date',$from->format('Y-m-d')) }}" style="width:150px;"></div>
        <div class="form-group" style="margin:0;"><label class="form-label" style="font-size:0.72rem;">To</label><input type="date" name="to_date" class="form-control" value="{{ request('to_date',$to->format('Y-m-d')) }}" style="width:150px;"></div>
        <button type="submit" class="btn btn-primary" style="height:40px;">Generate</button>
        <a href="{{ route('dashboard.reports.profit-loss-report') }}" class="btn btn-outline" style="height:40px;">Reset</a>
    </form>

    <div class="page-card">
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;padding:1.25rem;border-bottom:1px solid #e9edf5;">
            <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:1rem;">
                <div style="font-size:0.72rem;font-weight:600;color:#16a34a;text-transform:uppercase;">Revenue</div>
                <div style="font-size:1.6rem;font-weight:700;color:#15803d;">TZS {{ number_format($totalRevenue,2) }}</div>
            </div>
            <div style="background:#fff1f2;border:1px solid #fecdd3;border-radius:10px;padding:1rem;">
                <div style="font-size:0.72rem;font-weight:600;color:#e03057;text-transform:uppercase;">Purchase Cost</div>
                <div style="font-size:1.6rem;font-weight:700;color:#be123c;">TZS {{ number_format($totalCost,2) }}</div>
            </div>
            <div style="background:#fef3c7;border:1px solid #fde68a;border-radius:10px;padding:1rem;">
                <div style="font-size:0.72rem;font-weight:600;color:#d97706;text-transform:uppercase;">Expenses</div>
                <div style="font-size:1.6rem;font-weight:700;color:#b45309;">TZS {{ number_format($totalExpenses,2) }}</div>
            </div>
            <div style="background:linear-gradient(135deg,#0a192f,#1e3a8a);border-radius:10px;padding:1rem;color:#fff;">
                <div style="font-size:0.72rem;font-weight:600;color:#93c5fd;text-transform:uppercase;">Net Profit</div>
                <div style="font-size:1.6rem;font-weight:700;">TZS {{ number_format($netProfit,2) }}</div>
                <div style="font-size:0.75rem;color:#93c5fd;margin-top:0.25rem;">Gross: TZS {{ number_format($grossProfit,2) }}</div>
            </div>
        </div>
        <div style="padding:1rem;height:300px;"><canvas id="plChart"></canvas></div>
    </div>

    {{-- Monthly Breakdown Table --}}
    <div class="page-card" style="margin-top:1.5rem;">
        <div class="card-header"><div class="card-title">Monthly Breakdown</div></div>
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr><th>Month</th><th class="text-right">Revenue</th><th class="text-right">Purchase Cost</th><th class="text-right">Expenses</th><th class="text-right">Gross Profit</th><th class="text-right">Net Profit</th></tr>
                </thead>
                <tbody>
                    @foreach($monthly as $m)
                    <tr>
                        <td style="font-weight:600;">{{ $m['month'] }}</td>
                        <td class="text-right" style="color:#16a34a;">TZS {{ number_format($m['revenue'],2) }}</td>
                        <td class="text-right" style="color:#e11d48;">TZS {{ number_format($m['cost'],2) }}</td>
                        <td class="text-right" style="color:#d97706;">TZS {{ number_format($m['expenses'],2) }}</td>
                        <td class="text-right" style="font-weight:700;">TZS {{ number_format($m['revenue'] - $m['cost'],2) }}</td>
                        <td class="text-right" style="font-weight:700;color:{{ $m['profit'] >= 0 ? '#1d4ed8' : '#e11d48' }};">TZS {{ number_format($m['profit'],2) }}</td>
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
            { label: 'Revenue', data: {!! json_encode(array_column($monthly,'revenue')) !!}, backgroundColor: '#16a34a', borderRadius: 4 },
            { label: 'Cost', data: {!! json_encode(array_column($monthly,'cost')) !!}, backgroundColor: '#e11d48', borderRadius: 4 },
            { label: 'Expenses', data: {!! json_encode(array_column($monthly,'expenses')) !!}, backgroundColor: '#d97706', borderRadius: 4 },
            { label: 'Net Profit', data: {!! json_encode(array_column($monthly,'profit')) !!}, backgroundColor: '#2563eb', borderRadius: 4 }
        ]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { position:'top', labels:{ usePointStyle:true, boxWidth:8 } } },
        scales: {
            x: { grid:{ display:false }, ticks:{ font:{size:10} } },
            y: { beginAtZero:true, grid:{ color:'#f1f5f9' }, ticks:{ callback:v=>'TZS '+Number(v).toLocaleString() } }
        }
    }
});
</script>
@endsection
