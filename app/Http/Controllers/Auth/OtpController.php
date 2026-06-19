<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\NextSmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OtpController extends Controller
{
    protected NextSmsService $sms;

    public function __construct(NextSmsService $sms)
    {
        $this->middleware('auth');
        $this->sms = $sms;
    }

    public function show()
    {
        $user = auth()->user();

        if ($user->email_verified_at) {
            return redirect('/dashboard');
        }

        return view('auth.verify-otp', compact('user'));
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $user = auth()->user();

        if ($user->email_verified_at) {
            return response()->json(['success' => true, 'message' => 'Already verified.']);
        }

        if ($user->otp_expires_at && now()->gt($user->otp_expires_at)) {
            return response()->json(['success' => false, 'message' => 'OTP has expired. Please request a new one.'], 422);
        }

        if ($user->otp_code !== $request->otp) {
            return response()->json(['success' => false, 'message' => 'Invalid OTP code. Please try again.'], 422);
        }

        $user->update([
            'email_verified_at' => now(),
            'status' => 'active',
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);

        return response()->json(['success' => true, 'message' => 'Your account has been verified successfully!']);
    }

    public function resend(Request $request)
    {
        $user = auth()->user();

        if ($user->email_verified_at) {
            return response()->json(['success' => false, 'message' => 'Account already verified.'], 422);
        }

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->update([
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(30),
        ]);

        if ($user->phone) {
            $result = $this->sms->sendOtp($user->phone, $otp);
            if ($result['success']) {
                return response()->json(['success' => true, 'message' => 'New OTP has been sent to your phone.']);
            }
            return response()->json(['success' => false, 'message' => 'SMS failed: ' . $result['message']], 500);
        }

        return response()->json(['success' => false, 'message' => 'No phone number on file.'], 500);
    }

    public function activateByToken($token)
    {
        $user = User::where('activation_token', $token)
                    ->where(function ($q) {
                        $q->whereNull('activation_token_expires_at')
                          ->orWhere('activation_token_expires_at', '>', now());
                    })
                    ->first();

        if (!$user) {
            return redirect('/login')->with('status', 'Invalid or expired activation link.');
        }

        $user->update([
            'email_verified_at' => now(),
            'status' => 'active',
            'activation_token' => null,
            'activation_token_expires_at' => null,
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);

        auth()->login($user);

        return redirect('/dashboard')->with('status', 'Your account has been activated successfully!');
    }
}
