<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NextSmsService
{
    protected string $url;
    protected string $username;
    protected string $password;
    protected string $from;

    public function __construct()
    {
        $this->url      = config('services.nextsms.url', 'https://messaging-service.co.tz/api/sms/v1/text/single');
        $this->username = config('services.nextsms.username') ?? '';
        $this->password = config('services.nextsms.password') ?? '';
        $this->from     = config('services.nextsms.from', 'UZAZICLINIC') ?? 'UZAZICLINIC';
    }

    /**
     * Send a single SMS.
     *
     * @param string $to   Phone number (e.g. 255712345678)
     * @param string $text Message text
     * @return array  ['success'=>bool, 'message'=>string]
     */
    public function send(string $to, string $text): array
    {
        if (empty($this->username) || empty($this->password)) {
            Log::warning('NextSMS credentials not configured.');
            return ['success' => false, 'message' => 'SMS gateway not configured.'];
        }

        // Normalize number: strip leading + and ensure 255 prefix
        $to = ltrim($to, '+');
        if (str_starts_with($to, '0')) {
            $to = '255' . ltrim($to, '0');
        }
        if (!str_starts_with($to, '255')) {
            $to = '255' . $to;
        }

        $auth = base64_encode($this->username . ':' . $this->password);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $auth,
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
            ])->post($this->url, [
                'from' => $this->from,
                'to'   => $to,
                'text' => $text,
            ]);

            if ($response->successful()) {
                Log::info('NextSMS sent successfully', ['to' => $to, 'response' => $response->json()]);
                return ['success' => true, 'message' => 'SMS sent successfully.', 'data' => $response->json()];
            }

            Log::error('NextSMS failed', ['to' => $to, 'status' => $response->status(), 'body' => $response->body()]);
            return ['success' => false, 'message' => 'SMS gateway returned error: ' . $response->status()];
        } catch (\Exception $e) {
            Log::error('NextSMS exception: ' . $e->getMessage(), ['to' => $to]);
            return ['success' => false, 'message' => 'Failed to send SMS: ' . $e->getMessage()];
        }
    }

    /**
     * Send OTP SMS.
     */
    public function sendOtp(string $to, string $otp, string $appName = 'MannaPOS'): array
    {
        $text = "Welcome to {$appName}! Your verification code is: {$otp}. It expires in 30 minutes. Do not share this code with anyone.";
        return $this->send($to, $text);
    }

    /**
     * Send welcome SMS.
     */
    public function sendWelcome(string $to, string $name, string $appName = 'MannaPOS'): array
    {
        $text = "Hi {$name}, welcome to {$appName}! Your account has been created successfully. Enjoy your 14-day free trial. Login to get started.";
        return $this->send($to, $text);
    }

    /**
     * Send password reset SMS.
     */
    public function sendPasswordReset(string $to, string $url, string $appName = 'MannaPOS'): array
    {
        $text = "Your {$appName} password reset link: {$url}";
        return $this->send($to, $text);
    }
}
