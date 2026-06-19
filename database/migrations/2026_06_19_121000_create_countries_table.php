<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountriesTable extends Migration
{
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 2)->unique(); // ISO 3166-1 alpha-2
            $table->string('phone_code', 10)->nullable();
            $table->string('flag_emoji', 10)->nullable();
            $table->string('region', 50)->nullable(); // East Africa, West Africa, etc.
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('countries');
    }
}
