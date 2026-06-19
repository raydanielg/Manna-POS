@extends('layouts.dashboard')
@section('page_title','Approval Requests')
@section('content')
<div class="dash-content">
<div class="page-card">
  <div class="card-header">
    <div class="card-title">Approval Requests</div>
    <div class="filters-row">
      <div class="search-wrap">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
        <input type="text" placeholder="Search..." oninput="filterTable(this.value)">
      </div>
      <div style="display:flex;gap:0.4rem;">
        <select class="form-control" onchange="filterStatus(this.value)" style="width:auto;padding:0.3rem 0.6rem;font-size:0.8rem;">
          <option value="">All Status</option>
          <option value="pending">Pending</option>
          <option value="approved">Approved</option>
          <option value="rejected">Rejected</option>
        </select>
      </div>
      @if(!Auth::user()->isOwner())
      <button class="btn btn-success" onclick="openSubmitModal()">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        New Request
      </button>
      @endif
    </div>
  </div>
  <div style="overflow-x:auto;">
    <table class="tbl">
      <thead>
        <tr>
          <th>#</th>
          <th>Staff</th>
          <th>Module</th>
          <th>Action</th>
          <th>Reason</th>
          <th>Status</th>
          <th>Reviewer</th>
          <th>Notes</th>
          <th>Date</th>
          @can('approvals.approve')<th>Actions</th>@endcan
        </tr>
      </thead>
      <tbody id="tableBody">
        @forelse($requests as $i => $r)
        <tr data-status="{{ $r->status }}">
          <td class="text-slate-400">{{ $loop->iteration }}</td>
          <td class="font-semibold">{{ $r->user?->name ?? 'N/A' }}</td>
          <td><span class="badge badge-info">{{ ucfirst($r->module) }}</span></td>
          <td>{{ str_replace('_',' ',$r->action) }}</td>
          <td class="text-xs text-slate-500" style="max-width:200px;white-space:normal;">{{ Str::limit($r->reason, 60) }}</td>
          <td>
            @if($r->status === 'pending')<span class="badge badge-warning">Pending</span>
            @elseif($r->status === 'approved')<span class="badge badge-success">Approved</span>
            @else<span class="badge badge-danger">Rejected</span>@endif
          </td>
          <td class="text-xs">{{ $r->reviewer?->name ?? '-' }}</td>
          <td class="text-xs text-slate-500" style="max-width:150px;white-space:normal;">{{ $r->review_notes ?? '-' }}</td>
          <td class="text-xs text-slate-400">{{ $r->created_at->format('M d, H:i') }}</td>
          @can('approvals.approve')
          <td>
            @if($r->status === 'pending')
            <div style="display:flex;gap:0.3rem;">
              <button class="btn btn-sm btn-success" onclick="approveRequest({{ $r->id }})">Approve</button>
              <button class="btn btn-sm btn-delete" onclick="rejectRequest({{ $r->id }})">Reject</button>
            </div>
            @else
            <span class="text-xs text-slate-400">-</span>
            @endif
          </td>
          @endcan
        </tr>
        @empty
        <tr><td colspan="{{ Auth::user()->isOwner() ? 10 : 9 }}" class="tbl-empty">No approval requests found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($requests->hasPages())
  <div style="padding:1rem;">{{ $requests->links() }}</div>
  @endif
</div>
</div>

{{-- Submit Modal (staff only) --}}
@if(!Auth::user()->isOwner())
<div class="modal-overlay" id="submitModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">New Approval Request</div>
      <button class="modal-close" onclick="closeModal('submitModal')"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <div class="modal-body">
      <form id="submitForm">
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Module *</label>
            <select name="module" class="form-control" required>
              <option value="">Select module</option>
              <option value="sales">Sales</option>
              <option value="purchases">Purchases</option>
              <option value="expenses">Expenses</option>
              <option value="products">Products</option>
              <option value="contacts">Contacts</option>
              <option value="settings">Settings</option>
            </select>
            <div class="invalid-feedback"></div>
          </div>
          <div class="form-group">
            <label class="form-label">Action *</label>
            <select name="action" class="form-control" required>
              <option value="">Select action</option>
              <option value="create">Create</option>
              <option value="edit">Edit</option>
              <option value="delete">Delete</option>
              <option value="approve">Approve</option>
            </select>
            <div class="invalid-feedback"></div>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Reason *</label>
          <textarea name="reason" class="form-control" rows="3" required placeholder="Explain why you need this action performed..."></textarea>
          <div class="invalid-feedback"></div>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('submitModal')">Cancel</button>
      <button class="btn btn-primary" id="submitBtn" onclick="submitRequest()">Submit Request</button>
    </div>
  </div>
