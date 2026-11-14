<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_warnings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('semester_id')
                ->constrained()
                ->restrictOnDelete();

            // Mức cảnh báo theo TT08/2021: 1 = lần 1, 2 = lần 2, 3 = buộc thôi học
            $table->tinyInteger('warning_level')->unsigned();

            // Năm học của sinh viên tại thời điểm cảnh báo (1–4+)
            $table->tinyInteger('student_year')->unsigned();

            // Điểm trung bình học kỳ (ĐTBHK) – thang 4
            $table->decimal('semester_gpa', 3, 2);

            // Điểm trung bình chung tích lũy (ĐTBCTL) – thang 4
            $table->decimal('cumulative_gpa', 3, 2);

            // Số tín chỉ đăng ký trong kỳ
            $table->smallInteger('registered_credits')->unsigned();

            // Số tín chỉ rớt trong kỳ
            $table->smallInteger('failed_credits')->unsigned();

            // Tổng số tín chỉ nợ từ đầu khóa
            $table->smallInteger('total_debt_credits')->unsigned();

            // Loại vi phạm theo TT08/2021:
            //   low_semester_gpa       – ĐTBHK dưới ngưỡng
            //   low_cumulative_gpa     – ĐTBCTL dưới ngưỡng theo năm học
            //   failed_half_credits    – rớt > 50% tín chỉ đăng ký trong kỳ
            //   exceeded_debt_credits  – tổng nợ tích lũy > 24 tín chỉ
            $table->string('violation_type', 30);

            // Mô tả cụ thể lý do cảnh báo (tiếng Việt, căn cứ điều khoản TT08)
            $table->text('reason');

            // Trạng thái xử lý: active | resolved | dismissed
            $table->string('status', 20)->default('active');

            $table->timestamps();

            // Index hỗ trợ tìm kiếm theo sinh viên + trạng thái, học kỳ + mức cảnh báo
            $table->index(['student_id', 'status']);
            $table->index(['semester_id', 'warning_level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_warnings');
    }
};
