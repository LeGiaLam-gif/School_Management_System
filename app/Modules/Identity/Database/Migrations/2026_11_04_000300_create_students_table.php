<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ho so sinh vien (1-1 voi users). Chay SAU migration Academic (programs).
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('student_code', 20)->unique();
            $table->foreignId('program_id')->constrained()->restrictOnDelete();
            $table->unsignedSmallInteger('cohort_year');           // khoa tuyen sinh, vd 2022
            $table->date('date_of_birth')->nullable();
            $table->string('gender', 10)->nullable();               // Gender
            $table->string('phone', 20)->nullable();
            $table->string('address')->nullable();
            $table->string('status', 20)->default('studying');      // StudentStatus
            $table->timestamps();

            $table->index(['program_id', 'cohort_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
