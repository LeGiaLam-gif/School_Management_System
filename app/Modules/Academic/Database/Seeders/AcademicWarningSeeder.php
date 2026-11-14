<?php

namespace App\Modules\Academic\Database\Seeders;

use App\Modules\Academic\Models\AcademicWarning;
use App\Modules\Academic\Models\Semester;
use App\Modules\Identity\Models\Student;
use App\Shared\Enums\ViolationType;
use Illuminate\Database\Seeder;

/**
 * Dữ liệu mẫu cảnh báo học vụ theo Thông tư 08/2021/TT-BGDĐT.
 *
 * Idempotent: chạy lại nhiều lần không sinh bản ghi trùng.
 * Dùng học kỳ 2025-1 (đã kết thúc) để có số liệu thực tế.
 *
 * Kịch bản mẫu:
 *   SV đầu tiên  – low_semester_gpa   (kỳ đầu, ĐTBHK = 0.72 < 0.80)   → Lần 1
 *   SV thứ hai   – low_cumulative_gpa (năm 1, ĐTBCTL = 1.10 < 1.20)    → Lần 1
 *   SV thứ ba    – failed_half_credits (rớt 10/18 TC = 55.6%)           → Lần 1
 *   SV thứ tư    – exceeded_debt_credits (nợ tích lũy 27 TC > 24)       → Lần 2
 */
class AcademicWarningSeeder extends Seeder
{
    public function run(): void
    {
        $semester = Semester::where('code', '2025-1')->first();
        if (! $semester) {
            $this->command->warn('[AcademicWarningSeeder] Không tìm thấy học kỳ 2025-1, bỏ qua.');

            return;
        }

        $students = Student::orderBy('id')->take(4)->get();
        if ($students->count() < 4) {
            $this->command->warn('[AcademicWarningSeeder] Chưa đủ 4 sinh viên mẫu, bỏ qua.');

            return;
        }

        $warnings = [
            // ── Cảnh báo 1: ĐTBHK dưới ngưỡng học kỳ đầu ──────────────────
            [
                'student_id' => $students[0]->id,
                'semester_id' => $semester->id,
                'warning_level' => 1,
                'student_year' => 1,
                'semester_gpa' => 0.72,
                'cumulative_gpa' => 0.72,
                'registered_credits' => 15,
                'failed_credits' => 6,
                'total_debt_credits' => 6,
                'violation_type' => ViolationType::LowSemesterGpa->value,
                'reason' => 'ĐTBHK đạt 0,72/4,0, thấp hơn ngưỡng 0,80 quy định cho học kỳ đầu tiên ('
                    .ViolationType::LowSemesterGpa->regulation().').',
                'status' => 'active',
            ],

            // ── Cảnh báo 2: ĐTBCTL dưới ngưỡng năm 1 ───────────────────────
            [
                'student_id' => $students[1]->id,
                'semester_id' => $semester->id,
                'warning_level' => 1,
                'student_year' => 1,
                'semester_gpa' => 1.05,
                'cumulative_gpa' => 1.10,
                'registered_credits' => 16,
                'failed_credits' => 3,
                'total_debt_credits' => 5,
                'violation_type' => ViolationType::LowCumulativeGpa->value,
                'reason' => 'ĐTBCTL đạt 1,10/4,0, thấp hơn ngưỡng 1,20 quy định cho sinh viên năm thứ nhất ('
                    .ViolationType::LowCumulativeGpa->regulation().').',
                'status' => 'active',
            ],

            // ── Cảnh báo 3: Rớt trên 50% tín chỉ đăng ký ──────────────────
            [
                'student_id' => $students[2]->id,
                'semester_id' => $semester->id,
                'warning_level' => 1,
                'student_year' => 2,
                'semester_gpa' => 1.15,
                'cumulative_gpa' => 1.50,
                'registered_credits' => 18,
                'failed_credits' => 10,   // 10/18 ≈ 55.6% > 50%
                'total_debt_credits' => 16,
                'violation_type' => ViolationType::FailedHalfCredits->value,
                'reason' => 'Rớt 10/18 tín chỉ đăng ký (55,6%), vượt quá 50% số tín chỉ đăng ký trong học kỳ ('
                    .ViolationType::FailedHalfCredits->regulation().').',
                'status' => 'resolved',
            ],

            // ── Cảnh báo 4: Nợ tích lũy vượt 24 TC – cảnh báo lần 2 ───────
            [
                'student_id' => $students[3]->id,
                'semester_id' => $semester->id,
                'warning_level' => 2,
                'student_year' => 3,
                'semester_gpa' => 0.90,
                'cumulative_gpa' => 1.42,
                'registered_credits' => 17,
                'failed_credits' => 5,
                'total_debt_credits' => 27,  // > 24 TC → vi phạm
                'violation_type' => ViolationType::ExceededDebtCredits->value,
                'reason' => 'Tổng số tín chỉ nợ từ đầu khóa đạt 27 tín chỉ, vượt quá giới hạn 24 tín chỉ ('
                    .ViolationType::ExceededDebtCredits->regulation().'). Đây là lần cảnh báo thứ 2.',
                'status' => 'active',
            ],
        ];

        foreach ($warnings as $data) {
            AcademicWarning::firstOrCreate(
                [
                    'student_id' => $data['student_id'],
                    'semester_id' => $data['semester_id'],
                    'violation_type' => $data['violation_type'],
                ],
                $data
            );
        }

        $this->command->info('[AcademicWarningSeeder] Đã tạo '.count($warnings).' bản ghi cảnh báo học vụ mẫu.');
    }
}
