@extends('layouts.dashboard')
@section('page_title','Loan Products')
@section('content')
<div class="dash-content">
<div class="mf-wrap">
  <div class="page-card" style="max-width:1000px;margin:0 auto;">
    <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;">
      <div><div class="card-title">Loan Products</div><p style="font-size:.75rem;color:#64748b;margin-top:.25rem;">Configure loan templates for quick application</p></div>
      <button class="btn btn-primary btn-sm" onclick="document.getElementById('addModal').classList.add('open')" style="gap:.35rem;">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Add Product
      </button>
    </div>
    <div class="card-body" style="padding:0;">
      @if($products->count())
      <table class="mf-table">
        <thead>
          <tr><th>Name</th><th>Amount Range</th><th>Interest</th><th>Duration</th><th>Status</th><th style="text-align:right;">Actions</th></tr>
        </thead>
        <tbody>
          @foreach($products as $p)
          <tr>
            <td><strong style="color:#0f172a;">{{ $p->name }}</strong><br><span style="font-size:.7rem;color:#94a3b8;">{{ Str::limit($p->description, 40) }}</span></td>
            <td>{{ number_format($p->min_amount,0) }} — {{ number_format($p->max_amount,0) }}</td>
            <td>{{ $p->interest_rate }}% {{ ucfirst(str_replace('_',' ',$p->interest_type)) }}</td>
            <td>{{ $p->duration_min }}–{{ $p->duration_max }} months</td>
            <td><span class="status {{ $p->status }}">{{ ucfirst($p->status) }}</span></td>
            <td style="text-align:right;">
              <button class="btn btn-edit btn-sm" onclick="editProduct({{ $p->id }}, '{{ addslashes($p->name) }}', '{{ addslashes($p->description) }}', {{ $p->min_amount }}, {{ $p->max_amount }}, {{ $p->interest_rate }}, '{{ $p->interest_type }}', {{ $p->duration_min }}, {{ $p->duration_max }}, '{{ $p->status }}')">Edit</button>
              <form method="POST" action="{{ route('dashboard.microfinance.products.destroy', $p) }}" style="display:inline;" onsubmit="return confirm('Delete this product?');">
                @csrf @method('DELETE')
                <button class="btn btn-delete btn-sm">Delete</button>
              </form>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
      @else
      <div style="padding:3rem;text-align:center;color:#94a3b8;">
        <svg width="40" height="40" fill="none" stroke="#cbd5e1" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 1rem;display:block;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 14h6m-3-3v6m-7 4v-16a2 2 0 012-2h10a2 2 0 012 2v16l-3-2l-2 2l-2-2l-2 2l-2-2l-3 2"/></svg>
        <p style="font-weight:600;color:#64748b;">No loan products yet</p>
        <p style="font-size:.8rem;margin-top:.25rem;">Create your first loan product to get started</p>
      </div>
      @endif
    </div>
  </div>
</div>
</div>

{{-- Add Modal --}}
<div class="modal-overlay" id="addModal" onclick="if(event.target===this)this.classList.remove('open')">
  <div class="modal-box" style="max-width:520px;" onclick="event.stopPropagation()">
    <div class="modal-header"><h3 class="modal-title">Add Loan Product</h3><button class="modal-close" onclick="document.getElementById('addModal').classList.remove('open')">&times;</button></div>
    <form method="POST" action="{{ route('dashboard.microfinance.products.store') }}">
      @csrf
      <div class="form-row">
        <div class="form-group"><label class="form-label">Name *</label><input name="name" class="form-control" required></div>
        <div class="form-group"><label class="form-label">Status</label>
          <select name="status" class="form-control"><option value="active">Active</option><option value="inactive">Inactive</option></select>
        </div>
      </div>
      <div class="form-group"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="2"></textarea></div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Min Amount *</label><input name="min_amount" type="number" step="0.01" class="form-control" required></div>
        <div class="form-group"><label class="form-label">Max Amount *</label><input name="max_amount" type="number" step="0.01" class="form-control" required></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Interest Rate (%)</label><input name="interest_rate" type="number" step="0.01" class="form-control" value="12" required></div>
        <div class="form-group"><label class="form-label">Interest Type</label>
          <select name="interest_type" class="form-control"><option value="flat">Flat</option><option value="reducing_balance">Reducing Balance</option></select>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Min Duration (months)</label><input name="duration_min" type="number" class="form-control" value="1" required></div>
        <div class="form-group"><label class="form-label">Max Duration (months)</label><input name="duration_max" type="number" class="form-control" value="12" required></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" onclick="document.getElementById('addModal').classList.remove('open')">Cancel</button><button type="submit" class="btn btn-primary">Save Product</button></div>
    </form>
  </div>
</div>

{{-- Edit Modal --}}
<div class="modal-overlay" id="editModal" onclick="if(event.target===this)this.classList.remove('open')">
  <div class="modal-box" style="max-width:520px;" onclick="event.stopPropagation()">
    <div class="modal-header"><h3 class="modal-title">Edit Loan Product</h3><button class="modal-close" onclick="document.getElementById('editModal').classList.remove('open')">&times;</button></div>
    <form method="POST" id="editForm" action="">
      @csrf @method('PUT')
      <div class="form-row">
        <div class="form-group"><label class="form-label">Name *</label><input name="name" id="e_name" class="form-control" required></div>
        <div class="form-group"><label class="form-label">Status</label>
          <select name="status" id="e_status" class="form-control"><option value="active">Active</option><option value="inactive">Inactive</option></select>
        </div>
      </div>
      <div class="form-group"><label class="form-label">Description</label><textarea name="description" id="e_description" class="form-control" rows="2"></textarea></div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Min Amount *</label><input name="min_amount" id="e_min" type="number" step="0.01" class="form-control" required></div>
        <div class="form-group"><label class="form-label">Max Amount *</label><input name="max_amount" id="e_max" type="number" step="0.01" class="form-control" required></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Interest Rate (%)</label><input name="interest_rate" id="e_rate" type="number" step="0.01" class="form-control" required></div>
        <div class="form-group"><label class="form-label">Interest Type</label>
          <select name="interest_type" id="e_type" class="form-control"><option value="flat">Flat</option><option value="reducing_balance">Reducing Balance</option></select>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Min Duration (months)</label><input name="duration_min" id="e_dmin" type="number" class="form-control" required></div>
        <div class="form-group"><label class="form-label">Max Duration (months)</label><input name="duration_max" id="e_dmax" type="number" class="form-control" required></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" onclick="document.getElementById('editModal').classList.remove('open')">Cancel</button><button type="submit" class="btn btn-primary">Update Product</button></div>
    </form>
  </div>
</div>

<script>
function editProduct(id, name, desc, minAmt, maxAmt, rate, type, dmin, dmax, status) {
  document.getElementById('editForm').action = '/dashboard/microfinance/products/' + id;
  document.getElementById('e_name').value = name;
  document.getElementById('e_description').value = desc || '';
  document.getElementById('e_min').value = minAmt;
  document.getElementById('e_max').value = maxAmt;
  document.getElementById('e_rate').value = rate;
  document.getElementById('e_type').value = type;
  document.getElementById('e_dmin').value = dmin;
  document.getElementById('e_dmax').value = dmax;
  document.getElementById('e_status').value = status;
  document.getElementById('editModal').classList.add('open');
}
</script>
@endsection
