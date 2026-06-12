<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockAdjustmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->date('adjustment_date');
            $table->enum('type', ['addition', 'subtraction'])->default('addition');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->decimal('quantity', 15, 4);
            $table->decimal('unit_cost', 15, 2)->nullable();
            $table->string('reason')->nullable();
            $table->text('notes')->nullable();
            $table->foreign('product_id')->references('id')->on('products')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_adjustments');
    }
}
