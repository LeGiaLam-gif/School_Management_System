<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ho so giang vien (1-1 voi users). Chay SAU migration Academic (departments).
        Schema::create('lecturers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('lecturer_code', 20)->unique();
            $table->foreignId('department_id')->constrained()->restrictOnDelete();
            $table->string('academic_title', 50)->nullable();       // ThS, TS, PGS...
            $table->string('phone', 20)->nullable();
            $table->string('office', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lecturers');
    }
};
