@extends('layouts.auth')

@section('form-header')
    <h2 class="auth-form-title">Welcome back</h2>
    <p class="auth-form-subtitle">Sign in to your MannaPOS account to continue.</p>
@endsection

@section('form-content')

{{-- Credentials error banner --}}
@if ($errors->has('email') && str_contains($errors->first('email'), 'credentials'))
<div class="alert-banner alert-banner-error" id="credentialAlert">
    <div class="alert-banner-icon">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
    </div>
    <div>
        <div class="alert-banner-title">Login Failed</div>
        <div class="alert-banner-desc">{{ $errors->first('email') }}</div>
    </div>
</div>
@endif

{{-- Session status (e.g. password reset) --}}
@if (session('status'))
<div class="alert-banner alert-banner-info">
    <div class="alert-banner-icon">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </div>
    <div>{{ session('status') }}</div>
</div>
@endif

<form method="POST" action="{{ route('login') }}" id="loginForm" novalidate>
    @csrf

    {{-- Email --}}
    <div class="field-group {{ $errors->has('email') ? 'has-error' : '' }}" id="emailGroup">
        <label for="email" class="field-label">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
            Email address
        </label>
        <div class="field-input-wrap">
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autocomplete="email"
                autofocus
                placeholder="name@company.com"
                class="field-input {{ $errors->has('email') && !str_contains($errors->first('email'),'credentials') ? 'field-input-error' : '' }}"
                oninput="clearFieldError('emailGroup')"
            >
            <span class="field-valid-icon" id="emailValid">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
            </span>
        </div>
        @error('email')
            @if (!str_contains($message, 'credentials'))
            <div class="field-error-msg">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                {{ $message }}
            </div>
            @endif
        @enderror
    </div>

    {{-- Password --}}
    <div class="field-group {{ $errors->has('password') ? 'has-error' : '' }}" id="passwordGroup">
        <label for="password" class="field-label">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
            Password
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="forgot-link" tabindex="-1">Forgot password?</a>
            @endif
        </label>
        <div class="field-input-wrap">
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="••••••••"
                class="field-input {{ $errors->has('password') ? 'field-input-error' : '' }}"
                oninput="clearFieldError('passwordGroup')"
            >
            <button type="button" class="toggle-pw" onclick="togglePassword()" tabindex="-1" aria-label="Show password">
                <svg id="eyeOff" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                <svg id="eyeOn" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" style="display:none;"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </button>
        </div>
        @error('password')
        <div class="field-error-msg">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
            {{ $message }}
        </div>
        @enderror
    </div>

    {{-- Remember me --}}
    <div class="remember-row">
        <label class="remember-check">
            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
            <span class="check-box">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
            </span>
            <span>Remember me for 30 days</span>
        </label>
    </div>

    {{-- Submit button --}}
    <button type="submit" class="sign-in-btn" id="submitBtn">
        <span class="btn-content" id="btnContent">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/></svg>
            Sign in
        </span>
        <span class="btn-loading" id="btnLoading" style="display:none;">
            <svg class="spin-icon" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-opacity="0.25" stroke-width="3"/><path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" opacity="0.75"/></svg>
            Signing in…
        </span>
        <div class="btn-ripple" id="btnRipple"></div>
    </button>
</form>

<div class="divider-line">
    <span>or</span>
</div>

<div class="auth-footer-row">
    <span>Don't have an account?</span>
    <a href="{{ route('register') }}">Create account</a>
</div>

