@extends('layouts.dashboard')
@section('page_title','Deduction Types')
@section('content')
<div class="dash-content">
<div class="pay-wrap">
  <div class="page-card" style="max-width:800px;margin:0 auto;">
    <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;">
      <div><div class="card-title">Deduction Types</div><p style="font-size:.75rem;color:#64748b;margin-top:.25rem;">Configure automatic deduction rules</p></div>
      <button class="btn btn-primary btn-sm" onclick="document.getElementById('addModal').classList.add('open')" style="gap:.35rem;">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Add Type
      </button>
    </div>
    <div class="card-body" style="padding:0;">
      @if($types->count())
      <table class="mf-table">
        <thead><tr><th>Name</th><th>Type</th><th>Value</th><th>Status</th><th style="text-align:right;">Actions</th></tr></thead>
        <tbody>
          @foreach($types as $t)
          <tr>
            <td><strong style="color:#0f172a;">{{ $t->name }}</strong></td>
            <td><span class="status {{ $t->type }}">{{ ucfirst(str_replace('_',' ',$t->type)) }}</span></td>
            <td>{{ number_format($t->value, 2) }}{{ $t->type === 'percentage' ? '%' : '' }}</td>
            <td><span class="status {{ $t->is_active ? 'active' : 'rejected' }}">{{ $t->is_active ? 'Active' : 'Inactive' }}</span></td>
            <td style="text-align:right;">
              <button class="btn btn-edit btn-sm" onclick="editType({{ $t->id }}, '{{ addslashes($t->name) }}', '{{ $t->type }}', {{ $t->value }}, {{ $t->is_active ? 1 : 0 }})">Edit</button>
              <form method="POST" action="{{ route('dashboard.payroll.deductions.destroy', $t) }}" style="display:inline;" onsubmit="return confirm('Delete?');">@csrf @method('DELETE')
                <button type="submit" class="btn btn-delete btn-sm">Delete</button>
              </form>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
      @else
      <div style="padding:3rem;text-align:center;color:#94a3b8;"><p style="font-weight:600;color:#64748b;">No deduction types configured</p></div>
      @endif
    </div>
  </div>
</div>
</div>

<div class="modal-overlay" id="addModal" onclick="if(event.target===this)this.classList.remove('open')">
  <div class="modal-box" style="max-width:480px;" onclick="event.stopPropagation()">
    <div class="modal-header"><h3 class="modal-title">Add Deduction Type</h3><button class="modal-close" onclick="document.getElementById('addModal').classList.remove('open')">&times;</button></div>
    <form method="POST" action="{{ route('dashboard.payroll.deductions.store') }}">@csrf
      <div class="form-group"><label class="form-label">Name *</label><input name="name" class="form-control" required></div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Type</label>
          <select name="type" class="form-control"><option value="fixed">Fixed Amount</option><option value="percentage">Percentage</option></select>
        </div>
        <div class="form-group"><label class="form-label">Value *</label><input name="value" type="number" step="0.01" class="form-control" required></div>
      </div>
      <div class="form-group"><label><input type="checkbox" name="is_active" value="1" checked> Active</label></div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" onclick="document.getElementById('addModal').classList.remove('open')">Cancel</button><button type="submit" class="btn btn-primary">Save</button></div>
    </form>
  </div>
</div>

<div class="modal-overlay" id="editModal" onclick="if(event.target===this)this.classList.remove('open')">
  <div class="modal-box" style="max-width:480px;" onclick="event.stopPropagation()">
    <div class="modal-header"><h3 class="modal-title">Edit Deduction Type</h3><button class="modal-close" onclick="document.getElementById('editModal').classList.remove('open')">&times;</button></div>
    <form method="POST" id="editForm" action="">@csrf @method('PUT')
      <div class="form-group"><label class="form-label">Name *</label><input name="name" id="d_name" class="form-control" required></div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Type</label>
          <select name="type" id="d_type" class="form-control"><option value="fixed">Fixed Amount</option><option value="percentage">Percentage</option></select>
        </div>
        <div class="form-group"><label class="form-label">Value *</label><input name="value" id="d_val" type="number" step="0.01" class="form-control" required></div>
      </div>
      <div class="form-group"><label><input type="checkbox" name="is_active" id="d_active" value="1"> Active</label></div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" onclick="document.getElementById('editModal').classList.remove('open')">Cancel</button><button type="submit" class="btn btn-primary">Update</button></div>
    </form>
  </div>
</div>

<script>
function editType(id, name, type, val, active) {
  document.getElementById('editForm').action = '/dashboard/payroll/deductions/' + id;
  document.getElementById('d_name').value = name;
  document.getElementById('d_type').value = type;
  document.getElementById('d_val').value = val;
  document.getElementById('d_active').checked = active;
  document.getElementById('editModal').classList.add('open');
}
</script>
@endsection
