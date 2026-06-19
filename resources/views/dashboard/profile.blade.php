@extends('layouts.dashboard')
@section('page_title','My Profile')
@section('page_styles')
<style>
.profile-wrap{max-width:720px;margin:0 auto;}
.profile-card{background:#fff;border-radius:16px;border:1.5px solid #eef2f6;overflow:hidden;margin-bottom:1.5rem;}
.profile-header{padding:1.25rem 1.5rem;background:linear-gradient(135deg,#fafbff,#f8fafc);border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:1rem;}
.profile-avatar{width:52px;height:52px;border-radius:14px;background:linear-gradient(135deg,#2563eb,#7c3aed);display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.4rem;font-weight:800;flex-shrink:0;}
.profile-header h2{font-size:1rem;font-weight:800;color:#0f172a;letter-spacing:-0.01em;}
.profile-header p{font-size:0.75rem;color:#64748b;margin-top:0.15rem;}
.profile-body{padding:1.5rem;}

.section-divider{margin:1.5rem 0;padding:1.25rem;background:#f8fafc;border-radius:12px;border:1px solid #e9edf5;}
.section-divider .sec-title{font-size:0.85rem;font-weight:700;color:#0f172a;margin-bottom:1rem;display:flex;align-items:center;gap:0.5rem;}
.sec-title svg{width:18px;height:18px;color:#94a3b8;}

.required-mark{color:#ef4444;font-size:0.7rem;}
.field-hint{font-size:0.7rem;color:#94a3b8;margin-top:0.25rem;}

.avatar-upload-wrap{display:flex;align-items:center;gap:1.25rem;padding:1rem 1.5rem;background:linear-gradient(135deg,#f0f4ff,#f5f3ff);border-radius:14px;border:1.5px dashed #c7d2fe;margin-bottom:1.5rem;position:relative;}
.avatar-preview-wrap{position:relative;flex-shrink:0;}
.avatar-preview{width:72px;height:72px;border-radius:16px;object-fit:cover;border:3px solid #fff;box-shadow:0 4px 12px rgba(37,99,235,0.15);background:linear-gradient(135deg,#2563eb,#7c3aed);display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.8rem;font-weight:800;}
.avatar-preview img{width:100%;height:100%;border-radius:13px;object-fit:cover;}
.avatar-upload-btn{position:absolute;bottom:-4px;right:-4px;width:28px;height:28px;border-radius:50%;background:#2563eb;border:2px solid #fff;color:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 2px 8px rgba(37,99,235,0.3);transition:all 0.2s;}
.avatar-upload-btn:hover{transform:scale(1.1);background:#1d4ed8;}
.avatar-upload-btn svg{width:14px;height:14px;}
.avatar-upload-info h4{font-size:0.85rem;font-weight:700;color:#1e293b;}
.avatar-upload-info p{font-size:0.72rem;color:#64748b;margin-top:2px;}
.avatar-upload-progress{display:none;margin-top:0.5rem;width:100%;height:4px;background:#e2e8f0;border-radius:4px;overflow:hidden;}
.avatar-upload-progress div{height:100%;width:0%;background:linear-gradient(90deg,#2563eb,#7c3aed);border-radius:4px;transition:width 0.3s;}
</style>
@endsection
@section('content')
<div class="dash-content">
<div class="profile-wrap">

  <div class="profile-card">
    <div class="profile-header">
      <div class="profile-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
      <div>
        <h2>{{ $user->name }}</h2>
        <p>{{ ucfirst($user->role) }} &middot; {{ $user->email }}</p>
      </div>
    </div>
    <div class="profile-body">

      <div class="avatar-upload-wrap" id="avatarUploadWrap">
        <div class="avatar-preview-wrap">
          <div class="avatar-preview" id="avatarPreview">
            @if($user->avatar)
              <img src="{{ $user->avatar_url }}" alt="Avatar">
            @else
              {{ strtoupper(substr($user->name, 0, 1)) }}
            @endif
          </div>
          <label class="avatar-upload-btn" id="avatarLabel" title="Change photo">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <input type="file" id="avatarInput" accept="image/*" style="display:none;">
          </label>
        </div>
        <div class="avatar-upload-info" style="flex:1;">
          <h4>Profile Photo</h4>
          <p>Click the camera icon to upload. JPG, PNG, GIF, WebP &middot; Max 2MB</p>
          <div class="avatar-upload-progress" id="avatarProgress">
            <div id="avatarProgressBar"></div>
          </div>
        </div>
      </div>

      <form id="profileForm">
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Full Name <span class="required-mark">*</span></label>
            <input name="name" class="form-control" value="{{ $user->name }}" required>
          </div>
          <div class="form-group">
            <label class="form-label">Email <span class="required-mark">*</span></label>
            <input name="email" type="email" class="form-control" value="{{ $user->email }}" required>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Phone Number</label>
            <input name="phone" class="form-control" value="{{ $user->phone ?? '' }}" placeholder="+255 7xx xxx xxx">
          </div>
          <div class="form-group">
            <label class="form-label">Role</label>
            <input class="form-control" value="{{ ucfirst($user->role) }}" disabled style="background:#f8fafc;color:#94a3b8;">
          </div>
        </div>

        <div class="section-divider">
          <div class="sec-title">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            Change Password
          </div>
          <div class="form-group">
            <label class="form-label">Current Password</label>
            <input name="current_password" type="password" class="form-control" placeholder="Enter current password">
            <div class="field-hint">Leave blank if you don't want to change your password</div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">New Password</label>
              <input name="new_password" type="password" class="form-control" placeholder="Min 8 characters">
            </div>
            <div class="form-group">
              <label class="form-label">Confirm New Password</label>
              <input name="new_password_confirmation" type="password" class="form-control" placeholder="Re-enter new password">
            </div>
          </div>
        </div>

        <div style="display:flex;gap:0.75rem;justify-content:flex-end;">
          <button type="button" class="btn btn-secondary" onclick="location.reload()">Cancel</button>
          <button type="button" class="btn btn-primary" id="saveBtn" style="gap:0.5rem;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>

</div>
</div>
@endsection
@section('scripts')
<script>
const Toast = Swal.mixin({
  toast: true, position: 'top-end', showConfirmButton: false,
  timer: 3500, timerProgressBar: true,
  didOpen: (t) => { t.addEventListener('mouseenter', Swal.stopTimer); t.addEventListener('mouseleave', Swal.resumeTimer); }
});

// ── Profile Save ──
async function saveProfile() {
  clearFormErrors('profileForm');
  const data = Object.fromEntries(new FormData(document.getElementById('profileForm')));
  const btn = document.getElementById('saveBtn');
  btn.disabled = true;
  const originalHTML = btn.innerHTML;
  btn.innerHTML = `<svg class="animate-spin" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707"/></svg> Saving…`;
  try {
    await apiFetch('/dashboard/profile', {method:'PUT', body:JSON.stringify(data)});
    Toast.fire({ icon: 'success', title: 'Profile updated successfully!' });
  } catch(e) {
    if(e.errors) showFormErrors('profileForm', e.errors);
    else Toast.fire({ icon: 'error', title: e.message || 'Update failed' });
  } finally {
    btn.disabled = false;
    btn.innerHTML = originalHTML;
  }
}

document.getElementById('saveBtn').addEventListener('click', saveProfile);

// ── Avatar Upload ──
const avatarInput = document.getElementById('avatarInput');
const avatarPreview = document.getElementById('avatarPreview');
const avatarProgress = document.getElementById('avatarProgress');
const avatarProgressBar = document.getElementById('avatarProgressBar');

avatarInput.addEventListener('change', async function(e) {
  const file = e.target.files[0];
  if (!file) return;

  // Preview
  const reader = new FileReader();
  reader.onload = (ev) => {
    avatarPreview.innerHTML = `<img src="${ev.target.result}" alt="Avatar">`;
  };
  reader.readAsDataURL(file);

  // Upload via AJAX
  const formData = new FormData();
  formData.append('avatar', file);
  avatarProgress.style.display = 'block';

  const xhr = new XMLHttpRequest();
  xhr.open('POST', '/dashboard/profile/avatar');
  xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}');
  xhr.setRequestHeader('Accept', 'application/json');

  xhr.upload.onprogress = (ev) => {
    if (ev.lengthComputable) {
      const pct = Math.round((ev.loaded / ev.total) * 100);
      avatarProgressBar.style.width = pct + '%';
    }
  };

  xhr.onload = function() {
    avatarProgress.style.display = 'none';
    avatarProgressBar.style.width = '0%';
    try {
      const res = JSON.parse(xhr.responseText);
      if (res.success) {
        // Update preview with server URL
        avatarPreview.innerHTML = `<img src="${res.avatar_url}?t=${Date.now()}" alt="Avatar">`;
        // Update header avatar
        const hdrAvatar = document.querySelector('.user-avatar img');
        if (hdrAvatar) {
          hdrAvatar.src = res.avatar_url + '?t=' + Date.now();
        } else {
          const hdrDiv = document.querySelector('.user-avatar');
          if (hdrDiv) {
            hdrDiv.innerHTML = `<img src="${res.avatar_url}?t=${Date.now()}" style="width:100%;height:100%;border-radius:8px;object-fit:cover;">`;
          }
        }
        Toast.fire({ icon: 'success', title: res.message || 'Avatar updated!' });
      } else {
        Toast.fire({ icon: 'error', title: res.message || 'Upload failed' });
      }
    } catch(err) {
      Toast.fire({ icon: 'error', title: 'Upload failed' });
    }
  };

  xhr.onerror = () => {
    avatarProgress.style.display = 'none';
    avatarProgressBar.style.width = '0%';
    Toast.fire({ icon: 'error', title: 'Upload failed' });
  };

  xhr.send(formData);
});
</script>
@endsection
