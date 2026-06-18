@extends('layouts.dashboard')
@section('page_title','Sales Report')
@section('content')
<div class="dash-content">
    <div class="page-header">
        <div>
            <h1 class="page-title">Sales Report</h1>
            <p class="page-subtitle">Analyze sales performance, revenue trends, and top products</p>
        </div>
        <div class="page-actions">
            <a href="?from_date={{ $from->copy()->subMonth()->format('Y-m-d') }}&to_date={{ $to->copy()->subMonth()->format('Y-m-d') }}" class="btn btn-outline btn-sm">Last Month</a>
            <a href="?from_date={{ now()->startOfMonth()->format('Y-m-d') }}&to_date={{ now()->format('Y-m-d') }}" class="btn btn-outline btn-sm">This Month</a>
        </div>
    </div>

    {{-- Date Filter --}}
    <form method="GET" style="display:flex;gap:0.75rem;flex-wrap:wrap;align-items:flex-end;margin-bottom:1.5rem;background:#fff;padding:1rem;border-radius:12px;border:1px solid #e2e8f0;">
        <div class="form-group" style="margin:0;"><label class="form-label" style="font-size:0.72rem;">From</label><input type="date" name="from_date" class="form-control" value="{{ request('from_date',$from->format('Y-m-d')) }}" style="width:150px;"></div>
        <div class="form-group" style="margin:0;"><label class="form-label" style="font-size:0.72rem;">To</label><input type="date" name="to_date" class="form-control" value="{{ request('to_date',$to->format('Y-m-d')) }}" style="width:150px;"></div>
        <button type="submit" class="btn btn-primary" style="height:40px;">Generate Report</button>
        <a href="{{ route('dashboard.reports.sales-report') }}" class="btn btn-outline" style="height:40px;">Reset</a>
    </form>

    {{-- Summary Cards --}}
    <div class="summary-grid" style="grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem;margin-bottom:1.5rem;">
        <div class="summary-card" style="border-left:4px solid #16a34a;">
            <div class="summary-label" style="color:#16a34a;">Total Sales</div>
            <div class="summary-value" style="font-size:1.6rem;color:#15803d;">{{ number_format($summary['total_sales']) }}</div>
        </div>
        <div class="summary-card" style="border-left:4px solid #2563eb;">
            <div class="summary-label" style="color:#2563eb;">Total Revenue</div>
            <div class="summary-value" style="font-size:1.6rem;color:#1d4ed8;">TZS {{ number_format($summary['total_revenue'],2) }}</div>
        </div>
        <div class="summary-card" style="border-left:4px solid #d97706;">
            <div class="summary-label" style="color:#d97706;">Total Paid</div>
            <div class="summary-value" style="font-size:1.6rem;color:#b45309;">TZS {{ number_format($summary['total_paid'],2) }}</div>
        </div>
        <div class="summary-card" style="border-left:4px solid #e11d48;">
            <div class="summary-label" style="color:#e11d48;">Outstanding</div>
            <div class="summary-value" style="font-size:1.6rem;color:#be123c;">TZS {{ number_format($summary['total_outstanding'],2) }}</div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="page-grid" style="display:grid;grid-template-columns:2fr 1fr;gap:1.5rem;margin-bottom:1.5rem;">
        <div class="page-card">
            <div class="card-header"><div class="card-title">Daily Sales Trend</div></div>
            <div style="padding:1rem;height:280px;"><canvas id="salesChart"></canvas></div>
        </div>
        <div class="page-card">
            <div class="card-header"><div class="card-title">Top Products</div></div>
            <div style="padding:1rem;height:280px;"><canvas id="productsChart"></canvas></div>
        </div>
    </div>

    {{-- Sales Table --}}
    <div class="page-card">
        <div class="card-header">
            <div class="card-title">Sales Details</div>
            <div style="font-size:0.8rem;color:#64748b;">{{ $sales->firstItem() ?? 0 }}–{{ $sales->lastItem() ?? 0 }} of {{ $sales->total() }}</div>
        </div>
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr><th>#</th><th>Reference</th><th>Customer</th><th>Date</th><th class="text-right">Total</th><th class="text-right">Paid</th><th>Method</th><th>Status</th></tr>
                </thead>
                <tbody>
                    @forelse($sales as $s)
                    <tr>
                        <td class="text-slate-400">{{ $loop->iteration + ($sales->currentPage()-1)*$sales->perPage() }}</td>
                        <td class="font-mono text-xs" style="color:#2563eb;font-weight:600;">{{ $s->reference_no ?? $s->id }}</td>
                        <td>{{ $s->customer->name ?? 'Walk-in' }}</td>
                        <td style="white-space:nowrap;color:#64748b;font-size:0.82rem;">{{ $s->sale_date ? \Carbon\Carbon::parse($s->sale_date)->format('M d, Y') : '—' }}</td>
                        <td class="text-right" style="font-weight:700;">TZS {{ number_format($s->total_amount,2) }}</td>
                        <td class="text-right" style="color:#16a34a;font-weight:600;">TZS {{ number_format($s->paid_amount,2) }}</td>
                        <td><span class="badge badge-blue">{{ $s->payment_method ?? '—' }}</span></td>
                        <td><span class="badge {{ $s->status=='completed' ? 'badge-green' : ($s->status=='pending' ? 'badge-yellow' : 'badge-gray') }}">{{ ucfirst($s->status ?? 'Draft') }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center" style="padding:2.5rem;color:#94a3b8;">No sales found for the selected period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding:1rem;">{{ $sales->links() }}</div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const salesCtx = document.getElementById('salesChart').getContext('2d');
const salesLabels = {!! json_encode($dailySales->pluck('date')) !!};
const salesRevenue = {!! json_encode($dailySales->pluck('revenue')) !!};
const salesCount = {!! json_encode($dailySales->pluck('count')) !!};

new Chart(salesCtx, {
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
            y: { beginAtZero:true, grid:{ color:'#f1f5f9' }, ticks:{ callback:v=>'TZS '+Number(v).toLocaleString() } },
            y1: { position:'right', beginAtZero:true, grid:{ display:false }, ticks:{ stepSize:1 } }
        }
    }
});

const prodCtx = document.getElementById('productsChart').getContext('2d');
new Chart(prodCtx, {
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
