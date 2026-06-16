<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddCreatedByToCoreTables extends Migration {
    public function up() {
        $tables = ['products','customers','sales','purchases','suppliers','stock_adjustments','stock_transfers','discounts'];
        foreach ($tables as $table) {
            if (!Schema::hasColumn($table, 'created_by')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->after('id');
                });
            }
        }
        // Backfill existing records with first user or admin
        $firstUser = DB::table('users')->orderBy('id')->value('id') ?? 1;
        foreach ($tables as $table) {
            DB::table($table)->whereNull('created_by')->update(['created_by' => $firstUser]);
        }
    }
    public function down() {
        $tables = ['products','customers','sales','purchases','suppliers','stock_adjustments','stock_transfers','discounts'];
        foreach ($tables as $table) {
            if (Schema::hasColumn($table, 'created_by')) {
                Schema::table($table, function (Blueprint $t) { $t->dropForeign(['created_by']); $t->dropColumn('created_by'); });
            }
        }
    }
}
