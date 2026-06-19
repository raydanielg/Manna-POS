<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('file_cabinet_folders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('file_cabinet_folders')->onDelete('cascade');
            $table->string('name');
            $table->string('color')->default('#64748b');
            $table->timestamps();
        });

        Schema::table('file_cabinets', function (Blueprint $table) {
            $table->foreignId('folder_id')->nullable()->constrained('file_cabinet_folders')->onDelete('cascade')->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('file_cabinets', function (Blueprint $table) {
            $table->dropForeign(['folder_id']);
            $table->dropColumn('folder_id');
        });
        Schema::dropIfExists('file_cabinet_folders');
    }
};
