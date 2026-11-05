<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();          // vd 2026-1
            $table->string('name', 100);                   // vd Hoc ky 1
            $table->string('academic_year', 20);           // vd 2026-2027
            $table->date('start_date');
            $table->date('end_date');
            $table->dateTime('registration_open_at')->nullable();
            $table->dateTime('registration_close_at')->nullable();
            $table->date('drop_deadline')->nullable();     // han rut hoc phan
            $table->unsignedTinyInteger('min_credits')->default(14);
            $table->unsignedTinyInteger('max_credits')->default(24);
            $table->boolean('is_current')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('semesters');
    }
};
