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

            $profiles = [
                0 => ['mid' => 9.5, 'fin' => 9.0],
                1 => ['mid' => 8.5, 'fin' => 8.0],
                2 => ['mid' => 7.5, 'fin' => 7.0],
                3 => ['mid' => 6.0, 'fin' => 5.5],
                4 => ['mid' => 9.0, 'fin' => 8.5],
                5 => ['mid' => 8.0, 'fin' => 7.5],
                6 => ['mid' => 7.0, 'fin' => 6.5],
                7 => ['mid' => 5.5, 'fin' => 5.0],
                8 => ['mid' => 8.5, 'fin' => 9.0],
                9 => ['mid' => 6.5, 'fin' => 7.0],
                10 => ['mid' => 9.5, 'fin' => 9.5],
                11 => ['mid' => 4.5, 'fin' => 5.0],
            ];

            foreach ($enrollments as $s => $enrollment) {
                $profile = $profiles[$s % count($profiles)];
                $offset = ($i % 2 === 0) ? 0.0 : -0.5;
                $mid = max(4.0, min(10.0, round($profile['mid'] + $offset, 1)));
                $fin = max(4.0, min(10.0, round($profile['fin'] - $offset, 1)));

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
