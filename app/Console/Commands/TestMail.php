<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMail extends Command
{
    protected $signature = 'mail:test {email}';
    protected $description = 'Send a test email';

    public function handle()
    {
        $to = $this->argument('email');
        $host = config('mail.mailers.smtp.host');
        $port = config('mail.mailers.smtp.port');

        $this->info("SMTP Host: {$host}:{$port}");
        $this->info("Sending test email to: {$to}");

        try {
            Mail::raw("Hi! This is a test email from MannaPOS via {$host}. If you received this, your SMTP is working!", function ($message) use ($to) {
                $message->to($to)->subject('MannaPOS SMTP Test');
            });
            $this->info('Email sent successfully!');
        } catch (\Exception $e) {
            $this->error('Failed: ' . $e->getMessage());
        }

        return 0;
    }
}
