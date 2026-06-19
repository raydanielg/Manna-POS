@extends('layouts.dashboard')
@section('page_title','Loans')
@section('content')
<div class="dash-content">
<div class="mf-wrap">
  <div class="page-card" style="max-width:1200px;margin:0 auto;">
    <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;">
      <div><div class="card-title">Loan Applications</div></div>
      <a href="{{ route('dashboard.microfinance.loan.create') }}" class="btn btn-primary btn-sm" style="gap:.35rem;">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        New Application
      </a>
    </div>
    <div class="card-body" style="padding:0;">
      @if($loans->count())
      <table class="mf-table">
        <thead>
          <tr><th>Loan #</th><th>Customer</th><th>Principal</th><th>Interest</th><th>Total</th><th>Duration</th><th>Paid</th><th>Balance</th><th>Status</th><th style="text-align:right;">Actions</th></tr>
        </thead>
        <tbody>
          @foreach($loans as $l)
          <tr>
            <td><a href="{{ route('dashboard.microfinance.loan.show', $l) }}" style="color:#2563eb;font-weight:700;text-decoration:none;">{{ $l->loan_number }}</a></td>
            <td>{{ $l->customer->name ?? 'N/A' }}</td>
            <td>{{ number_format($l->principal_amount, 0) }}</td>
            <td>{{ $l->interest_rate }}%</td>
            <td>{{ number_format($l->total_amount, 0) }}</td>
            <td>{{ $l->duration_months }}mo</td>
            <td>{{ number_format($l->paid_amount, 0) }}</td>
            <td style="{{ $l->balance > 0 ? 'color:#ef4444;font-weight:600;' : 'color:#22c55e;font-weight:600;' }}">{{ number_format($l->balance, 0) }}</td>
            <td><span class="status {{ $l->status }}">{{ ucfirst($l->status) }}</span></td>
            <td style="text-align:right;">
              <a href="{{ route('dashboard.microfinance.loan.show', $l) }}" class="btn btn-view btn-sm">View</a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
      @else
      <div style="padding:3rem;text-align:center;color:#94a3b8;">
        <svg width="40" height="40" fill="none" stroke="#cbd5e1" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 1rem;display:block;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 14h6m-3-3v6m-7 4v-16a2 2 0 012-2h10a2 2 0 012 2v16l-3-2l-2 2l-2-2l-2 2l-2-2l-3 2"/></svg>
        <p style="font-weight:600;color:#64748b;">No loan applications yet</p>
      </div>
      @endif
    </div>
  </div>
</div>
</div>
@endsection
