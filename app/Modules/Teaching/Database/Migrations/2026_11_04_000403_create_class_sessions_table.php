<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tung buoi hoc cu the: don vi diem danh; cho phep nghi bu / doi phong tung buoi.
        Schema::create('class_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_section_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_schedule_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedSmallInteger('session_no');
            $table->date('session_date');
            $table->unsignedTinyInteger('start_period');
            $table->unsignedTinyInteger('end_period');
            $table->foreignId('room_id')->constrained()->restrictOnDelete();
            $table->string('status', 20)->default('scheduled'); // SessionStatus
            $table->timestamps();

            $table->unique(['class_section_id', 'session_no']);
            $table->index(['class_section_id', 'session_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_sessions');
    }
};
