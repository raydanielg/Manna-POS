@extends('layouts.dashboard')
@section('page_title','Payroll')
@section('page_styles')
<style>
.pay-wrap{max-width:1100px;margin:0 auto;}
.pay-hero{background:linear-gradient(135deg,#0f172a,#1e3a8a);border-radius:16px;padding:1.5rem 2rem;color:#fff;margin-bottom:1.5rem;position:relative;overflow:hidden;}
.pay-hero h1{font-size:1.2rem;font-weight:800;position:relative;z-index:1;}
.pay-hero p{font-size:.78rem;opacity:.8;margin-top:.25rem;position:relative;z-index:1;}
.pay-hero .actions{display:flex;gap:.5rem;margin-top:1rem;position:relative;z-index:1;flex-wrap:wrap;}
.pay-hero .btn{background:rgba(255,255,255,.12);color:#fff;border:1px solid rgba(255,255,255,.2);border-radius:10px;padding:.5rem 1rem;font-size:.78rem;font-weight:700;cursor:pointer;transition:all .2s;text-decoration:none;display:inline-flex;align-items:center;gap:.35rem;}
.pay-hero .btn:hover{background:rgba(255,255,255,.2);}
.pay-hero .btn-primary{background:#fff;color:#1e3a8a;border:none;}
</style>
@endsection
@section('content')
<div class="dash-content">
<div class="pay-wrap">

  <div class="pay-hero">
    <h1>Payroll Management</h1>
    <p>Manage salaries, deductions, and generate payslips</p>
    <div class="actions">
      <a href="{{ route('dashboard.payroll.periods') }}" class="btn btn-primary">Payroll Periods</a>
      <a href="{{ route('dashboard.payroll.deductions') }}" class="btn">Deduction Types</a>
    </div>
  </div>

  <div class="mf-stats" style="margin-bottom:1.5rem;">
    <div class="mf-stat"><div class="bar blue"></div><div class="label">Total Staff</div><div class="value">{{ number_format($stats['total_staff']) }}</div></div>
    <div class="mf-stat"><div class="bar green"></div><div class="label">Total Paid</div><div class="value">{{ number_format($stats['total_paid'], 0) }}</div></div>
    <div class="mf-stat"><div class="bar amber"></div><div class="label">Open Periods</div><div class="value">{{ number_format($stats['pending_payroll']) }}</div></div>
    <div class="mf-stat"><div class="bar violet"></div><div class="label">Entries</div><div class="value">{{ number_format($stats['total_entries']) }}</div></div>
  </div>

  <div class="mf-grid" style="grid-template-columns:1fr 1fr;">
    <div class="mf-card">
      <div class="mf-card-head"><h3>Recent Periods</h3><a href="{{ route('dashboard.payroll.periods') }}" style="font-size:.75rem;color:#2563eb;font-weight:700;">View All</a></div>
      <div class="mf-card-body" style="padding:0;">
        @if($periods->count())
        <table class="mf-table">
          <thead><tr><th>Period</th><th>Dates</th><th>Status</th></tr></thead>
          <tbody>
            @foreach($periods as $p)
            <tr>
              <td><a href="{{ route('dashboard.payroll.period.show', $p) }}" style="color:#2563eb;font-weight:700;text-decoration:none;">{{ $p->name }}</a></td>
              <td style="font-size:.72rem;color:#64748b;">{{ $p->start_date->format('M d') }} - {{ $p->end_date->format('M d, Y') }}</td>
              <td><span class="status {{ $p->status }}">{{ ucfirst($p->status) }}</span></td>
            </tr>
            @endforeach
          </tbody>
        </table>
        @else
        <div style="padding:2rem;text-align:center;color:#94a3b8;">No payroll periods yet.</div>
        @endif
      </div>
    </div>

    <div class="mf-card">
      <div class="mf-card-head"><h3>Current Period</h3></div>
      <div class="mf-card-body">
        @if($currentPeriod)
        <div style="text-align:center;padding:1rem 0;">
          <div style="font-size:1.1rem;font-weight:800;color:#0f172a;">{{ $currentPeriod->name }}</div>
          <div style="font-size:.8rem;color:#64748b;margin-top:.35rem;">{{ $currentPeriod->start_date->format('M d') }} — {{ $currentPeriod->end_date->format('M d, Y') }}</div>
          <div style="margin-top:1rem;">
            <a href="{{ route('dashboard.payroll.period.show', $currentPeriod) }}" class="btn btn-primary btn-sm">Process Payroll</a>
          </div>
        </div>
        @else
        <div style="text-align:center;padding:2rem;color:#94a3b8;">
          <p>No open payroll period. <a href="{{ route('dashboard.payroll.periods') }}" style="color:#2563eb;">Create one</a>.</p>
        </div>
        @endif
      </div>
    </div>
  </div>

</div>
</div>
@endsection
