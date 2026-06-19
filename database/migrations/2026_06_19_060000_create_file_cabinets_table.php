<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('file_cabinets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type'); // mime type
            $table->string('file_extension', 20);
            $table->unsignedBigInteger('file_size')->default(0);
            $table->string('category')->nullable(); // e.g. invoice, receipt, contract
            $table->foreignId('related_id')->nullable(); // polymorphic-like relation
            $table->string('related_type')->nullable(); // e.g. sale, purchase, customer
            $table->enum('visibility', ['private', 'shared'])->default('private');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('file_cabinets');
    }
};
