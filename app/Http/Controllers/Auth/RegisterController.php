<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\WelcomeEmail;
use App\Mail\OtpVerificationEmail;
use App\Services\NextSmsService;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/verify-otp';

    protected NextSmsService $sms;

    public function __construct(NextSmsService $sms)
    {
        $this->middleware('guest');
        $this->sms = $sms;
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name'       => ['required', 'string', 'max:100'],
            'last_name'        => ['required', 'string', 'max:100'],
            'phone'            => ['required', 'string', 'max:30'],
            'email'            => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
            'business_name'    => ['required', 'string', 'max:191'],
            'business_type'    => ['nullable', 'string', 'max:100'],
            'business_country' => ['required', 'string', 'max:100'],
            'currency'         => ['required', 'string', 'max:10'],
        ]);
    }

    protected function create(array $data)
    {
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $token = Str::random(64);

        $user = User::create([
            'name'             => $data['first_name'] . ' ' . $data['last_name'],
            'email'            => $data['email'],
            'password'         => Hash::make($data['password']),
            'phone'            => $data['phone'],
            'role'             => 'user',
            'status'           => 'pending',
            'business_name'    => $data['business_name'],
            'business_type'    => $data['business_type'] ?? null,
            'business_country' => $data['business_country'],
            'business_city'    => $data['business_city'] ?? null,
            'currency'         => $data['currency'],
            'setup_completed'  => false,
            'otp_code'         => $otp,
            'otp_expires_at'   => now()->addMinutes(30),
            'activation_token' => $token,
            'activation_token_expires_at' => now()->addHours(24),
        ]);

        // Find or create a free trial plan
        $freePlan = SubscriptionPlan::where('price_monthly', 0)->orderBy('id')->first();
        if (!$freePlan) {
            $freePlan = SubscriptionPlan::create([
                'name'          => 'Free Trial',
                'slug'          => 'free-trial',
                'description'   => '14-day free trial with full access',
                'price_monthly' => 0,
                'price_yearly'  => 0,
                'currency'      => 'TZS',
                'max_users'     => 2,
                'max_products'  => 50,
                'max_locations' => 1,
                'features'      => ['POS Sales', 'Inventory', 'Basic Reports', 'Customers'],
                'is_active'     => true,
                'badge_color'   => 'green',
                'sort_order'    => 0,
            ]);
        }

        UserSubscription::create([
            'user_id'              => $user->id,
            'subscription_plan_id' => $freePlan->id,
            'billing_cycle'        => 'monthly',
            'amount_paid'          => 0,
            'currency'             => $user->currency ?? 'TZS',
            'status'               => 'trial',
            'starts_at'            => now(),
            'expires_at'           => now()->addDays(14),
            'notes'                => '14-day free trial on registration',
        ]);

        // Send OTP via SMS only (email OTP disabled)
        if ($user->phone) {
            $this->sms->sendWelcome($user->phone, explode(' ', $user->name)[0]);
            $this->sms->sendOtp($user->phone, $otp);
        }

        return $user;
    }
}
