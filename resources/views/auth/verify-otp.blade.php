@extends('layouts.auth')

@section('form-header')
    <h2 class="auth-form-title">Verify Your Account</h2>
    <p class="auth-form-subtitle">Enter the 6-digit code sent to <strong>{{ $user->phone ? substr($user->phone,0,3) . '****' . substr($user->phone,-4) : 'your phone' }}</strong></p>
@endsection

@section('form-content')
<style>
    .otp-inputs {
        display: flex;
        gap: 0.75rem;
        justify-content: center;
        margin-bottom: 1.5rem;
    }
    .otp-inputs input {
        width: 48px;
        height: 56px;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        text-align: center;
        font-size: 1.5rem;
        font-weight: 700;
        color: #1d4ed8;
        background: #f9fafb;
        transition: all 0.2s ease;
        outline: none;
    }
    .otp-inputs input:focus {
        border-color: #1d4ed8;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(29,78,216,0.15);
    }
    .timer {
        text-align: center;
        color: #6b7280;
        font-size: 0.85rem;
        margin-bottom: 1rem;
    }
    .timer .countdown {
        color: #dc2626;
        font-weight: 600;
    }
    .resend-link {
        text-align: center;
        margin-top: 1rem;
    }
    .resend-link button {
        background: none;
        border: none;
        color: #2563eb;
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
        text-decoration: none;
    }
    .resend-link button:disabled {
        color: #9ca3af;
        cursor: not-allowed;
    }
    .activation-card {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        border: 1px solid #bfdbfe;
        border-radius: 12px;
        padding: 1rem 1.25rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .activation-card svg {
        width: 24px;
        height: 24px;
        color: #2563eb;
        flex-shrink: 0;
    }
    .activation-card p {
        margin: 0;
        font-size: 0.85rem;
        color: #1e40af;
        line-height: 1.5;
    }
    .activation-card a {
        color: #1d4ed8;
        font-weight: 600;
        text-decoration: none;
    }
</style>

<div class="activation-card">
    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
    <p>We sent a code to your phone via SMS. Make sure your phone has signal or <a href="{{ url('/activate/' . $user->activation_token) }}">click here to activate instantly</a>.</p>
</div>

<div class="otp-inputs" id="otpInputs">
    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]">
    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]">
    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]">
    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]">
    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]">
    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]">
</div>

<input type="hidden" id="otpValue" name="otp">

<div class="timer">Code expires in <span class="countdown" id="countdown">29:59</span></div>

<button type="button" class="btn btn-primary btn-block" id="verifyBtn">
    <div class="spinner"></div>
    <span class="btn-text">Verify Account</span>
</button>

<div class="resend-link">
    <button type="button" id="resendBtn" disabled>Resend Code <span id="resendTimer">(60s)</span></button>
</div>

<div class="auth-footer" style="margin-top:1.5rem;">
    <span>Not your email?</span>
    <a href="{{ route('logout') }}" class="auth-footer-link" onclick="event.preventDefault();document.getElementById('logout-form').submit();">Log out</a>
</div>
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const inputs = document.querySelectorAll('#otpInputs input');
const otpValue = document.getElementById('otpValue');
const verifyBtn = document.getElementById('verifyBtn');
const resendBtn = document.getElementById('resendBtn');
const resendTimer = document.getElementById('resendTimer');
const countdownEl = document.getElementById('countdown');

// Auto-focus and move between inputs
inputs.forEach((input, idx) => {
    input.addEventListener('input', (e) => {
        if (!/^\d*$/.test(e.target.value)) {
            e.target.value = '';
            return;
        }
        if (e.target.value && idx < inputs.length - 1) {
            inputs[idx + 1].focus();
        }
        updateOtp();
    });
    input.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && !e.target.value && idx > 0) {
            inputs[idx - 1].focus();
        }
    });
    input.addEventListener('paste', (e) => {
        e.preventDefault();
        const paste = e.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6);
        paste.split('').forEach((digit, i) => {
            if (inputs[i]) inputs[i].value = digit;
        });
        if (inputs[paste.length]) inputs[paste.length].focus();
        updateOtp();
    });
});

function updateOtp() {
    otpValue.value = Array.from(inputs).map(i => i.value).join('');
}

// Countdown timer
let seconds = 30 * 60;
function updateCountdown() {
    const m = Math.floor(seconds / 60).toString().padStart(2, '0');
    const s = (seconds % 60).toString().padStart(2, '0');
    countdownEl.textContent = m + ':' + s;
    if (seconds > 0) seconds--;
    else countdownEl.textContent = 'Expired';
}
setInterval(updateCountdown, 1000);

// Resend timer
let resendSeconds = 60;
function updateResendTimer() {
    if (resendSeconds > 0) {
        resendSeconds--;
        resendTimer.textContent = '(' + resendSeconds + 's)';
    } else {
        resendBtn.disabled = false;
        resendTimer.textContent = '';
    }
}
setInterval(updateResendTimer, 1000);

// Verify
verifyBtn.addEventListener('click', () => {
    const otp = otpValue.value;
    if (otp.length !== 6) {
        Swal.fire({ icon: 'warning', title: 'Enter all 6 digits', confirmButtonColor: '#1d4ed8' });
        return;
    }
    verifyBtn.classList.add('loading');
    verifyBtn.querySelector('.btn-text').textContent = 'Verifying...';

    fetch('{{ route("verify.otp.post") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ otp })
    })
    .then(r => r.json())
    .then(data => {
        verifyBtn.classList.remove('loading');
        verifyBtn.querySelector('.btn-text').textContent = 'Verify Account';
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Verified!',
                text: data.message,
                confirmButtonColor: '#1d4ed8',
                timer: 2000
            }).then(() => {
                window.location.href = '/dashboard';
            });
        } else {
            Swal.fire({ icon: 'error', title: 'Failed', text: data.message, confirmButtonColor: '#1d4ed8' });
        }
    })
    .catch(err => {
        verifyBtn.classList.remove('loading');
        verifyBtn.querySelector('.btn-text').textContent = 'Verify Account';
        Swal.fire({ icon: 'error', title: 'Error', text: 'Something went wrong. Please try again.', confirmButtonColor: '#1d4ed8' });
    });
});

let currentMethod = 'email';
const methodLabels = {
    email: '{{ $user->email }}',
    sms: '{{ $user->phone ? substr($user->phone,0,2) . "xxxx" . substr($user->phone,-4) : "No phone" }}'
};
const methodHints = {
    email: "We sent a code to your email. Can't find it? Check your spam folder or <a href='{{ url('/activate/' . $user->activation_token) }}'>click here to activate instantly</a>.",
    sms: "We sent a code to your phone via SMS. Make sure your phone has signal."
};

function pickMethod(el, method) {
    document.querySelectorAll('.method-chip').forEach(c => c.classList.remove('on'));
    el.classList.add('on');
    currentMethod = method;
    document.getElementById('targetLabel').textContent = methodLabels[method];
    document.getElementById('methodHint').innerHTML = methodHints[method];
}

// Resend
resendBtn.addEventListener('click', () => {
    resendBtn.disabled = true;
    resendSeconds = 60;
    resendTimer.textContent = '(60s)';

    fetch('{{ route("verify.otp.resend") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ method: currentMethod })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            Swal.fire({ icon: 'success', title: 'Sent!', text: data.message, timer: 2000, showConfirmButton: false });
            seconds = 30 * 60;
        } else {
            Swal.fire({ icon: 'error', title: 'Failed', text: data.message, confirmButtonColor: '#1d4ed8' });
        }
    });
});
</script>
@endsection
