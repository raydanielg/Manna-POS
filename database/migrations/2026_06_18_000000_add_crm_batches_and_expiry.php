<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCrmBatchesAndExpiry extends Migration
{
    public function up()
    {
        // 1. CRM Activities
        Schema::create('crm_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('type', ['call', 'email', 'meeting', 'note', 'task', 'sms', 'visit'])->default('note');
            $table->string('subject')->nullable();
            $table->text('description')->nullable();
            $table->dateTime('follow_up_date')->nullable();
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
        });

        // 2. Product Batches (for expiry & supplier price per batch)
        Schema::create('product_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('purchase_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->string('batch_number')->nullable();
            $table->decimal('quantity', 15, 4)->default(0);
            $table->decimal('unit_cost', 15, 2)->default(0);
            $table->date('expiry_date')->nullable();
            $table->date('manufacture_date')->nullable();
            $table->enum('status', ['active', 'expired', 'depleted'])->default('active');
            $table->timestamps();
            $table->index(['product_id', 'expiry_date']);
            $table->index(['supplier_id', 'product_id']);
        });

        // 3. Add expiry_date to purchase_items for easier lookup
        Schema::table('purchase_items', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_items', 'expiry_date')) {
                $table->date('expiry_date')->nullable()->after('total');
            }
            if (!Schema::hasColumn('purchase_items', 'batch_number')) {
                $table->string('batch_number')->nullable()->after('expiry_date');
            }
        });

        // 4. Add customer_group_id to customers if CRM needs segmentation
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'lead_source')) {
                $table->string('lead_source')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('customers', 'last_contact_date')) {
                $table->date('last_contact_date')->nullable()->after('lead_source');
            }
        });
    }

    public function down()
    {
        Schema::dropIfExists('crm_activities');
        Schema::dropIfExists('product_batches');
        Schema::table('purchase_items', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_items', 'expiry_date')) {
                $table->dropColumn('expiry_date');
            }
            if (Schema::hasColumn('purchase_items', 'batch_number')) {
                $table->dropColumn('batch_number');
            }
        });
        Schema::table('customers', function (Blueprint $table) {
            if (Schema::hasColumn('customers', 'lead_source')) {
                $table->dropColumn('lead_source');
            }
            if (Schema::hasColumn('customers', 'last_contact_date')) {
                $table->dropColumn('last_contact_date');
            }
        });
    }
}
