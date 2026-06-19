<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Loan Products (templates for loans)
        Schema::create('loan_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('min_amount', 15, 2)->default(0);
            $table->decimal('max_amount', 15, 2)->default(0);
            $table->decimal('interest_rate', 5, 2)->default(0); // annual %
            $table->enum('interest_type', ['flat', 'reducing_balance'])->default('flat');
            $table->integer('duration_min')->default(1); // months
            $table->integer('duration_max')->default(12);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        // Loans (applications)
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('loan_product_id')->nullable()->constrained('loan_products')->onDelete('set null');
            $table->string('loan_number')->unique();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->decimal('principal_amount', 15, 2);
            $table->decimal('interest_rate', 5, 2);
            $table->enum('interest_type', ['flat', 'reducing_balance'])->default('flat');
            $table->integer('duration_months');
            $table->decimal('total_interest', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('balance', 15, 2)->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'disbursed', 'active', 'completed', 'defaulted'])->default('pending');
            $table->text('purpose')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        // Loan Repayment Schedules
        Schema::create('loan_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->onDelete('cascade');
            $table->integer('installment_number');
            $table->date('due_date');
            $table->decimal('principal_amount', 15, 2);
            $table->decimal('interest_amount', 15, 2);
            $table->decimal('total_amount', 15, 2);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('balance', 15, 2)->default(0);
            $table->enum('status', ['pending', 'paid', 'partial', 'overdue'])->default('pending');
            $table->date('paid_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Guarantors
        Schema::create('guarantors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('loan_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('id_number')->nullable();
            $table->text('address')->nullable();
            $table->string('relationship')->nullable();
            $table->decimal('pledged_amount', 15, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Loan Repayments (payments made)
        Schema::create('loan_repayments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->onDelete('cascade');
            $table->foreignId('loan_schedule_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('amount', 15, 2);
            $table->date('payment_date');
            $table->string('payment_method')->default('cash');
            $table->string('reference_number')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_repayments');
        Schema::dropIfExists('guarantors');
        Schema::dropIfExists('loan_schedules');
        Schema::dropIfExists('loans');
        Schema::dropIfExists('loan_products');
    }
};
