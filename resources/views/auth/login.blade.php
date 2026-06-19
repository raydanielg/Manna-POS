@extends('layouts.auth')

@section('form-header')
    <h2 class="auth-form-title">Welcome back</h2>
    <p class="auth-form-subtitle">Sign in to your MannaPOS account</p>
@endsection

@section('form-content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const Toast = Swal.mixin({
    toast: true, position: 'top-end',
    showConfirmButton: false, timer: 4500, timerProgressBar: true,
});
</script>

    <form method="POST" action="{{ route('login') }}" id="loginForm">
        @csrf

        <div class="form-group">
            <label for="email" class="form-label">Email address</label>
            <div class="input-wrapper">
                <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="name@company.com">
            </div>
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <div class="input-wrapper">
                <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <input id="password" type="password" class="form-control" name="password" required autocomplete="current-password" placeholder="••••••••">
            </div>
        </div>

        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;">
            <label class="form-check" style="margin-bottom:0;">
                <input type="checkbox" class="form-check-input" name="remember" {{ old('remember') ? 'checked' : '' }}>
                <span class="form-check-label">Remember me</span>
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" style="font-size:.82rem;color:#2563eb;font-weight:600;text-decoration:none;">Forgot password?</a>
            @endif
        </div>

        <button type="submit" class="btn btn-primary btn-block" id="loginBtn">
            <div class="spinner"></div>
            <span class="btn-text">Sign in</span>
        </button>
    </form>

    {{-- Social login options --}}
    <div style="margin-top:1.5rem;">
        <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1rem;">
            <div style="flex:1;height:1px;background:#e2e8f0;"></div>
            <span style="font-size:0.75rem;color:#94a3b8;font-weight:500;">Or continue with</span>
            <div style="flex:1;height:1px;background:#e2e8f0;"></div>
        </div>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:0.5rem;">
            <button type="button" class="social-btn" style="background:#1877F2;" title="Facebook">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                <span class="hidden sm:inline">Facebook</span>
            </button>
            <button type="button" class="social-btn" style="background:#0f1419;" title="X">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                <span class="hidden sm:inline">X</span>
            </button>
            <button type="button" class="social-btn" style="background:#24292f;" title="GitHub">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.84 1.236 1.84 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/></svg>
                <span class="hidden sm:inline">GitHub</span>
            </button>
            <button type="button" class="social-btn" style="background:#EA4335;" title="Google">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.48 10.92v3.28h4.92c-.2 1.28-.98 2.36-2.08 3.08l3.36 2.6c1.96-1.8 3.1-4.46 3.1-7.62 0-.74-.06-1.44-.18-2.12H12.5v.78h.02z" opacity=".4"/><path d="M5.16 14.42l-.72.56-2.52 1.96C3.64 19.96 7.48 22 12 22c3.24 0 5.96-1.08 7.94-2.9l-3.36-2.6c-.9.6-2.04.96-3.26.96-2.5 0-4.62-1.68-5.38-3.96H.5v2.54l4.66 3.38z"/><path d="M12 4.96c1.46 0 2.78.5 3.82 1.34l2.86-2.86C16.96 1.66 14.66.5 12 .5 7.48.5 3.64 2.54 1.42 5.52l4.66 3.62c.76-2.28 2.88-3.96 5.38-3.96h.54z"/></svg>
                <span class="hidden sm:inline">Google</span>
            </button>
            <button type="button" class="social-btn" style="background:#000;" title="Apple">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.8-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/></svg>
                <span class="hidden sm:inline">Apple</span>
            </button>
        </div>
    </div>

    <div class="auth-footer">
        <span>Don't have an account?</span>
        <a href="{{ route('register') }}" class="auth-footer-link">Create free account</a>
    </div>

    <script>
    // Show server errors as toasts
    @if($errors->any())
        @foreach($errors->all() as $err)
            Toast.fire({ icon: 'error', title: @json($err) });
        @endforeach
    @endif
    @if(session('status'))
        Toast.fire({ icon: 'success', title: @json(session('status')) });
    @endif

    document.getElementById('loginForm').addEventListener('submit', function() {
        const btn = document.getElementById('loginBtn');
        btn.classList.add('loading');
        btn.querySelector('.btn-text').textContent = 'Signing in…';
    });
    </script>
@endsection
