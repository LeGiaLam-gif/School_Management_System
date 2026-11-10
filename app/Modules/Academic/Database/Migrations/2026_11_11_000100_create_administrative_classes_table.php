<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('administrative_classes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name');
            $table->foreignId('program_id')->constrained()->restrictOnDelete();
            $table->unsignedSmallInteger('academic_year'); // Khoa tuyen sinh, vd: 2022
            $table->foreignId('advisor_lecturer_id')->nullable()->constrained('lecturers')->nullOnDelete();
            $table->timestamps();

            $table->index(['program_id', 'academic_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('administrative_classes');
    }
};
