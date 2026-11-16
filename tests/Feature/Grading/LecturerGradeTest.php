<?php

namespace Tests\Feature\Grading;

use App\Modules\Academic\Models\Course;
use App\Modules\Academic\Models\Department;
use App\Modules\Academic\Models\Program;
use App\Modules\Academic\Models\Semester;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Grading\Models\GradeComponent;
use App\Modules\Identity\Database\Seeders\RolePermissionSeeder;
use App\Modules\Identity\Models\Lecturer;
use App\Modules\Identity\Models\Student;
use App\Modules\Identity\Models\User;
use App\Modules\Teaching\Models\ClassSection;
use App\Shared\Enums\ClassSectionStatus;
use App\Shared\Enums\EnrollmentStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LecturerGradeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_lecturer_can_enter_and_publish_grades_for_own_class(): void
    {
        $department = Department::query()->create(['code' => 'CNTT', 'name' => 'Công nghệ thông tin']);
        $program = Program::query()->create([
            'department_id' => $department->id,
            'code' => 'KTPM',
            'name' => 'Kỹ thuật phần mềm',
            'total_credits' => 130,
        ]);
        $semester = Semester::query()->create([
            'code' => '2026-HK1',
            'name' => 'Học kỳ 1',
            'academic_year' => '2026-2027',
            'start_date' => '2026-09-01',
            'end_date' => '2027-01-15',
            'is_current' => true,
        ]);
        $course = Course::query()->create([
            'department_id' => $department->id,
            'code' => 'INT101',
            'name' => 'Lập trình Web',
            'credits' => 3,
        ]);

        /** @var User $lecturerUser */
        $lecturerUser = User::factory()->create();
        $lecturerUser->assignRole('lecturer');
        $lecturer = Lecturer::query()->create([
            'user_id' => $lecturerUser->id,
            'department_id' => $department->id,
            'lecturer_code' => 'GV001',
        ]);

        $section = ClassSection::query()->create([
            'course_id' => $course->id,
            'semester_id' => $semester->id,
            'lecturer_id' => $lecturer->id,
            'code' => 'INT101-01',
            'capacity' => 40,
            'enrolled_count' => 1,
            'status' => ClassSectionStatus::Finished,
        ]);

        $midterm = GradeComponent::query()->create([
            'class_section_id' => $section->id,
            'name' => 'Giữa kỳ',
            'weight' => 40,
            'max_score' => 10,
            'sort_order' => 1,
        ]);
        $final = GradeComponent::query()->create([
            'class_section_id' => $section->id,
            'name' => 'Cuối kỳ',
            'weight' => 60,
            'max_score' => 10,
            'sort_order' => 2,
        ]);

        /** @var User $studentUser */
        $studentUser = User::factory()->create(['name' => 'Trần Thị B']);
        $studentUser->assignRole('student');
        $student = Student::query()->create([
            'user_id' => $studentUser->id,
            'program_id' => $program->id,
            'student_code' => 'SV002',
            'cohort_year' => 2024,
        ]);

        $enrollment = Enrollment::query()->create([
            'student_id' => $student->id,
            'class_section_id' => $section->id,
            'status' => EnrollmentStatus::Registered,
        ]);

        $this->actingAs($lecturerUser)
            ->post(route('lecturer.grades.store', $section), [
                'grades' => [
                    $enrollment->id => [
                        $midterm->id => 8.0,
                        $final->id => 9.0,
                    ],
                ],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('final_results', [
            'enrollment_id' => $enrollment->id,
            'total_score' => 8.60,
            'letter_grade' => 'A',
            'is_passed' => true,
            'published_at' => null,
        ]);

        $this->actingAs($lecturerUser)
            ->post(route('lecturer.grades.publish', $section))
            ->assertRedirect();

        $this->assertDatabaseHas('class_sections', [
            'id' => $section->id,
            'status' => ClassSectionStatus::GradesPublished->value,
        ]);

        $this->actingAs($lecturerUser)
            ->post(route('lecturer.grades.store', $section), [
                'grades' => [
                    $enrollment->id => [
                        $midterm->id => 5.0,
                        $final->id => 5.0,
                    ],
                ],
            ])
            ->assertForbidden();
    }

    public function test_lecturer_cannot_edit_grades_of_other_lecturers_class(): void
    {
        $department = Department::query()->create(['code' => 'CNTT2', 'name' => 'Khoa CNTT']);
        $semester = Semester::query()->create([
            'code' => '2026-HK2',
            'name' => 'Học kỳ 2',
            'academic_year' => '2026-2027',
            'start_date' => '2027-02-01',
            'end_date' => '2027-06-15',
            'is_current' => false,
        ]);
        $course = Course::query()->create([
            'department_id' => $department->id,
            'code' => 'INT202',
            'name' => 'Cơ sở dữ liệu',
            'credits' => 3,
        ]);

        /** @var User $ownerUser */
        $ownerUser = User::factory()->create();
        $ownerUser->assignRole('lecturer');
        $owner = Lecturer::query()->create([
            'user_id' => $ownerUser->id,
            'department_id' => $department->id,
            'lecturer_code' => 'GV010',
        ]);

        /** @var User $otherUser */
        $otherUser = User::factory()->create();
        $otherUser->assignRole('lecturer');
        Lecturer::query()->create([
            'user_id' => $otherUser->id,
            'department_id' => $department->id,
            'lecturer_code' => 'GV011',
        ]);

        $section = ClassSection::query()->create([
            'course_id' => $course->id,
            'semester_id' => $semester->id,
            'lecturer_id' => $owner->id,
            'code' => 'INT202-01',
            'capacity' => 40,
            'enrolled_count' => 0,
            'status' => ClassSectionStatus::InProgress,
        ]);

        $this->actingAs($otherUser)
            ->post(route('lecturer.grades.components', $section), [
                'components' => [
                    ['name' => 'Giữa kỳ', 'weight' => 40, 'max_score' => 10],
                    ['name' => 'Cuối kỳ', 'weight' => 60, 'max_score' => 10],
                ],
            ])
            ->assertForbidden();
    }
}
