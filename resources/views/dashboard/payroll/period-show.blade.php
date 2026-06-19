@extends('layouts.dashboard')
@section('page_title',$period->name)
@section('page_styles')
<style>
.pay-summary{display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:1.5rem;}
.pay-sum{background:#fff;border-radius:14px;border:1.5px solid #eef2f6;padding:1.25rem;text-align:center;}
.pay-sum .label{font-size:.65rem;font-weight:700;text-transform:uppercase;color:#94a3b8;}
.pay-sum .value{font-size:1.3rem;font-weight:800;color:#0f172a;margin-top:.35rem;}
</style>
@endsection
@section('content')
<div class="dash-content">
<div class="pay-wrap">

  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;">
    <div>
      <h1 style="font-size:1.2rem;font-weight:800;color:#0f172a;">{{ $period->name }}</h1>
      <p style="font-size:.8rem;color:#64748b;margin-top:.25rem;">{{ $period->start_date->format('M d, Y') }} — {{ $period->end_date->format('M d, Y') }} @if($period->pay_date)&middot; Pay Date: {{ $period->pay_date->format('M d, Y') }}@endif</p>
    </div>
    <div style="display:flex;gap:.5rem;">
      <button class="btn btn-primary btn-sm" onclick="document.getElementById('addEntryModal').classList.add('open')" style="gap:.35rem;">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Add Entry
      </button>
      <a href="{{ route('dashboard.payroll.periods') }}" class="btn btn-secondary btn-sm">Back</a>
    </div>
  </div>

  <div class="pay-summary">
    <div class="pay-sum"><div class="label">Staff</div><div class="value">{{ $summary['staff_count'] }}</div></div>
    <div class="pay-sum"><div class="label">Gross</div><div class="value">{{ number_format($summary['total_gross'], 0) }}</div></div>
    <div class="pay-sum"><div class="label">Deductions</div><div class="value" style="color:#ef4444;">{{ number_format($summary['total_deductions'], 0) }}</div></div>
    <div class="pay-sum"><div class="label">Net Pay</div><div class="value" style="color:#22c55e;">{{ number_format($summary['total_net'], 0) }}</div></div>
  </div>

  <div class="page-card" style="max-width:1200px;margin:0 auto;">
    <div class="card-body" style="padding:0;">
      @if($period->entries->count())
      <div style="overflow-x:auto;">
        <table class="mf-table" style="min-width:1000px;">
          <thead>
            <tr>
              <th>Staff</th><th>Basic</th><th>OT</th><th>Bonus</th><th>Gross</th>
              <th>Tax</th><th>NSSF</th><th>NHIF</th><th>Loan</th><th>Other</th><th>Total Ded.</th><th>Net</th><th style="text-align:right;">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($period->entries as $e)
            <tr>
              <td><strong style="color:#0f172a;font-size:.82rem;">{{ $e->staff->name ?? 'N/A' }}</strong></td>
              <td>{{ number_format($e->basic_salary, 0) }}</td>
              <td>{{ number_format($e->overtime_amount, 0) }}</td>
              <td>{{ number_format($e->bonus, 0) }}</td>
              <td style="font-weight:700;">{{ number_format($e->gross_salary, 0) }}</td>
              <td style="color:#ef4444;">{{ number_format($e->tax_deduction, 0) }}</td>
              <td style="color:#ef4444;">{{ number_format($e->nssf_deduction, 0) }}</td>
              <td style="color:#ef4444;">{{ number_format($e->nhif_deduction, 0) }}</td>
              <td style="color:#ef4444;">{{ number_format($e->loan_deduction, 0) }}</td>
              <td style="color:#ef4444;">{{ number_format($e->other_deduction, 0) }}</td>
              <td style="color:#ef4444;font-weight:700;">{{ number_format($e->total_deduction, 0) }}</td>
              <td style="color:#22c55e;font-weight:800;">{{ number_format($e->net_salary, 0) }}</td>
              <td style="text-align:right;white-space:nowrap;">
                <a href="{{ route('dashboard.payroll.payslip', $e) }}" target="_blank" class="btn btn-view btn-sm">Payslip</a>
                @if($e->status === 'draft')
                <form method="POST" action="{{ route('dashboard.payroll.entry.status', $e) }}" style="display:inline;">@csrf
                  <input type="hidden" name="status" value="approved">
                  <button type="submit" class="btn btn-edit btn-sm">Approve</button>
                </form>
                @endif
                <form method="POST" action="{{ route('dashboard.payroll.entry.destroy', $e) }}" style="display:inline;" onsubmit="return confirm('Remove entry?');">@csrf @method('DELETE')
                  <button type="submit" class="btn btn-delete btn-sm">Delete</button>
                </form>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @else
      <div style="padding:3rem;text-align:center;color:#94a3b8;"><p style="font-weight:600;color:#64748b;">No entries yet. Add staff to this period.</p></div>
      @endif
    </div>
  </div>

</div>
</div>

{{-- Add Entry Modal --}}
<div class="modal-overlay" id="addEntryModal" onclick="if(event.target===this)this.classList.remove('open')">
  <div class="modal-box" style="max-width:700px;max-height:90vh;overflow-y:auto;" onclick="event.stopPropagation()">
    <div class="modal-header"><h3 class="modal-title">Add Payroll Entry</h3><button class="modal-close" onclick="document.getElementById('addEntryModal').classList.remove('open')">&times;</button></div>
    <form method="POST" action="{{ route('dashboard.payroll.entry.store', $period) }}" id="entryForm">@csrf
      <div class="form-group"><label class="form-label">Staff Member *</label>
        <select name="staff_id" class="form-control" required>
          <option value="">Select staff…</option>
          @foreach($staffList as $s)
          <option value="{{ $s->id }}" data-salary="{{ $s->salary ?? 0 }}">{{ $s->name }} ({{ $s->role ?? 'Staff' }})</option>
          @endforeach
        </select>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Basic Salary *</label><input name="basic_salary" id="basicSalary" type="number" step="0.01" class="form-control" required oninput="calcPay()"></div>
        <div class="form-group"><label class="form-label">Allowance</label><input name="allowance" id="allowance" type="number" step="0.01" class="form-control" value="0" oninput="calcPay()"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Overtime Hours</label><input name="overtime_hours" id="otHours" type="number" step="0.5" class="form-control" value="0" oninput="calcPay()"></div>
        <div class="form-group"><label class="form-label">Overtime Rate</label><input name="overtime_rate" id="otRate" type="number" step="0.01" class="form-control" value="0" oninput="calcPay()"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Bonus</label><input name="bonus" id="bonus" type="number" step="0.01" class="form-control" value="0" oninput="calcPay()"></div>
        <div class="form-group"><label class="form-label">Tax Deduction</label><input name="tax_deduction" id="taxDed" type="number" step="0.01" class="form-control" value="0" oninput="calcPay()"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">NSSF</label><input name="nssf_deduction" id="nssf" type="number" step="0.01" class="form-control" value="0" oninput="calcPay()"></div>
        <div class="form-group"><label class="form-label">NHIF</label><input name="nhif_deduction" id="nhif" type="number" step="0.01" class="form-control" value="0" oninput="calcPay()"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Loan Deduction</label><input name="loan_deduction" id="loanDed" type="number" step="0.01" class="form-control" value="0" oninput="calcPay()"></div>
        <div class="form-group"><label class="form-label">Other Deduction</label><input name="other_deduction" id="otherDed" type="number" step="0.01" class="form-control" value="0" oninput="calcPay()"></div>
      </div>
      <div class="form-group"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2"></textarea></div>

      <div class="calc-preview">
        <h4>Preview</h4>
        <div class="calc-row"><span>Gross Salary</span><span id="pGross" style="font-weight:800;color:#2563eb;">0.00</span></div>
        <div class="calc-row"><span>Total Deductions</span><span id="pDed" style="font-weight:800;color:#ef4444;">0.00</span></div>
        <div class="calc-row"><span>Net Salary</span><span id="pNet" style="font-weight:800;color:#22c55e;">0.00</span></div>
      </div>

      <div class="modal-footer"><button type="button" class="btn btn-secondary" onclick="document.getElementById('addEntryModal').classList.remove('open')">Cancel</button><button type="submit" class="btn btn-primary">Save Entry</button></div>
    </form>
  </div>
</div>

<script>
document.querySelector('select[name="staff_id"]').addEventListener('change', function() {
  const opt = this.options[this.selectedIndex];
  const salary = parseFloat(opt.dataset.salary) || 0;
  document.getElementById('basicSalary').value = salary;
  calcPay();
});

function calcPay() {
  const basic = parseFloat(document.getElementById('basicSalary').value) || 0;
  const otH = parseFloat(document.getElementById('otHours').value) || 0;
  const otR = parseFloat(document.getElementById('otRate').value) || 0;
  const bonus = parseFloat(document.getElementById('bonus').value) || 0;
  const allowance = parseFloat(document.getElementById('allowance').value) || 0;
  const tax = parseFloat(document.getElementById('taxDed').value) || 0;
  const nssf = parseFloat(document.getElementById('nssf').value) || 0;
  const nhif = parseFloat(document.getElementById('nhif').value) || 0;
  const loan = parseFloat(document.getElementById('loanDed').value) || 0;
  const other = parseFloat(document.getElementById('otherDed').value) || 0;

  const gross = basic + (otH * otR) + bonus + allowance;
  const ded = tax + nssf + nhif + loan + other;
  const net = gross - ded;

  document.getElementById('pGross').textContent = gross.toLocaleString(undefined,{minimumFractionDigits:2});
  document.getElementById('pDed').textContent = ded.toLocaleString(undefined,{minimumFractionDigits:2});
  document.getElementById('pNet').textContent = net.toLocaleString(undefined,{minimumFractionDigits:2});
}
</script>
@endsection
