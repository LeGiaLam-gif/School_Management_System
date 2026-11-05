<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Lop hoc phan = mot lan mo hoc phan trong mot hoc ky (thay cho course_sections cua repo cu).
        Schema::create('class_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->restrictOnDelete();
            $table->foreignId('semester_id')->constrained()->restrictOnDelete();
            $table->foreignId('lecturer_id')->nullable()->constrained()->nullOnDelete(); // null cho toi khi phan cong
            $table->string('code', 30);                                  // vd CS101.01
            $table->unsignedSmallInteger('capacity');
            // So dem phi chuan: cap nhat TRONG transaction dang ky, sau lockForUpdate (BR-05).
            $table->unsignedSmallInteger('enrolled_count')->default(0);
            $table->string('status', 20)->default('draft')->index();    // ClassSectionStatus
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['course_id', 'semester_id', 'code']);
            $table->index(['semester_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_sections');
    }
};
