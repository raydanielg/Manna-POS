<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddCreatedByToRemainingTables extends Migration {
    public function up() {
        $tables = [
            'brands',
            'product_categories',
            'units',
            'tax_rates',
            'expense_categories',
            'customer_groups',
            'warranties',
            'roles',
            'selling_price_groups',
            'shipments',
            'business_locations',
            'product_variations',
            'notification_templates',
        ];

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
            if (Schema::hasColumn($table, 'created_by')) {
                DB::table($table)->whereNull('created_by')->update(['created_by' => $firstUser]);
            }
        }
    }

    public function down() {
        $tables = [
            'brands',
            'product_categories',
            'units',
            'tax_rates',
            'expense_categories',
            'customer_groups',
            'warranties',
            'roles',
            'selling_price_groups',
            'shipments',
            'business_locations',
            'product_variations',
            'notification_templates',
        ];

        foreach ($tables as $table) {
            if (Schema::hasColumn($table, 'created_by')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->dropForeign(['created_by']);
                    $t->dropColumn('created_by');
                });
            }
        }
    }
}
