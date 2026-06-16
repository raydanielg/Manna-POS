<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLocationAndStatusToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'phone')) { $table->string('phone')->nullable()->after('email'); }
            if (!Schema::hasColumn('users', 'status')) { $table->string('status')->default('active')->after('role'); }
            if (!Schema::hasColumn('users', 'business_name')) { $table->string('business_name')->nullable()->after('status'); }
            if (!Schema::hasColumn('users', 'business_type')) { $table->string('business_type')->nullable()->after('business_name'); }
            if (!Schema::hasColumn('users', 'business_address')) { $table->string('business_address')->nullable()->after('business_type'); }
            if (!Schema::hasColumn('users', 'business_city')) { $table->string('business_city')->nullable()->after('business_address'); }
            if (!Schema::hasColumn('users', 'business_country')) { $table->string('business_country')->nullable()->after('business_city'); }
            if (!Schema::hasColumn('users', 'currency')) { $table->string('currency', 3)->default('TZS')->after('business_country'); }
            if (!Schema::hasColumn('users', 'tax_percentage')) { $table->decimal('tax_percentage', 5, 2)->default(0)->after('currency'); }
            if (!Schema::hasColumn('users', 'fiscal_year_start')) { $table->string('fiscal_year_start')->nullable()->after('tax_percentage'); }
            if (!Schema::hasColumn('users', 'owner_id')) {
                $table->foreignId('owner_id')->nullable()->after('fiscal_year_start');
                $table->foreign('owner_id')->references('id')->on('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('users', 'block_reason')) { $table->text('block_reason')->nullable()->after('owner_id'); }
            if (!Schema::hasColumn('users', 'blocked_at')) { $table->timestamp('blocked_at')->nullable()->after('block_reason'); }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $cols = ['phone','status','business_name','business_type','business_address','business_city','business_country','currency','tax_percentage','fiscal_year_start','owner_id','block_reason','blocked_at'];
            foreach ($cols as $c) { if (Schema::hasColumn('users', $c)) { $table->dropColumn($c); } }
        });
    }
}
