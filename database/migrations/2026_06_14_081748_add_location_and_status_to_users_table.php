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
            $table->string('phone')->nullable()->after('email');
            $table->string('status')->default('active')->after('role');
            $table->string('business_name')->nullable()->after('status');
            $table->string('business_type')->nullable()->after('business_name');
            $table->string('business_address')->nullable()->after('business_type');
            $table->string('business_city')->nullable()->after('business_address');
            $table->string('business_country')->nullable()->after('business_city');
            $table->string('currency', 3)->default('TZS')->after('business_country');
            $table->decimal('tax_percentage', 5, 2)->default(0)->after('currency');
            $table->string('fiscal_year_start')->nullable()->after('tax_percentage');
            $table->foreignId('owner_id')->nullable()->constrained('users')->after('fiscal_year_start');
            $table->text('block_reason')->nullable()->after('owner_id');
            $table->timestamp('blocked_at')->nullable()->after('block_reason');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone','status','business_name','business_type','business_address','business_city','business_country','currency','tax_percentage','fiscal_year_start','owner_id','block_reason','blocked_at']);
        });
    }
}
