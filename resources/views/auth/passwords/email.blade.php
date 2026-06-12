@extends('layouts.auth')

@section('form-header')
    <h2 class="auth-form-title">Forgot password?</h2>
    <p class="auth-form-subtitle">No worries, we'll send you reset instructions.</p>
@endsection

@section('form-content')
    <form method="POST" action="{{ route('password.email') }}" id="forgotPasswordForm">
        @csrf

        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        <div class="form-group">
            <label for="email" class="form-label">Email address</label>
            <div class="input-wrapper">
                <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="name@company.com">
            </div>
            @error('email')
                <div class="invalid-feedback">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary btn-block">
            <span class="btn-text">Send reset link</span>
        </button>
    </form>

    <div class="auth-footer">
        <span>Remember your password?</span>
        <a href="{{ route('login') }}" class="auth-footer-link">Sign in</a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const forgotPasswordForm = document.getElementById('forgotPasswordForm');
            forgotPasswordForm.addEventListener('submit', function(e) {
                const btn = this.querySelector('.btn-primary');
                const btnText = btn.querySelector('.btn-text');
                btn.classList.add('loading');
                btnText.textContent = 'Sending...';
            });
        });
    </script>
@endsection
