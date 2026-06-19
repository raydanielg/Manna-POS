<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentFieldsToUserSubscriptionsTable extends Migration
{
    public function up()
    {
        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->string('snippe_session_ref')->nullable()->after('transaction_ref');
            $table->string('payment_status')->nullable()->after('snippe_session_ref');
            $table->timestamp('paid_at')->nullable()->after('payment_status');
        });
    }

    public function down()
    {
        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->dropColumn(['snippe_session_ref', 'payment_status', 'paid_at']);
        });
    }
}
