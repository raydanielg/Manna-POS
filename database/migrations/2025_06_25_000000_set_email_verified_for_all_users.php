<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class SetEmailVerifiedForAllUsers extends Migration
{
    public function up()
    {
        DB::table('users')
            ->whereNull('email_verified_at')
            ->update([
                'email_verified_at' => now(),
                'status' => 'active',
                'otp_code' => null,
                'otp_expires_at' => null,
            ]);
    }

    public function down()
    {
        // No rollback — we intentionally verify all users
    }
}
