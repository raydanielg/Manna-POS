@extends('layouts.dashboard')
@section('page_title','Microfinance Dashboard')
@section('page_styles')
<style>
.mf-wrap{max-width:1200px;margin:0 auto;}
.mf-hero{background:linear-gradient(135deg,#0f172a,#1e3a8a);border-radius:16px;padding:1.5rem 2rem;color:#fff;margin-bottom:1.5rem;position:relative;overflow:hidden;}
.mf-hero::before{content:'';position:absolute;top:-30%;right:-5%;width:250px;height:250px;background:radial-gradient(circle,rgba(255,255,255,.08) 0%,transparent 70%);border-radius:50%;}
.mf-hero h1{font-size:1.3rem;font-weight:800;position:relative;z-index:1;}
.mf-hero p{font-size:.8rem;opacity:.8;margin-top:.25rem;position:relative;z-index:1;}
.mf-hero .actions{display:flex;gap:.5rem;margin-top:1rem;position:relative;z-index:1;flex-wrap:wrap;}
.mf-hero .btn{background:rgba(255,255,255,.12);color:#fff;border:1px solid rgba(255,255,255,.2);border-radius:10px;padding:.5rem 1rem;font-size:.78rem;font-weight:700;cursor:pointer;transition:all .2s;text-decoration:none;display:inline-flex;align-items:center;gap:.35rem;}
.mf-hero .btn:hover{background:rgba(255,255,255,.2);}
.mf-hero .btn-primary{background:#fff;color:#1e3a8a;border:none;}
.mf-hero .btn-primary:hover{background:#f1f5f9;}

.mf-stats{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:1rem;margin-bottom:1.5rem;}
.mf-stat{background:#fff;border-radius:14px;border:1.5px solid #eef2f6;padding:1.25rem;position:relative;overflow:hidden;transition:all .2s;}
.mf-stat:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(15,23,42,.06);}
.mf-stat .label{font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#64748b;margin-bottom:.35rem;}
.mf-stat .value{font-size:1.4rem;font-weight:800;color:#0f172a;}
.mf-stat .bar{position:absolute;left:0;top:0;bottom:0;width:4px;border-radius:14px 0 0 14px;}
.mf-stat .bar.blue{background:#3b82f6;}
.mf-stat .bar.green{background:#22c55e;}
.mf-stat .bar.amber{background:#f59e0b;}
.mf-stat .bar.red{background:#ef4444;}
.mf-stat .bar.violet{background:#8b5cf6;}
.mf-stat .bar.emerald{background:#10b981;}

.mf-grid{display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;}
.mf-card{background:#fff;border-radius:14px;border:1.5px solid #eef2f6;overflow:hidden;}
.mf-card-head{padding:1rem 1.25rem;border-bottom:1px solid #f1f5f9;background:#fcfdfe;display:flex;align-items:center;justify-content:space-between;}
.mf-card-head h3{font-size:.9rem;font-weight:800;color:#0f172a;}
.mf-card-body{padding:1rem;}
.mf-table{width:100%;border-collapse:collapse;}
.mf-table th{font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#64748b;padding:.6rem .75rem;text-align:left;border-bottom:1px solid #f1f5f9;background:#fafbff;}
.mf-table td{font-size:.8rem;color:#374151;padding:.6rem .75rem;border-bottom:1px solid #f8fafc;}
.mf-table tr:last-child td{border-bottom:none;}
.mf-table tr:hover td{background:#fafbff;}
.status{display:inline-flex;align-items:center;gap:.35rem;font-size:.68rem;font-weight:700;padding:.2rem .5rem;border-radius:6px;}
.status.pending{background:#fef3c7;color:#92400e;}
.status.approved{background:#dbeafe;color:#1e40af;}
.status.active{background:#d1fae5;color:#065f46;}
.status.completed{background:#f3f4f6;color:#374151;}
.status.rejected{background:#fee2e2;color:#991b1b;}
.status.overdue{background:#fee2e2;color:#991b1b;}

@media(max-width:768px){
  .mf-grid{grid-template-columns:1fr;}
  .mf-hero{padding:1.25rem;}
}
</style>
@endsection
@section('content')
<div class="dash-content">
<div class="mf-wrap">

  <div class="mf-hero">
    <h1>Microfinance & Loans</h1>
    <p>Manage loan products, applications, schedules, and guarantors</p>
    <div class="actions">
      <a href="{{ route('dashboard.microfinance.loan.create') }}" class="btn btn-primary">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        New Loan Application
      </a>
      <a href="{{ route('dashboard.microfinance.products') }}" class="btn">Loan Products</a>
      <a href="{{ route('dashboard.microfinance.guarantors') }}" class="btn">Guarantors</a>
    </div>
  </div>

  <div class="mf-stats">
    <div class="mf-stat"><div class="bar blue"></div><div class="label">Total Loans</div><div class="value">{{ number_format($stats['total_loans']) }}</div></div>
    <div class="mf-stat"><div class="bar green"></div><div class="label">Active Loans</div><div class="value">{{ number_format($stats['active_loans']) }}</div></div>
    <div class="mf-stat"><div class="bar amber"></div><div class="label">Pending</div><div class="value">{{ number_format($stats['pending_loans']) }}</div></div>
    <div class="mf-stat"><div class="bar violet"></div><div class="label">Total Principal</div><div class="value">{{ number_format($stats['total_principal'], 0) }}</div></div>
    <div class="mf-stat"><div class="bar emerald"></div><div class="label">Total Repaid</div><div class="value">{{ number_format($stats['total_repaid'], 0) }}</div></div>
    <div class="mf-stat"><div class="bar red"></div><div class="label">Outstanding</div><div class="value">{{ number_format($stats['total_outstanding'], 0) }}</div></div>
  </div>

  <div class="mf-grid">
    <div class="mf-card">
      <div class="mf-card-head"><h3>Recent Loans</h3><a href="{{ route('dashboard.microfinance.loans') }}" style="font-size:.75rem;color:#2563eb;font-weight:700;">View All</a></div>
      <div class="mf-card-body" style="padding:0;">
        @if($recentLoans->count())
        <table class="mf-table">
          <thead><tr><th>Loan #</th><th>Customer</th><th>Amount</th><th>Status</th></tr></thead>
          <tbody>
            @foreach($recentLoans as $l)
            <tr>
              <td><a href="{{ route('dashboard.microfinance.loan.show', $l) }}" style="color:#2563eb;font-weight:700;text-decoration:none;">{{ $l->loan_number }}</a></td>
              <td>{{ $l->customer->name ?? 'N/A' }}</td>
              <td>{{ number_format($l->principal_amount, 0) }}</td>
              <td><span class="status {{ $l->status }}">{{ ucfirst($l->status) }}</span></td>
            </tr>
            @endforeach
          </tbody>
        </table>
        @else
        <div style="padding:2rem;text-align:center;color:#94a3b8;font-size:.85rem;">No loans yet. <a href="{{ route('dashboard.microfinance.loan.create') }}" style="color:#2563eb;">Create one</a>.</div>
        @endif
      </div>
    </div>

    <div class="mf-card">
      <div class="mf-card-head"><h3>Overdue Installments</h3></div>
      <div class="mf-card-body" style="padding:0;">
        @if($overdueSchedules->count())
        <table class="mf-table">
          <thead><tr><th>Loan #</th><th>Customer</th><th>Due</th><th>Amount</th></tr></thead>
          <tbody>
            @foreach($overdueSchedules as $s)
            <tr>
              <td>{{ $s->loan->loan_number }}</td>
              <td>{{ $s->loan->customer->name ?? 'N/A' }}</td>
              <td style="color:#ef4444;font-weight:600;">{{ $s->due_date->format('M d, Y') }}</td>
              <td>{{ number_format($s->total_amount, 0) }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
        @else
        <div style="padding:2rem;text-align:center;color:#94a3b8;font-size:.85rem;">No overdue installments. Great!</div>
        @endif
      </div>
    </div>
  </div>

</div>
</div>
@endsection
