<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Diem danh theo BUOI HOC (class_sessions) cua tung dang ky (thay cho attendances theo ngay cua repo cu).
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('enrollment_id')->constrained()->restrictOnDelete();
            $table->string('status', 20)->default('present'); // AttendanceStatus
            $table->string('note')->nullable();
            $table->foreignId('marked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('marked_at')->nullable();
            $table->timestamps();

            $table->unique(['class_session_id', 'enrollment_id']);
            $table->index('enrollment_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};
