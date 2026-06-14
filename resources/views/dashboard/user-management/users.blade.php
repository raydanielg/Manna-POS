@extends('layouts.dashboard')
@section('page_title','User Management')
@section('content')
<div class="dash-content">

{{-- ── Stats Row ─────────────────────────────────────────── --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:1rem;margin-bottom:1.5rem;">

  <div class="page-card" style="padding:1rem 1.25rem;display:flex;align-items:center;gap:0.85rem;">
    <div style="width:42px;height:42px;border-radius:11px;background:#6366f118;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
      <svg width="20" height="20" fill="none" stroke="#6366f1" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
    </div>
    <div><div id="st-total" style="font-size:1.5rem;font-weight:800;color:#0f172a;line-height:1;">—</div><div style="font-size:.7rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;margin-top:2px;">Total Users</div></div>
  </div>

  <div class="page-card" style="padding:1rem 1.25rem;display:flex;align-items:center;gap:0.85rem;">
    <div style="width:42px;height:42px;border-radius:11px;background:#10b98118;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
      <svg width="20" height="20" fill="none" stroke="#10b981" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </div>
    <div><div id="st-active" style="font-size:1.5rem;font-weight:800;color:#0f172a;line-height:1;">—</div><div style="font-size:.7rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;margin-top:2px;">Active</div></div>
  </div>

  <div class="page-card" style="padding:1rem 1.25rem;display:flex;align-items:center;gap:0.85rem;">
    <div style="width:42px;height:42px;border-radius:11px;background:#0ea5e918;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
      <svg width="20" height="20" fill="none" stroke="#0ea5e9" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg>
    </div>
    <div><div id="st-cashier" style="font-size:1.5rem;font-weight:800;color:#0f172a;line-height:1;">—</div><div style="font-size:.7rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;margin-top:2px;">Cashiers</div></div>
  </div>

  <div class="page-card" style="padding:1rem 1.25rem;display:flex;align-items:center;gap:0.85rem;">
    <div style="width:42px;height:42px;border-radius:11px;background:#f59e0b18;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
      <svg width="20" height="20" fill="none" stroke="#f59e0b" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
    </div>
    <div><div id="st-manager" style="font-size:1.5rem;font-weight:800;color:#0f172a;line-height:1;">—</div><div style="font-size:.7rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;margin-top:2px;">Managers</div></div>
  </div>

  <div class="page-card" style="padding:1rem 1.25rem;display:flex;align-items:center;gap:0.85rem;">
    <div style="width:42px;height:42px;border-radius:11px;background:#ef444418;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
      <svg width="20" height="20" fill="none" stroke="#ef4444" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.955 11.955 0 003 10c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
    </div>
    <div><div id="st-admin" style="font-size:1.5rem;font-weight:800;color:#0f172a;line-height:1;">—</div><div style="font-size:.7rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;margin-top:2px;">Admins</div></div>
  </div>

</div>

{{-- ── Main Card ─────────────────────────────────────────── --}}
<div class="page-card">
  <div class="card-header" style="flex-wrap:wrap;gap:.75rem;">
    <div class="card-title">System Users</div>
    <div class="filters-row" style="flex-wrap:wrap;gap:.5rem;">
      <div class="search-wrap">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
        <input type="text" id="searchInput" placeholder="Search name or email…" oninput="debouncedLoad()">
      </div>
      <select id="filterRole" class="form-control" style="width:auto;min-width:130px;" onchange="loadList()">
        <option value="">All Roles</option>
        <option value="admin">Admin</option>
        <option value="manager">Manager</option>
        <option value="cashier">Cashier</option>
        <option value="user">User</option>
      </select>
      <select id="filterLocation" class="form-control" style="width:auto;min-width:150px;" onchange="loadList()">
        <option value="">All Locations</option>
      </select>
      <select id="filterStatus" class="form-control" style="width:auto;min-width:120px;" onchange="loadList()">
        <option value="">All Status</option>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
      </select>
      <button class="btn btn-success" onclick="openAddModal()">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Add User
      </button>
    </div>
  </div>

  <div style="overflow-x:auto;">
    <table class="tbl">
      <thead>
        <tr><th>#</th><th>User</th><th>Role</th><th>Location</th><th>Status</th><th>Joined</th><th>Actions</th></tr>
      </thead>
      <tbody id="tableBody">
        <tr><td colspan="7" class="tbl-empty">Loading…</td></tr>
      </tbody>
    </table>
  </div>
</div>
</div>

{{-- ── Add/Edit Modal ────────────────────────────────────── --}}
<div class="modal-overlay" id="modal">
  <div class="modal modal-lg" style="max-width:640px;">
    <div class="modal-header">
      <div class="modal-title" id="modal-title">Add User</div>
      <button class="modal-close" onclick="closeModal('modal')">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <div class="modal-body" style="max-height:75vh;overflow-y:auto;">

      <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.5rem;padding:1rem;background:#f8fafc;border-radius:12px;border:1px solid #e2e8f0;">
        <div id="avatarPreview" style="width:56px;height:56px;border-radius:14px;background:linear-gradient(135deg,#6366f1,#8b5cf6);display:flex;align-items:center;justify-content:center;font-size:1.4rem;font-weight:800;color:white;flex-shrink:0;letter-spacing:-1px;">?</div>
        <div>
          <div style="font-size:.8rem;font-weight:600;color:#475569;">User Avatar</div>
          <div style="font-size:.75rem;color:#94a3b8;margin-top:2px;">Auto-generated from name initials</div>
        </div>
      </div>

      <form id="itemForm" onsubmit="return false;">
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Full Name *</label>
            <input name="name" id="inp-name" class="form-control" required placeholder="John Doe" oninput="updateAvatar()">
            <div class="invalid-feedback"></div>
          </div>
          <div class="form-group">
            <label class="form-label">Email Address *</label>
            <input name="email" type="email" class="form-control" required placeholder="john@company.com">
            <div class="invalid-feedback"></div>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Password <span id="pwHint" style="font-weight:400;color:#94a3b8;font-size:.75rem;">(required for new user)</span></label>
            <div style="position:relative;">
              <input name="password" id="inp-password" type="password" class="form-control" placeholder="Min 8 characters" style="padding-right:2.5rem;">
              <button type="button" onclick="togglePw()" style="position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#94a3b8;padding:0;display:flex;align-items:center;" tabindex="-1">
                <svg id="pwEyeOff" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                <svg id="pwEyeOn" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" style="display:none;"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
              </button>
            </div>
            <div class="invalid-feedback"></div>
          </div>
          <div class="form-group">
            <label class="form-label">Phone</label>
            <input name="phone" class="form-control" placeholder="+255 7XX XXX XXX">
            <div class="invalid-feedback"></div>
          </div>
        </div>

        {{-- Role Picker --}}
        <div class="form-group">
          <label class="form-label">Select Role *</label>
          <input type="hidden" name="role" id="selectedRole" value="user">
          <div id="rolePicker" style="display:grid;grid-template-columns:repeat(2,1fr);gap:.6rem;margin-top:.4rem;">

            <div class="role-card" data-role="admin" onclick="selectRole('admin')"
              style="border:2px solid #e2e8f0;border-radius:10px;padding:.75rem 1rem;cursor:pointer;transition:all .15s;display:flex;align-items:center;gap:.65rem;background:#fff;">
              <div style="width:36px;height:36px;border-radius:9px;background:#ef444415;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="18" height="18" fill="none" stroke="#ef4444" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.955 11.955 0 003 10c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
              </div>
              <div><div style="font-size:.83rem;font-weight:700;color:#1e293b;">Admin</div><div style="font-size:.72rem;color:#94a3b8;">Full system access</div></div>
            </div>

            <div class="role-card" data-role="manager" onclick="selectRole('manager')"
              style="border:2px solid #e2e8f0;border-radius:10px;padding:.75rem 1rem;cursor:pointer;transition:all .15s;display:flex;align-items:center;gap:.65rem;background:#fff;">
              <div style="width:36px;height:36px;border-radius:9px;background:#f59e0b15;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="18" height="18" fill="none" stroke="#f59e0b" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
              </div>
              <div><div style="font-size:.83rem;font-weight:700;color:#1e293b;">Manager</div><div style="font-size:.72rem;color:#94a3b8;">Operations management</div></div>
            </div>

            <div class="role-card" data-role="cashier" onclick="selectRole('cashier')"
              style="border:2px solid #e2e8f0;border-radius:10px;padding:.75rem 1rem;cursor:pointer;transition:all .15s;display:flex;align-items:center;gap:.65rem;background:#fff;">
              <div style="width:36px;height:36px;border-radius:9px;background:#0ea5e915;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="18" height="18" fill="none" stroke="#0ea5e9" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg>
              </div>
              <div><div style="font-size:.83rem;font-weight:700;color:#1e293b;">Cashier</div><div style="font-size:.72rem;color:#94a3b8;">POS & sales only</div></div>
            </div>

            <div class="role-card" data-role="user" onclick="selectRole('user')"
              style="border:2px solid #e2e8f0;border-radius:10px;padding:.75rem 1rem;cursor:pointer;transition:all .15s;display:flex;align-items:center;gap:.65rem;background:#fff;">
              <div style="width:36px;height:36px;border-radius:9px;background:#6366f115;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="18" height="18" fill="none" stroke="#6366f1" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
              </div>
              <div><div style="font-size:.83rem;font-weight:700;color:#1e293b;">User</div><div style="font-size:.72rem;color:#94a3b8;">Basic dashboard access</div></div>
            </div>

          </div>
          <div class="invalid-feedback"></div>
        </div>

        <div class="form-group" id="locationWrap" style="display:none;">
          <label class="form-label">Assign to Location / Store</label>
          <select name="location_id" id="inp-location" class="form-control">
            <option value="">— No specific location —</option>
          </select>
          <div style="font-size:.75rem;color:#94a3b8;margin-top:.3rem;">The store/branch this staff member works at.</div>
          <div class="invalid-feedback"></div>
        </div>

        <div class="form-row">
          <div class="form-group" style="flex:1;">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control" placeholder="Optional notes…" rows="2" style="resize:none;"></textarea>
          </div>
          <div class="form-group" style="flex:0 0 auto;min-width:130px;">
            <label class="form-label">Account Status</label>
            <label style="display:flex;align-items:center;gap:.6rem;cursor:pointer;margin-top:.5rem;">
              <div class="toggle-wrap" onclick="toggleStatus(this)">
                <input type="hidden" name="is_active" id="inp-is_active" value="1">
                <div class="toggle-track active-track"><div class="toggle-thumb"></div></div>
              </div>
              <span id="statusLabel" style="font-size:.82rem;font-weight:600;color:#10b981;">Active</span>
            </label>
          </div>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal')">Cancel</button>
      <button class="btn btn-primary" id="saveBtn" onclick="saveItem()">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        Save User
      </button>
    </div>
  </div>
</div>

{{-- ── Permissions Preview Modal ─────────────────────────── --}}
<div class="modal-overlay" id="permsModal">
  <div class="modal" style="max-width:520px;">
    <div class="modal-header">
      <div class="modal-title" id="permsTitle">User Permissions</div>
      <button class="modal-close" onclick="closeModal('permsModal')"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <div class="modal-body" id="permsBody" style="max-height:65vh;overflow-y:auto;"></div>
    <div class="modal-footer"><button class="btn btn-secondary" onclick="closeModal('permsModal')">Close</button></div>
  </div>
</div>

@endsection
@section('scripts')
<style>
.role-card.selected { border-color: var(--rc) !important; background: var(--rc-bg) !important; }
.toggle-wrap { cursor:pointer; }
.toggle-track { width:42px;height:22px;border-radius:99px;background:#e2e8f0;position:relative;transition:background .2s;display:flex;align-items:center;padding:2px; }
.toggle-track.active-track { background:#10b981; }
.toggle-thumb { width:18px;height:18px;border-radius:50%;background:#fff;box-shadow:0 1px 4px rgba(0,0,0,.2);transition:transform .2s; }
.toggle-track.active-track .toggle-thumb { transform:translateX(20px); }
.perm-group { margin-bottom:1rem; }
.perm-group-title { font-size:.7rem;font-weight:800;letter-spacing:.1em;text-transform:uppercase;color:#94a3b8;margin-bottom:.5rem;display:flex;align-items:center;gap:.4rem; }
.perm-chip { display:inline-flex;align-items:center;gap:.3rem;background:#f1f5f9;border:1px solid #e2e8f0;border-radius:6px;padding:.2rem .6rem;font-size:.72rem;font-weight:600;color:#475569;margin:2px; }
.perm-chip.has { background:#ecfdf5;border-color:#6ee7b7;color:#047857; }
</style>
<script>
const API_USERS = '/api/dashboard/users';
const API_LOCS  = '/api/dashboard/locations';
const API_ROLES = '/api/dashboard/roles';
let editId = null, locations = [], debounceTimer;

const roleColors = {
  admin:   { bg:'#fef2f2', border:'#ef4444', text:'#dc2626' },
  manager: { bg:'#fffbeb', border:'#f59e0b', text:'#d97706' },
  cashier: { bg:'#eff6ff', border:'#3b82f6', text:'#2563eb' },
  user:    { bg:'#f5f3ff', border:'#8b5cf6', text:'#7c3aed' },
};

const roleIcons = {
  admin:   `<svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.955 11.955 0 003 10c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>`,
  manager: `<svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>`,
  cashier: `<svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg>`,
  user:    `<svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>`,
};

const roleBadge = r => {
  const c = roleColors[r] || roleColors.user;
  const ico = roleIcons[r] || roleIcons.user;
  const label = (r||'user').charAt(0).toUpperCase()+(r||'user').slice(1);
  return `<span style="display:inline-flex;align-items:center;gap:.3rem;padding:.2rem .65rem;border-radius:99px;font-size:.72rem;font-weight:700;background:${c.bg};color:${c.text};border:1px solid ${c.border}40;">${ico} ${label}</span>`;
};

function initials(name) { return (name||'?').split(' ').slice(0,2).map(w=>w[0]).join('').toUpperCase()||'?'; }
const avatarColors = ['#6366f1','#8b5cf6','#ec4899','#ef4444','#f59e0b','#10b981','#0ea5e9','#14b8a6'];
function avatarColor(name) { let h=0; for(let c of (name||'?')) h=(h*31+c.charCodeAt(0))&0xfffffff; return avatarColors[h%avatarColors.length]; }
function avatarHtml(name, size=36) {
  const bg=avatarColor(name), ini=initials(name), r=Math.round(size*.3), fs=Math.round(size*.35);
  return `<div style="width:${size}px;height:${size}px;border-radius:${r}px;background:${bg};display:flex;align-items:center;justify-content:center;font-size:${fs}px;font-weight:800;color:#fff;letter-spacing:-1px;flex-shrink:0;">${ini}</div>`;
}
function updateAvatar() {
  const name=document.getElementById('inp-name').value;
  const el=document.getElementById('avatarPreview');
  el.style.background=avatarColor(name)||'#6366f1';
  el.textContent=initials(name)||'?';
}
function debouncedLoad() { clearTimeout(debounceTimer); debounceTimer=setTimeout(loadList,280); }

async function loadStats() {
  try {
    const s=await apiFetch('/api/dashboard/users/stats');
    document.getElementById('st-total').textContent   = s.total;
    document.getElementById('st-active').textContent  = s.active;
    document.getElementById('st-cashier').textContent = s.cashier;
    document.getElementById('st-manager').textContent = s.manager;
    document.getElementById('st-admin').textContent   = s.admin;
  } catch(e) {}
}

async function loadLocations() {
  try {
    locations = await apiFetch(API_LOCS);
    const opt = locs => locs.map(l=>`<option value="${l.id}">${l.name}${l.city?' — '+l.city:''}</option>`).join('');
    document.getElementById('filterLocation').innerHTML='<option value="">All Locations</option>'+opt(locations);
    document.getElementById('inp-location').innerHTML='<option value="">— No specific location —</option>'+opt(locations);
  } catch(e) {}
}

async function loadList() {
  const s=document.getElementById('searchInput').value;
  const r=document.getElementById('filterRole').value;
  const l=document.getElementById('filterLocation').value;
  const st=document.getElementById('filterStatus').value;
  const tbody=document.getElementById('tableBody');
  tbody.innerHTML='<tr><td colspan="7" class="tbl-empty">Loading…</td></tr>';
  try {
    const params=new URLSearchParams({search:s,role:r,location_id:l,status:st});
    const items=await apiFetch(`${API_USERS}?${params}`);
    if(!items.length){tbody.innerHTML='<tr><td colspan="7" class="tbl-empty">No users found.</td></tr>';return;}
    tbody.innerHTML=items.map((u,i)=>{
      const loc=u.location
        ?`<span style="font-size:.75rem;color:#64748b;display:flex;align-items:center;gap:.3rem;"><svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>${u.location.name}</span>`
        :`<span style="font-size:.75rem;color:#cbd5e1;">—</span>`;
      const status=u.is_active
        ?`<span style="display:inline-flex;align-items:center;gap:.3rem;font-size:.72rem;font-weight:700;color:#10b981;"><span style="width:7px;height:7px;border-radius:50%;background:#10b981;display:inline-block;"></span> Active</span>`
        :`<span style="display:inline-flex;align-items:center;gap:.3rem;font-size:.72rem;font-weight:700;color:#94a3b8;"><span style="width:7px;height:7px;border-radius:50%;background:#cbd5e1;display:inline-block;"></span> Inactive</span>`;
      return `<tr>
        <td class="text-slate-400" style="font-size:.8rem;">${i+1}</td>
        <td>
          <div style="display:flex;align-items:center;gap:.75rem;">
            ${avatarHtml(u.name,38)}
            <div>
              <div style="font-weight:700;font-size:.875rem;color:#1e293b;">${u.name}</div>
              <div style="font-size:.75rem;color:#94a3b8;">${u.email}</div>
            </div>
          </div>
        </td>
        <td>${roleBadge(u.role)}</td>
        <td>${loc}</td>
        <td>${status}</td>
        <td style="font-size:.75rem;color:#94a3b8;">${u.created_at?new Date(u.created_at).toLocaleDateString('en-GB',{day:'numeric',month:'short',year:'numeric'}):'—'}</td>
        <td>
          <div style="display:flex;gap:.35rem;">
            <button class="btn btn-sm btn-icon" title="View Permissions" style="background:#f1f5f9;color:#6366f1;border:none;" onclick="viewPerms(${u.id},'${u.name.replace(/'/g,"\\'")}','${u.role}')">
              <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.955 11.955 0 003 10c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
            </button>
            <button class="btn btn-sm btn-edit btn-icon" title="Edit" onclick="editItem(${u.id})">
              <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </button>
            <button class="btn btn-sm btn-delete btn-icon" title="Delete" onclick="deleteItem(${u.id},'${u.name.replace(/'/g,"\\'")}')">
              <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
          </div>
        </td>
      </tr>`;
    }).join('');
  } catch(e) { tbody.innerHTML=`<tr><td colspan="7" class="tbl-empty" style="color:#ef4444;">Error loading users.</td></tr>`; }
}

function selectRole(role) {
  document.getElementById('selectedRole').value=role;
  const rc=roleColors[role]||roleColors.user;
  document.querySelectorAll('.role-card').forEach(c=>{
    const sel=c.dataset.role===role;
    c.style.borderColor=sel?rc.border:'#e2e8f0';
    c.style.background=sel?rc.bg:'#fff';
  });
  document.getElementById('locationWrap').style.display=['cashier','manager'].includes(role)?'':'none';
}

function togglePw() {
  const inp=document.getElementById('inp-password');
  inp.type=inp.type==='password'?'text':'password';
  document.getElementById('pwEyeOff').style.display=inp.type==='text'?'none':'';
  document.getElementById('pwEyeOn').style.display=inp.type==='text'?'':'none';
}

function toggleStatus(wrap) {
  const track=wrap.querySelector('.toggle-track');
  const inp=document.getElementById('inp-is_active');
  const label=document.getElementById('statusLabel');
  const isNowActive=!track.classList.contains('active-track');
  track.classList.toggle('active-track',isNowActive);
  inp.value=isNowActive?'1':'0';
  label.textContent=isNowActive?'Active':'Inactive';
  label.style.color=isNowActive?'#10b981':'#94a3b8';
}

function resetModal() {
  document.getElementById('itemForm').reset();
  document.getElementById('inp-is_active').value='1';
  document.querySelector('.toggle-track').classList.add('active-track');
  document.getElementById('statusLabel').textContent='Active';
  document.getElementById('statusLabel').style.color='#10b981';
  document.getElementById('avatarPreview').textContent='?';
  document.getElementById('avatarPreview').style.background='#6366f1';
  document.querySelectorAll('.invalid-feedback').forEach(el=>{el.style.display='none';el.textContent='';});
  selectRole('user');
}

function openAddModal() {
  editId=null;
  document.getElementById('modal-title').textContent='Add New User';
  document.getElementById('pwHint').textContent='(required for new user)';
  document.getElementById('inp-password').required=true;
  document.getElementById('saveBtn').innerHTML=`<svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Save User`;
  resetModal();
  openModal('modal');
}

async function editItem(id) {
  try {
    const u=await apiFetch(`${API_USERS}/${id}`);
    editId=id;
    document.getElementById('modal-title').textContent='Edit User';
    document.getElementById('pwHint').textContent='(leave blank to keep current)';
    document.getElementById('inp-password').required=false;
    document.getElementById('saveBtn').innerHTML=`<svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Update User`;
    resetModal();
    const f=document.getElementById('itemForm');
    f.elements['name'].value=u.name;
    f.elements['email'].value=u.email;
    f.elements['notes'].value=u.notes||'';
    selectRole(u.role||'user');
    if(u.location_id) document.getElementById('inp-location').value=u.location_id;
    const active=u.is_active!==false;
    document.getElementById('inp-is_active').value=active?'1':'0';
    const track=document.querySelector('.toggle-track');
    track.classList.toggle('active-track',active);
    document.getElementById('statusLabel').textContent=active?'Active':'Inactive';
    document.getElementById('statusLabel').style.color=active?'#10b981':'#94a3b8';
    document.getElementById('avatarPreview').textContent=initials(u.name);
    document.getElementById('avatarPreview').style.background=avatarColor(u.name);
    openModal('modal');
  } catch(e) { showToast('Failed to load user.','error'); }
}

async function saveItem() {
  const btn=document.getElementById('saveBtn');
  const f=document.getElementById('itemForm');
  document.querySelectorAll('.invalid-feedback').forEach(el=>{el.style.display='none';el.textContent='';});
  const data={
    name:f.elements['name'].value.trim(),
    email:f.elements['email'].value.trim(),
    password:f.elements['password'].value,
    role:document.getElementById('selectedRole').value,
    location_id:f.elements['location_id'].value||null,
    is_active:document.getElementById('inp-is_active').value==='1',
    notes:f.elements['notes'].value.trim(),
  };
  if(!data.name){markErr('name','Full name is required');return;}
  if(!data.email){markErr('email','Email is required');return;}
  if(!editId&&!data.password){markErr('password','Password is required');return;}
  if(data.password&&data.password.length<8){markErr('password','Password must be at least 8 characters');return;}
  if(!data.password) delete data.password;
  btn.disabled=true;
  btn.innerHTML='<svg class="spin" width="15" height="15" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-opacity=".25" stroke-width="3"/><path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg> Saving…';
  try {
    const res=editId
      ?await apiFetch(`${API_USERS}/${editId}`,{method:'PUT',body:JSON.stringify(data)})
      :await apiFetch(API_USERS,{method:'POST',body:JSON.stringify(data)});
    if(res.success){showToast(editId?'User updated.':'User created.','success');closeModal('modal');loadList();loadStats();}
    else showToast(res.message||'Failed.','error');
  } catch(e) {
    if(e.errors) Object.entries(e.errors).forEach(([k,v])=>markErr(k,v[0]));
    else showToast(e.message||'Failed.','error');
  } finally {
    btn.disabled=false;
    btn.innerHTML=`<svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> ${editId?'Update':'Save'} User`;
  }
}

async function deleteItem(id,name) {
  const ok=await showConfirm(`Delete user "${name}"?`,'This action cannot be undone.');
  if(!ok) return;
  try {
    const res=await apiFetch(`${API_USERS}/${id}`,{method:'DELETE'});
    if(res.success){showToast('User deleted.','success');loadList();loadStats();}
    else showToast(res.message||'Failed.','error');
  } catch(e) { showToast(e.message||'Failed.','error'); }
}

async function viewPerms(id,name,role) {
  document.getElementById('permsTitle').textContent=`Permissions — ${name}`;
  document.getElementById('permsBody').innerHTML='<div style="text-align:center;padding:2rem;color:#94a3b8;">Loading…</div>';
  openModal('permsModal');
  try {
    const [permGroups,roleList]=await Promise.all([
      apiFetch('/api/dashboard/roles/permissions'),
      apiFetch(`${API_ROLES}?search=${encodeURIComponent(role)}`),
    ]);
    const roleData=roleList.find(x=>x.name===role);
    const userPerms=role==='admin'?'all':(roleData?.permissions||[]);
    let html=`<div style="margin-bottom:1rem;padding:.75rem 1rem;background:#f8fafc;border-radius:10px;border:1px solid #e2e8f0;display:flex;align-items:center;gap:.75rem;">${avatarHtml(name,42)}<div><div style="font-weight:700;color:#1e293b;">${name}</div><div>${roleBadge(role)}</div></div></div>`;
    if(userPerms==='all'){
      html+=`<div style="padding:1rem;background:#ecfdf5;border:1px solid #6ee7b7;border-radius:10px;color:#047857;font-weight:600;font-size:.85rem;text-align:center;display:flex;align-items:center;justify-content:center;gap:.5rem;"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.955 11.955 0 003 10c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg> Administrator — Full access to all features</div>`;
    } else {
      Object.entries(permGroups).forEach(([group,perms])=>{
        const chips=perms.map(p=>`<span class="perm-chip ${userPerms.includes(p)?'has':''}">${userPerms.includes(p)?'✓':''} ${p.split('.')[1]}</span>`).join('');
        html+=`<div class="perm-group"><div class="perm-group-title">${group}</div><div>${chips}</div></div>`;
      });
    }
    document.getElementById('permsBody').innerHTML=html;
  } catch(e) { document.getElementById('permsBody').innerHTML='<div style="color:#ef4444;padding:1rem;">Failed to load permissions.</div>'; }
}

function markErr(field,msg) {
  const inp=document.querySelector(`[name="${field}"]`);
  if(!inp) return;
  inp.style.borderColor='#ef4444';
  const wrap=inp.closest('.form-group');
  const fb=wrap&&wrap.querySelector('.invalid-feedback');
  if(fb){fb.textContent=msg;fb.style.display='block';}
}

document.addEventListener('DOMContentLoaded',()=>{ loadStats(); loadLocations(); loadList(); });
</script>
@endsection
