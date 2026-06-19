@extends('layouts.dashboard')
@section('page_title','Payroll Periods')
@section('content')
<div class="dash-content">
<div class="pay-wrap">
  <div class="page-card" style="max-width:1000px;margin:0 auto;">
    <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;">
      <div><div class="card-title">Payroll Periods</div></div>
      <button class="btn btn-primary btn-sm" onclick="document.getElementById('addModal').classList.add('open')" style="gap:.35rem;">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        New Period
      </button>
    </div>
    <div class="card-body" style="padding:0;">
      @if($periods->count())
      <table class="mf-table">
        <thead><tr><th>Name</th><th>Period</th><th>Pay Date</th><th>Status</th><th style="text-align:right;">Actions</th></tr></thead>
        <tbody>
          @foreach($periods as $p)
          <tr>
            <td><a href="{{ route('dashboard.payroll.period.show', $p) }}" style="color:#2563eb;font-weight:700;text-decoration:none;">{{ $p->name }}</a></td>
            <td style="font-size:.75rem;color:#64748b;">{{ $p->start_date->format('M d, Y') }} — {{ $p->end_date->format('M d, Y') }}</td>
            <td style="font-size:.75rem;color:#64748b;">{{ $p->pay_date ? $p->pay_date->format('M d, Y') : '-' }}</td>
            <td><span class="status {{ $p->status }}">{{ ucfirst($p->status) }}</span></td>
            <td style="text-align:right;"><a href="{{ route('dashboard.payroll.period.show', $p) }}" class="btn btn-view btn-sm">Open</a></td>
          </tr>
          @endforeach
        </tbody>
      </table>
      @else
      <div style="padding:3rem;text-align:center;color:#94a3b8;"><p style="font-weight:600;color:#64748b;">No periods yet</p></div>
      @endif
    </div>
  </div>
</div>
</div>

<div class="modal-overlay" id="addModal" onclick="if(event.target===this)this.classList.remove('open')">
  <div class="modal-box" style="max-width:480px;" onclick="event.stopPropagation()">
    <div class="modal-header"><h3 class="modal-title">New Payroll Period</h3><button class="modal-close" onclick="document.getElementById('addModal').classList.remove('open')">&times;</button></div>
    <form method="POST" action="{{ route('dashboard.payroll.periods.store') }}">@csrf
      <div class="form-group"><label class="form-label">Period Name *</label><input name="name" class="form-control" placeholder="e.g. January 2026" required></div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Start Date *</label><input name="start_date" type="date" class="form-control" required></div>
        <div class="form-group"><label class="form-label">End Date *</label><input name="end_date" type="date" class="form-control" required></div>
      </div>
      <div class="form-group"><label class="form-label">Pay Date</label><input name="pay_date" type="date" class="form-control"></div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" onclick="document.getElementById('addModal').classList.remove('open')">Cancel</button><button type="submit" class="btn btn-primary">Create Period</button></div>
    </form>
  </div>
</div>
@endsection
