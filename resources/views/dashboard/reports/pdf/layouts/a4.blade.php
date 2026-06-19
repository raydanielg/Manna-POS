<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Report')</title>
    @php $userCurrency = auth()->user()->currency ?? 'TZS'; @endphp
    <style>
        @page { size: A4; margin: 15mm 18mm; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10pt; color: #1e293b; line-height: 1.5; margin: 0; padding: 0; }
        .header { border-bottom: 2px solid #2563eb; padding-bottom: 10px; margin-bottom: 16px; }
        .header h1 { font-size: 16pt; font-weight: 700; color: #0f172a; margin: 0 0 4px 0; }
        .header .sub { font-size: 8pt; color: #64748b; }
        .header .date-range { font-size: 8pt; color: #94a3b8; }
        .summary-grid { display: flex; gap: 10px; margin-bottom: 16px; flex-wrap: wrap; }
        .summary-card { flex: 1; min-width: 120px; border: 1px solid #e2e8f0; border-radius: 4px; padding: 8px 12px; }
        .summary-card .label { font-size: 7pt; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
        .summary-card .value { font-size: 13pt; font-weight: 700; color: #0f172a; margin-top: 2px; }
        table { width: 100%; border-collapse: collapse; font-size: 8pt; margin-bottom: 12px; }
        table thead th { background: #f1f5f9; color: #475569; font-weight: 600; text-align: left; padding: 6px 8px; border: 1px solid #e2e8f0; font-size: 7pt; text-transform: uppercase; letter-spacing: 0.3px; }
        table tbody td { padding: 5px 8px; border: 1px solid #e2e8f0; color: #334155; }
        table tbody tr:nth-child(even) { background: #f8fafc; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: 700; }
        .text-green { color: #16a34a; }
        .text-red { color: #dc2626; }
        .text-blue { color: #2563eb; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; font-size: 7pt; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 6px; }
        .page-break { page-break-before: always; }
        .mb-2 { margin-bottom: 8px; }
        .mt-2 { margin-top: 8px; }
        .badge { display: inline-block; padding: 1px 6px; font-size: 7pt; border-radius: 3px; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-warning { background: #fef9c3; color: #854d0e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-info { background: #dbeafe; color: #1e40af; }
        .badge-gray { background: #f1f5f9; color: #475569; }
    </style>
</head>
<body>
    <div class="header">
        <h1>@yield('title', 'Report')</h1>
        <div class="sub">@yield('subtitle', '')</div>
        <div class="date-range">@yield('date_range', '')</div>
    </div>

    @yield('content')

    <div class="footer">
        Generated on {{ now()->format('M d, Y H:i') }} &middot; mannaPOS
    </div>
</body>
</html>
