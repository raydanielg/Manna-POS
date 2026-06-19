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
