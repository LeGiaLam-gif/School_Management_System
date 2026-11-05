<?php

namespace App\Modules\Grading\Database\Seeders;

use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Grading\Models\FinalResult;
use App\Modules\Grading\Models\Grade;
use App\Modules\Grading\Models\GradeComponent;
use App\Modules\Grading\Support\GradeCalculator;
use App\Modules\Teaching\Models\ClassSection;
use App\Shared\Enums\ClassSectionStatus;
use Illuminate\Database\Seeder;

/**
 * Diem mau (chuyen tu CourseSectionSeeder cu). Thanh phan diem giu cong thuc cua repo cu:
 * Giua ky 40% + Cuoi ky 60%, nhung nay la cau hinh theo lop thay vi hardcode.
 * Lop hoc ky cu da co diem va da cong bo; lop hoc ky hien tai chi co cau hinh thanh phan.
 */
class GradingDemoSeeder extends Seeder
{
    public function run(): void
    {
        $calculator = app(GradeCalculator::class);

        $sections = ClassSection::with('semester')->orderBy('id')->get();

        foreach ($sections as $i => $section) {
            $midterm = GradeComponent::firstOrCreate(
                ['class_section_id' => $section->id, 'name' => 'Giữa kỳ'],
                ['weight' => 40, 'max_score' => 10, 'sort_order' => 1]
            );
            $final = GradeComponent::firstOrCreate(
                ['class_section_id' => $section->id, 'name' => 'Cuối kỳ'],
                ['weight' => 60, 'max_score' => 10, 'sort_order' => 2]
            );

            if ($section->status !== ClassSectionStatus::GradesPublished) {
                continue;
            }

            $enrollments = Enrollment::where('class_section_id', $section->id)->orderBy('id')->get();

            foreach ($enrollments as $s => $enrollment) {
                $mid = round(5 + (($s * 7 + $i * 3) % 50) / 10, 1);
                $fin = round(5 + (($s * 11 + $i * 5) % 50) / 10, 1);

                Grade::firstOrCreate(
                    ['enrollment_id' => $enrollment->id, 'grade_component_id' => $midterm->id],
                    ['score' => $mid, 'graded_at' => $section->semester->end_date]
                );
                Grade::firstOrCreate(
                    ['enrollment_id' => $enrollment->id, 'grade_component_id' => $final->id],
                    ['score' => $fin, 'graded_at' => $section->semester->end_date]
                );

                $total = $calculator->weightedTotal([
                    ['score' => $mid, 'max' => 10, 'weight' => 40],
                    ['score' => $fin, 'max' => 10, 'weight' => 60],
                ]);

                FinalResult::firstOrCreate(
                    ['enrollment_id' => $enrollment->id],
                    [
                        'total_score' => $total,
                        'letter_grade' => $calculator->toLetter($total),
                        'gpa_point' => $calculator->toGpa4($total),
                        'is_passed' => $calculator->isPassed($total),
                        'published_at' => $section->semester->end_date,
                        'locked_at' => $section->semester->end_date,
                    ]
                );
            }
        }
    }
}
