<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaffAttendanceTable extends Migration
{
    public function up()
    {
        Schema::create('staff_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->time('clock_in')->nullable();
            $table->time('clock_out')->nullable();
            $table->string('status', 20)->default('present');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['staff_id', 'date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('staff_attendance');
    }
}
