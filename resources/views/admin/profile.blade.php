@extends('admin.layouts.app')
@section('page_title', 'Admin Profile')
@section('content')
<div class="page-card" style="max-width:720px;margin:0 auto;">
    <div class="card-header">
        <div class="card-title">Admin Profile</div>
    </div>
    <div style="padding:1.5rem;">
        <form id="profileForm">
            <div class="form-row">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-control" name="name" id="profile_name" value="{{ Auth::user()->name ?? '' }}">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" name="email" id="profile_email" value="{{ Auth::user()->email ?? '' }}">
                    <div class="invalid-feedback"></div>
                </div>
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="text" class="form-control" name="phone" id="profile_phone" value="{{ Auth::user()->phone ?? '' }}">
                <div class="invalid-feedback"></div>
            </div>
            <div style="border-top:1px solid #f1f5f9;padding-top:1.25rem;margin-top:0.5rem;">
                <div style="font-size:0.95rem;font-weight:700;color:#0f172a;margin-bottom:0.75rem;">Change Password</div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Current Password</label>
                        <input type="password" class="form-control" name="current_password" id="current_password">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" class="form-control" name="password" id="new_password">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" class="form-control" name="password_confirmation" id="new_password_confirmation">
                    <div class="invalid-feedback"></div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="margin-top:0.5rem;" id="saveProfileBtn">Save Changes</button>
        </form>
    </div>
</div>
@endsection
@section('scripts')
<script>
document.getElementById('profileForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    clearFormErrors('profileForm');
    const btn = document.getElementById('saveProfileBtn');
    btn.disabled = true;
    btn.textContent = 'Saving...';
    try {
        const data = {
            name: document.getElementById('profile_name').value,
            email: document.getElementById('profile_email').value,
            phone: document.getElementById('profile_phone').value,
        };
        const pw = document.getElementById('new_password').value;
        if (pw) {
            data.current_password = document.getElementById('current_password').value;
            data.password = pw;
            data.password_confirmation = document.getElementById('new_password_confirmation').value;
        }
        await apiFetch('/api/admin/profile', { method: 'PUT', body: JSON.stringify(data) });
        Swal.fire('Saved', 'Profile updated successfully', 'success');
        document.getElementById('current_password').value = '';
        document.getElementById('new_password').value = '';
        document.getElementById('new_password_confirmation').value = '';
    } catch (e) {
        if (e.errors) showFormErrors('profileForm', e.errors);
        else Swal.fire('Error', e.message || 'Save failed', 'error');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Save Changes';
    }
});
</script>
@endsection
