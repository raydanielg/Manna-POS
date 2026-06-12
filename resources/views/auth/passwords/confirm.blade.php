@extends('layouts.auth')

@section('form-header')
    <h2 class="auth-form-title">Confirm password</h2>
    <p class="auth-form-subtitle">Please confirm your password before continuing.</p>
@endsection

@section('form-content')
    <form method="POST" action="{{ route('password.confirm') }}" id="confirmPasswordForm">
        @csrf

        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <div class="input-wrapper">
                <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="••••••••">
            </div>
            @error('password')
                <div class="invalid-feedback">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary btn-block">
            <span class="btn-text">Confirm password</span>
        </button>

        @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="btn-link" style="display: block; text-align: center; margin-top: 1rem;">
                {{ __('Forgot Your Password?') }}
            </a>
        @endif
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const confirmPasswordForm = document.getElementById('confirmPasswordForm');
            confirmPasswordForm.addEventListener('submit', function(e) {
                const btn = this.querySelector('.btn-primary');
                const btnText = btn.querySelector('.btn-text');
                btn.classList.add('loading');
                btnText.textContent = 'Confirming...';
            });
        });
    </script>
@endsection
