<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('bank_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_account_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['deposit','withdrawal','transfer_in','transfer_out','payment','refund','adjustment']);
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_after', 15, 2);
            $table->string('reference_number')->nullable();
            $table->text('description')->nullable();
            $table->string('category')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('contact_type')->nullable();
            $table->nullableMorphs('transactionable');
            $table->date('transaction_date');
            $table->timestamps();

            $table->index(['bank_account_id','transaction_date']);
            $table->index(['user_id','type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('bank_transactions');
    }
}
