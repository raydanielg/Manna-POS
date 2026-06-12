<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImageToProductsTable extends Migration {
    public function up() {
        if (!Schema::hasColumn('products', 'image')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('image')->nullable()->after('description');
            });
        }
    }
    public function down() {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }
}
