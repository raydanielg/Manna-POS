@extends('layouts.dashboard')
@section('page_title','My Profile')
@section('content')
<div class="dash-content">
<div class="page-card" style="max-width:700px;">
  <div class="card-header">
    <div class="card-title">My Profile</div>
  </div>
  <div class="card-body">
    <form id="profileForm">
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Full Name *</label>
          <input name="name" class="form-control" value="{{ $user->name }}" required>
          <div class="invalid-feedback"></div>
        </div>
        <div class="form-group">
          <label class="form-label">Email *</label>
          <input name="email" type="email" class="form-control" value="{{ $user->email }}" required>
          <div class="invalid-feedback"></div>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Phone</label>
          <input name="phone" class="form-control" value="{{ $user->phone ?? '' }}" placeholder="+255 7xx xxx xxx">
          <div class="invalid-feedback"></div>
        </div>
        <div class="form-group">
          <label class="form-label">Role</label>
          <input class="form-control" value="{{ ucfirst($user->role) }}" disabled style="background:#f8fafc;">
        </div>
      </div>
      
      <div style="margin:1.5rem 0;padding:1rem;background:#f8fafc;border-radius:8px;border:1px solid #e2e8f0;">
        <div style="font-size:0.85rem;font-weight:700;color:#0f172a;margin-bottom:1rem;">Change Password</div>
        <div class="form-group">
          <label class="form-label">Current Password</label>
          <input name="current_password" type="password" class="form-control" placeholder="Enter current password">
          <div class="invalid-feedback"></div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">New Password</label>
            <input name="new_password" type="password" class="form-control" placeholder="Min 8 characters">
            <div class="invalid-feedback"></div>
          </div>
          <div class="form-group">
            <label class="form-label">Confirm New Password</label>
            <input name="new_password_confirmation" type="password" class="form-control" placeholder="Confirm new password">
            <div class="invalid-feedback"></div>
          </div>
        </div>
      </div>

      <div style="display:flex;gap:0.75rem;justify-content:flex-end;margin-top:1rem;">
        <button type="button" class="btn btn-secondary" onclick="location.reload()">Cancel</button>
        <button type="button" class="btn btn-primary" id="saveBtn" onclick="saveProfile()">Save Changes</button>
      </div>
    </form>
  </div>
</div>
</div>
@endsection
@section('scripts')
<script>
async function saveProfile() {
  clearFormErrors('profileForm');
  const data = Object.fromEntries(new FormData(document.getElementById('profileForm')));
  const btn = document.getElementById('saveBtn');
  btn.disabled = true;
  btn.textContent = 'Saving...';
  try {
    await apiFetch('/dashboard/profile', {method:'PUT', body:JSON.stringify(data)});
    showToast('Profile updated successfully!');
  } catch(e) {
    if(e.errors) showFormErrors('profileForm',e.errors);
    else showToast(e.message||'Update failed','error');
  } finally {
    btn.disabled = false;
    btn.textContent = 'Save Changes';
  }
}
</script>
@endsection
