@extends('layouts.dashboard')
@section('page_title','New Loan Application')
@section('page_styles')
<style>
.loan-form{max-width:700px;margin:0 auto;background:#fff;border-radius:16px;border:1.5px solid #eef2f6;padding:1.5rem 2rem;}
.loan-form h2{font-size:1.1rem;font-weight:800;color:#0f172a;margin-bottom:1.5rem;display:flex;align-items:center;gap:.5rem;}
.loan-form h2 svg{color:#2563eb;}
.calc-preview{background:linear-gradient(135deg,#f0fdf4,#f8fafc);border:1.5px solid #d1fae5;border-radius:14px;padding:1.25rem;margin-top:1.5rem;}
.calc-preview h4{font-size:.85rem;font-weight:700;color:#065f46;margin-bottom:1rem;}
.calc-row{display:flex;justify-content:space-between;padding:.35rem 0;font-size:.82rem;color:#475569;border-bottom:1px dashed #d1fae5;}
.calc-row:last-child{border-bottom:none;font-weight:800;color:#0f172a;font-size:.95rem;margin-top:.25rem;padding-top:.5rem;}
</style>
@endsection
@section('content')
<div class="dash-content">
<div class="loan-form">
  <h2><svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 14h6m-3-3v6m-7 4v-16a2 2 0 012-2h10a2 2 0 012 2v16l-3-2l-2 2l-2-2l-2 2l-2-2l-3 2"/></svg> New Loan Application</h2>
  <form method="POST" action="{{ route('dashboard.microfinance.loan.store') }}" id="loanForm">
    @csrf
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Loan Product (optional)</label>
        <select name="loan_product_id" class="form-control" id="productSelect">
          <option value="">-- Custom --</option>
          @foreach($products as $p)
          <option value="{{ $p->id }}" data-min="{{ $p->min_amount }}" data-max="{{ $p->max_amount }}" data-rate="{{ $p->interest_rate }}" data-type="{{ $p->interest_type }}" data-dmin="{{ $p->duration_min }}" data-dmax="{{ $p->duration_max }}">{{ $p->name }} ({{ $p->interest_rate }}% {{ ucfirst(str_replace('_',' ',$p->interest_type)) }})</option>
          @endforeach
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Customer *</label>
        <select name="customer_id" class="form-control" required>
          <option value="">Select customer…</option>
          @foreach($customers as $c)
          <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->phone ?? 'no phone' }})</option>
          @endforeach
        </select>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Principal Amount *</label>
        <input name="principal_amount" type="number" step="0.01" class="form-control" id="principal" required oninput="calc()">
      </div>
      <div class="form-group">
        <label class="form-label">Duration (months) *</label>
        <input name="duration_months" type="number" class="form-control" id="duration" required oninput="calc()">
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Interest Rate (%)</label>
        <input name="interest_rate" type="number" step="0.01" class="form-control" id="rate" required oninput="calc()">
      </div>
      <div class="form-group">
        <label class="form-label">Interest Type</label>
        <select name="interest_type" class="form-control" id="itype" onchange="calc()">
          <option value="flat">Flat</option>
          <option value="reducing_balance">Reducing Balance</option>
        </select>
      </div>
    </div>
    <div class="form-group">
      <label class="form-label">Purpose</label>
      <textarea name="purpose" class="form-control" rows="2" placeholder="What is the loan for?"></textarea>
    </div>
    <div class="form-group">
      <label class="form-label">Notes</label>
      <textarea name="notes" class="form-control" rows="2"></textarea>
    </div>

    <div class="calc-preview">
      <h4>Calculation Preview</h4>
      <div class="calc-row"><span>Principal</span><span id="cPrincipal">0</span></div>
      <div class="calc-row"><span>Interest</span><span id="cInterest">0</span></div>
      <div class="calc-row"><span>Total Repayable</span><span id="cTotal">0</span></div>
      <div class="calc-row"><span>Monthly Installment</span><span id="cMonthly">0</span></div>
    </div>

    <div style="display:flex;justify-content:flex-end;gap:.75rem;margin-top:1.5rem;">
      <a href="{{ route('dashboard.microfinance.loans') }}" class="btn btn-secondary">Cancel</a>
      <button type="submit" class="btn btn-primary" style="gap:.35rem;">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        Submit Application
      </button>
    </div>
  </form>
</div>
</div>
@endsection
@section('scripts')
<script>
document.getElementById('productSelect').addEventListener('change', function() {
  const opt = this.options[this.selectedIndex];
  if (!opt.value) return;
  document.getElementById('rate').value = opt.dataset.rate;
  document.getElementById('itype').value = opt.dataset.type;
  if (!document.getElementById('duration').value) document.getElementById('duration').value = opt.dataset.dmin;
  calc();
});

function calc() {
  const p = parseFloat(document.getElementById('principal').value) || 0;
  const m = parseInt(document.getElementById('duration').value) || 1;
  const r = parseFloat(document.getElementById('rate').value) || 0;
  const type = document.getElementById('itype').value;
  let interest = 0;

  if (type === 'flat') {
    interest = p * (r/100) * (m/12);
  } else {
    // Simple reducing balance approx
    interest = p * (r/100) * (m/12) * 0.6;
  }

  const total = p + interest;
  const monthly = total / m;

  document.getElementById('cPrincipal').textContent = p.toLocaleString(undefined,{minimumFractionDigits:2});
  document.getElementById('cInterest').textContent = interest.toLocaleString(undefined,{minimumFractionDigits:2});
  document.getElementById('cTotal').textContent = total.toLocaleString(undefined,{minimumFractionDigits:2});
  document.getElementById('cMonthly').textContent = monthly.toLocaleString(undefined,{minimumFractionDigits:2});
}
</script>
@endsection
