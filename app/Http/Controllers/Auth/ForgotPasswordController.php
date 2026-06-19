<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\NextSmsService;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    protected NextSmsService $sms;

    public function __construct(NextSmsService $sms)
    {
        $this->sms = $sms;
    }

    /**
     * Send a reset link to the given user, and also SMS.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $broker = $this->broker();
        $user = $broker->getUser(['email' => $request->email]);

        if ($user) {
            $token = $broker->createToken($user);
            $resetUrl = url(route('password.reset', ['token' => $token, 'email' => $user->email], false));

            if ($user->phone) {
                $this->sms->sendPasswordReset($user->phone, $resetUrl);
            }
        }

        $response = $broker->sendResetLink(
            $request->only('email')
        );

        return $response == Password::RESET_LINK_SENT
            ? back()->with('status', __($response))
            : back()->withErrors(['email' => __($response)]);
    }
}
