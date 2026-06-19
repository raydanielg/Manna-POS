@extends('layouts.dashboard')
@section('page_title','Guarantors')
@section('content')
<div class="dash-content">
<div class="mf-wrap">
  <div class="page-card" style="max-width:1100px;margin:0 auto;">
    <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;">
      <div><div class="card-title">Guarantors</div></div>
      <button class="btn btn-primary btn-sm" onclick="document.getElementById('addModal').classList.add('open')" style="gap:.35rem;">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Add Guarantor
      </button>
    </div>
    <div class="card-body" style="padding:0;">
      @if($guarantors->count())
      <table class="mf-table">
        <thead>
          <tr><th>Name</th><th>Phone</th><th>ID Number</th><th>Relationship</th><th>Pledged</th><th>Loan</th><th style="text-align:right;">Actions</th></tr>
        </thead>
        <tbody>
          @foreach($guarantors as $g)
          <tr>
            <td><strong style="color:#0f172a;">{{ $g->name }}</strong><br><span style="font-size:.7rem;color:#94a3b8;">{{ $g->email ?? '' }}</span></td>
            <td>{{ $g->phone }}</td>
            <td>{{ $g->id_number ?? '-' }}</td>
            <td>{{ $g->relationship ?? '-' }}</td>
            <td>{{ $g->pledged_amount ? number_format($g->pledged_amount, 2) : '-' }}</td>
            <td>@if($g->loan)<a href="{{ route('dashboard.microfinance.loan.show', $g->loan) }}" style="color:#2563eb;font-weight:600;font-size:.75rem;">{{ $g->loan->loan_number }}</a>@else<span style="color:#94a3b8;font-size:.75rem;">Unassigned</span>@endif</td>
            <td style="text-align:right;">
              <button class="btn btn-edit btn-sm" onclick="editGuarantor({{ $g->id }}, '{{ addslashes($g->name) }}', '{{ $g->phone }}', '{{ $g->email ?? '' }}', '{{ $g->id_number ?? '' }}', '{{ addslashes($g->address ?? '') }}', '{{ $g->relationship ?? '' }}', {{ $g->pledged_amount ?? 0 }}, '{{ addslashes($g->notes ?? '') }}', {{ $g->loan_id ?? 'null' }})">Edit</button>
              <form method="POST" action="{{ route('dashboard.microfinance.guarantors.destroy', $g) }}" style="display:inline;" onsubmit="return confirm('Remove this guarantor?');">@csrf @method('DELETE')<button class="btn btn-delete btn-sm">Remove</button></form>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
      @else
      <div style="padding:3rem;text-align:center;color:#94a3b8;"><p style="font-weight:600;color:#64748b;">No guarantors yet</p></div>
      @endif
    </div>
  </div>
</div>
</div>

<div class="modal-overlay" id="addModal" onclick="if(event.target===this)this.classList.remove('open')">
  <div class="modal-box" style="max-width:520px;" onclick="event.stopPropagation()">
    <div class="modal-header"><h3 class="modal-title">Add Guarantor</h3><button class="modal-close" onclick="document.getElementById('addModal').classList.remove('open')">&times;</button></div>
    <form method="POST" action="{{ route('dashboard.microfinance.guarantors.store') }}">@csrf
      <div class="form-row">
        <div class="form-group"><label class="form-label">Name *</label><input name="name" class="form-control" required></div>
        <div class="form-group"><label class="form-label">Phone *</label><input name="phone" class="form-control" required></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Email</label><input name="email" type="email" class="form-control"></div>
        <div class="form-group"><label class="form-label">ID Number</label><input name="id_number" class="form-control"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Relationship</label><input name="relationship" class="form-control" placeholder="e.g. Brother, Friend"></div>
        <div class="form-group"><label class="form-label">Pledged Amount</label><input name="pledged_amount" type="number" step="0.01" class="form-control"></div>
      </div>
      <div class="form-group"><label class="form-label">Address</label><textarea name="address" class="form-control" rows="2"></textarea></div>
      <div class="form-group"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" onclick="document.getElementById('addModal').classList.remove('open')">Cancel</button><button type="submit" class="btn btn-primary">Save Guarantor</button></div>
    </form>
  </div>
</div>

<div class="modal-overlay" id="editModal" onclick="if(event.target===this)this.classList.remove('open')">
  <div class="modal-box" style="max-width:520px;" onclick="event.stopPropagation()">
    <div class="modal-header"><h3 class="modal-title">Edit Guarantor</h3><button class="modal-close" onclick="document.getElementById('editModal').classList.remove('open')">&times;</button></div>
    <form method="POST" id="editForm" action="">@csrf @method('PUT')
      <div class="form-row">
        <div class="form-group"><label class="form-label">Name *</label><input name="name" id="g_name" class="form-control" required></div>
        <div class="form-group"><label class="form-label">Phone *</label><input name="phone" id="g_phone" class="form-control" required></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Email</label><input name="email" id="g_email" type="email" class="form-control"></div>
        <div class="form-group"><label class="form-label">ID Number</label><input name="id_number" id="g_id" class="form-control"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Relationship</label><input name="relationship" id="g_rel" class="form-control"></div>
        <div class="form-group"><label class="form-label">Pledged Amount</label><input name="pledged_amount" id="g_pledge" type="number" step="0.01" class="form-control"></div>
      </div>
      <div class="form-group"><label class="form-label">Address</label><textarea name="address" id="g_addr" class="form-control" rows="2"></textarea></div>
      <div class="form-group"><label class="form-label">Notes</label><textarea name="notes" id="g_notes" class="form-control" rows="2"></textarea></div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" onclick="document.getElementById('editModal').classList.remove('open')">Cancel</button><button type="submit" class="btn btn-primary">Update Guarantor</button></div>
    </form>
  </div>
</div>

<script>
function editGuarantor(id, name, phone, email, idNum, address, rel, pledge, notes, loanId) {
  document.getElementById('editForm').action = '/dashboard/microfinance/guarantors/' + id;
  document.getElementById('g_name').value = name;
  document.getElementById('g_phone').value = phone;
  document.getElementById('g_email').value = email;
  document.getElementById('g_id').value = idNum;
  document.getElementById('g_addr').value = address;
  document.getElementById('g_rel').value = rel;
  document.getElementById('g_pledge').value = pledge;
  document.getElementById('g_notes').value = notes;
  document.getElementById('editModal').classList.add('open');
}
</script>
@endsection
