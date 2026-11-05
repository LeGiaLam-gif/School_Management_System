<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Khung chuong trinh: nganh - hoc phan.
        Schema::create('program_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('suggested_semester_no')->nullable();
            $table->string('type', 20)->default('required'); // CourseType
            $table->timestamps();

            $table->unique(['program_id', 'course_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_courses');
    }
};
