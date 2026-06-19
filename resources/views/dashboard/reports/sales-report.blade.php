@extends('layouts.dashboard')
@section('page_title','Sales Report')
@section('content')
<div class="dash-content animate__animated animate__fadeInUp report-page">

    {{-- Header --}}
    <div class="report-header-bar" data-aos="fade-down">
        <div>
            <h1>Sales Report</h1>
            <p>{{ $from->format('M d, Y') }} — {{ $to->format('M d, Y') }} &middot; Analyze sales performance, revenue trends, and top products</p>
        </div>
        <div class="report-actions no-print">
            <a href="?from_date={{ $from->copy()->subMonth()->format('Y-m-d') }}&to_date={{ $to->copy()->subMonth()->format('Y-m-d') }}" class="btn btn-secondary">Last Month</a>
            <a href="?from_date={{ now()->startOfMonth()->format('Y-m-d') }}&to_date={{ now()->format('Y-m-d') }}" class="btn btn-secondary">This Month</a>
            <button type="button" class="btn btn-primary" onclick="openPdfPreview('Sales Report')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                Preview PDF
            </button>
            <button type="button" class="btn btn-success" onclick="exportTableToCSV('#salesTable', 'sales-report-{{ $from->format('Y-m-d') }}.csv')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export Excel
            </button>
        </div>
    </div>

    {{-- Date Filter --}}
    <form method="GET" class="report-filters no-print" data-aos="fade-up" data-aos-delay="50">
        <div>
            <label>From Date</label>
            <input type="date" name="from_date" value="{{ request('from_date',$from->format('Y-m-d')) }}">
        </div>
        <div>
            <label>To Date</label>
            <input type="date" name="to_date" value="{{ request('to_date',$to->format('Y-m-d')) }}">
        </div>
        <button type="submit" class="btn btn-primary" style="height:40px;">Generate Report</button>
        <a href="{{ route('dashboard.reports.sales-report') }}" class="btn btn-secondary" style="height:40px;">Reset</a>
    </form>

    {{-- Summary Cards --}}
    <div class="report-summary" data-aos="fade-up" data-aos-delay="100">
        <div class="report-summary-card">
            <div class="rsc-bar green"></div>
            <div class="rsc-label">Total Sales</div>
            <div class="rsc-value">{{ number_format($summary['total_sales']) }}</div>
        </div>
        <div class="report-summary-card">
            <div class="rsc-bar blue"></div>
            <div class="rsc-label">Total Revenue</div>
            <div class="rsc-value">TZS {{ number_format($summary['total_revenue'],2) }}</div>
        </div>
        <div class="report-summary-card">
            <div class="rsc-bar amber"></div>
            <div class="rsc-label">Total Paid</div>
            <div class="rsc-value">TZS {{ number_format($summary['total_paid'],2) }}</div>
        </div>
        <div class="report-summary-card">
            <div class="rsc-bar red"></div>
            <div class="rsc-label">Outstanding</div>
            <div class="rsc-value">TZS {{ number_format($summary['total_outstanding'],2) }}</div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="report-chart-row" data-aos="fade-up" data-aos-delay="150">
        <div class="report-chart-card">
            <div class="rch-head">Daily Sales Trend</div>
            <div class="rch-body"><canvas id="salesChart"></canvas></div>
        </div>
        <div class="report-chart-card">
            <div class="rch-head">Top Products by Revenue</div>
            <div class="rch-body"><canvas id="productsChart"></canvas></div>
        </div>
    </div>

    {{-- Sales Table --}}
    <div class="report-table-wrap" data-aos="fade-up" data-aos-delay="200">
        <div class="rtw-head">
            <div class="rtw-title">Sales Details</div>
            <div style="font-size:0.8rem;color:#64748b;">{{ $sales->firstItem() ?? 0 }}–{{ $sales->lastItem() ?? 0 }} of {{ $sales->total() }}</div>
        </div>
        <div class="rtw-body tbl-responsive">
            <table class="report-table" id="salesTable">
                <thead>
                    <tr>
                        <th>#</th><th>Reference</th><th>Customer</th><th>Date</th><th class="text-right">Total</th><th class="text-right">Paid</th><th>Method</th><th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $s)
                    <tr data-aos="fade-up" data-aos-delay="{{ 250 + $loop->iteration * 40 }}">
                        <td class="text-slate-400">{{ $loop->iteration + ($sales->currentPage()-1)*$sales->perPage() }}</td>
                        <td class="font-mono text-xs" style="color:#2563eb;font-weight:600;">{{ $s->reference_no ?? $s->id }}</td>
                        <td>{{ $s->customer->name ?? 'Walk-in' }}</td>
                        <td style="white-space:nowrap;color:#64748b;font-size:0.82rem;">{{ $s->sale_date ? \Carbon\Carbon::parse($s->sale_date)->format('M d, Y') : '—' }}</td>
                        <td class="text-right" style="font-weight:700;">TZS {{ number_format($s->total_amount,2) }}</td>
                        <td class="text-right" style="color:#16a34a;font-weight:600;">TZS {{ number_format($s->paid_amount,2) }}</td>
                        <td><span class="badge badge-info">{{ $s->payment_method ?? '—' }}</span></td>
                        <td>
                            @if($s->status=='completed')
                                <span class="badge badge-success">Completed</span>
                            @elseif($s->status=='pending')
                                <span class="badge badge-warning">Pending</span>
                            @else
                                <span class="badge badge-gray">{{ ucfirst($s->status ?? 'Draft') }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <svg class="empty-icon" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <div class="empty-title">No sales found</div>
                                <div class="empty-desc">Adjust the date range or wait for new transactions.</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding:1rem;" class="no-print">{{ $sales->links() }}</div>
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
