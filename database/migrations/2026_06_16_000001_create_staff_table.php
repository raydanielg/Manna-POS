<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaffTable extends Migration
{
    public function up()
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone', 20)->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('role')->default('staff');
            $table->string('department', 100)->nullable();
            $table->string('position', 100)->nullable();
            $table->decimal('salary', 12, 2)->default(0);
            $table->string('pay_type', 20)->default('monthly');
            $table->date('hire_date')->nullable();
            $table->string('status', 20)->default('active');
            $table->text('address')->nullable();
            $table->string('emergency_contact', 100)->nullable();
            $table->string('emergency_phone', 20)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('staff');
    }
}
