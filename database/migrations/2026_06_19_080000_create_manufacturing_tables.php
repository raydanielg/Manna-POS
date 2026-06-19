<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Recipes / BOM (Bill of Materials)
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // final product
            $table->decimal('output_quantity', 15, 4)->default(1); // how many units produced
            $table->string('output_unit')->nullable();
            $table->decimal('labor_cost', 15, 2)->default(0);
            $table->decimal('overhead_cost', 15, 2)->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        // Recipe Ingredients / BOM Items
        Schema::create('recipe_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // raw material
            $table->decimal('quantity', 15, 4);
            $table->string('unit')->nullable();
            $table->decimal('cost', 15, 2)->nullable(); // estimated cost
            $table->timestamps();
        });

        // Production Runs
        Schema::create('production_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('recipe_id')->constrained()->onDelete('cascade');
            $table->string('batch_number')->unique();
            $table->decimal('planned_quantity', 15, 4);
            $table->decimal('actual_quantity', 15, 4)->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('total_cost', 15, 2)->nullable();
            $table->enum('status', ['planned', 'in_progress', 'completed', 'cancelled'])->default('planned');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Production Material Usage
        Schema::create('production_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_run_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->decimal('planned_quantity', 15, 4);
            $table->decimal('actual_quantity', 15, 4)->nullable();
            $table->decimal('unit_cost', 15, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_usages');
        Schema::dropIfExists('production_runs');
        Schema::dropIfExists('recipe_items');
        Schema::dropIfExists('recipes');
    }
};
