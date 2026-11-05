<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Lich hoc lap hang tuan. day_of_week theo ISO-8601: 1 = Thu hai ... 7 = Chu nhat.
        Schema::create('class_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_section_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_id')->constrained()->restrictOnDelete();
            $table->unsignedTinyInteger('day_of_week');
            $table->unsignedTinyInteger('start_period');
            $table->unsignedTinyInteger('end_period');
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();

            $table->index(['room_id', 'day_of_week']); // do xung dot phong (BR-06)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_schedules');
    }
};
