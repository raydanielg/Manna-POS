<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBusinessFieldsToUsersTable extends Migration {
    public function up() {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->after('email');
            $table->string('business_name')->nullable()->after('role');
            $table->string('business_type', 50)->nullable()->after('business_name');
            $table->string('business_address')->nullable()->after('business_type');
            $table->string('business_city', 100)->nullable()->after('business_address');
            $table->string('business_country', 100)->default('Tanzania')->after('business_city');
            $table->string('currency', 10)->default('TZS')->after('business_country');
            $table->decimal('tax_percentage', 5, 2)->default(18.00)->after('currency');
            $table->string('fiscal_year_start', 20)->default('January')->after('tax_percentage');
        });
    }
    public function down() {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone','business_name','business_type','business_address','business_city','business_country','currency','tax_percentage','fiscal_year_start']);
        });
    }
}