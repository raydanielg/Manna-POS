<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_todos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('date')->nullable();
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['user_id', 'date']);
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'sort_order']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_todos');
    }
};