</div>
@endif

{{-- Review Modal --}}
<div class="modal-overlay" id="reviewModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title" id="reviewTitle">Review Request</div>
      <button class="modal-close" onclick="closeModal('reviewModal')"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <div class="modal-body">
      <form id="reviewForm">
        <div class="form-group">
          <label class="form-label">Review Notes</label>
          <textarea name="notes" class="form-control" rows="3" placeholder="Add notes (required for rejection)..."></textarea>
          <div class="invalid-feedback"></div>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('reviewModal')">Cancel</button>
      <button class="btn btn-success" id="approveBtn" onclick="confirmApprove()" style="display:none;">Approve</button>
      <button class="btn btn-danger" id="rejectBtn" onclick="confirmReject()" style="display:none;">Reject</button>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
let reviewId = null;
let reviewAction = null;
const APPROVE_URL = '{{ route("dashboard.approvals.approve", 0) }}';
const REJECT_URL = '{{ route("dashboard.approvals.reject", 0) }}';

function filterTable(val) {
  document.querySelectorAll('#tableBody tr[data-status]').forEach(row => {
    const text = row.textContent.toLowerCase();
    row.style.display = text.includes(val.toLowerCase()) ? '' : 'none';
  });
}
function filterStatus(val) {
  document.querySelectorAll('#tableBody tr[data-status]').forEach(row => {
    if (!val || row.dataset.status === val) row.style.display = '';
    else row.style.display = 'none';
  });
}

function openSubmitModal() {
  document.getElementById('submitForm').reset();
  clearFormErrors('submitForm');
  openModal('submitModal');
}

async function submitRequest() {
  clearFormErrors('submitForm');
  const data = Object.fromEntries(new FormData(document.getElementById('submitForm')));
  const btn = document.getElementById('submitBtn');
  btn.disabled = true; btn.textContent = 'Submitting...';
  try {
    await apiFetch('{{ route("dashboard.approvals.store") }}', { method: 'POST', body: JSON.stringify(data) });
    closeModal('submitModal');
    showToast('Request submitted!');
    setTimeout(() => location.reload(), 1000);
  } catch (e) {
    if (e.errors) showFormErrors('submitForm', e.errors);
    else showToast(e.message || 'Failed', 'error');
  } finally {
    btn.disabled = false; btn.textContent = 'Submit Request';
  }
}

function approveRequest(id) {
  reviewId = id; reviewAction = 'approve';
  document.getElementById('reviewTitle').textContent = 'Approve Request';
  document.getElementById('approveBtn').style.display = '';
  document.getElementById('rejectBtn').style.display = 'none';
  document.getElementById('reviewForm').reset();
  clearFormErrors('reviewForm');
  openModal('reviewModal');
}

function rejectRequest(id) {
  reviewId = id; reviewAction = 'reject';
  document.getElementById('reviewTitle').textContent = 'Reject Request';
  document.getElementById('approveBtn').style.display = 'none';
  document.getElementById('rejectBtn').style.display = '';
  document.getElementById('reviewForm').reset();
  clearFormErrors('reviewForm');
  openModal('reviewModal');
}

async function confirmApprove() {
  const notes = document.querySelector('#reviewForm [name="notes"]').value;
  const btn = document.getElementById('approveBtn');
  btn.disabled = true; btn.textContent = 'Approving...';
  try {
    await apiFetch(APPROVE_URL.replace('/0/', '/' + reviewId + '/'), { method: 'POST', body: JSON.stringify({ notes }) });
    closeModal('reviewModal');
    showToast('Approved!');
    setTimeout(() => location.reload(), 1000);
  } catch (e) {
    showToast(e.message || 'Failed', 'error');
    btn.disabled = false; btn.textContent = 'Approve';
  }
}

async function confirmReject() {
  const notes = document.querySelector('#reviewForm [name="notes"]').value;
  if (!notes) { showToast('Notes are required for rejection', 'error'); return; }
  const btn = document.getElementById('rejectBtn');
  btn.disabled = true; btn.textContent = 'Rejecting...';
  try {
    await apiFetch(REJECT_URL.replace('/0/', '/' + reviewId + '/'), { method: 'POST', body: JSON.stringify({ notes }) });
    closeModal('reviewModal');
    showToast('Rejected!');
    setTimeout(() => location.reload(), 1000);
  } catch (e) {
    showToast(e.message || 'Failed', 'error');
    btn.disabled = false; btn.textContent = 'Reject';
  }
}
</script>
@endsection
