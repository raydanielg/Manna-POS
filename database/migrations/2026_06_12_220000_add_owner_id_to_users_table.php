<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOwnerIdToUsersTable extends Migration {
    public function up() {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'owner_id')) {
                $table->unsignedBigInteger('owner_id')->nullable()->after('id');
            }
        });
    }
    public function down() {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('owner_id');
        });
    }
}