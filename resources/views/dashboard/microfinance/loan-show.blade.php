@extends('layouts.dashboard')
@section('page_title','Loan Details')
@section('page_styles')
<style>
.loan-detail{max-width:1100px;margin:0 auto;}
.loan-header{background:linear-gradient(135deg,#0f172a,#1e3a8a);border-radius:16px;padding:1.5rem 2rem;color:#fff;margin-bottom:1.5rem;position:relative;overflow:hidden;}
.loan-header h1{font-size:1.2rem;font-weight:800;position:relative;z-index:1;}
.loan-header .meta{font-size:.78rem;opacity:.8;margin-top:.25rem;position:relative;z-index:1;}
.loan-header .actions{display:flex;gap:.5rem;margin-top:1rem;position:relative;z-index:1;flex-wrap:wrap;}
.loan-header .btn{background:rgba(255,255,255,.12);color:#fff;border:1px solid rgba(255,255,255,.2);border-radius:10px;padding:.5rem 1rem;font-size:.78rem;font-weight:700;cursor:pointer;transition:all .2s;text-decoration:none;display:inline-flex;align-items:center;gap:.35rem;}
.loan-header .btn-success{background:#22c55e;color:#fff;border:none;}
.loan-header .btn-danger{background:#ef4444;color:#fff;border:none;}
.detail-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:1rem;margin-bottom:1.5rem;}
.detail-item{background:#fff;border-radius:12px;border:1.5px solid #eef2f6;padding:1rem;}
.detail-item .label{font-size:.68rem;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-bottom:.25rem;}
.detail-item .value{font-size:1rem;font-weight:700;color:#0f172a;}
</style>
@endsection
@section('content')
<div class="dash-content">
<div class="loan-detail">
  <div class="loan-header">
    <h1>{{ $loan->loan_number }}</h1>
    <div class="meta">{{ $loan->customer->name ?? 'N/A' }} &middot; {{ number_format($loan->principal_amount) }} for {{ $loan->duration_months }} months</div>
    <div class="actions">
      @if($loan->status === 'pending')
      <form method="POST" action="{{ route('dashboard.microfinance.loan.status', $loan) }}" style="display:inline;">@csrf <input type="hidden" name="status" value="approved"><button type="submit" class="btn btn-success">Approve</button></form>
      <form method="POST" action="{{ route('dashboard.microfinance.loan.status', $loan) }}" style="display:inline;">@csrf <input type="hidden" name="status" value="rejected"><button type="submit" class="btn btn-danger">Reject</button></form>
      @endif
      @if($loan->status === 'approved')
      <form method="POST" action="{{ route('dashboard.microfinance.loan.status', $loan) }}" style="display:inline;">@csrf <input type="hidden" name="status" value="disbursed"><button type="submit" class="btn btn-success">Disburse</button></form>
      @endif
      @if(in_array($loan->status, ['active','disbursed']))
      <button class="btn" onclick="document.getElementById('payModal').classList.add('open')">Record Payment</button>
      @endif
      <a href="{{ route('dashboard.microfinance.loans') }}" class="btn">Back</a>
    </div>
  </div>

  <div class="detail-grid">
    <div class="detail-item"><div class="label">Principal</div><div class="value">{{ number_format($loan->principal_amount, 2) }}</div></div>
    <div class="detail-item"><div class="label">Interest</div><div class="value">{{ $loan->interest_rate }}% {{ ucfirst(str_replace('_',' ',$loan->interest_type)) }}</div></div>
    <div class="detail-item"><div class="label">Total Interest</div><div class="value">{{ number_format($loan->total_interest, 2) }}</div></div>
    <div class="detail-item"><div class="label">Total Amount</div><div class="value">{{ number_format($loan->total_amount, 2) }}</div></div>
    <div class="detail-item"><div class="label">Paid</div><div class="value" style="color:#22c55e;">{{ number_format($loan->paid_amount, 2) }}</div></div>
    <div class="detail-item"><div class="label">Balance</div><div class="value" style="color:#ef4444;">{{ number_format($loan->balance, 2) }}</div></div>
    <div class="detail-item"><div class="label">Status</div><div class="value"><span class="status {{ $loan->status }}">{{ ucfirst($loan->status) }}</span></div></div>
    <div class="detail-item"><div class="label">Duration</div><div class="value">{{ $loan->duration_months }} months</div></div>
    @if($loan->start_date)
    <div class="detail-item"><div class="label">Start</div><div class="value">{{ $loan->start_date->format('M d, Y') }}</div></div>
    <div class="detail-item"><div class="label">End</div><div class="value">{{ $loan->end_date->format('M d, Y') }}</div></div>
    @endif
  </div>

  <div class="mf-grid" style="grid-template-columns:1.2fr .8fr;">
    <div class="mf-card">
      <div class="mf-card-head"><h3>Repayment Schedule</h3></div>
      <div class="mf-card-body" style="padding:0;">
        @if($loan->schedules->count())
        <table class="mf-table">
          <thead><tr><th>#</th><th>Due Date</th><th>Principal</th><th>Interest</th><th>Total</th><th>Paid</th><th>Status</th></tr></thead>
          <tbody>
            @foreach($loan->schedules as $s)
            <tr><td>{{ $s->installment_number }}</td><td>{{ $s->due_date->format('M d, Y') }}</td><td>{{ number_format($s->principal_amount, 2) }}</td><td>{{ number_format($s->interest_amount, 2) }}</td><td>{{ number_format($s->total_amount, 2) }}</td><td>{{ number_format($s->paid_amount, 2) }}</td><td><span class="status {{ $s->status }}">{{ ucfirst($s->status) }}</span></td></tr>
            @endforeach
          </tbody>
        </table>
        @else<div style="padding:2rem;text-align:center;color:#94a3b8;">No schedule generated.</div>@endif
      </div>
    </div>
    <div class="mf-card">
      <div class="mf-card-head"><h3>Guarantors</h3></div>
      <div class="mf-card-body">
        @if($loan->guarantors->count())
        @foreach($loan->guarantors as $g)
        <div style="padding:.75rem 0;border-bottom:1px solid #f1f5f9;">
          <div style="font-weight:700;color:#0f172a;font-size:.85rem;">{{ $g->name }}</div>
          <div style="font-size:.75rem;color:#64748b;">{{ $g->phone }} @if($g->relationship)&middot; {{ $g->relationship }}@endif</div>
          @if($g->pledged_amount)<div style="font-size:.72rem;color:#2563eb;font-weight:600;">Pledged: {{ number_format($g->pledged_amount, 2) }}</div>@endif
        </div>
        @endforeach
        @else<div style="padding:1rem;text-align:center;color:#94a3b8;font-size:.8rem;">No guarantors added.</div>@endif
      </div>
    </div>
  </div>
</div>
</div>

<div class="modal-overlay" id="payModal" onclick="if(event.target===this)this.classList.remove('open')">
  <div class="modal-box" style="max-width:480px;" onclick="event.stopPropagation()">
    <div class="modal-header"><h3 class="modal-title">Record Payment</h3><button class="modal-close" onclick="document.getElementById('payModal').classList.remove('open')">&times;</button></div>
    <form method="POST" action="{{ route('dashboard.microfinance.loan.repay', $loan) }}">@csrf
      <div class="form-row">
        <div class="form-group"><label class="form-label">Amount *</label><input name="amount" type="number" step="0.01" class="form-control" required></div>
        <div class="form-group"><label class="form-label">Payment Date *</label><input name="payment_date" type="date" class="form-control" value="{{ date('Y-m-d') }}" required></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Method</label>
          <select name="payment_method" class="form-control"><option value="cash">Cash</option><option value="bank_transfer">Bank Transfer</option><option value="mobile_money">Mobile Money</option></select>
        </div>
        <div class="form-group"><label class="form-label">Reference</label><input name="reference_number" class="form-control"></div>
      </div>
      <div class="form-group"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" onclick="document.getElementById('payModal').classList.remove('open')">Cancel</button><button type="submit" class="btn btn-primary">Record Payment</button></div>
    </form>
  </div>
</div>
@endsection
