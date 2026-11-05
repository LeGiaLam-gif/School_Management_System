<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bang trung tam: diem danh, diem thanh phan va ket qua deu tham chieu enrollment_id.
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->restrictOnDelete();
            $table->foreignId('class_section_id')->constrained()->restrictOnDelete();
            $table->string('status', 20)->default('registered');       // EnrollmentStatus
            $table->timestamp('registered_at')->nullable();
            $table->timestamp('dropped_at')->nullable();
            // Admin dang ky ho (ngoai le co ghi log): ai lam va vi sao.
            $table->foreignId('registered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('override_reason')->nullable();
            $table->timestamps();

            // Khong dang ky trung; hoc lai = lop hoc phan khac => ban ghi moi (muc 6.5).
            $table->unique(['student_id', 'class_section_id']);
            $table->index(['class_section_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