<style>
/* ── Alert banners ─────────────────────────────── */
.alert-banner {
    display:flex; align-items:flex-start; gap:0.75rem;
    padding:0.9rem 1.1rem; border-radius:12px;
    font-size:0.85rem; margin-bottom:1.5rem;
    animation: slideInDown 0.35s cubic-bezier(0.34,1.56,0.64,1);
}
@keyframes slideInDown {
    from { opacity:0; transform:translateY(-12px); }
    to   { opacity:1; transform:translateY(0); }
}
.alert-banner-error {
    background:linear-gradient(135deg,#fff1f2,#ffe4e6);
    border:1.5px solid rgba(239,68,68,0.25);
    color:#be123c;
}
.alert-banner-info {
    background:linear-gradient(135deg,#eff6ff,#dbeafe);
    border:1.5px solid rgba(59,130,246,0.25);
    color:#1d4ed8;
}
.alert-banner-icon {
    width:32px; height:32px; border-radius:8px;
    background:rgba(255,255,255,0.6);
    display:flex; align-items:center; justify-content:center;
    flex-shrink:0;
}
.alert-banner-icon svg { width:17px; height:17px; }
.alert-banner-title  { font-weight:700; font-size:0.82rem; margin-bottom:0.15rem; }
.alert-banner-desc   { font-size:0.78rem; opacity:0.9; }

/* ── Field groups ──────────────────────────────── */
.field-group { margin-bottom:1.35rem; }
.field-label {
    display:flex; align-items:center; justify-content:space-between;
    font-size:0.82rem; font-weight:600; color:#374151;
    margin-bottom:0.5rem;
    gap:0.5rem;
}
.field-label svg { width:15px; height:15px; color:#94a3b8; margin-right:0.35rem; }
.forgot-link {
    font-size:0.75rem; color:#10b981; text-decoration:none;
    font-weight:600; margin-left:auto;
    transition:color 0.15s;
}
.forgot-link:hover { color:#059669; text-decoration:underline; }

.field-input-wrap { position:relative; }
.field-input {
    width:100%; padding:0.72rem 2.8rem 0.72rem 0.9rem;
    background:#f8fafc; border:1.5px solid #e2e8f0;
    border-radius:10px; font-size:0.88rem; color:#0f172a;
    font-family:'Inter',sans-serif; outline:none;
    transition:border-color 0.18s, box-shadow 0.18s, background 0.18s;
    box-sizing:border-box;
}
.field-input::placeholder { color:#94a3b8; }
.field-input:focus {
    border-color:#10b981; background:#fff;
    box-shadow:0 0 0 3px rgba(16,185,129,0.12);
}
.field-input-error {
    border-color:#ef4444 !important;
    background:#fff5f5 !important;
    box-shadow:0 0 0 3px rgba(239,68,68,0.1) !important;
}
.has-error .field-input { border-color:#ef4444; background:#fff5f5; }

/* Valid check icon */
.field-valid-icon {
    position:absolute; right:0.75rem; top:50%; transform:translateY(-50%);
    display:none; color:#10b981;
}
.field-valid-icon svg { width:16px; height:16px; }
.field-input:valid:not(:placeholder-shown) ~ .field-valid-icon { display:flex; }
.has-error .field-input:valid:not(:placeholder-shown) ~ .field-valid-icon { display:none; }

/* Password toggle */
.toggle-pw {
    position:absolute; right:0.75rem; top:50%; transform:translateY(-50%);
    background:none; border:none; cursor:pointer; padding:2px;
    color:#94a3b8; display:flex; align-items:center; transition:color 0.15s;
}
.toggle-pw:hover { color:#475569; }
.toggle-pw svg { width:18px; height:18px; }

/* Field error */
.field-error-msg {
    display:flex; align-items:center; gap:0.45rem;
    font-size:0.78rem; font-weight:600; color:#dc2626;
    background:linear-gradient(135deg,#fef2f2,#fee2e2);
    border:1px solid rgba(239,68,68,0.2); border-left:3px solid #ef4444;
    border-radius:8px; padding:0.55rem 0.85rem; margin-top:0.5rem;
    animation: slideInDown 0.3s ease;
}
.field-error-msg svg { width:14px; height:14px; flex-shrink:0; }

/* ── Remember row ──────────────────────────────── */
.remember-row { margin-bottom:1.75rem; }
.remember-check {
    display:flex; align-items:center; gap:0.6rem;
    cursor:pointer; user-select:none; width:fit-content;
}
.remember-check input[type=checkbox] { display:none; }
.check-box {
    width:18px; height:18px; border-radius:5px;
    border:2px solid #d1d5db; background:#fff;
    display:flex; align-items:center; justify-content:center;
    flex-shrink:0; transition:all 0.18s;
}
.check-box svg { width:11px; height:11px; color:#fff; opacity:0; transition:opacity 0.15s; }
.remember-check input:checked + .check-box {
    background:#10b981; border-color:#10b981;
}
.remember-check input:checked + .check-box svg { opacity:1; }
.remember-check span:last-child { font-size:0.85rem; color:#4b5563; font-weight:500; }

/* ── Sign-in button ────────────────────────────── */
.sign-in-btn {
    width:100%; padding:0; border:none; border-radius:12px;
    font-size:0.95rem; font-weight:700; cursor:pointer;
    background:linear-gradient(135deg,#10b981 0%,#059669 100%);
    color:#fff; position:relative; overflow:hidden;
    box-shadow:0 4px 18px rgba(16,185,129,0.38);
    transition:box-shadow 0.2s, transform 0.15s;
    min-height:52px;
    font-family:'Inter',sans-serif;
}
.sign-in-btn:hover:not(:disabled) {
    box-shadow:0 8px 28px rgba(16,185,129,0.45);
    transform:translateY(-1px);
}
.sign-in-btn:active { transform:translateY(0); }
.sign-in-btn:disabled { opacity:0.7; cursor:not-allowed; }
.btn-content, .btn-loading {
    display:flex; align-items:center; justify-content:center;
    gap:0.55rem; padding:0.85rem 1.5rem;
}
.btn-content svg { width:18px; height:18px; }
.spin-icon { width:18px; height:18px; animation:spin 0.7s linear infinite; }
@keyframes spin { to { transform:rotate(360deg); } }

/* Ripple */
.btn-ripple {
    position:absolute; inset:0; pointer-events:none;
    background:radial-gradient(circle,rgba(255,255,255,0.3) 0%,transparent 60%);
    opacity:0; transform:scale(0);
    transition:transform 0.5s ease, opacity 0.5s ease;
}
.sign-in-btn:active .btn-ripple { opacity:1; transform:scale(2.5); }

/* ── Divider & footer ──────────────────────────── */
.divider-line {
    display:flex; align-items:center; gap:0.85rem;
    margin:1.5rem 0 1.25rem; color:#cbd5e1; font-size:0.78rem;
}
.divider-line::before, .divider-line::after { content:''; flex:1; height:1px; background:#e9edf5; }

.auth-footer-row {
    text-align:center; font-size:0.88rem;
    color:#64748b; font-weight:500;
}
.auth-footer-row a {
    color:#10b981; font-weight:700; text-decoration:none;
    margin-left:0.35rem; transition:color 0.15s;
}
.auth-footer-row a:hover { color:#059669; text-decoration:underline; }

/* ── Shake animation on error ──────────────────── */
@keyframes shake {
    0%,100% { transform:translateX(0); }
    20%,60%  { transform:translateX(-6px); }
    40%,80%  { transform:translateX(6px); }
}
.shake { animation:shake 0.45s ease; }
</style>

<script>
// ── Password toggle ────────────────────────────
function togglePassword() {
    const pw  = document.getElementById('password');
    const off = document.getElementById('eyeOff');
    const on  = document.getElementById('eyeOn');
    if (pw.type === 'password') {
        pw.type = 'text'; off.style.display='none'; on.style.display='';
    } else {
        pw.type = 'password'; on.style.display='none'; off.style.display='';
    }
}

// ── Clear field error on input ─────────────────
function clearFieldError(groupId) {
    const g = document.getElementById(groupId);
    if (g) g.classList.remove('has-error');
    const errMsg = g && g.querySelector('.field-error-msg');
    if (errMsg) errMsg.remove();
    const input = g && g.querySelector('.field-input');
    if (input) input.classList.remove('field-input-error');
}

// ── Form submit ────────────────────────────────
document.getElementById('loginForm').addEventListener('submit', function(e) {
    const btn    = document.getElementById('submitBtn');
    const cont   = document.getElementById('btnContent');
    const loader = document.getElementById('btnLoading');
    let hasError = false;

    // Clear previous
    document.querySelectorAll('.field-group').forEach(g => g.classList.remove('has-error'));

    // Client-side validation
    const email    = document.getElementById('email');
    const password = document.getElementById('password');

    if (!email.value.trim()) {
        markError('emailGroup', 'Email address is required');
        hasError = true;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
        markError('emailGroup', 'Please enter a valid email address');
        hasError = true;
    }
    if (!password.value) {
        markError('passwordGroup', 'Password is required');
        hasError = true;
    }

    if (hasError) { e.preventDefault(); return; }

    // Show loading state
    btn.disabled = true;
    cont.style.display   = 'none';
    loader.style.display = 'flex';
});

function markError(groupId, msg) {
    const g = document.getElementById(groupId);
    if (!g) return;
    g.classList.add('has-error');
    g.classList.add('shake');
    setTimeout(() => g.classList.remove('shake'), 500);

    const inp = g.querySelector('.field-input');
    if (inp) inp.classList.add('field-input-error');

    // Remove existing error
    const existing = g.querySelector('.field-error-msg');
    if (existing) existing.remove();

    const div = document.createElement('div');
    div.className = 'field-error-msg';
    div.innerHTML = `<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>${msg}`;
    g.querySelector('.field-input-wrap').insertAdjacentElement('afterend', div);
}

// ── Shake errors that already exist (from server) ───
@if($errors->any())
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.has-error').forEach(g => {
        g.classList.add('shake');
        setTimeout(() => g.classList.remove('shake'), 500);
    });
});
@endif
</script>
@endsection
