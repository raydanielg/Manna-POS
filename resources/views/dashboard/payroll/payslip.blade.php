@extends('layouts.dashboard')
@section('page_title','Payslip')
@section('page_styles')
<style>
.payslip-wrap{max-width:700px;margin:0 auto;background:#fff;border-radius:16px;border:1.5px solid #eef2f6;padding:2.5rem;}
.payslip-head{text-align:center;margin-bottom:2rem;padding-bottom:1.5rem;border-bottom:2px solid #f1f5f9;}
.payslip-head h2{font-size:1.4rem;font-weight:800;color:#0f172a;}
.payslip-head p{font-size:.8rem;color:#64748b;margin-top:.25rem;}
.payslip-grid{display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.5rem;}
.payslip-section{background:#f8fafc;border-radius:12px;padding:1.25rem;}
.payslip-section h4{font-size:.75rem;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-bottom:1rem;letter-spacing:.06em;}
.pl-row{display:flex;justify-content:space-between;padding:.4rem 0;font-size:.85rem;border-bottom:1px dashed #e2e8f0;}
.pl-row:last-child{border-bottom:none;}
.pl-row.total{font-weight:800;color:#0f172a;font-size:1rem;padding-top:.5rem;margin-top:.5rem;border-top:2px solid #e2e8f0;}
.pl-row.negative{color:#ef4444;}
.pl-row.positive{color:#22c55e;}
.payslip-footer{margin-top:2rem;padding-top:1.5rem;border-top:2px solid #f1f5f9;text-align:center;font-size:.72rem;color:#94a3b8;}
@media print{
  .sidebar,.header,.dash-content{padding:0 !important;}
  .main-content{margin-left:0 !important;}
  .payslip-wrap{border:none;box-shadow:none;}
}
</style>
@endsection
@section('content')
<div class="dash-content">
<div class="payslip-wrap">
  <div class="payslip-head">
    <h2>PAYSLIP</h2>
    <p>{{ $entry->period->name ?? 'N/A' }} &middot; {{ $entry->staff->name ?? 'N/A' }}</p>
  </div>

  <div class="payslip-grid">
    <div class="payslip-section">
      <h4>Earnings</h4>
      <div class="pl-row"><span>Basic Salary</span><span>{{ number_format($entry->basic_salary, 2) }}</span></div>
      <div class="pl-row"><span>Overtime ({{ $entry->overtime_hours }} hrs @ {{ $entry->overtime_rate }})</span><span>{{ number_format($entry->overtime_amount, 2) }}</span></div>
      <div class="pl-row"><span>Bonus</span><span>{{ number_format($entry->bonus, 2) }}</span></div>
      <div class="pl-row"><span>Allowance</span><span>{{ number_format($entry->allowance, 2) }}</span></div>
      <div class="pl-row total positive"><span>Gross Salary</span><span>{{ number_format($entry->gross_salary, 2) }}</span></div>
    </div>

    <div class="payslip-section">
      <h4>Deductions</h4>
      <div class="pl-row negative"><span>Tax</span><span>{{ number_format($entry->tax_deduction, 2) }}</span></div>
      <div class="pl-row negative"><span>NSSF</span><span>{{ number_format($entry->nssf_deduction, 2) }}</span></div>
      <div class="pl-row negative"><span>NHIF</span><span>{{ number_format($entry->nhif_deduction, 2) }}</span></div>
      <div class="pl-row negative"><span>Loan</span><span>{{ number_format($entry->loan_deduction, 2) }}</span></div>
      <div class="pl-row negative"><span>Other</span><span>{{ number_format($entry->other_deduction, 2) }}</span></div>
      <div class="pl-row total negative"><span>Total Deductions</span><span>{{ number_format($entry->total_deduction, 2) }}</span></div>
    </div>
  </div>

  <div style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);border-radius:14px;padding:1.5rem;text-align:center;border:1.5px solid #bbf7d0;">
    <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;color:#15803d;letter-spacing:.06em;">Net Salary</div>
    <div style="font-size:2rem;font-weight:800;color:#0f172a;margin-top:.35rem;">{{ number_format($entry->net_salary, 2) }}</div>
  </div>

  @if($entry->notes)
  <div style="margin-top:1.5rem;padding:1rem;background:#fffbeb;border-radius:10px;border:1px solid #fef3c7;font-size:.82rem;color:#92400e;">
    <strong>Notes:</strong> {{ $entry->notes }}
  </div>
  @endif

  <div class="payslip-footer">
    <p>Status: <strong>{{ ucfirst($entry->status) }}</strong> &middot; Generated on {{ now()->format('M d, Y H:i') }}</p>
  </div>

  <div style="display:flex;justify-content:center;gap:.75rem;margin-top:1.5rem;">
    <button onclick="window.print()" class="btn btn-primary" style="gap:.35rem;">
      <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 9V3h12v6M6 15h12M6 15v6h12v-6"/></svg>
      Print Payslip
    </button>
    <a href="{{ url()->previous() }}" class="btn btn-secondary">Back</a>
  </div>
</div>
</div>
@endsection
