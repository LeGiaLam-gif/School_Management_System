<?php

namespace App\Modules\Enrollment\Database\Seeders;

use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Identity\Models\Student;
use App\Modules\Teaching\Models\ClassSection;
use App\Shared\Enums\EnrollmentStatus;
use Illuminate\Database\Seeder;

/**
 * Dang ky mau (chuyen tu CourseSectionSeeder cu): hoc ky cu cho tat ca sinh vien (completed),
 * hoc ky hien tai cho 8 sinh vien dau (registered).
 */
class EnrollmentDemoSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::orderBy('id')->get();
        $sections = ClassSection::with('semester')->orderBy('id')->get();

        foreach ($sections as $section) {
            $isCurrent = $section->semester->is_current;
            $enrolled = $isCurrent ? $students->take(8) : $students;

            foreach ($enrolled as $student) {
                Enrollment::firstOrCreate(
                    ['student_id' => $student->id, 'class_section_id' => $section->id],
                    [
                        'status' => $isCurrent ? EnrollmentStatus::Registered : EnrollmentStatus::Completed,
                        'registered_at' => $section->semester->start_date,
                    ]
                );
            }

            $section->update(['enrolled_count' => $section->enrollments()->active()->count()]);
        }
    }
}
