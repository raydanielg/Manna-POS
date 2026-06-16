@extends('admin.layouts.app')
@section('page_title', 'Add New User')
@section('content')
<div class="page-card" style="max-width:720px;margin:0 auto;">
    <div class="card-header">
        <div class="card-title">Add New User</div>
        <a href="{{ url('/admin/users') }}" class="btn btn-secondary btn-sm">Back to Users</a>
    </div>
    <div style="padding:1.5rem;">
        <form id="createUserForm">
            <div class="form-row">
                <div class="form-group">
                    <label>Name *</label>
                    <input type="text" class="form-control" id="name" required>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" class="form-control" id="email" required>
                    <div class="invalid-feedback"></div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Password *</label>
                    <input type="password" class="form-control" id="password" required>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="form-group">
                    <label>Confirm Password *</label>
                    <input type="password" class="form-control" id="password_confirmation" required>
                    <div class="invalid-feedback"></div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" class="form-control" id="phone">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <select class="form-control" id="role">
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" id="status">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div style="display:flex;gap:0.75rem;margin-top:1rem;">
                <button type="submit" class="btn btn-primary" id="submitBtn">Create User</button>
                <button type="reset" class="btn btn-secondary">Reset</button>
            </div>
        </form>
    </div>
</div>
@endsection
@section('scripts')
const API = '/api/admin/users';

document.getElementById('createUserForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    document.getElementById('submitBtn').disabled = true;
    const body = {
        name: document.getElementById('name').value,
        email: document.getElementById('email').value,
        password: document.getElementById('password').value,
        password_confirmation: document.getElementById('password_confirmation').value,
        phone: document.getElementById('phone').value,
        role: document.getElementById('role').value,
        status: document.getElementById('status').value,
    };
    try {
        await apiFetch(API, { method: 'POST', body });
        Swal.fire({ icon: 'success', title: 'User Created!', text: 'User has been created successfully.', timer: 2000, showConfirmButton: false });
        document.getElementById('createUserForm').reset();
    } catch (e) {
        if (e.data && e.data.errors) {
            for (const [field, msgs] of Object.entries(e.data.errors)) {
                const el = document.getElementById(field);
                if (el) { el.classList.add('is-invalid'); el.nextElementSibling.textContent = msgs[0]; }
            }
        }
        Swal.fire({ icon: 'error', title: 'Error', text: e.data?.message || 'Something went wrong' });
    } finally {
        document.getElementById('submitBtn').disabled = false;
    }
});
@endsection