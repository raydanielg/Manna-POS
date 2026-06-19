<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;

class TestSmtp extends Command
{
    protected $signature = 'test:smtp {email? : Email address to send test to}';
    protected $description = 'Test SMTP mail settings';

    public function handle(): int
    {
        $email = $this->argument('email') ?? config('mail.from.address');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Invalid email address: ' . $email);
            return 1;
        }

        $this->info('SMTP Settings:');
        $this->table(['Setting', 'Value'], [
            ['Host', config('mail.mailers.smtp.host')],
            ['Port', config('mail.mailers.smtp.port')],
            ['Encryption', config('mail.mailers.smtp.encryption') ?: 'None'],
            ['Username', config('mail.mailers.smtp.username')],
            ['From Address', config('mail.from.address')],
        ]);

        $this->newLine();
        $this->info('Sending test email to: ' . $email);

        try {
            Mail::raw('This is a test email from MannaPOS.\n\nIf you received this, your SMTP configuration is working correctly.\n\nSent at: ' . now(), function (Message $message) use ($email) {
                $message->to($email)
                        ->subject('MannaPOS SMTP Test');
            });

            $this->newLine();
            $this->info('SUCCESS: Test email sent to ' . $email);
            $this->info('Check your inbox (and spam folder) for the test email.');
            return 0;
        } catch (\Exception $e) {
            $this->newLine();
            $this->error('FAILED: Could not send email');
            $this->error($e->getMessage());
            return 1;
        }
    }
}
