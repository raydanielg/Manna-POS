<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NextSmsService;

class TestSms extends Command
{
    protected $signature = 'sms:test {phone} {--message=Hi, this is a test SMS from MannaPOS.}';
    protected $description = 'Send a test SMS via NextSMS';

    public function handle(NextSmsService $sms)
    {
        $phone = $this->argument('phone');
        $message = $this->option('message');

        $this->info("Sending SMS to: {$phone}");
        $this->info("Message: {$message}");

        $result = $sms->send($phone, $message);

        if ($result['success']) {
            $this->info('SMS sent successfully!');
            $this->info('Response: ' . json_encode($result['data'] ?? $result['message']));
        } else {
            $this->error('Failed to send SMS.');
            $this->error('Reason: ' . $result['message']);
        }

        return $result['success'] ? 0 : 1;
    }
}
